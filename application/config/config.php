<?php

/*
 * 这个文件是 youpaiyunzhi 的一部分。
 *
 * (c) shsrain <shsrain@163.com>
 *
 * 对于全版权和许可信息，请查看分发此源代码的许可文件。
 */

 /**
  * 这是一个配置文件。
  *
  * @author shsrain <shsrain@163.com>
  */

$config = array(

  'view' => array(
    'templates'	=> 'application/views/templates',
  	'compilation_cache' => 'application/cache/compilation_cache',
  ),
  'upload'=>array(
    'path'=>dirname(BASE_PATH).DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR,
  ),

);

return $config;
