<?php

/*
 * @author zhangqi <569072171.qq.com>
 */
class Monitor
{
    public static $monitor;
    public $vList = [];
    public $errList = [];
    public $err = 0;
    public $e;
    public $n;
    public function __construct($n, $e) {
        $this->n = $n;
        $this->e = $e;
    }

    /*
     * 得到唯一实例
     * @return Monitor
     */
    public static function getInstance() {
        if(!self::$monitor) {
            self::$monitor = new Monitor(5, 7);
        }
        return self::$monitor;
    }

    /**
     * 检测异常，如果产生异常，需要对异常进行处理，并删除异常；如果不删除，则持续产生异常
     * @return array
     */
    public function getException()
    {
        return $this->errList;
    }

    /**
     * 检测异常，如果产生异常，需要对异常进行处理，并删除异常；如果不删除，则持续产生异常
     * @return bool
     */
    public function checkException()
    {
        if(!empty($this->errList)) {
            return true;
        }
        return false;
    }

    /**
     * 处理异常
     * @param $t - 时间
     * @return bool
     */
    public function handleException($t)
    {
        unset($this->errList[$t]);
        return true;
    }

    /**
     * 监控，需要定时器，每秒轮询
     */
    public function poll() {
        //模拟产生指标
        $v = rand(0, 10);

        //产生指标时间
        $t = time();

        //模拟持久化指标
        $this->storeV($t, $v);

        //有异常，则异常+1
        if($v <= $this->e) {
            $this->err++;
            //如果异常超过n
            if($this->err >= $this->n) {
                //模拟持久化错误
                $this->storeErr($t, $this->err);
                $this->err = 0;
            }
        }
        //无异常，则异常清零
        else {
            $this->err = 0;
        }
    }

    /**
     * 需要持久化指标v（redis or other），以下以变量模拟持久化
     * @param $v
     */
    public function storeV($t, $v) {
        $this->vList[$t]  = $v;
    }

    /**
     * 需要持久化异常err（redis or other），以下以变量模拟持久化
     * @param $v
     */
    public function storeErr($t, $err) {
        $this->errList[$t] = $err;
    }
}
