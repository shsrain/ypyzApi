<?php

/*
 * 这个文件是 youpaiyunzhi 的一部分。
 *
 * (c) shsrain <shsrain@163.com>
 *
 * 对于全版权和许可信息，请查看分发此源代码的许可文件。
 */

use OAuth2\Storage\UserCredentialsInterface;

namespace OAuth2\Storage;

/**
 * 这是一个PasswordCredentials认证实现类。
 *
 * @author shsrain <shsrain@163.com>
 */
class PasswordCredentials implements UserCredentialsInterface
{
  /**
   * Grant access tokens for basic user credentials.
   *
   * Check the supplied username and password for validity.
   *
   * You can also use the $client_id param to do any checks required based
   * on a client, if you need that.
   *
   * Required for OAuth2::GRANT_TYPE_USER_CREDENTIALS.
   *
   * @param $username
   * Username to be check with.
   * @param $password
   * Password to be check with.
   *
   * @return
   * TRUE if the username and password are valid, and FALSE if it isn't.
   * Moreover, if the username and password are valid, and you want to
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.3
   *
   * @ingroup oauth2_section_4
   */
  public function checkUserCredentials($username, $password)
  {
    $db_config = require __DIR__.'/../config/database.php';
    $db_config = $db_config['upihealth2'];
    $pdo = new \PDO($db_config['dsn'],$db_config['username'],$db_config['password']);
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    $pdo->query('SET NAMES '.$db_config['char_set']);

    //$res = $pdo->query("SELECT * FROM user where username='".$username."' and passwd='".md5($password)."'");
    $res = $pdo->query("SELECT * FROM user where username='".$username."'");
    $result_arr = $res->fetchAll(\PDO::FETCH_ASSOC);
    if(isset($result_arr[0]['username']) && (md5($result_arr[0]['passwd'])==$password))
    {
      return true;
    }
    else
    {
      return false;
    }
  }

  /**
   * @return
   * ARRAY the associated "user_id" and optional "scope" values
   * This function MUST return FALSE if the requested user does not exist or is
   * invalid. "scope" is a space-separated list of restricted scopes.
   * @code
   * return array(
   *     "user_id"  => USER_ID,    // REQUIRED user_id to be stored with the authorization code or access token
   *     "scope"    => SCOPE       // OPTIONAL space-separated list of restricted scopes
   * );
   * @endcode
   */
  public function getUserDetails($username)
  {
    if($username != null)
    {
      $user_id = $username;
    }
     return array(
         "user_id"  => $user_id,    // REQUIRED user_id to be stored with the authorization code or access token
         "scope"    => 'all'       // OPTIONAL space-separated list of restricted scopes
     );
  }
}
