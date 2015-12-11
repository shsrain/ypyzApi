<?php

/*
 * 这个文件是 youpaiyunzhi 的一部分。
 *
 * (c) shsrain <shsrain@163.com>
 *
 * 对于全版权和许可信息，请查看分发此源代码的许可文件。
 */

 /**
  * 这是一个助手函数文件。
  *
  * @author shsrain <shsrain@163.com>
  */

  /**
   * encrypt。
   *
   * @author liulin<liulindb@126.com>
   */
 if (! function_exists('encrypt'))
 {
  function encrypt($data,$key,$iv)
  {
      $td = mcrypt_module_open(MCRYPT_3DES, '', MCRYPT_MODE_CBC, '');
      // $iv = base64_decode($iv);
      $data = PaddingPKCS7($data);
      // $key = base64_decode($key);
      mcrypt_generic_init($td, $key, $iv);
      $ret = base64_encode(mcrypt_generic($td, $data));
      mcrypt_generic_deinit($td);
      mcrypt_module_close($td);
      return $ret;
  }
 }

 /**
  * decrypt。
  *
  * @author liulin<liulindb@126.com>
  */
 if (! function_exists('decrypt'))
 {
  /**
   *解密
   * @param <type> $data
   * @return <type>
   */
  function decrypt($data,$key,$iv)
  {
    $td = mcrypt_module_open(MCRYPT_3DES, '', MCRYPT_MODE_CBC, '');
    // $iv = base64_decode(iv);
    // $key = base64_decode(key);
    mcrypt_generic_init($td, $key, $iv);
    $ret = trim(mdecrypt_generic($td, base64_decode($data)));
    $ret = UnPaddingPKCS7($ret);
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);
    return $ret;
  }
 }

 /**
  * PaddingPKCS7。
  *
  * @author liulin<liulindb@126.com>
  */
 if (! function_exists('PaddingPKCS7'))
 {
  function PaddingPKCS7($data)
  {
    $block_size = mcrypt_get_block_size('tripledes', 'cbc');
    $padding_char = $block_size - (strlen($data) % $block_size);
    $data .= str_repeat(chr($padding_char), $padding_char);
    return $data;
  }
 }

 /**
  * UnPaddingPKCS7。
  *
  * @author liulin<liulindb@126.com>
  */
 if (! function_exists('UnPaddingPKCS7'))
 {
  function UnPaddingPKCS7($text)
  {
     $pad = ord($text{strlen($text) - 1});
     if ($pad > strlen($text))
     {
       return false;
     }
     if (strspn($text, chr($pad), strlen($text) - $pad) != $pad)
     {
       return false;
     }
       return substr($text, 0, -1 * $pad);
   }
 }

 /**
  * 短信发送函数。
  *
  * @author liulin<liulindb@126.com>
  */
 if (! function_exists('sx139000'))
 {
   function sx139000($to,$msg,$time=null)
   {
     $name='yunhe';
     $pwd='yunhe123';

     $url="http://www.139000.com/send/gsend.asp?name=$name&pwd=$pwd&dst=$to&msg=$msg&time=$time";
     $fp = fopen($url,"r");
     $ret= fgetss($fp,255);
     return($ret);
     fclose($fp);
   }
 }

 if (! function_exists('set_common_headers'))
 {

 	/**
 	 * set_common_headers
 	 *
 	 * 设置公共响应头。
 	 *
 	 * @param	object
 	 * @return
 	 */
    function set_common_headers($app)
    {
      $app['http']->response->headers->set('Server', 'www.didijiankang.cn');
      $app['http']->response->headers->set('X-Powered-By', 'YouPaiYunZhi');
      $app['http']->response->headers->set('Content-Type', 'application/json;charset=utf-8');
      $app['http']->response->headers->set('Accept', 'application/api.youpaiyunzhi+json; version=1');
    }
 }

// 测试断点开始方法。
if (! function_exists('test_start'))
{
  function test_start($is_start = true){
    if($is_start == true){
        return true;
    }else{
      return false;
    }
  }
}

// 测试断点结束方法。
if (! function_exists('test_stop'))
{
  function test_stop(){
        exit();
  }
}

if (! function_exists('http_log'))
{
  /**
  * 记录HTTP请求原文为日志文件
  * @return string
  */
  function http_log()
  {
    if(!file_exists('./log/http.txt'))
    {
      file_put_contents('./log/http.txt','');
    }
    $content = file_get_contents("./log/http.txt");
    $ret="time：".date('Y-m-d H:i:s',time())."\n";
    $ret.="---start---"."\n";
    $ret .=get_http_raw()."\n";
    $ret.="---end---"."\n\r";
    $content.=$ret;
    file_put_contents('./log/http.txt', $content);
  }
}

if (! function_exists('get_http_raw'))
{
/**
* 获取HTTP请求原文
* @return string
*/
function get_http_raw()
{
  $raw = '';
  // (1) 请求行
  $raw .= $_SERVER['REQUEST_METHOD'].' '.$_SERVER['REQUEST_URI'].' '.$_SERVER['SERVER_PROTOCOL']."\r\n";
  // (2) 请求Headers
  foreach($_SERVER as $key => $value)
  {
    if(substr($key, 0, 5) === 'HTTP_')
    {
      $key = substr($key, 5);
      $key = str_replace('_', '-', $key);

      $raw .= $key.': '.$value."\r\n";
    }
  }
  // (3) 空
  $raw .= "\r\n";
  // (4) 请求Body
  $raw .= file_get_contents('php://input');
  return $raw;
 }
}
