<?php

/*
 * 这个文件是 youpaiyunzhi 的一部分。
 *
 * (c) shsrain <shsrain@163.com>
 *
 * 对于全版权和许可信息，请查看分发此源代码的许可文件。
 */

 /**
  * 这是一个oauth2授权模块路由文件。
  *
  * @author shsrain <shsrain@163.com>
  */

/**
 * oauth2接口
 * URL: oauth2.php/oauth2
 * 支持格式：JSON
 */

// oauth2读取接口

/**
* Route: oauth2/token
* 获取访问Token。
* URL: oauth2.php/oauth2/token
* 支持格式：JSON
* HTTP请求方式：POST
* 是否需要授权/登录：否
* 请求参数：
* grant_ type 	必选 	password 	认证类型，必须为password。
* client_ id 	必选 	string 	客户端标识。
* client_ secret 	必选 	string 	客户端密匙。
* username 	必选 	string 	用户登录账户。
* password 	必选 	string 	用户登录密码。
* 注意事项: 无
*/
$app['http']->post(
   '/oauth2/token',
   function () use ($app)
   {
     $app['oauth2']->handleTokenRequest(\OAuth2\Request::createFromGlobals())->send();
   }
);

/**
* Route: oauth2/refresh_token
* 刷新Access Token(oauth2/token)。
* URL: oauth2.php/oauth2/refresh_token
* 支持格式：JSON
* HTTP请求方式：POST
* 是否需要授权/登录：否
* 请求参数：
* client_ id 	必选 	string 	应用Key。
* client_ secret 	必选 	string 	应用Key。
* grant_ type 	必选 	string 	刷新Access Token: refresh_ token。
* refresh_ token 	必选 	string 	刷新Token，用于获取新的access_ token。
* 注意事项: 无
*/
$app['http']->post(
   '/oauth2/refresh_token',
   function () use ($app)
   {
     $app['oauth2']->handleTokenRequest(\OAuth2\Request::createFromGlobals())->send();
   }
);
