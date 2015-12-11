<?php

/*
 * 这个文件是 youpaiyunzhi 的一部分。
 *
 * (c) shsrain <shsrain@163.com>
 *
 * 对于全版权和许可信息，请查看分发此源代码的许可文件。
 */
use Gregwar\Image\Image;

 /**
  * 这是一个咨询模块资源路由文件。
  *
  * @author shsrain <shsrain@163.com>
  */

$headers = $app['loader']->helper('headers',$app);
$middleware = $app['loader']->helper('middleware',$app);

/**
 * 咨询相关接口
 * URL: inquiry.php
 * 支持格式：JSON
 * 注意事项: 无
 */

// 咨询读取接口

/**
* Route: inquiries/public
* 返回最新的咨询列表。
* URL: inquiry.php/inquiries/public
* 支持格式：JSON
* HTTP请求方式：GET
* 是否需要登录：否
* 请求参数：
* count 可选 int 单页返回的记录条数，默认为5。
* page 可选 int 返回结果的页码，默认为1。
* 注意事项: 无
*/
$app['http']->get(
   '/inquiries/public',
   function () use ($app,$headers)
   {
      //$response['data'] = $app['loader']->migration('inquiry','inquiries.public',$app);
      $entity = $app['bus']->dispatch(new InquiriesPublicCommand($app));
      $body = $app['encoder']->encode($entity[0]);

      $headers['setCommonHeader']();
      $app['http']->response->setStatus(200);
      $app['http']->response->setBody($body);
      $app['http']->stop();
   }
);

/**
* Route: inquiries/resolved
* 获取多条已解决的咨询列表。
* URL: inquiry.php/inquiries/resolved
* 支持格式：JSON
* HTTP请求方式：GET
* 是否需要登录：否
* 请求参数：
* count 可选 int 单页返回的记录条数，默认为5。
* page 可选 int 返回结果的页码，默认为1。
* 注意事项: 无
*/
$app['http']->get(
   '/inquiries/resolved',
   function () use ($app,$headers)
   {
       // $response['data'] = $app['loader']->migration('inquiry','inquiries.resolved',$app);
       $entity = $app['bus']->dispatch(new InquiriesResolvedCommand($app));
       $body = $app['encoder']->encode($entity[0]);

       $headers['setCommonHeader']();
       $app['http']->response->setStatus(200);
       $app['http']->response->setBody($body);
       $app['http']->stop();
   }
);

/**
* Route: inquiries/by_me
* 根据用户ID获取多条咨询列表。
* URL: inquiry.php/inquiries/by_me
* 支持格式：JSON
* HTTP请求方式：GET
* 是否需要登录：是
* 请求参数：
* client_id 	必选 	string 	应用Key。
* access_token 	必选 	string 	OAuth2授权。
* count 	可选 	int 	单页返回的记录条数，默认为5。
* page 	可选 	int 	返回结果的页码，默认为1。
* 注意事项: 无
*/
$app['http']->get(
   '/inquiries/by_me',
   $middleware['oauth'](),
   function () use ($app,$headers)
   {
       // $response['data'] = $app['loader']->migration('inquiry','inquiries.by_me',$app);
       $entity = $app['bus']->dispatch(new InquiriesByMeCommand($app));
       $body = $app['encoder']->encode($entity[0]);

       $headers['setCommonHeader']();
       $app['http']->response->setStatus(200);
       $app['http']->response->setBody($body);
       $app['http']->stop();
   }
);

/**
* Route: inquiries/show
* 根据ID获取单条咨询信息。
* URL: inquiry.php/inquiries/show
* 支持格式：JSON
* HTTP请求方式：GET
* 是否需要登录：是
* 请求参数：
* access_ token 	必选 	string 	OAuth2授权。
* inquiry_id 必选 int64 需要获取的咨询ID。
* 注意事项: 无
*/
$app['http']->get(
   '/inquiries/show',
   $middleware['oauth'](),
   $middleware['parseParam']('inquiry_id'),
   function () use ($app,$headers)
   {
       // $response['datum'] = $app['loader']->migration('inquiry','inquiries.show',$app);
       $entity = $app['bus']->dispatch(new InquiriesShowCommand($app));
       $body = $app['encoder']->encode($entity[0]);

       $headers['setCommonHeader']();
       $app['http']->response->setStatus(200);
       $app['http']->response->setBody($body);
       $app['http']->stop();
   }
);

// 提问读取接口

/**
* Route: advisories/show
* 根据咨询ID获取回复的信息列表。
* URL: inquiry.php/advisories/show
* 支持格式：JSON
* HTTP请求方式：GET
* 是否需要登录：是
* 请求参数：
* access_ token 	必选 	string 	OAuth2授权。
* inquiry_id 必选 int64 需要获取的咨询ID。
* count 	可选 	int 	单页返回的记录条数，默认为5。
* page 	可选 	int 	返回结果的页码，默认为1。
* 注意事项: 无
*/
$app['http']->get(
   '/advisories/show',
   $middleware['oauth'](),
   $middleware['parseParam']('inquiry_id'),
   function () use ($app,$headers)
   {
       //$response['data'] = $app['loader']->migration('inquiry','advisories.show',$app);
       $entity = $app['bus']->dispatch(new AdvisoriesShowCommand($app));
       $body = $app['encoder']->encode($entity[0]);

       $headers['setCommonHeader']();
       $app['http']->response->setStatus(200);
       $app['http']->response->setBody($body);
       $app['http']->stop();
   }
);

// 提醒读取接口

/**
* Route: remind/msg_unread
* 根据咨询ID获取回复消息未读数。
* URL: inquiry.php/remind/msg_unread
* 支持格式：JSON
* HTTP请求方式：GET
* 是否需要登录：是
* 请求参数：
* inquiry_id 必选 int64 需要获取的咨询ID。
* 注意事项: 无
*/
$app['http']->get(
   '/remind/msg_unread',
   $middleware['oauth'](),
   $middleware['parseParam']('inquiry_id'),
   function () use ($app,$headers)
   {
       //$body['data'] = $app['loader']->migration('inquiry','advisories.msg_new',$app);
       $entity = $app['bus']->dispatch(new RemindMsgUnreadCommand($app));
       $body = $app['encoder']->encode($entity[0]);

       $headers['setCommonHeader']();
       $app['http']->response->setStatus(200);
       $app['http']->response->setBody($body);
       $app['http']->stop();
   }
);

/**
* Route: remind/msg_unreadall
* 获取所有回复消息未读数。
* URL: inquiry.php/remind/msg_unreadall
* 支持格式：JSON
* HTTP请求方式：GET
* 是否需要登录：是
* 请求参数：
* 注意事项: 无
*/
$app['http']->get(
   '/remind/msg_unreadall',
   $middleware['oauth'](),
   function () use ($app,$headers)
   {
       $entity = $app['bus']->dispatch(new RemindMsgUnreadAllCommand($app));
       $body = $app['encoder']->encode($entity[0]);

       $headers['setCommonHeader']();
       $app['http']->response->setStatus(200);
       $app['http']->response->setBody($body);
       $app['http']->stop();
   }
);

/**
* Route: advisories/reply
* 针对某一条咨询的某一次提问获取回复。
* URL: inquiry.php/advisories/reply
* 支持格式：JSON
* HTTP请求方式：GET
* 是否需要登录：是
* 请求参数：
* dialog_id 必选 int 咨询对话ID编号。
* 注意事项: 无
*/
$app['http']->get(
   '/advisories/reply',
   $middleware['oauth'](),
   $middleware['parseParam']('dialog_id'),
   function () use ($app,$headers)
   {
       //$response['data'] = $app['loader']->migration('inquiry','advisories.reply',$app);
       $entity = $app['bus']->dispatch(new AdvisoriesReplyCommand($app));
       $body = $app['encoder']->encode($entity[0]);

       $headers['setCommonHeader']();
       $app['http']->response->setStatus(200);
       $app['http']->response->setBody($body);
       $app['http']->stop();
   }
);

// 用户读取接口

/**
* Route: users/show
* 获取用户信息。
* URL: inquiry.php/users/show
* 支持格式：JSON
* HTTP请求方式：GET
* 是否需要登录：是
* 请求参数：
* access_token 必选 string 访问token。
* 注意事项: 无
*/
$app['http']->get(
   '/users/show',
   $middleware['oauth'](),
   function () use ($app,$headers)
   {
      $entity = $app['bus']->dispatch(new UsersShowCommand($app));
      $body = $app['encoder']->encode($entity[0]);

      $headers['setCommonHeader']();
      $app['http']->response->setStatus(200);
      $app['http']->response->setBody($body);
      $app['http']->stop();
   }
);

// 关注读取接口

/**
* Route: focus/public
* 获取用户的关注列表。
* URL: inquiry.php/focus/public
* 支持格式：JSON
* HTTP请求方式：GET
* 是否需要登录：是
* 请求参数：
* access_token 必选 string 访问token。
* 注意事项: 无
*/
$app['http']->get(
   '/focus/public',
   $middleware['oauth'](),
   function () use ($app,$headers)
   {
      $entity = $app['bus']->dispatch(new FocusPublicCommand($app));
      $body = $app['encoder']->encode($entity[0]);

      $headers['setCommonHeader']();
      $app['http']->response->setStatus(200);
      $app['http']->response->setBody($body);
      $app['http']->stop();
   }
);

// 咨询写入接口

/**
* Route: inquiries/update
* 发布一条新的咨询。
* URL: inquiry.php/inquiries/update
* 支持格式：JSON
* HTTP请求方式：POST
* 是否需要登录：是
* 请求参数：
* public_userid 	必选 	int 	发布咨询的用户。
* patient_id 	必选 	int 	关注人的ID，即为哪个关注者咨询。
* description 	必选 	string 	病情及症状描述。
* casehistory_id 	可选 	int 	关联关注人的病历ID，默认值0:不关联病历，其他值：关联的ID。
* picture 	可选 	array 	附加图片附件数据。最多三张图，也可不填。
* 注意事项: 无
*/
$app['http']->post(
   '/inquiries/update',
   $middleware['oauth'](),
   //$middleware['jsonSchema']($app['http']->request->getBody(),__DIR__.'/schema/inquiries.update.json'),
   function () use ($app,$headers)
   {
       //$response['data'] = $app['loader']->migration('inquiry','inquiries.update',$app);
       $entity = $app['bus']->dispatch(new InquiriesUpdateCommand($app));
       $body = $app['encoder']->encode($entity[0]);

       $headers['setCommonHeader']();
       $app['http']->response->setStatus(200);
       $app['http']->response->setBody($body);
       $app['http']->stop();
   }
);


/**
* Route: inquiries/upload
* 发布一条新的咨询。
* URL: inquiry.php/inquiries/upload
* 支持格式：JSON
* HTTP请求方式：POST
* 是否需要登录：是
* 请求参数：
* file 	必选 	上传文件的键。
* userid 	必选 	int 	上传者用户id。
* 注意事项: 上传时设置媒体类型。
*/
$app['http']->post(
   '/inquiries/upload',
   $middleware['oauth_test'](),
   function () use ($app,$headers)
   {
       $files_key = 'uploadfile';
       $new_files = array();
       foreach($_FILES as $file_key=>$file_value)
       {
         foreach($file_value as $file_k=>$file_v)
         {
           switch ($file_k) {
              case 'name':
              $new_files['name'][] =$file_v;
              break;
              case 'type':
              $new_files['type'][] =$file_v;
              break;
              case 'tmp_name':
              $new_files['tmp_name'][] =$file_v;
              break;
              case 'error':
              $new_files['error'][] =$file_v;
              break;
              case 'size':
              $new_files['size'][] =$file_v;
              break;
             default:
               break;
           }
         }

       }
       unset($_FILES);
       $_FILES[$files_key] = $new_files;
       $files_uploads = $app['global_config']['upload']['path'];
       /* 过滤 $_FILES 数组 */
       if(is_array($_FILES[$files_key]['error']))
       {
         foreach($_FILES[$files_key]['error'] as $key=>$value)
         {
           // 删除多文件上传时为空的情况。
           if($value == 4)
           {
             unset($_FILES[$files_key]['name'][$key]);
             unset($_FILES[$files_key]['type'][$key]);
             unset($_FILES[$files_key]['tmp_name'][$key]);
             unset($_FILES[$files_key]['error'][$key]);
             unset($_FILES[$files_key]['size'][$key]);
           }
         }
         // 依据文件名是否相同检查是否重复上传同一个文件。
         if (count($_FILES[$files_key]['name']) != count(array_unique($_FILES[$files_key]['name'])))
         {
           $body['code'] = 30109;
           $body['message'] = '上传了重复文件。';
           $headers['setCommonHeader']();
           $app['http']->response->setStatus(200);
           $app['http']->response->setBody($app['encoder']->encode($body));
           $app['http']->stop();
         }
       }
       // 检查是否存在上传文件。
       if(empty($_FILES[$files_key]['tmp_name']))
       {
         $body['code'] = 30109;
         $body['message'] = '没有文件上传。';
         $headers['setCommonHeader']();
         $app['http']->response->setStatus(200);
         $app['http']->response->setBody($app['encoder']->encode($body));
         $app['http']->stop();
       }
       /* 创建上传组件相关对象 */
       $storage = new \Upload\Storage\FileSystem($files_uploads,true);
       $file = new \Upload\File($files_key, $storage);
       $data = array();
       $i = 0;
       $k = 0;
       /* 文件上传数量限制 */
       if ($file->count()>5)
       {
           $error = '已到最大数量了！';
           $body['code'] = 30109;
           $body['message'] = $error;
           $headers['setCommonHeader']();
           $app['http']->response->setStatus(200);
           $app['http']->response->setBody($app['encoder']->encode($body));
           $app['http']->stop();
       }
       $file->addValidations(array(
           new \Upload\Validation\Mimetype(array('image/png','image/jpeg')),
           new \Upload\Validation\Size('5M')
       ))->beforeValidate(function($fileInfo) use ($file){
         /* 文件上传失败，捕获错误代码 */
         if ($file->getErrors())
         {
             $error = $file->getErrors();
             $body['code'] = 30109;
             $body['message'] = $error;
             $headers['setCommonHeader']();
             $app['http']->response->setStatus(200);
             $app['http']->response->setBody($app['encoder']->encode($body));
             $app['http']->stop();
         }
         /* 无效上传 */
         $res_getName = $fileInfo->getName();
         if (empty($res_getName))
         {
             $error = '未知上传错误！';
             $body['code'] = 30109;
             $body['message'] = $error;
             $headers['setCommonHeader']();
             $app['http']->response->setStatus(200);
             $app['http']->response->setBody($app['encoder']->encode($body));
             $app['http']->stop();
         }
         /* 检查是否合法上传 */
         if (!$fileInfo->isUploadedFile())
         {
             $error = '非法上传文件！';
             $body['code'] = 30109;
             $body['message'] = $error;
             $headers['setCommonHeader']();
             $app['http']->response->setStatus(200);
             $app['http']->response->setBody($app['encoder']->encode($body));
             $app['http']->stop();
         }
       })->afterValidate(function($fileInfo) use (&$data,&$i){
         /* 记录原始文件信息 */
         $data[$i] = array(
             'name'       => mb_convert_encoding($fileInfo->getNameWithExtension(), "UTF-8", "GBK"),
             'extension'  => $fileInfo->getExtension(),
             'mime'       => $fileInfo->getMimetype(),
             'size'       => $fileInfo->getSize(),
             'md5'        => $fileInfo->getMd5(),
             'sha1'       => $fileInfo->getHash('sha1'),
             'dimensions' => $fileInfo->getDimensions()
         );
         $i++;
       })->beforeUpload(function($fileInfo) use (&$data){
         /* 对上传文件进行设置 */
         $new_filename = date("YmdHis").uniqid();
         $fileInfo->setName($new_filename);
       })->afterUpload(function($fileInfo) use (&$data,&$k,$files_uploads,$app){
         /* 裁剪，压缩 */
         /*Image::open($files_uploads.$fileInfo->getNameWithExtension())
              ->save($files_uploads.$fileInfo->getName().'_thumb.jpg','jpg',30);
         */
         /*Image::open($files_uploads.$fileInfo->getNameWithExtension())
              ->resize(100, 100)
              ->save($files_uploads.$fileInfo->getName().'_small.'.$fileInfo->getExtension());
        */
         /* 记录保存成功的文件 */
         $data[$k]['savename'] = $fileInfo->getNameWithExtension();
         $data[$k]['savepath'] = $files_uploads.$fileInfo->getNameWithExtension();
         //$data[$k]['thumb'] = $fileInfo->getName().'_thumb.jpg';
         //$data[$k]['small'] = $fileInfo->getName().'_small.'.$fileInfo->getExtension();
         /* 保存到数据库 */
         // 保存原始图像
         $app['pdo']->ypyz_attachment()->insert(array(
             "moduleid" => 'inquiry',
             "filename" => $data[$k]['name'],
             "savename" => $data[$k]['savename'],
             "filesize" => $data[$k]['size'],
             "fileext" => $data[$k]['extension'],
             "isimage" => 1,
             "isthumb" => 0,
             "userid" => $app['access_token_data']['user_id'],
             "createtime" => time(),
             "uploadip" => $_SERVER["REMOTE_ADDR"],
             "status" => 0,
         ));
         // 保存原始图像的压缩文件
         /*$insert_id = $app['pdo']->ypyz_attachment()->insert_id();
         $app['pdo']->ypyz_attachment()->insert(array(
            "moduleid" => 'inquiry',
             "id" => $insert_id,
             "filename" => $data[$k]['thumb'],
             "filesize" => $data[$k]['size'],
             "fileext" => $data[$k]['extension'],
             "isimage" => 0,
             "isthumb" => 1,
             //"userid" => $app['access_token_data']['user_id'],
             "userid" => '15919940006',
             "createtime" => time(),
             "uploadip" => $_SERVER["REMOTE_ADDR"],
             "status" => 0,
         ));
         */
         $k++;
       });

       try {
           $file->upload();
       } catch (\Exception $e) {
           $errors = $file->getErrors();
           $body['code'] = 30109;
           $body['message'] = $errors;
           $headers['setCommonHeader']();
           $app['http']->response->setStatus(200);
           $app['http']->response->setBody($app['encoder']->encode($body));
           $app['http']->stop();
       }

       // 设置返回数据。
       $body['code'] = 200;
       $body['message'] = '操作成功。';

       foreach($data as $key =>$value)
       {
         unset($data[$key]['name']);
         unset($data[$key]['extension']);
         unset($data[$key]['mime']);
         unset($data[$key]['size']);
         unset($data[$key]['md5']);
         unset($data[$key]['sha1']);
         unset($data[$key]['dimensions']);
         unset($data[$key]['savepath']);
         //unset($data[$key]['thumb']);
         //unset($data[$key]['small']);
       }
       $body['data'] = $data;
       $headers['setCommonHeader']();
       $app['http']->response->setStatus(200);
       $app['http']->response->setBody($app['encoder']->encode($body));
       $app['http']->stop();
   }
);

// 提问写入接口

/**
* Route: advisories/update
* 针对某一条咨询提出新问题。
* URL: inquiry.php/advisories/update
* 支持格式：JSON
* HTTP请求方式：POST
* 是否需要登录：是
* 请求参数：
* inquiry_ id 	必选 	int 	所属的咨询ID编号。
* by_ userid 	必选 	int 	发送者用户ID。
* to_ userid 	必选 	int 	接收者用户ID。0则为不指定。
* message 	必选 	string 	发送的消息。
* 注意事项: 无
*/
$app['http']->post(
   '/advisories/update',
   $middleware['oauth'](),
   //$middleware['jsonSchema']($app['http']->request->getBody(),__DIR__.'/schema/advisories.update.json'),
   function () use ($app,$headers)
   {
       //$response['data'] = $app['loader']->migration('inquiry','advisories.update',$app);
       $entity = $app['bus']->dispatch(new AdvisoriesUpdateCommand($app));
       $body = $app['encoder']->encode($entity[0]);

       $headers['setCommonHeader']();
       $app['http']->response->setStatus(200);
       $app['http']->response->setBody($body);
       $app['http']->stop();
   }
);

// 提醒写入接口

/**
* Route: remind/msg_read
* 根据咨询ID对未读回复数进行清零。
* URL: inquiry.php/remind/msg_read
* 支持格式：JSON
* HTTP请求方式：GET
* 是否需要登录：是
* 请求参数：
* inquiry_id 必选 int64 需要获取的咨询ID。
* 注意事项: 无
*/
$app['http']->get(
   '/remind/msg_read',
   $middleware['oauth'](),
   $middleware['parseParam']('inquiry_id'),
   function () use ($app,$headers)
   {
       //$body['data'] = $app['loader']->migration('inquiry','advisories.msg_new',$app);
       $entity = $app['bus']->dispatch(new RemindMsgReadCommand($app));
       $body = $app['encoder']->encode($entity[0]);

       $headers['setCommonHeader']();
       $app['http']->response->setStatus(200);
       $app['http']->response->setBody($body);
       $app['http']->stop();
   }
);

/**
* Route: remind/msg_readall
* 对所有未读回复数进行清零。
* URL: inquiry.php/remind/msg_readall
* 支持格式：JSON
* HTTP请求方式：GET
* 是否需要登录：是
* 注意事项: 无
*/
$app['http']->get(
   '/remind/msg_readall',
   $middleware['oauth'](),
   function () use ($app,$headers)
   {
       $entity = $app['bus']->dispatch(new RemindMsgReadAllCommand($app));
       $body = $app['encoder']->encode($entity[0]);

       $headers['setCommonHeader']();
       $app['http']->response->setStatus(200);
       $app['http']->response->setBody($body);
       $app['http']->stop();
   }
);

/**
* Route: message/send
* 发送一条短信息。
* URL: inquiry.php/message/send
* 支持格式：JSON
* HTTP请求方式：POST
* 是否需要登录：是
* 注意事项: 无
*/
$app['http']->get(
   '/message/send',
   //$middleware['oauth'](),
   function () use ($app,$headers)
   {
       $to = $app['http']->request->params('send_mobile');
       $msg = $app['http']->request->params('message');
       $encode = mb_detect_encoding($msg, array('ASCII','GB2312','GBK','UTF-8'));
       if ($encode == "UTF-8")
       {
         $msg = iconv("UTF-8","GBK//TRANSLIT",$msg);
       }

       $smsresult = sx139000($to,$msg,$time=null);
       $tmp=substr_count($smsresult,'num=1');
       if($tmp>=1)
       {
         $body['code'] = 200;
         $body['message'] = '发送成功。';
       }
       else
       {
         $body['code'] = 30501;
         $body['message'] = '发送失败，请重新发送。';
       }
       $headers['setCommonHeader']();
       $app['http']->response->setStatus(200);
       $app['http']->response->setBody($app['encoder']->encode($body));
       $app['http']->stop();
   }
);

/**
* Route: case/by_user
* 对所有未读回复数进行清零。
* URL: inquiry.php/case/by_user
* 支持格式：JSON
* HTTP请求方式：GET
* 是否需要登录：是
* 请求参数：
* patient_id  必选 int64 关注者id。
* 注意事项: 无
*/
$app['http']->get(
   '/casehistory/by_user',
   $middleware['oauth'](),
   function () use ($app,$headers)
   {
       //$entity = $app['bus']->dispatch(new RemindMsgReadAllCommand($app));
       //$body = $app['encoder']->encode($entity[0]);
       $entity = array(
         'is_exist' => 1,
         'casehistory_id'=>23,
         'redirect_url'=>"http://www.didijiankang.cn",
       );
       $body['data'] = $entity;
       $body['code'] = 200;
       $body['message'] = '操作成功。';

       $headers['setCommonHeader']();
       $app['http']->response->setStatus(200);
       $app['http']->response->setBody($app['encoder']->encode($body));
       $app['http']->stop();
   }
);

/**
* Route: case/by_user
* 对所有未读回复数进行清零。
* URL: inquiry.php/casehistory/update_url
* 支持格式：JSON
* HTTP请求方式：GET
* 是否需要登录：是
* 请求参数：
* patient_id  必选 int64 关注者id。
* 注意事项: 无
*/
$app['http']->get(
   '/casehistory/redirect_url',
   $middleware['oauth'](),
   function () use ($app,$headers)
   {
       //$entity = $app['bus']->dispatch(new RemindMsgReadAllCommand($app));
       //$body = $app['encoder']->encode($entity[0]);
       $app['http']->response->redirect('http://www.didijiankang.cn?patient_id=2', 200);
   }
);

// 404路由错误返回Json消息。
$app['http']->notFound(function () use ($app,$headers) {

  $body['request'] = $app['http']->request->getRootUri().$app['http']->request->getResourceUri();
  $body['code'] = 20009;
  $body['message'] = '请求的api不存在。';

  $headers['setCommonHeader']();
  $app['http']->response->setStatus(404);
  $app['http']->response->setBody($app['encoder']->encode($body));
  $app['http']->stop();
});
