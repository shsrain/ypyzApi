<?php

/*
 * 这个文件是 youpaiyunzhi 的一部分。
 *
 * (c) shsrain <shsrain@163.com>
 *
 * 对于全版权和许可信息，请查看分发此源代码的许可文件。
 */

 /**
  * 这是一个辅助加载文件类。
  *
  * @author shsrain <shsrain@163.com>
  */
class Loader {

    /**
     * 加载模块路由。
     *
     * @return
     */
    public function manifest( $modules, $app )
    {
       //return require_once __DIR__.'/../modules/'.$modules.'/manifest.php';
       $path = __DIR__.'/../modules/'.$modules.'/manifest.php';
       if(file_exists($path))
       {
         return require $path;
       }
       else
       {
         return false;
       }
    }

    /**
     * 加载模块controller。
     *
     * @return
     */
    public function controller( $modules, $controller, $app )
    {
      $path = __DIR__.'/../modules/'.$modules.'/controllers/'.$controller.'.php';
      if(file_exists($path))
      {
        return require_once $path;
      }
      else
      {
        return false;
      }
    }
    /**
     * 加载模块命令。
     *
     * @return
     */
    public function command( $modules, $command, $app )
    {
        return require_once __DIR__.'/../modules/'.$modules.'/commands'.'/'.$command.'.php';
    }

    /**
     * 加载配置文件。
     *
     * @return
     */
    public function config( $config, $app)
    {
        return require __DIR__.'/../config/'.$config.'.php';
    }

    /**
     * 加载助手方法。
     *
     * @return
     */
    public function helper( $helper, $app)
    {
        return require_once  __DIR__.'/../helpers/'.$helper.'.php';
    }

    /**
     * 加载迁移数据。
     *
     * @return
     */
    public function migration($modules,$migrations, $app)
    {
        return require_once __DIR__.'/../modules/'.$modules.'/migrations/'.$migrations.'.php';
    }
}
