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
 * 这是一个根据咨询ID获取回复消息未读数命令类。
 *
 * @author shsrain <shsrain@163.com>
 */
class RemindMsgUnreadAllCommand implements CommandInterface
{

    public $app = null;

    public function __construct($app)
    {
        $this->app = $app;
    }
}

/**
 * 这是一个根据咨询ID获取回复消息未读数命令的处理类。
 *
 * @author shsrain <shsrain@163.com>
 */
class RemindMsgUnreadAllCommandHandler extends AbstractCommandHandler
{

    public $command = null;
    public $app = null;

    public function getSupportedCommandClassName()
    {
        return 'RemindMsgUnreadAllCommand';
    }

    protected function execute(CommandInterface $command)
    {
        $this->command = $command;
        $this->app = $command->app;

        $result = $this->remindMsgUnreadAll();
        $return['amount'] = $result;
        $return['code'] = 200;
        $return['message'] = '操作成功。';
        return $return;
    }

    public function remindMsgUnreadAll()
    {
      $totalItems=$this->app['pdo']->ypyz_dialog()->where(array(
        'to_userid'=>$this->app['access_token_data']['user_id'],
        'is_read'=>0,
      ))->count();

      return $totalItems;
    }
}
