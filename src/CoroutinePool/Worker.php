<?php

namespace Mix\Concurrent\CoroutinePool;

use Mix\Core\BeanObject;
use Mix\Core\Channel;
use Mix\Core\Coroutine;

/**
 * Class Worker
 * @package Mix\Concurrent
 * @author LIUJIAN <coder.keda@gmail.com>
 */
class Worker extends BeanObject
{

    /**
     * 工作池
     * @var \Mix\Core\Channel
     */
    public $workerPool;

    /**
     * 任务通道
     * @var \Mix\Core\Channel
     */
    public $jobChannel;

    /**
     * 退出
     * @var \Mix\Core\Channel
     */
    protected $_quit;

    /**
     * 初始化事件
     */
    public function onInitialize()
    {
        parent::onInitialize(); // TODO: Change the autogenerated stub
        // 初始化
        $this->jobChannel = new Channel();
        $this->_quit      = new Channel();
    }

    /**
     * 启动
     */
    public function start()
    {
        Coroutine::create(function () {
            while (true) {
                $this->workerPool->push($this->jobChannel);
                $job = $this->jobChannel->pop();
                if (!$job) {
                    return;
                }
                list($callback, $params) = $job;
                Coroutine::create(function () use ($callback, $params) {
                    call_user_func_array($callback, $params);
                });
            }
        });
        Coroutine::create(function () {
            $this->_quit->pop();
            $this->jobChannel->close();
        });
    }

    /**
     * 停止
     */
    public function stop()
    {
        Coroutine::create(function () {
            $this->_quit->push(true);
        });
    }

}
