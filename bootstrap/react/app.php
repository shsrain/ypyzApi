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

  // 注册数据库配置信息。
  $app['db_config'] = $app['loader']->config('database',$app);

  // 注册全局配置信息。
  $app['global_config'] = $app['loader']->config('config',$app);

  // 注册数据库服务。
  $app['pdo'] = function () use ( $app )
  {
      $db_config = $app['db_config']['default'];
      $pdo = new \PDO($db_config['dsn'], $db_config['username'], $db_config['password']);
      $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
      $pdo->query('SET NAMES '.$db_config['char_set']);
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

  // 注册认证服务。
  $app['oauth2'] = function () use ($app)
  {
    $db_config = $app['db_config']['oauth2'];
    $storage = new \OAuth2\Storage\Pdo(array('dsn' => $db_config['dsn'], 'username' => $db_config['username'], 'password' => $db_config['password']));
    $server = new \OAuth2\Server($storage);
    require_once __DIR__.'/../../application/libraries/PasswordCredentials.php';
    $server->addGrantType(new \OAuth2\GrantType\UserCredentials(new \OAuth2\Storage\PasswordCredentials()));
    require_once __DIR__.'/../../application/libraries/RefreshToken.php';
    $server->addGrantType(new \OAuth2\GrantType\RefreshToken(new \OAuth2\Storage\RefreshToken(),array('always_issue_new_refresh_token' => true)));
    return $server;
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
