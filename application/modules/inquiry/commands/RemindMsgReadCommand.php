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
 * 这是一个根据咨询ID对未读回复数进行清零命令类。
 *
 * @author shsrain <shsrain@163.com>
 */
class RemindMsgReadCommand implements CommandInterface
{

    public $app = null;

    public function __construct($app)
    {
        $this->app = $app;
    }
}

/**
 * 这是一个根据咨询ID对未读回复数进行清零命令的处理类。
 *
 * @author shsrain <shsrain@163.com>
 */
class RemindMsgReadCommandHandler extends AbstractCommandHandler
{

    public $command = null;
    public $app = null;

    public function getSupportedCommandClassName()
    {
        return 'RemindMsgReadCommand';
    }

    protected function execute(CommandInterface $command)
    {
        $this->command = $command;
        $this->app = $command->app;

        $res = $this->isInquiryExist($this->app['http']->request->get('inquiry_id'));
        if($res == false)
        {
          $return['request'] = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
          $return['code'] = 30101;
          $return['message'] = '当前咨询不存在。';
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

        $result = $this->remindMsgRead();
        $return['code'] = 200;
        $return['message'] = '操作成功。';
        return $return;
    }

    public function remindMsgRead()
    {
      $result=$this->app['pdo']->ypyz_dialog()
      ->where(array(
        'inquiry_id'=>$this->app['http']->request->get('inquiry_id'),
        'to_userid'=>$this->app['access_token_data']['user_id'],
        'is_read'=>0,
      ))->update(array('is_read'=>1));
      return $result;
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
