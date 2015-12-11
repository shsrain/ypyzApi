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
 * 这是一个针对某一条咨询的某一次提问获取回复命令类。
 *
 * @author shsrain <shsrain@163.com>
 */
class AdvisoriesReplyCommand implements CommandInterface
{

    public $app = null;

    public function __construct($app)
    {
        $this->app = $app;
    }
}

/**
 * 这是一个针对某一条咨询的某一次提问获取回复命令的处理类。
 *
 * @author shsrain <shsrain@163.com>
 */
class AdvisoriesReplyCommandHandler extends AbstractCommandHandler
{

    public $command = null;
    public $app = null;

    public function getSupportedCommandClassName()
    {
        return 'AdvisoriesReplyCommand';
    }

    protected function execute(CommandInterface $command)
    {
      $this->command = $command;
      $this->app = $command->app;

      $res = $this->isDialogExist($this->app['http']->request->get('dialog_id'));
      if($res == false)
      {
        $return['request'] = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
        $return['code'] = 30201;
        $return['message'] = '当前提问不存在。';
        return $return;
      }

      $res = $this->isDialogOwner($this->app['http']->request->get('dialog_id'),$this->app['access_token_data']['user_id']);
      if($res == false)
      {
        $return['request'] = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
        $return['code'] = 30203;
        $return['message'] = '不是你的提问。';
        return $return;
      }

      $result = $this->advisoriesReply();
      if($result == null)
      {
        $return['request'] = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
        $return['code'] = 30206;
        $return['message'] = '提问回复不存在。';
        return $return;
      }
      else
      {
        $return['data'] = $result['data'];
        $return['page'] = $result['page'];
        $return['code'] = 200;
        $return['message'] = 'success.';
        return $return;
      }
    }

    public function advisoriesReply()
    {

      $results=$this->app['pdo']->ypyz_dialog()
      ->where('reply_id',$this->app['http']->request->get('dialog_id'))
      ->order("send_time ASC");

      $result = array();
      foreach($results as $key=>$res)
      {
          $result['data'][$key]['dialog_id']  = $res['dialog_id'];
          $result['data'][$key]['inquiry_id'] = $res['inquiry_id'];
          $result['data'][$key]['by_userid']  = $res['by_userid'];
          $result['data'][$key]['to_userid']  = $res['to_userid'];
          $result['data'][$key]['reply_id']   = $res['reply_id'];
          $result['data'][$key]['message']    = $res['message'];
          $result['data'][$key]['send_time']  = $res['send_time'];
          $result['data'][$key]['avatar']  = "http://api.didijiankang.cn/api/images/18565829843247062.png";

      }
      return $result;
    }

    // 检查用户提问是否存在。
    public function isDialogExist( $dialog_id )
    {
      $results=$this->app['pdo']->ypyz_dialog()
      ->where(array(
        'dialog_id'=>$dialog_id,
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

    // 检查对话是否属于某个用户。
    public function isDialogOwner( $dialog_id, $by_userid )
    {
      $results=$this->app['pdo']->ypyz_dialog()
      ->where(array(
        'dialog_id'=>$dialog_id,
        'by_userid'=>$by_userid
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
