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
 * 这是一个针对某一条咨询提出新问题命令类。
 *
 * @author shsrain <shsrain@163.com>
 */
class AdvisoriesUpdateCommand implements CommandInterface
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
 * 这是一个针对某一条咨询提出新问题命令的处理类。
 *
 * @author shsrain <shsrain@163.com>
 */
class AdvisoriesUpdateCommandHandler extends AbstractCommandHandler
{

    public $command = null;
    public $app = null;
    public $db_upihealth2 = null;
    public $userInfo = null;

    public function getSupportedCommandClassName()
    {
        return 'AdvisoriesUpdateCommand';
    }

    protected function execute(CommandInterface $command)
    {
      $this->command = $command;
      $this->app = $command->app;
      $this->db_upihealth2 = $command->db_upihealth2;

      $body = $this->app['http']->request->params();
      $res = $this->isUserExist($body['by_userid']);
      if($res == false)
      {
        $return['request'] = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
        $return['code'] = 30001;
        $return['message'] = '用户不存在。';
        return $return;
      }

      $res = $this->isInquiryExist($body['inquiry_id']);
      if($res == false)
      {
        $return['request'] = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
        $return['code'] = 30101;
        $return['message'] = '当前咨询不存在。';
        return $return;
      }

      $res = $this->isInquiryOwner($body['inquiry_id'],$body['by_userid']);
      if($res == false)
      {
        $return['request'] = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
        $return['code'] = 30102;
        $return['message'] = '不是你的咨询。';
        return $return;
      }
      $this->userInfo = $this->usersShow();
      $res = $this->app['pdo']->ypyz_dialog()->where('dialog_id',$this->advisoriesUpdate())->fetch();
      $result['dialog_id']  = $res['dialog_id'];
      $result['inquiry_id'] = $res['inquiry_id'];
      $result['by_userid']  = $res['by_userid'];
      $result['to_userid']  = $res['to_userid'];
      $result['reply_id']   = $res['reply_id'];
      $result['message']    = $res['message'];
      $result['send_time']  = $res['send_time'];
      $result['avatar'] = "http://api.didijiankang.cn/api/images/".$this->userInfo['avatar'];
      $return['datum'] = $result;
      $return['code'] = 200;
      $return['message'] = '操作成功。';
      return $return;
    }

    public function advisoriesUpdate()
    {
      $body = $this->app['http']->request->params();
      $this->app['pdo']->ypyz_dialog()->insert(array(
          "inquiry_id" => $body['inquiry_id'],
          "by_userid"  => $body['by_userid'],
          "to_userid"  => $body['to_userid'],
          "reply_id"   => $body['to_userid'],
          "message"    => $body['message'],
          "send_time"  => time(),
      ));
      return $this->app['pdo']->ypyz_dialog()->insert_id();
    }

    // 检查用户是否存在。
    public function isUserExist( $user_id = null )
    {
      if($user_id != $this->app['access_token_data']['user_id'])
      {
        return false;
      }
      else
      {
        return true;
      }
    }

    // 检查咨询是否存在。
    public function isInquiryExist( $inquiry_id )
    {
      $results=$this->app['pdo']->ypyz_inquiry()
      ->where(array(
        'inquiry_id'=>$inquiry_id,
      ))->fetch();
      if($results == null)
      {
        return false;
      }
      else
      {
        return true;
      }
    }

    // 检查咨询是否属于某个用户。
    public function isInquiryOwner( $inquiry_id, $public_userid )
    {
      $results=$this->app['pdo']->ypyz_inquiry()
      ->where(array(
        'inquiry_id'=>$inquiry_id,
        'public_userid'=>$public_userid
      ))->fetch();
      if($results == null)
      {
        return false;
      }
      else
      {
        return true;
      }
    }

    // 获取用户信息。
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
