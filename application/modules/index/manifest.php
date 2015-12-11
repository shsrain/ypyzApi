<?php

/*
 * 这个文件是 youpaiyunzhi 的一部分。
 *
 * (c) shsrain <shsrain@163.com>
 *
 * 对于全版权和许可信息，请查看分发此源代码的许可文件。
 */

 /**
  * 这是一个模块资源路由文件。
  *
  * @author shsrain <shsrain@163.com>
  */

  $app['router']->addRoute('GET', '/index/user/index', 'User@index');
  $app['router']->addRoute('GET', '/index/user/public', 'User@lists');
  $app['router']->addRoute('GET', '/index/user/login', 'User@login');
