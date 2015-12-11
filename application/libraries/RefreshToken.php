<?php

/*
 * 这个文件是 youpaiyunzhi 的一部分。
 *
 * (c) shsrain <shsrain@163.com>
 *
 * 对于全版权和许可信息，请查看分发此源代码的许可文件。
 */

use OAuth2\Storage\RefreshTokenInterface;

namespace OAuth2\Storage;

/**
 * 这是一个RefreshToken实现类。
 *
 * @author shsrain <shsrain@163.com>
 */
class RefreshToken implements RefreshTokenInterface
{
  /**
   * Grant refresh access tokens.
   *
   * Retrieve the stored data for the given refresh token.
   *
   * Required for OAuth2::GRANT_TYPE_REFRESH_TOKEN.
   *
   * @param $refresh_token
   * Refresh token to be check with.
   *
   * @return
   * An associative array as below, and NULL if the refresh_token is
   * invalid:
   * - refresh_token: Refresh token identifier.
   * - client_id: Client identifier.
   * - user_id: User identifier.
   * - expires: Expiration unix timestamp, or 0 if the token doesn't expire.
   * - scope: (optional) Scope values in space-separated string.
   *
   * @see http://tools.ietf.org/html/rfc6749#section-6
   *
   * @ingroup oauth2_section_6
   */
  public function getRefreshToken($refresh_token)
  {
    $db_config = require __DIR__.'/../config/database.php';
    $db_config = $db_config['oauth2'];
    $pdo = new \PDO($db_config['dsn'],$db_config['username'],$db_config['password']);
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    $pdo->query('SET NAMES '.$db_config['char_set']);

    $res = $pdo->query("SELECT * FROM oauth_refresh_tokens where refresh_token='".$refresh_token."'");
    $result_arr = $res->fetchAll(\PDO::FETCH_ASSOC);
    if($result_arr != null)
    {
      $return = array(
        'refresh_token'=>$result_arr[0]['refresh_token'],
        'client_id'=>$result_arr[0]['client_id'],
        'user_id'=>$result_arr[0]['user_id'],
        'expires'=>strtotime($result_arr[0]['expires']),
        'scope'=>$result_arr[0]['scope']
      );

      return $return;
    }
    else
    {
      return null;
    }
  }

  /**
   * Take the provided refresh token values and store them somewhere.
   *
   * This function should be the storage counterpart to getRefreshToken().
   *
   * If storage fails for some reason, we're not currently checking for
   * any sort of success/failure, so you should bail out of the script
   * and provide a descriptive fail message.
   *
   * Required for OAuth2::GRANT_TYPE_REFRESH_TOKEN.
   *
   * @param $refresh_token
   * Refresh token to be stored.
   * @param $client_id
   * Client identifier to be stored.
   * @param $user_id
   * User identifier to be stored.
   * @param $expires
   * Expiration timestamp to be stored. 0 if the token doesn't expire.
   * @param $scope
   * (optional) Scopes to be stored in space-separated string.
   *
   * @ingroup oauth2_section_6
   */
  public function setRefreshToken($refresh_token, $client_id, $useexitr_id, $expires, $scope = null)
  {
    exit('500 set Refresh Token Error');
  }

  /**
   * Expire a used refresh token.
   *
   * This is not explicitly required in the spec, but is almost implied.
   * After granting a new refresh token, the old one is no longer useful and
   * so should be forcibly expired in the data store so it can't be used again.
   *
   * If storage fails for some reason, we're not currently checking for
   * any sort of success/failure, so you should bail out of the script
   * and provide a descriptive fail message.
   *
   * @param $refresh_token
   * Refresh token to be expirse.
   *
   * @ingroup oauth2_section_6
   */
  public function unsetRefreshToken($refresh_token)
  {
    $db_config = require __DIR__.'/../config/database.php';
    $db_config = $db_config['oauth2'];
    $pdo = new \PDO($db_config['dsn'],$db_config['username'],$db_config['password']);
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    $pdo->query('SET NAMES '.$db_config['char_set']);

    $pdo->query("DELETE FROM oauth_refresh_tokens where refresh_token='".$refresh_token."'");
  }
}
