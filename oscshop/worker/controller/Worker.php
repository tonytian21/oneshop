<?php
namespace osc\worker\controller;

use think\worker\Server;
use Workerman\Lib\Timer;
use think\Db;
use think\cache\Driver\Redis;

class Worker extends Server
{
    /**
     * 每个进程启动
     * @param $worker
     */
    public function onWorkerStart($worker)
    {
        $redis = new Redis();
        // 定时器添加商品
        Timer::add(1, function ()use($worker,$redis){
            //获取redis上的商品添加队列，生成期数信息，并记录期数至redis备用
            $goods_id = $redis->rpop('GOODS:ADDTERM');

            $this->AutoAddTerm($goods_id);
        });

        // 定时器计算开奖结果
        Timer::add(1, function ()use($worker,$redis){
            $term_num = $redis->rpop('PRIZE:COMPUTE');

            $this->ComputePrizeNum($term_num);
        });
    }

    /**
     * [AutoAddTerm description]
     * @param [type] $goods_id [description]
     */
    private function AutoAddTerm($goods_id){
        if(!$goods_id)
            return ;

        $goodsInfo = Db::name('goods')->where('goods_id',$goods_id)->find();

        if(!$goodsInfo)
            return;

        //计算总共需要参与的份数 价格/最小购买价格
        $totalCopies = intval($goodsInfo['price'])/intval($goodsInfo['minimum']);
        
        $redis = new Redis();
        //数据库中增加期数信息
        $lenth = strlen(strval(intval($goodsInfo['max_term_count'])));
        for($i = 0 ; $i < intval($goodsInfo['max_term_count']); $i++){
            $termData['goods_id'] = $goods_id;
            $termData['term_num'] = date('Ymd',time()).$goods_id.str_pad($i,$lenth,'0',STR_PAD_LEFT);
            $termData['minimum'] = $goodsInfo['minimum'];
            $termData['robot_buy_ratio'] = $goodsInfo['robot_buy_ratio'];
            $termData['robot_win'] = $goodsInfo['robot_win'];
            $termData['totalcopies'] = $totalCopies;
            $termData['saledcopies'] = 0;
            $termData['complate'] = 0;
            $termData['complatetime'] = 0;
            $termData['winneruid'] = 0;
            $termData['ticketnum'] = '';
            $termData['order_id'] = 0;

            Db::name('goods_term')->insert($termData,false,true);

            $tmpArr = null;
            //生成期数对应的兑奖记录码，最后100条内随机生成一条至机器人购买队列
            for ($j=0; $j < $totalCopies; $j++) { 
                $tmpArr[$j] = $j;
            }

            for($n = 0; $n < $totalCopies; $n++){
                $randKey = array_rand($tmpArr,1);
                if($n == 0 && $termData['robot_buy_ratio']){
                    //预留一个给机器人购买
                    $redis->lpush('ROBOT:'.$termData['term_num'],10000001 + $randKey);
                }else{
                    $redis->lpush('GOODS:'.$termData['term_num'],10000001 + $randKey);
                }

                unset($tmpArr[$randKey]);
            }
        }
    }

    /**
     * [ComputePrizeNum description]
     * @param [type] $term_num [description]
     */
    private function ComputePrizeNum($term_num){
        //获取期数相关信息
        $termInfo = Db::name('goods_term')->where('term_num',$term_num)->find();

        if(!$termInfo)
            return;

        //获取购买记录最后100条,不足100条则获取全部记录
        $buyRecordList = Db::name('order_goods')->where('term_id',$termInfo['term_id'])->limit(100)->order('order_goods_id desc')->select();

        if(!$buyRecordList)
            return;

        $lastRobotBuyTime = 0;
        $lastRobotBuyId = 0;
        //指定中奖人的中奖号码
        $targetTicket = '';
        $robotTargetTicket = '';

        $totalTicket = 0;
        //每个时间记录按时、分、秒、毫秒依次排列
        for($i = 0; $i < count($buyRecordList); $i++){
            $tmpTicketTime = intval(date('Gis.', substr($buyRecordList[$i]['createtime'], 0, -3)) . substr($buyRecordList[$i]['createtime'], -3));
            $totalTicket += $tmpTicketTime;

            if(!$lastRobotBuyTime && $buyRecordList[$i]['isrobot']){
                $lastRobotBuyTime = $buyRecordList[$i]['createtime'];
                $lastRobotBuyId = $buyRecordList[$i]['order_goods_id'];
                $robotTargetTicket = $buyRecordList[$i]['ticketnum'];
            }

            if(!$targetTicket && $termInfo['winneruid'] && $termInfo['winneruid'] == $buyRecordList[$i]['uid']){
                $targetTicket = $buyRecordList[$i]['ticketnum'];
            }
        }

        //计算正确的余数
        $finalMod = null;

        if($targetTicket){
            $finalMod = $targetTicket - 10000001;
        }else{
            //判断期数是否指定机器人中奖
            if($termInfo['robot_win']){
                //判断当前期数机器人参与比例是否小于20%，小于20%则正常开奖
                $robotBuyCount = Db::name('order_goods')->where('term_id='.$termInfo['term_id'].' and isrobot=1')->count();

                if(doubleval($robotBuyCount*1.00 / $termInfo['totalcopies']) > 0.2){
                    $finalMod = $robotTargetTicket - 10000001;
                }
            }
        }

        //将这100个数值之和除以该商品总参与人次后取余数
        $tmpTag = $totalTicket / intval($termInfo['totalcopies']);
        $tmpMod = $totalTicket % intval($termInfo['totalcopies']);

        if(!is_null($finalMod)){
            //计算正确的除数,更新机器人购买的时间
            $successTotal = intval($termInfo['totalcopies']) * $tmpTag + $finalMod;
            $diffMod = $successTotal - $totalTicket;
            if($diffMod){
                $lastRobotBuyTime += $diffMod;
                if(Db::name('order_goods')->where('order_goods_id',$lastRobotBuyId)->update(['createtime'=>$lastRobotBuyTime])){
                    $tmpMod = $finalMod;
                }
            }
        }
        
        //(余数) + 10000001 即为中奖号码
        $lastTicket = 10000001 + $tmpMod;
        
        //更改期数表 为等待开奖状态 complate = 2  winneruid order_id
        $termInfo['complate'] = 2;
        $termInfo['ticketnum'] = $lastTicket;
        //获取中奖的订单信息
        $orderGoodsInfo = Db::name('order_goods')->where("term_id = ".$termInfo['term_id']." and ticketnum='".$lastTicket."'")->find();

        if($orderGoodsInfo){
            $termInfo['winneruid'] = $orderGoodsInfo['uid'];
            $termInfo['order_id'] = $orderGoodsInfo['order_id'];
        }
        
        Db::name('goods_term')->update($termInfo,false,true);
    }
}