<?php

namespace common\helper;

class HttpHelper
{
    /**
     * 发送请求并获取返回的信息
     * @param string $url
     * @param string $param
     * @param string $this_header
     * @return string
     */
    public static function HttpPost($url, $param, $this_header = '')
    {
        $ch = curl_init(); //初始化curl

        if (empty($this_header)) {
            $this_header = array(
                "content-type: application/x-www-form-urlencoded;charset=UTF-8"
            );
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this_header);
        curl_setopt($ch, CURLOPT_URL, $url);//设置链接
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息
        curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);//POST数据
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//设置访问https时不需要证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);//设置访问https时不需要证书
        $response = curl_exec($ch);//接收返回信息
        if (curl_errno($ch)) {//出错则显示错误信息
            print curl_error($ch);
        }
        curl_close($ch); //关闭curl链接
        return $response;//显示返回信息
    }

    public static function postData($url, $data = [])
    {
        $o = '';
        foreach ($data as $k => $v) {
            $o .= "$k=" . urlencode($v) . "&";
        }
        $post_data = substr($o, 0, -1);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        //为了支持cookie
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);

        curl_close($ch);
        return $result;
    }
}
