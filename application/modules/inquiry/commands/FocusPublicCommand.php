<?php

/*
 * 这个文件是 youpaiyunzhi 的一部分。
 *
 * (c) shsrain <shsrain@163.com>
 *
 * 对于全版权和许可信息，请查看分发此源代码的许可文件。
 */

use Kilte\Pagination\Pagination;
use PhpDDD\Command\CommandInterface;
use PhpDDD\Command\Handler\AbstractCommandHandler;

/**
 * 这是一个获取用户的关注者列表命令类。
 *
 * @author shsrain <shsrain@163.com>
 */
class FocusPublicCommand implements CommandInterface
{

    public $app = null;
    public $db_upihealth2 = null;

    public function __construct($app)
    {
        $this->app = $app;

        $db_config = $this->app['db_config']['upihealth2'];
        $pdo = new \PDO($db_config['dsn'],$db_config['username'],$db_config['password']);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->query('SET NAMES '.$db_config['char_set']);
        $this->db_upihealth2 = new NotORM($pdo);
    }
}

/**
 * 这是一个获取用户的关注者列表命令的处理类。
 *
 * @author shsrain <shsrain@163.com>
 */
class FocusPublicCommandHandler extends AbstractCommandHandler
{

    public $command = null;
    public $app = null;
    public $db_upihealth2 = null;

    public function getSupportedCommandClassName()
    {
        return 'FocusPublicCommand';
    }

    protected function execute(CommandInterface $command)
    {
        $this->command = $command;
        $this->app = $command->app;
        $this->db_upihealth2 = $command->db_upihealth2;

        $result = $this->focusPublic();
        if($result == null)
        {
          $return['request'] = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
          $return['code'] = 30401;
          $return['message'] = '列表不存在。';
        }
        else
        {
          $return['data'] = $result;
          $return['code'] = 200;
          $return['message'] = '操作成功。';
        }
        return $return;
    }

    public function focusPublic()
    {

      $totalItems = $this->db_upihealth2->myconcern()
      ->where(array('username'=>$this->app['access_token_data']['user_id']))->count();
      if($totalItems==0)
      {
          return null;
      }
      $results = $this->db_upihealth2->myconcern()->where(array('username'=>$this->app['access_token_data']['user_id']));
      $result = array();
      foreach($results as $key=>$res)
      {
          $result[$key]['username'] = $res['username'];
          $result[$key]['user_measure'] = $res['user_measure'];
          $result[$key]['remark'] = $res['remark'];
          $result[$key]['sex'] = $res['sex'];
          $result[$key]['age'] = $res['age'];
      }
      return $result;
    }
}
