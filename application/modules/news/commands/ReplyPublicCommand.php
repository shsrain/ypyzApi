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
class ReplyPublicCommand implements CommandInterface
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
class ReplyPublicCommandHandler extends AbstractCommandHandler
{

    public $command = null;
    public $app = null;
    public $db_website = null;

    public function getSupportedCommandClassName()
    {
        return 'ReplyPublicCommand';
    }

    protected function execute(CommandInterface $command)
    {
        $this->command = $command;
        $this->app = $command->app;
        $this->db_website = $command->db_website;

        $result = $this->replyPublic();
        if($result == null)
        {
          $return['request'] = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
          $return['code'] = 30105;
          $return['message'] = '列表不存在。';
        }
        else
        {
          $return['data'] = $result['data'];
          $return['page'] = $result['page'];
          $return['code'] = 200;
          $return['message'] = '操作成功。';
        }
        return $return;
    }

    public function replyPublic()
    {
      $currentPage = ($this->app['http']->request->get('page') <= 0) ? 1 : $this->app['http']->request->get('page');
      $itemsPerPage = ($this->app['http']->request->get('count') <= 0) ? 5 : $this->app['http']->request->get('count');
      $neighbours = ($this->app['http']->request->get('neighbor') <= 0) ? 1 : $this->app['http']->request->get('neighbor');

      $totalItems=$this->db_website->ypyz_document_reply()->where('document_id',$this->app['http']->request->get('id'))->count();
      if($totalItems==0)
      {
          return null;
      }
      $pagination = new Pagination($totalItems, $currentPage, $itemsPerPage, $neighbours);
      $offset = $pagination->offset();
      $limit = $pagination->limit();
      $results=$this->db_website->ypyz_document_reply()
      ->where('document_id',$this->app['http']->request->get('id'))
      ->order("id DESC")
      ->limit($limit,$offset);
      $i = 0;
      foreach($results as $key=>$res)
      {
          $result['data'][$i]['id'] = $res['id'];
          $result['data'][$i]['document_id'] = $res['document_id'];
          $result['data'][$i]['by_userid'] = $res['by_userid'];
          $result['data'][$i]['message'] = $res['message'];
          $result['data'][$i]['public_time'] = $res['public_time'];
          $user = $this->usersShow($res['by_userid']);
          $result['data'][$i]['avatar'] = "http://api.didijiankang.cn/api/images/".$user['avatar'];
          $result['data'][$i]['nickname'] = $user['nickname'];
          $i++;
      }


      $totalPages = $pagination->totalPages();
      if ($currentPage > $totalPages)
      {
          $result['page']['current_cursor'] = $totalPages;
          $result['page']['previous_cursor'] = ($totalPages == 1) ? 1 : $totalPages-1;
          $result['page']['next_cursor'] = $totalPages;
      }
      else
      {
          $result['page']['current_cursor'] = $currentPage;
          $result['page']['previous_cursor'] = ($currentPage <= 1) ? 1 : $currentPage-1;
          if(($currentPage+1) >= $totalPages)
          {
              $result['page']['next_cursor'] =  $totalPages;
          }
          else
          {
              $result['page']['next_cursor'] =  $currentPage+1;
          }
      }
      $result['page']['total_number'] = $totalItems;
      return $result;
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
