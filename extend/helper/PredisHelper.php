<?php

namespace common\helper;

use Yii;
use Predis\Client;
use common\models\RedisLog;

class PredisHelper
{
    //服务器参数
    private static $redisParam = array(
        'Redis_Servers' => [
            [
                'scheme' => 'tcp',
                'host' => '192.168.7.216',
                'port' => '6301',
                'alias' => 'master',
                'password' => 'hscode8888'
            ],
            [
                'scheme' => 'tcp',
                'host' => '192.168.7.216',
                'port' => '6302',
                'password' => 'hscode8888'
            ]
        ],
        'Redis_Options' => ['replication' => true],
        'Redis_Prix' => ''
    );

    /**
     * 写hash数据
     */
    public static function set($table, $key, $value)
    {
        try {
            $client = new Client(static::$redisParam['Redis_Servers'], static::$redisParam['Redis_Options']);
            return $client->hset(static::$redisParam['Redis_Prix'] . $table, $key, $value);
        } catch (\Exception $ex) {
            static::Log('set', $ex, true, $table, $key, $value);
            throw  $ex;
        }
    }

    /**
     * 读redis  hash数据
     */
    public static function get($table, $key)
    {
        try {
            $client = new Client(static::$redisParam['Redis_Servers'], static::$redisParam['Redis_Options']);
            return $client->hget(static::$redisParam['Redis_Prix'] . $table, $key);
        } catch (\Exception $ex) {
            static::Log('get', $ex, true, $table, $key);
            throw  $ex;
        }
    }

    /**
     * 读取redis hash记录
     * @param string $table
     * @throws \Exception
     * @return array
     */
    public static function getAll($table)
    {
        try {
            $client = new Client(static::$redisParam['Redis_Servers'], static::$redisParam['Redis_Options']);
            return $client->hgetall(static::$redisParam['Redis_Prix'] . $table);
        } catch (\Exception $ex) {
            static::Log('getall', $ex, true, $table);
            throw  $ex;
        }
    }

    /**
     * 删除redis hash记录
     */
    public static function remove($table, $key)
    {
        try {
            $client = new Client(static::$redisParam['Redis_Servers'], static::$redisParam['Redis_Options']);
            return $client->hdel(static::$redisParam['Redis_Prix'] . $table, $key);
        } catch (\Exception $ex) {
            static::Log('remove', $ex, true, $table, $key);
            throw  $ex;
        }
    }

    public static function lpush($table, $value)
    {
        try {
            $client = new Client(static::$redisParam['Redis_Servers'], static::$redisParam['Redis_Options']);
            return $client->lpush(static::$redisParam['Redis_Prix'] . $table, $value);
        } catch (\Exception $ex) {
            static::Log('lpush', $ex, true, $table, '', $value);
            throw  $ex;
        }
    }

    public static function rpush($table, $value)
    {
        try {
            $client = new Client(static::$redisParam['Redis_Servers'], static::$redisParam['Redis_Options']);
            return $client->rpush(static::$redisParam['Redis_Prix'] . $table, $value);
        } catch (\Exception $ex) {
            static::Log('rpush', $ex, true, $table, '', $value);
            throw  $ex;
        }
    }

    public static function lpop($table)
    {
        try {
            $client = new Client(static::$redisParam['Redis_Servers'], static::$redisParam['Redis_Options']);
            return $client->lpop(static::$redisParam['Redis_Prix'] . $table);
        } catch (\Exception $ex) {
            static::Log('lpop', $ex, true, $table);
            throw  $ex;
        }
    }

    public static function rpop($table)
    {
        try {
            $client = new Client(static::$redisParam['Redis_Servers'], static::$redisParam['Redis_Options']);
            return $client->rpop(static::$redisParam['Redis_Prix'] . $table);
        } catch (\Exception $ex) {
            static::Log('rpop', $ex, true, $table);
            throw  $ex;
        }
    }

    /**
     * 构造GUID字符串
     */
    public static function guid()
    {
        mt_srand((double)microtime() * 10000);//optional for php 4.2.0 and up.
        $charId = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = chr(123)// "{"
            . substr($charId, 0, 8) . $hyphen
            . substr($charId, 8, 4) . $hyphen
            . substr($charId, 12, 4) . $hyphen
            . substr($charId, 16, 4) . $hyphen
            . substr($charId, 20, 12)
            . chr(125);// "}"
        return str_replace('}', '', str_replace('{', '', $uuid));
    }

    /**
     * 记录redis操作失败日志
     * @param string $commandType
     * @param string $ex
     * @param bool $fileLog
     * @param string $hashName
     * @param string $keyName
     * @param string $jsonData
     */
    private static function Log($commandType, $ex, $fileLog = true, $hashName = '', $keyName = '', $jsonData = '')
    {
        $redisLogModel = new RedisLog();
        $redisLogModel->SaveRedisLog(static::$redisParam['Redis_Prix'] . $hashName, $keyName, $jsonData, $commandType, $ex->getCode(), $ex->getMessage(), $ex->getTraceAsString());
    }
}
