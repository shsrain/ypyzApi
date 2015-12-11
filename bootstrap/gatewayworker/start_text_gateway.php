<?php

/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

 use \Workerman\Worker;
 use \Workerman\WebServer;
 use \GatewayWorker\Gateway;
 use \GatewayWorker\BusinessWorker;
 use \Workerman\Autoloader;

// 自动加载类
require_once __DIR__ . '/../../system/socket/Workerman/Autoloader.php';
Autoloader::setRootPath(__DIR__);

// ##########新增端口支持Text协议 开始##########
// 新增8283端口，开启Text文本协议
$gateway_text = new Gateway("Text://0.0.0.0:7170");
// 进程名称，主要是status时方便识别
$gateway_text->name = 'GatewayText';
// 开启多少text协议的gateway进程
$gateway_text->count = 4;
// 本机ip（分布式部署时需要设置成内网ip）
$gateway_text->lanIp = '127.0.0.1';
// gateway内部通讯起始端口，起始端口不要重复
$gateway_text->startPort = 7171;
// 心跳间隔
$gateway_text->pingInterval = 10;
// 心跳数据
$gateway_text->pingData = '{"type":"ping"}';
// ##########新增端口支持Text协议 结束##########
// Gateway进程启动后的回调函数，一般在这个回调里面初始化一些全局数据
$gateway_text->onWorkerStart = function($gateway_text)
{
    echo "gateway_text Worker starting...\n";
};
// Gateway进程关闭的回调函数，一般在这个回调里面做数据清理或者保存数据工作
$gateway_text->onWorkerStop = function($gateway_text)
{
    echo "gateway_text Worker starting...\n";
};
// 如果不是在根目录启动，则运行runAll方法
if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}
