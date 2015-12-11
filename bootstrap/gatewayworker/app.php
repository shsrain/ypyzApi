<?php

/*
 * 这个文件是 youpaiyunzhi 的一部分。
 *
 * (c) shsrain <shsrain@163.com>
 *
 * 对于全版权和许可信息，请查看分发此源代码的许可文件。
 */

 /**
  * 这是一个服务注册文件。
  *
  * @author shsrain <shsrain@163.com>
  */
require_once __DIR__.'/../autoload.php';

use Pimple\Container;
use Webmozart\Json\JsonEncoder;
use Webmozart\Json\JsonDecoder;
use Webmozart\Json\JsonValidator;
use PhpDDD\Command\Bus\SequentialCommandBus;
use Webmozart\Json\ValidationFailedException;
use PhpDDD\Command\Handler\Locator\CommandHandlerLocator;

function app()
{

  // 创建服务容器。
  $app = new Container();

  // 注册加载服务。
  $app['loader'] = function ()
  {
      require_once __DIR__.'/../../application/libraries/Loader.php';
      return new Loader();
  };

  // 注册数据库服务。
  $app['pdo'] = function () use ( $app )
  {
      $database = $app['loader']->config('database',$app);
      $config['db_dsn'] = $database['default']['dsn'];
      $config['db_user'] = $database['default']['username'];
      $config['db_password'] = $database['default']['password'];
      $config['db_char_set'] = $database['default']['char_set'];

      $pdo = new PDO($config['db_dsn'], $config['db_user'], $config['db_password']);
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $pdo->query('SET NAMES '.$config['db_char_set']);
      return new NotORM($pdo);
  };

  // 注册事件服务。
  $app['event'] = function ()
  {
      return new Evenement\EventEmitter();
  };

  // 注册命令服务。
  $app['buslocator'] = function ()
  {
    return new CommandHandlerLocator();
  };

  $app['bus'] = function () use ($app)
  {
    $app['loader']->config('bus',$app);
    return new SequentialCommandBus($app['buslocator']);
  };

  // 注册视图服务。
  $app['view'] = function () use ($app)
  {
      $config = $app['loader']->config('config',$app);
      $loader = new Twig_Loader_Filesystem($config['view']['templates']);
      $twig = new Twig_Environment($loader, array(
          'cache' => $config['view']['compilation_cache'],
      ));
      return $twig;
  };

  // 注册Json解析服务。
  $app['encoder'] = function ()
  {
      return new JsonEncoder();
  };

  $app['decoder'] = function ()
  {
      return new JsonDecoder();
  };

  $app['JsonValidator'] = function ()
  {
      return new JsonValidator();
  };

  return $app;
}
