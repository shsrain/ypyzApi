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
 * 这是一个获取用户信息命令类。
 *
 * @author shsrain <shsrain@163.com>
 */
class UsersShowCommand implements CommandInterface
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
 * 这是一个获取用户信息命令的处理类。
 *
 * @author shsrain <shsrain@163.com>
 */
class UsersShowCommandHandler extends AbstractCommandHandler
{

    public $command = null;
    public $app = null;
    public $db_upihealth2 = null;

    public function getSupportedCommandClassName()
    {
        return 'UsersShowCommand';
    }

    protected function execute(CommandInterface $command)
    {
        $this->command = $command;
        $this->app = $command->app;
        $this->db_upihealth2 = $command->db_upihealth2;

        $result = $this->usersShow();
        if($result == null)
        {
          $return['request'] = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
          $return['code'] = 30105;
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

    public function usersShow()
    {
      $res=$this->db_upihealth2->user()->where(array('username'=>$this->app['access_token_data']['user_id']))->fetch();
      if($res != null)
      {
        $result = array();
        $result['username'] = $res['username'];
        $result['nickname'] = $res['nickname'];
        $result['sex'] = $res['sex'];
        $result['birth'] = $res['birth'];
        $result['avatar'] = $res['avatar'];
        return $result;
      }
      else
      {
        return null;
      }
    }
}
