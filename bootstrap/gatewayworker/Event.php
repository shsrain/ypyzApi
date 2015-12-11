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

/**
 * 应用逻辑入口
 * 主要是处理 onMessage onClose
 */

require_once __DIR__.'/app.php';

use \GatewayWorker\Lib\Gateway;
use \GatewayWorker\Lib\Store;

class Event
{

  /**
   * 有连接时
   * @param int $client_id 客户端id
   */
  public static function onConnect($client_id)
  {
      // to do
      $app = app();
      $app['client_id'] = $client_id;
      //$app['bus']->dispatch(new ConnectCommand($app));
  }

   /**
    * 有消息时
    * @param int $client_id 客户端id
    * @param string $message 请求数据
    */
   public static function onMessage($client_id, $message)
   {
      // to do
      $app = app();
      $app['client_id'] = $client_id;
      print_r("Client request + ".$message." + \n");
      try
      {
        $app['request'] = $app['decoder']->decode($message);
      }
      catch( Webmozart\Json\DecodingFailedException $e)
      {
        $response['code'] = 400;
        $response['message'] = 'Bad request';
        print_r("Client bad request + ".$app['encoder']->encode($response)." + \n");
        Gateway::sendToAll($app['encoder']->encode($response));
        return;
      }

      switch ($app['request']->type) {
        case 'pong':
          return;
          break;
        case 'login':
          // $app['bus']->dispatch(new LoginCommand($app));
          break;
        case 'say':
          // $app['bus']->dispatch(new SayCommand($app));
          break;

        default:

          break;
      }
   }

   /**
    * 有断开时
    * @param integer $client_id 客户端id
    */
   public static function onClose($client_id)
   {
      // to do
      $app = app();
      $app['client_id'] = $client_id;
      $app['bus']->dispatch(new CloseCommand($app));
   }
}
