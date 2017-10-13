<?php

namespace common\helper;

use RedisClient\ClientFactory;
use RedisLock\RedisLock;

/**
 * Class Lock
 * @package common\helper
 * 封装 RedisLock
 *
 * https://redis.io/topics/distlock
 * https://github.com/cheprasov/php-redis-lock
 */
class Lock
{
    private static $_instance;

    private static $_key;

    /**
     * @var RedisLock
     */
    private $_lock;

    protected function __construct($key = '')
    {
        if (empty($key)) {
            $key = 'redis_lock_key';
        }
        self::$_key = $key;
    }

    public static function getInstance($key = '')
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($key);
        }
        return self::$_instance;
    }

    /**
     * @param int $lockTime 加锁时间
     * @param int $waitTime 等待时间
     * @return bool
     */
    public function lock($lockTime, $waitTime = 0)
    {
        $redis = ClientFactory::create([
            'server' => 'tcp://127.0.0.1:6379'
        ]);

        $this->_lock = new RedisLock($redis, self::$_key, RedisLock::FLAG_CATCH_EXCEPTIONS);

        if (!$this->_lock->acquire($lockTime, $waitTime)) {
            return false;
        }

        return true;
    }

    public function release()
    {
        $this->_lock->release();
        return true;
    }
}
