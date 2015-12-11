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
 * 这是一个根据咨询ID获取回复的信息列表命令类。
 *
 * @author shsrain <shsrain@163.com>
 */
class AdvisoriesShowCommand implements CommandInterface
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
 * 这是一个根据咨询ID获取回复的信息列表命令的处理类。
 *
 * @author shsrain <shsrain@163.com>
 */
class AdvisoriesShowCommandHandler extends AbstractCommandHandler
{
    public $command = null;
    public $app = null;
    public $db_upihealth2 = null;
    public $userInfo = null;

    public function getSupportedCommandClassName()
    {
        return 'AdvisoriesShowCommand';
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

      $this->userInfo = $this->usersShow();

      $result = $this->inquiriesShow();
      if($result == null)
      {
        $return['request'] = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
        $return['code'] = 30105;
        $return['message'] = '列表不存在。';
      }
      else
      {
        $result_advisorie = $this->advisoriesShow();
        $result_pictures = $this->inquiriesShowPicture();

        $return['inquiries'] = $result;
        $return['pictures'] = $result_pictures;
        $return['advisories'] = $result_advisorie;
        if($result_advisorie['page']['current_cursor'] == 1 || $result_advisorie['data'] == null)
        {
           $return['first_page'] = 1;
        }
        else
        {
          $return['first_page'] = 0;
        }
        $return['code'] = 200;
        $return['message'] = '操作成功。';
        return $return;
      }
    }

    public function advisoriesShow()
    {
      $currentPage = ($this->app['http']->request->get('page') <= 0) ? 1000000 : $this->app['http']->request->get('page');
      $itemsPerPage = ($this->app['http']->request->get('count') <= 0) ? 5 : $this->app['http']->request->get('count');
      $neighbours = ($this->app['http']->request->get('neighbor') <= 0) ? 1 : $this->app['http']->request->get('neighbor');

      $totalItems=$this->app['pdo']->ypyz_dialog()
      ->where(array(
        'inquiry_id'=>$this->app['http']->request->get('inquiry_id'),
        'by_userid'=>$this->app['access_token_data'],
      ))->count();
      if($totalItems==0)
      {
          return null;
      }
      $pagination = new Pagination($totalItems, $currentPage, $itemsPerPage, $neighbours);
      $offset = $pagination->offset();
      $limit = $pagination->limit();
      $results=$this->app['pdo']->ypyz_dialog()
      ->where(array(
        'inquiry_id'=>$this->app['http']->request->get('inquiry_id'),
        'by_userid'=>$this->app['access_token_data'],
      ))
      ->order("send_time ASC")
      ->limit($limit,$offset);

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
          $result['data'][$key]['avatar'] = "http://api.didijiankang.cn/api/images/".$this->userInfo['avatar'];
          $reply_results=$this->app['pdo']->ypyz_dialog()
          ->where(array(
            'reply_id'=>$res['dialog_id']
          ))
          ->order("send_time ASC");
          if(count($reply_results))
          {
            foreach($reply_results as $k=>$r)
            {
              $result['data'][$key]['reply'][$k]  = array(
                'dialog_id'  => $r['dialog_id'],
                'inquiry_id' => $r['inquiry_id'],
                'by_userid'  => $r['by_userid'],
                'to_userid'  => $r['to_userid'],
                'reply_id'   => $r['reply_id'],
                'message'    => $r['message'],
                'send_time'  => $r['send_time'],
                'avatar'  => "http://api.didijiankang.cn/api/images/".$this->userInfo['avatar'],
              );
            }
          }
          else
          {
            $result['data'][$key]['reply'] = array();
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

        /*
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
        */
        $result['avatar'] = "http://api.didijiankang.cn/api/images/".$this->userInfo['avatar'];
        return $result;
      }
      else
      {
        return null;
      }
    }

    public function inquiriesShowPicture()
    {
      $res=$this->app['pdo']->ypyz_inquiry()
      ->where(array(
        'inquiry_id'=>$this->app['http']->request->get('inquiry_id'),
        'public_userid'=>$this->app['access_token_data']['user_id']
      ))->fetch();
      if($res != null)
      {
        $result = array();
        $picture_arr = explode(',',$res['picture']);
        foreach($picture_arr as $k=>$pic)
        {
          $pic_key_name = 'picture_'.$k;
          $result[$k][$pic_key_name] = "http://api.didijiankang.cn/api/images/uploads/".$pic;
          $result[$k]['public_time'] = $res['public_time'];
          $result[$k]['avatar'] = "http://api.didijiankang.cn/api/images/".$this->userInfo['avatar'];
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
