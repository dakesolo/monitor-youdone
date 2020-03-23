<?php

use Workerman\Timer;
use Workerman\Worker;
require_once __DIR__ . '/Workerman/Autoloader.php';

// 创建一个Worker监听2345端口，使用http协议通讯
$http_worker = new Worker("http://0.0.0.0:8080");

// 启动4个进程对外提供服务
$http_worker->count = 1;


require_once __DIR__ . '/App/Monitor.php';



$http_worker->onMessage = function($connection, $requset)
{
    $method = substr($requset->path(), 1);
    $monitor = Monitor::getInstance();
    if($method == 'checkException') {
        $connection->send(json_encode($monitor->$method()));
        return;
    }
    $connection->send("api no support");
};


$http_worker->onWorkerStart  = function($worker)
{
    if($worker->id === 0) {
        $monitor = Monitor::getInstance();
        Timer::add(1, [$monitor, 'poll']);
    }
};

Worker::runAll();