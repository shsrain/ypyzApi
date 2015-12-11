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
class InquiriesPublicCommand implements CommandInterface
{

    public $app = null;

    public function __construct($app)
    {
        $this->app = $app;
    }
}

/**
 * 这是一个咨询列表命令的处理类。
 *
 * @author shsrain <shsrain@163.com>
 */
class InquiriesPublicCommandHandler extends AbstractCommandHandler
{

    public $command = null;
    public $app = null;

    public function getSupportedCommandClassName()
    {
        return 'InquiriesPublicCommand';
    }

    protected function execute(CommandInterface $command)
    {
        $this->command = $command;
        $this->app = $command->app;

        $result = $this->inquiriesPublic();
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

    public function inquiriesPublic()
    {
      $currentPage = ($this->app['http']->request->get('page') <= 0) ? 1 : $this->app['http']->request->get('page');
      $itemsPerPage = ($this->app['http']->request->get('count') <= 0) ? 5 : $this->app['http']->request->get('count');
      $neighbours = ($this->app['http']->request->get('neighbor') <= 0) ? 1 : $this->app['http']->request->get('neighbor');

      $totalItems=$this->app['pdo']->ypyz_inquiry()->count();
      if($totalItems==0)
      {
          return null;
      }
      $pagination = new Pagination($totalItems, $currentPage, $itemsPerPage, $neighbours);
      $offset = $pagination->offset();
      $limit = $pagination->limit();
      $results=$this->app['pdo']->ypyz_inquiry()
      ->order("inquiry_id DESC")
      ->limit($limit,$offset);

      $result = array();
      foreach($results as $key=>$res)
      {
          $result['data'][$key]['inquiry_id'] = $res['inquiry_id'];
          $result['data'][$key]['patient_id'] = $res['patient_id'];
          $result['data'][$key]['casehistory_id'] = $res['casehistory_id'];
          $result['data'][$key]['description'] = $res['description'];
          $result['data'][$key]['public_userid'] = $res['public_userid'];
          $result['data'][$key]['public_time'] = $res['public_time'];
          $result['data'][$key]['is_solve'] = $res['is_solve'];
          $result['data'][$key]['end_time'] = $res['end_time'];
          $result['data'][$key]['is_reply'] = $res['is_reply'];
          $result['data'][$key]['last_reply_time'] = $res['last_reply_time'];
          $result['data'][$key]['last_reply_userid'] = $res['last_reply_userid'];
          $picture_arr = explode(',',$res['picture']);
          foreach($picture_arr as $k=>$pic)
          {
            $result['data'][$key]['picture'][$k] = "http://api.didijiankang.cn/api/images/uploads/".$pic;
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
