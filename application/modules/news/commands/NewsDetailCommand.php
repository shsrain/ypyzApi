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
class NewsDetailCommand implements CommandInterface
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
class NewsDetailCommandHandler extends AbstractCommandHandler
{

    public $command = null;
    public $app = null;
    public $pdo = null;
    public $db_website = null;

    public function getSupportedCommandClassName()
    {
        return 'NewsDetailCommand';
    }

    protected function execute(CommandInterface $command)
    {
        $this->command = $command;
        $this->app = $command->app;
        $this->pdo = $command->pdo;
        $this->db_website = $command->db_website;

        $result = $this->newsDetail();
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

    public function newsDetail()
    {
      $res = $this->pdo->query("SELECT ypyz_document_article.id,ypyz_document.title,ypyz_document.comment,ypyz_document_article.content,ypyz_picture.path FROM ypyz_document_article
        LEFT JOIN ypyz_document ON ypyz_document_article.id=ypyz_document.id
        LEFT JOIN ypyz_picture ON ypyz_picture.id=ypyz_document.cover_id
        WHERE ypyz_document.id=".$this->app['http']->request->get('id'));
      $result_arr = $res->fetchAll(\PDO::FETCH_ASSOC);
      if($result_arr != null)
      {
        $result = array();
        $result['id'] = $result_arr[0]['id'];
        $result['title'] = $result_arr[0]['title'];
        $result['comment'] = $result_arr[0]['comment'];
        $result['content'] = $result_arr[0]['content'];
        if($result_arr[0]['path'] == null)
        {
            $result['thumb']="";
        }else{
            $result['thumb'] = "http://www.didijiankang.cn/webhtml/".$result_arr[0]['path'];
        }
        return $result;
      }
      else
      {
        return null;
      }
    }
}
