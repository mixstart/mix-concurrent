<?php

namespace Mix\Concurrent\Sync;

use Mix\Core\Bean\AbstractObject;
use Mix\Core\Coroutine\Channel;

/**
 * Class WaitGroup
 * @package Mix\Concurrent
 * @author LIUJIAN <coder.keda@gmail.com>
 */
class WaitGroup extends AbstractObject
{

    /**
     * @var int
     */
    protected $_count = 0;

    /**
     * @var \Mix\Core\Coroutine\Channel
     */
    protected $_chan;

    /**
     * 初始化事件
     */
    public function onInitialize()
    {
        parent::onInitialize(); // TODO: Change the autogenerated stub
        $this->_chan = new Channel();
    }

    /**
     * 增加
     * @param int $num
     */
    public function add($num = 1)
    {
        $this->_count += $num;
    }

    /**
     * 完成
     * @return bool
     */
    public function done()
    {
        return $this->_chan->push(true);
    }

    /**
     * 等待
     * @return bool
     */
    public function wait()
    {
        for ($i = 0; $i < $this->_count; $i++) {
            $this->_chan->pop();
        }
        return true;
    }

}
