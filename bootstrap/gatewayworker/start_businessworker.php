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

// bussinessWorker 进程
$worker = new BusinessWorker();
// worker名称
$worker->name = 'BusinessWorker';
// bussinessWorker进程数量
$worker->count = 4;
// BusinessWorker启动后的回调函数，一般在这个回调里面初始化一些全局数据
$worker->onWorkerStart = function($worker)
{
    echo "BusinessWorker Worker starting...\n";
};
// BusinessWorker关闭的回调函数，一般在这个回调里面做数据清理或者保存数据工作
$worker->onWorkerStop = function($worker)
{
    echo "BusinessWorker Worker stopping...\n";
};
// 如果不是在根目录启动，则运行runAll方法
if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}
