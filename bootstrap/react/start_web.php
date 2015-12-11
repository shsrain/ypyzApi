<?php

/*
 * 这个文件是 youpaiyunzhi 的一部分。
 *
 * (c) shsrain <shsrain@163.com>
 *
 * 对于全版权和许可信息，请查看分发此源代码的许可文件。
 */

 /**
  * 这是一个基于事件驱动异步非阻塞模式的Http Server服务器。
  *
  * @author shsrain <shsrain@163.com>
  */

// 定义入口根目录。
defined('BASE_PATH') or define('BASE_PATH',str_replace('\\','/',realpath(dirname(__FILE__).'/'))."/");
// 加载核心文件。
require_once __DIR__.'/app.php';
$app = app();
$httpKernel = function ($request, $response) use ($app)
{
    // 注册消息。
    $app['request'] = $request;
    $app['response'] = $response;
    // 创建消息路由。
    $dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) use ($app)
    {
      $app['router'] = $r;
      $app['path_block'] = array_values(array_filter(explode('/',$app['request']->getPath())));
      if(!empty($app['path_block']))
      {
        $app['loader']->manifest($app['path_block'][0],$app);
      }
   });
   // 消息解析来源。
   $httpMethod = $app['request']->getMethod();
   $uri = parse_url($app['request']->getPath(), PHP_URL_PATH);
   $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
   // 路由解析。
   switch ($routeInfo[0])
   {
       case FastRoute\Dispatcher::NOT_FOUND:
           // ... 404 Not Found
           $headers = array('Content-Type' => 'text/plain');
           $app['response']->writeHead(404, $headers);
           $app['response']->end('404');
           break;
       case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
           $allowedMethods = $routeInfo[1];
           // ... 405 Method Not Allowed
           $headers = array('Content-Type' => 'text/plain');
           $app['response']->writeHead(405, $headers);
           $app['response']->end('405');
           break;
       case FastRoute\Dispatcher::FOUND:
           $handler = $routeInfo[1];
           $vars = $routeInfo[2];
           // ... call $handler with $vars
           $app['vars'] = $vars;
           $handler_block = array_values(array_filter(explode('@',$handler)));
           $app['loader']->controller($app['path_block'][0],$handler_block[0],$app);
           $controller = new $handler_block[0]($app);
           $controller->$handler_block[1]();
           break;
   }
};
// 创建 Http Server 服务器。
$app['port'] = 1337;
$app['loop'] = React\EventLoop\Factory::create();
$app['socket'] = new React\Socket\Server($app['loop']);
$app['http'] = new React\Http\Server($app['socket']);
$app['http']->on('request',$httpKernel);
$app['socket']->listen($app['port']);
$app['loop']->run();
