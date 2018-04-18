<?php

namespace App\Handlers;

/*
 * 类名：ChuanglanSmsApi
 * 功能：创蓝接口请求类
 * 详细：构造创蓝短信接口请求，获取远程HTTP数据
 * 版本：1.3
 */
class ChuanglanSmsApi {

    protected $api_notice_account;  // 普通账号
    protected $api_notice_password;
    protected $api_marketing_account;   // 会员营销账号
    protected $api_marketing_password;
    protected $api_send_url;
    protected $api_balance_query_url;

    public function __construct()
    {
        $this->api_notice_account = config('services.chuanglan.api_notice_account');
        $this->api_notice_password = config('services.chuanglan.api_notice_password');
        $this->api_marketing_account = config('services.chuanglan.api_marketing_account');
        $this->api_marketing_password = config('services.chuanglan.api_marketing_password');
        $this->api_send_url = config('services.chuanglan.api_send_url');
        $this->api_balance_query_url = config('services.chuanglan.api_balance_query_url');
    }


    /* 发送短信
     * @param string $mobile        手机号码
     * @param string $msg           短信内容
     * @param string $needstatus    是否需要状态报告
     */
    public function sendSMS( $mobile, $msg, $needstatus = 'false', $type = 1) {

        if ($type == 1) {
            $account = $this->api_notice_account;
            $password = $this->api_notice_password;
        }
        if ($type == 2) {
            $account = $this->api_marketing_account;
            $password = $this->api_marketing_password;
        }

        //创蓝接口参数
        $postArr = array (
                    'account'  =>  $account,
                    'password' => $password,
                    'msg' => urlencode($msg),
                    'phone' => $mobile,
                    'report' => $needstatus
        );
        
        $result = $this->curlPost( $this->api_send_url , $postArr);

        return $result;
    }

    /** * 查询额度
     *  查询地址 */
    public function queryBalance($type = 1) {

        if ($type == 1) {
            $account =  $account = $this->api_notice_account;
        }
        if ($type == 2) {
            $account =  $account = $this->api_marketing_account;
        }

        //查询参数
        $postArr = array(
            'account' => $account,
            'pswd' => self::API_PASSWORD,
        );

        $result = $this->curlPost($this->api_balance_query_url, $postArr);
        return $result;
    }

    /* 处理返回值  */
    public function execResult($result) {
        $result = json_decode($result,true);
        if(!$result){
            return FALSE;
        }
        //{"time":"20170916170030","msgId":"17091617003023662","errorMsg":"","code":"0"}
        $res[1]=$result['code'];
        $res[2]=$result['msgId'];
        return $res;
    }

    /**
     * 通过CURL发送HTTP请求
     * @param string $url  //请求URL
     * @param array $postFields //请求参数 
     * @return mixed
     */
    private function curlPost($url, $postFields) {
        $postFields = json_encode($postFields);
        $ch = curl_init ();
        curl_setopt( $ch, CURLOPT_URL, $url ); 
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8'
                )
        );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt( $ch, CURLOPT_TIMEOUT,1); 
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0);
		$ret = curl_exec ( $ch );
        if (false == $ret) {
            $result = curl_error(  $ch);
        } else {
            $rsp = curl_getinfo( $ch, CURLINFO_HTTP_CODE);
            if (200 != $rsp) {
                $result = "请求状态 ". $rsp . " " . curl_error($ch);
            } else {
                $result = $ret;
            }
        }
        curl_close ( $ch );
        return $result;
    }

    //魔术获取
    public function __get($name) {

        return $this->$name;

    }

    //魔术设置
    public function __set($name,$value) {

        $this->$name = $value;

    }

}
