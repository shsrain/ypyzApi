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
 * 这是一个发布一条新的咨询命令类。
 *
 * @author shsrain <shsrain@163.com>
 */
class InquiriesUpdateCommand implements CommandInterface
{

    public $app = null;

    public function __construct($app)
    {
        $this->app = $app;
    }
}

/**
 * 这是一个发布一条新的咨询命令的处理类。
 *
 * @author shsrain <shsrain@163.com>
 */
class InquiriesUpdateCommandHandler extends AbstractCommandHandler
{

    public $command = null;
    public $app = null;

    public function getSupportedCommandClassName()
    {
        return 'InquiriesUpdateCommand';
    }

    protected function execute(CommandInterface $command)
    {
        $this->command = $command;
        $this->app = $command->app;

        $res = $this->isUserExist($this->app['http']->request->params('public_userid'));
        if($res == false)
        {
          $return['request'] = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
          $return['code'] = 30001;
          $return['message'] = '用户不存在。';
          return $return;
        }

        $res = $this->app['pdo']->ypyz_inquiry()->where('inquiry_id',$this->inquiriesUpdate())->fetch();
        $result['inquiry_id']        = $res['inquiry_id'];
        $result['patient_id']        = $res['patient_id'];
        $result['casehistory_id']    = $res['casehistory_id'];
        $result['description']       = $res['description'];
        $result['public_userid']     = $res['public_userid'];
        $result['public_time']       = $res['public_time'];
        $result['is_solve']          = $res['is_solve'];
        $result['end_time']          = $res['end_time'];
        $result['is_reply']          = $res['is_reply'];
        $result['last_reply_time']   = $res['last_reply_time'];
        $result['last_reply_userid'] = $res['last_reply_userid'];
        $picture_arr = explode(",",$res['picture']);
        foreach($picture_arr as $k=>$pic){
          $result['picture_'.$k] = "http://api.didijiankang.cn/api/images/uploads/".$pic;
        }
        $return['datum'] = $result;
        $return['code'] = 200;
        $return['message'] = '操作成功。';
        return $return;
    }

    public function inquiriesUpdate()
    {
        $picture = "";
        foreach($this->app['http']->request->params() as $key=>$value)
        {
          if(preg_match("/^picture_[0-9]$/", $key, $m))
          {
            $picture .= $value.",";
          }
        }
        $picture = substr($picture,0,strlen($picture)-1);

        $this->app['pdo']->ypyz_inquiry()->insert(array(
            "patient_id"        => $this->app['http']->request->params('patient_id'),
            "casehistory_id"    => $this->app['http']->request->params('casehistory_id'),
            "picture"           => $picture,
            "description"       => $this->app['http']->request->params('description'),
            "public_userid"     => $this->app['http']->request->params('public_userid'),
            "public_time"       => time(),
            "is_solve"          => 0,
            "end_time"          => 0,
            "is_reply"          => 0,
            "last_reply_time"   => 0,
            "last_reply_userid" => 0,
        ));
        return $this->app['pdo']->ypyz_inquiry()->insert_id();
    }

    // 检查用户是否存在。
    public function isUserExist( $user_id = null )
    {

      if($user_id != $this->app['access_token_data']['user_id'])
      {
        return false;
      }
      else
      {
        return true;
      }
    }

    // 检查关注者是否存在。
    public function isPatientExist( $patient_id = null )
    {
      return true;
    }

    // 检查电子病历是否存在。
    public function isCasehistoryExist( $casehistory_id = null )
    {
      return true;
    }

    // 检查描述是否为空。
    public function isDescriptionExist( $description = null )
    {
      return true;
    }
}
