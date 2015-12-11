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
 * 这是一个返回最新的咨询列表命令类。
 *
 * @author shsrain <shsrain@163.com>
 */
class ReplyUpdateCommand implements CommandInterface
{

    public $app = null;
    public $db_website = null;

    public function __construct($app)
    {
        $this->app = $app;

        $db_config = $this->app['db_config']['website'];
        $pdo = new \PDO($db_config['dsn'],$db_config['username'],$db_config['password']);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->query('SET NAMES '.$db_config['char_set']);
        $this->db_website = new NotORM($pdo);
    }
}

/**
 * 这是一个咨询列表命令的处理类。
 *
 * @author shsrain <shsrain@163.com>
 */
class ReplyUpdateCommandHandler extends AbstractCommandHandler
{

    public $command = null;
    public $app = null;
    public $db_website = null;

    public function getSupportedCommandClassName()
    {
        return 'ReplyUpdateCommand';
    }

    protected function execute(CommandInterface $command)
    {
        $this->command = $command;
        $this->app = $command->app;
        $this->db_website = $command->db_website;
        $insert_id = $this->replyUpdate();
        $res = $this->db_website->ypyz_document_reply()->where('id',$insert_id)->fetch();
        $result['id']           = $res['id'];
        $result['document_id']  = $res['document_id'];
        $result['by_userid']    = $res['by_userid'];
        $result['message']      = $res['message'];
        $result['public_time']  = $res['public_time'];
        $user = $this->usersShow($res['by_userid']);
        $result['avatar'] = "http://api.didijiankang.cn/api/images/".$user['avatar'];
        $result['nickname'] = $user['nickname'];
        $return['datum']        = $result;
        $return['code']         = 200;
        $return['message']      = '操作成功。';
        return $return;
    }

    public function replyUpdate()
    {
      $this->db_website->ypyz_document_reply()->insert(array(
          "document_id"  => $this->app['http']->request->params('id'),
          "by_userid"    => $this->app['http']->request->params('by_userid'),
          "message"      => $this->app['http']->request->params('message'),
          "public_time"  => time(),
      ));
      $insert_id = $this->db_website->ypyz_document_reply()->insert_id();
      $res = $this->db_website->ypyz_document()->where('id',$this->app['http']->request->params('id'))->fetch();
      $res['comment'] +=1;
     foreach($res as $value)
     {
       $this->db_website->ypyz_document()->insert_update(
           array("id" => $this->app['http']->request->params('id')),
           array("comment" =>$res['comment'])
       );
     }
      return $insert_id;
    }

    public function usersShow($uid=null)
    {

      $db_config = $this->app['db_config']['upihealth2'];
      $pdo = new \PDO($db_config['dsn'],$db_config['username'],$db_config['password']);
      $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
      $pdo->query('SET NAMES '.$db_config['char_set']);
      $this->db_upihealth2 = new NotORM($pdo);

      $res=$this->db_upihealth2->user()->where(array('username'=>$uid))->fetch();
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
