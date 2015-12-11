<?php

/*
 * 这个文件是 youpaiyunzhi 的一部分。
 *
 * (c) shsrain <shsrain@163.com>
 *
 * 对于全版权和许可信息，请查看分发此源代码的许可文件。
 */

 /**
  * 这是一个响应头匿名函数数组。
  *
  * @author shsrain <shsrain@163.com>
  */

$headers = array(

  /**
	 * setCommonHeader
	 *
	 * 响应头。
	 *
	 * @param	string
	 * @param	string
	 * @param	init
	 * @return	Response
	 */
  'setCommonHeader' => function() use ($app)
  {
      $app['http']->response->headers->set('Server', 'www.didijiankang.cn');
      $app['http']->response->headers->set('X-Powered-By', 'YouPaiYunZhi');
      $app['http']->response->headers->set('Content-Type', 'application/json;charset=utf-8');
      $app['http']->response->headers->set('Accept', 'application/api.youpaiyunzhi+json; version=1');
  },
);

return $headers;
