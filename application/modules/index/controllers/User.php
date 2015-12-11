<?php

/*
 * 这个文件是 youpaiyunzhi 的一部分。
 *
 * (c) shsrain <shsrain@163.com>
 *
 * 对于全版权和许可信息，请查看分发此源代码的许可文件。
 */

 /**
  * 这是一个用户管理控制器。
  *
  * @author shsrain <shsrain@163.com>
  */

 class User{

   public $app = null;

   public function __construct($app)
   {
      $this->app = $app;
   }

  public function index()
  {
     $results=$this->app['pdo']->ypyz_inquiry()
     ->order("inquiry_id DESC")
     ->limit(4,2);
     $result = array();
     foreach($results as $key=>$res)
     {
         $result['data'][$key]['inquiry_id'] = $res['inquiry_id'];

         //$result['data'][$key]['picture'] = $res['picture'];
         $picture_arr = explode(',',$res['picture']);
         foreach($picture_arr as $k=>$pic)
         {
           $result['data'][$key]['picture'][$k] = "http://api.didijiankang.cn/api/images/uploads/".$pic;
         }
     }
     $headers = array('Content-Type' => 'text/plain');
     $this->app['response']->writeHead(200, $headers);
     $this->app['response']->end($this->app['encoder']->encode($result));
   }

  public function lists()
  {
     $results=$this->app['pdo']->ypyz_inquiry()
     ->order("inquiry_id DESC")
     ->limit(4,2);
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
         //$result['data'][$key]['picture'] = $res['picture'];
         $picture_arr = explode(',',$res['picture']);
         foreach($picture_arr as $k=>$pic)
         {
           $result['data'][$key]['picture'][$k] = "http://api.didijiankang.cn/api/images/uploads/".$pic;
         }
     }
     $headers = array('Content-Type' => 'text/plain');
     $this->app['response']->writeHead(200, $headers);
     $this->app['response']->end($this->app['encoder']->encode($result));
   }

   public function login()
   {
     print_r('user login action');
   }
 }
