<?php

/*
 * 这个文件是 youpaiyunzhi 的一部分。
 *
 * (c) shsrain <shsrain@163.com>
 *
 * 对于全版权和许可信息，请查看分发此源代码的许可文件。
 */

 /**
  * 这是一个路由中间件匿名函数数组。
  *
  * @author shsrain <shsrain@163.com>
  */

$middleware = array(

  /**
	 * oauth2
	 *
	 * oauth2授权中间件。
	 *
	 * @param	string
	 * @param	string
	 * @param	init
	 * @return	Response
	 */
  'oauth2' => function() use ($app)
  {
    return function () use ($app)
    {
      if($app['http']->request->get('access_token')==null)
      {
        $body['request'] = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
        $body['code'] = 20010;
        $body['message'] = 'Access token null.';
        $body = $app['encoder']->encode($body);

        set_common_headers($app);
        $app['http']->response->setStatus(200);
        $app['http']->response->setBody($body);
        $app['http']->stop();
      }
      if (!$app['oauth2']->verifyResourceRequest(OAuth2\Request::createFromGlobals()))
      {
        $body['request'] = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
        $body['code'] = 20011;
        $body['message'] = 'Access token invalid';
        $body = $app['encoder']->encode($body);

        set_common_headers($app);
        $app['http']->response->setStatus(200);
        $app['http']->response->setBody($body);
        $app['http']->stop();
      }
      else
      {
        $app['access_token_data'] = $app['oauth2']->getAccessTokenData(OAuth2\Request::createFromGlobals());
      }
    };
  },

  /**
	 * clientCheck
	 *
	 * 应用client_id验证中间件。
	 *
	 * @param	string
	 * @param	string
	 * @param	init
	 * @return	Response
	 */
  'clientCheck' => function() use ($app)
  {
    return function () use ($app)
    {
      if($app['http']->request->get('client_id')==null)
      {
        $body['request'] = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
        $body['code'] = 20013;
        $body['message'] = 'Client id null.';
        $body = $app['encoder']->encode($body);

        set_common_headers($app);
        $app['http']->response->setStatus(200);
        $app['http']->response->setBody($body);
        $app['http']->stop();
      }
      else
      {
        $db_config = require __DIR__.'/../config/database.php';
        $db_config = $db_config['oauth2'];
        $pdo = new \PDO($db_config['dsn'],$db_config['username'],$db_config['password']);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->query('SET NAMES '.$db_config['char_set']);

        $res = $pdo->query("SELECT * FROM oauth_clients where client_id='".$app['http']->request->get('client_id')."'");
        $result_arr = $res->fetchAll(\PDO::FETCH_ASSOC);
        if($result_arr == null)
        {
          $body['request'] = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
          $body['code'] = 20014;
          $body['message'] = 'Client id invalid.';
          $body = $app['encoder']->encode($body);

          set_common_headers($app);
          $app['http']->response->setStatus(200);
          $app['http']->response->setBody($body);
          $app['http']->stop();
        }
      }

    };
  },

  /**
	 * parseParam
	 *
	 * GET请求参数验证中间件。
	 *
	 * @param	string
	 * @param	string
	 * @param	init
	 * @return	Response
	 */
  'parseParam' => function($param) use ($app)
  {
    return function () use ($param,$app)
    {
      if($app['http']->request->get($param) == null)
      {
        $body['request'] = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
        $body['code'] = 20006;
        $body['message'] = '缺少必要的参数 ('.$param.')。';
        $body = $app['encoder']->encode($body);

        set_common_headers($app);
        $app['http']->response->setStatus(200);
        $app['http']->response->setBody($body);
        $app['http']->stop();
      }
    };
  },

  /**
	 * jsonDecode
	 *
	 * JSON解码验证中间件。
	 *
	 * @param	string
	 * @param	string
	 * @param	init
	 * @return	Response
	 */
  'jsonDecode' => function ($json) use ($app)
  {
    return function () use ( $json,$app )
    {
      try
      {
        $json_request = $app['decoder']->decode($json);
      }
      catch( Webmozart\Json\DecodingFailedException $e)
      {
        $body['request'] = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
        $body['code'] = 20015;
        $body['message'] = 'Json不存在或者解析出现错误。';
        $body = $app['encoder']->encode($body);

        set_common_headers($app);
        $app['http']->response->setStatus(200);
        $app['http']->response->setBody($body);
        $app['http']->stop();
      }
    };
  },

  /**
	 * jsonSchema
	 *
	 * JSON模式验证中间件。
	 *
	 * @param	string
	 * @param	string
	 * @param	init
	 * @return	Response
	 */
  'jsonSchema' => function ($json,$schema) use ($app)
  {
    return function () use ( $json,$schema,$app )
    {
      try
      {
        $json_request = $app['decoder']->decode($json);
      }
      catch( Webmozart\Json\DecodingFailedException $e)
      {
        $body['request'] = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
        $body['code'] = 20015;
        $body['message'] = 'Json不存在或者解析出现错误。';
        $body = $app['encoder']->encode($body);

        set_common_headers($app);
        $app['http']->response->setStatus(200);
        $app['http']->response->setBody($body);
        $app['http']->stop();
      }

      $errors = $app['JsonValidator']->validate($json_request, $schema);
      if (count($errors) > 0)
      {
        $body['request'] = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
        $body['data'] = $errors;
        $body['code'] = 20016;
        $body['message'] = 'JSON Schemas 验证失败。';
        $body = $app['encoder']->encode($body);

        set_common_headers($app);
        $app['http']->response->setStatus(200);
        $app['http']->response->setBody($body);
        $app['http']->stop();
      }
    };
  },

  /**
	 * oauth
	 *
	 * oauth认证中间件。
	 *
	 * @return	Response
	 */
  'oauth' => function() use ($app)
  {
    return function () use ($app)
    {
      // 连接认证数据库
      $db_config = require __DIR__.'/../config/database.php';
      $db_config = $db_config['upihealth2'];
      try {
      $pdo = new \PDO($db_config['dsn'],$db_config['username'],$db_config['password']);
      $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
      $pdo->query('SET NAMES '.$db_config['char_set']);

      // 认证参数检查
      if(isset($_REQUEST['loginid']) == false)
      {
        $body['request'] = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
        $body['code'] = 20006;
        $body['message'] = '缺少必要的参数 (loginid)。';
        $body = $app['encoder']->encode($body);

        set_common_headers($app);
        $app['http']->response->setStatus(200);
        $app['http']->response->setBody($body);
        $app['http']->stop();
      }
      if(isset($_REQUEST['accesstoken']) == false)
      {
        $body['request'] = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
        $body['code'] = 20006;
        $body['message'] = '缺少必要的参数 (accesstoken)。';
        $body = $app['encoder']->encode($body);

        set_common_headers($app);
        $app['http']->response->setStatus(200);
        $app['http']->response->setBody($body);
        $app['http']->stop();
      }

      // 认证用户
      $username=$_REQUEST['loginid'];
      $request_token = $_REQUEST['accesstoken'];

      $key=substr(md5($username.'@yh'),0,24);
      $iv="12345678";
      $accesstoken=decrypt($request_token,$key,$iv);

      $res = $pdo->query("SELECT username FROM user WHERE username='".$username."' AND accesstoken='".$accesstoken."'");
      $result_arr = $res->fetchAll(\PDO::FETCH_ASSOC);
      if($result_arr == null){

          $arr['code']=1001;
          $arr['msg']='身份认证错误，请重新登录。';
          exit(json_encode($arr));
      }
      else
      {
        $app['access_token_data'] = array('user_id'=>strval($username));
      }
      } catch (PDOException $e) {
        $arr['code']=1111;
        $arr['msg']='数据库连接错误。';
        exit(json_encode($arr));
      }
    };
  },
  /**
	 * oauth_test
	 *
	 * oauth认证中间件。
	 *
	 * @return	Response
	 */
  'oauth_test' => function() use ($app)
  {
    return function () use ($app)
    {
      // 连接认证数据库
      $db_config = require __DIR__.'/../config/database.php';
      $db_config = $db_config['upihealth2'];
      $pdo = new \PDO($db_config['dsn'],$db_config['username'],$db_config['password']);
      $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
      $pdo->query('SET NAMES '.$db_config['char_set']);

      // 认证参数检查
      if(isset($_REQUEST['loginid']) == false)
      {
        $body['request'] = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
        $body['code'] = 20006;
        $body['message'] = '缺少必要的参数 (loginid)。';
        $body = $app['encoder']->encode($body);

        set_common_headers($app);
        $app['http']->response->setStatus(200);
        $app['http']->response->setBody($body);
        $app['http']->stop();
      }
      if(isset($_REQUEST['accesstoken']) == false)
      {
        $body['request'] = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
        $body['code'] = 20006;
        $body['message'] = '缺少必要的参数 (accesstoken)。';
        $body = $app['encoder']->encode($body);

        set_common_headers($app);
        $app['http']->response->setStatus(200);
        $app['http']->response->setBody($body);
        $app['http']->stop();
      }

      // 认证用户
      $username=$_REQUEST['loginid'];
      if(test_start(true))
      {
        $username =$_REQUEST['loginid'];
      }
      $request_token = $_REQUEST['accesstoken'];

      $key=substr(md5($username.'@yh'),0,24);
      $iv="12345678";
      $accesstoken=decrypt($request_token,$key,$iv);

      $res = $pdo->query("SELECT username FROM user WHERE username='".$username."' AND accesstoken='".$accesstoken."'");
      $result_arr = $res->fetchAll(\PDO::FETCH_ASSOC);
      if($result_arr == null){
        if(test_start(true))
        {
          $app['access_token_data'] = array('user_id'=>strval($username));
          return;
        }
          $arr['code']=1001;
          $arr['msg']='身份认证错误，请重新登录。';
          exit(json_encode($arr));
      }
      else
      {
        $app['access_token_data'] = array('user_id'=>strval($username));
      }
    };
  },

);

return $middleware;
