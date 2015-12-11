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
class NewsPublicCommand implements CommandInterface
{

    public $app = null;
    public $db_website = null;
    public $pdo = null;

    public function __construct($app)
    {
        $this->app = $app;

        $db_config = $this->app['db_config']['website'];
        $pdo = new \PDO($db_config['dsn'],$db_config['username'],$db_config['password']);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->query('SET NAMES '.$db_config['char_set']);
        $this->pdo = $pdo;
        $this->db_website = new NotORM($pdo);
    }
}

/**
 * 这是一个咨询列表命令的处理类。
 *
 * @author shsrain <shsrain@163.com>
 */
class NewsPublicCommandHandler extends AbstractCommandHandler
{

    public $command = null;
    public $app = null;
    public $pdo = null;
    public $db_website = null;

    public function getSupportedCommandClassName()
    {
        return 'NewsPublicCommand';
    }

    protected function execute(CommandInterface $command)
    {
        $this->command = $command;
        $this->app = $command->app;
        $this->pdo = $command->pdo;
        $this->db_website = $command->db_website;

        $result = $this->newsPublic();
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

    public function newsPublic()
    {
      $currentPage = ($this->app['http']->request->get('page') <= 0) ? 1 : $this->app['http']->request->get('page');
      $itemsPerPage = ($this->app['http']->request->get('count') <= 0) ? 5 : $this->app['http']->request->get('count');
      $neighbours = ($this->app['http']->request->get('neighbor') <= 0) ? 1 : $this->app['http']->request->get('neighbor');

      $totalItems=$this->db_website->ypyz_document_article()->count();
      if($totalItems==0)
      {
          return null;
      }
      $pagination = new Pagination($totalItems, $currentPage, $itemsPerPage, $neighbours);
      $offset = $pagination->offset();
      $limit = $pagination->limit();
      $res = $this->pdo->query("SELECT ypyz_document_article.id,ypyz_document.title,ypyz_document.comment,ypyz_document.description,ypyz_picture.path FROM ypyz_document_article
        LEFT JOIN ypyz_document ON ypyz_document_article.id=ypyz_document.id
        LEFT JOIN ypyz_picture ON ypyz_picture.id=ypyz_document.cover_id LIMIT {$offset},{$limit}");
      $result_arr = $res->fetchAll(\PDO::FETCH_ASSOC);

      foreach($result_arr as $key=>$res)
      {
          $result['data'][$key]['id'] = $res['id'];
          $result['data'][$key]['title'] = $res['title'];
          if(emtpy( $res['comment']))
          {
            $result['data'][$key]['comment'] = 0;
          }else{
            $result['data'][$key]['comment'] = $res['comment'];
          }
          if(emtpy($res['description']))
          {
            $result['data'][$key]['description'] = "暂无描述";
          }else{
            $result['data'][$key]['description'] = $res['description'];
          }
          if($res['path'] != null)
          {
            $result['data'][$key]['thumb'] = "http://www.didijiankang.cn/webhtml/".$res['path'];
          }else{
            $result['data'][$key]['thumb'] = "";
          }

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
}
