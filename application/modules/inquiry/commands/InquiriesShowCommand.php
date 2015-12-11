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
 * 这是一个根据ID获取单条咨询信息命令类。
 *
 * @author shsrain <shsrain@163.com>
 */
class InquiriesShowCommand implements CommandInterface
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
 * 这是一个根据ID获取单条咨询信息命令的处理类。
 *
 * @author shsrain <shsrain@163.com>
 */
class InquiriesShowCommandHandler extends AbstractCommandHandler
{

    public $command = null;
    public $app = null;
    public $db_upihealth2 = null;

    public function getSupportedCommandClassName()
    {
        return 'InquiriesShowCommand';
    }

    protected function execute(CommandInterface $command)
    {
      $this->command = $command;
      $this->app = $command->app;
      $this->db_upihealth2 = $command->db_upihealth2;

      $res = $this->isInquiryExist($this->app['http']->request->get('inquiry_id'));
      if($res == false)
      {
        $return['request'] = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
        $return['code'] = 30101;
        $return['message'] = 'Target inquiry does not exist.';
        return $return;
      }

      $res = $this->isInquiryOwner($this->app['http']->request->get('inquiry_id'),$this->app['access_token_data']['user_id']);
      if($res == false)
      {
        $return['request'] = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
        $return['code'] = 30102;
        $return['message'] = '不是你的咨询。';
        return $return;
      }

      $result = $this->inquiriesShow();
      if($result == null)
      {
        $return['request'] = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
        $return['code'] = 30105;
        $return['message'] = '列表不存在。';
      }
      else
      {
        $return['datum'] = $result;
        $return['code'] = 200;
        $return['message'] = '操作成功。';
      }
      return $return;
    }

    public function inquiriesShow()
    {
      $res=$this->app['pdo']->ypyz_inquiry()
      ->where(array(
        'inquiry_id'=>$this->app['http']->request->get('inquiry_id'),
        'public_userid'=>$this->app['access_token_data']['user_id']
      ))->fetch();
      if($res != null)
      {
        $result = array();
        $result['inquiry_id'] = $res['inquiry_id'];
        $result['patient_id'] = $res['patient_id'];
        $result['casehistory_id'] = $res['casehistory_id'];
        $result['description'] = $res['description'];
        $result['public_userid'] = $res['public_userid'];
        $result['public_time'] = $res['public_time'];
        $result['is_solve'] = $res['is_solve'];
        $result['end_time'] = $res['end_time'];
        $result['is_reply'] = $res['is_reply'];
        $result['last_reply_time'] = $res['last_reply_time'];
        $result['last_reply_userid'] = $res['last_reply_userid'];
        $picture_arr = explode(',',$res['picture']);
        foreach($picture_arr as $k=>$pic)
        {
          $result['data'][$key]['picture'][$k] = "http://api.didijiankang.cn/api/images/uploads/".$pic;
        }
        $res2=$this->db_upihealth2->myconcern()->where(array('user_measure'=>$res['patient_id']))->fetch();
        if($res2 != null)
        {
          $result['remark '] = $res2['remark'];
          $result['sex '] = $res2['sex'];
          $result['age '] = $res2['age'];
        }
        else
        {
          $result['remark '] = null;
          $result['sex '] = null;
          $result['age '] = null;
        }
        $res3=$this->db_upihealth2->user()->where(array('username'=>$res['public_userid']))->fetch();
        if($res3 != null)
        {
          $result['avatar'] = $res3['avatar'];
        }
        else
        {
          $result['avatar'] = null;
        }
        return $result;
      }
      else
      {
        return null;
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
}
