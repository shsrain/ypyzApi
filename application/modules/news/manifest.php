<?php

/*
 * 这个文件是 youpaiyunzhi 的一部分。
 *
 * (c) shsrain <shsrain@163.com>
 *
 * 对于全版权和许可信息，请查看分发此源代码的许可文件。
 */

 /**
  * 这是一个资讯模块资源路由文件。
  *
  * @author shsrain <shsrain@163.com>
  */

$headers = $app['loader']->helper('headers',$app);
$middleware = $app['loader']->helper('middleware',$app);

/**
 * 资讯相关接口
 * URL: news.php
 * 支持格式：JSON
 * 注意事项: GET方式的请求参数使用原始拼接的方式。
 */

// 资讯读取接口

/**
* Route: news/public
* 返回最新的资讯列表。
* URL: news.php/news/public
* 支持格式：JSON
* HTTP请求方式：GET
* 是否需要登录：否
* 请求参数：
* count 可选 int 单页返回的记录条数，默认为5。
* page 可选 int 返回结果的页码，默认为1。
* 注意事项: 无
*/
$app['http']->get(
   '/news/public',
   function () use ($app,$headers)
   {
     $entity = $app['bus']->dispatch(new NewsPublicCommand($app));
     $body = $app['encoder']->encode($entity[0]);

     $headers['setCommonHeader']();
     $app['http']->response->setStatus(200);
     $app['http']->response->setBody($body);
     $app['http']->stop();
   }
);

/**
* Route: news/detail
* 获取单条资讯详细。
* URL: news.php/news/detail
* 支持格式：JSON
* HTTP请求方式：GET
* 是否需要登录：是
* 请求参数：
* count 	可选 	int 	单页返回的记录条数，默认为5。
* page 	可选 	int 	返回结果的页码，默认为1。
* 注意事项: 无
*/
$app['http']->get(
   '/news/detail',
   function () use ($app,$headers)
   {
     $entity = $app['bus']->dispatch(new NewsDetailCommand($app));
     $body = $app['encoder']->encode($entity[0]);

     $headers['setCommonHeader']();
     $app['http']->response->setStatus(200);
     $app['http']->response->setBody($body);
     $app['http']->stop();
   }
);

/**
* Route: reply/public
* 获取单条资讯的评论。
* URL: news.php/reply/public
* 支持格式：JSON
* HTTP请求方式：GET
* 是否需要登录：是
* 请求参数：
* count 	可选 	int 	单页返回的记录条数，默认为5。
* page 	可选 	int 	返回结果的页码，默认为1。
* 注意事项: 无
*/
$app['http']->get(
   '/reply/public',
   function () use ($app,$headers)
   {
     $entity = $app['bus']->dispatch(new ReplyPublicCommand($app));
     $body = $app['encoder']->encode($entity[0]);

     $headers['setCommonHeader']();
     $app['http']->response->setStatus(200);
     $app['http']->response->setBody($body);
     $app['http']->stop();
   }
);

/**
* Route: reply/update
* 评论资讯。
* URL: news.php/reply/update
* 支持格式：JSON
* HTTP请求方式：POST
* 是否需要登录：是
* 请求参数：
* client_id 	必选 	string 	应用Key。
* access_token 	必选 	string 	OAuth2授权。
* 注意事项: 无
*/
$app['http']->post(
   '/reply/update',
   //$middleware['oauth'](),
   function () use ($app,$headers)
   {
     $entity = $app['bus']->dispatch(new ReplyUpdateCommand($app));
     $body = $app['encoder']->encode($entity[0]);

     $headers['setCommonHeader']();
     $app['http']->response->setStatus(200);
     $app['http']->response->setBody($body);
     $app['http']->stop();
   }
);

// 404路由错误返回Json消息。
$app['http']->notFound(function () use ($app,$headers) {

  $body['request'] = $app['http']->request->getRootUri().$app['http']->request->getResourceUri();
  $body['code'] = 20009;
  $body['message'] = '请求的api不存在。';

  $headers['setCommonHeader']();
  $app['http']->response->setStatus(404);
  $app['http']->response->setBody($app['encoder']->encode($body));
  $app['http']->stop();
});
