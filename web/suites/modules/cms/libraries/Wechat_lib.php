<?php

/**
 * Created by PhpStorm.
 * User: dev_001
 * Date: 18/3/27
 * Time: 下午3:26
 */
class Wechat_lib
{
    private $appId;
    private $secret;
    private $ci;
    public function __construct()
    {
        $this->ci = &get_instance();
        $this->appId = $this->ci->config->item('appId');
        $this->secret = $this->ci->config->item('secret');
        require_once (getcwd().'/suites/config/wxBizDataCrypt.php');
    }

    /**
     * @return mixed
     */
    public function getAccessToken(){
//        exit('x1');
        $this->ci->load->model('wechat_access_token_mdl');
        $record = $this->ci->wechat_access_token_mdl->getRecord($this->appId);

        if(!$record['access_token'] || time() >= $record['valid_date']){
            $ch = curl_init(); //初始化一个CURL对象
            curl_setopt($ch, CURLOPT_URL, "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appId."&secret=".$this->secret);//设置你所需要抓取的URL
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置curl参数，要求结果是否输出到屏幕上，为true的时候是不返回到网页中,假设上面的0换成1的话，那么接下来的$data就需要echo一下。
            $data = json_decode(curl_exec($ch),true);
//            p($data);exit;
            $this->ci->wechat_access_token_mdl->updateToken($this->appId,$data['access_token'],time()+$data['expires_in']);
            $token = $data['access_token'];
        }else{
            $token = $record['access_token'];
        }

        return $token;

    }

    /**
     * @param $code
     * @return mixed
     */
    public function getOpenIdByCode($code){
        $ch = curl_init(); //初始化一个CURL对象
        https://api.weixin.qq.com/sns/jscode2session?appid=wx86279151247b3b1d&secret=83cbd86edbd4433a70c167c5540ca4ac&js_code=003d61yR15mL5a14PDxR1k5RxR1d61yR&grant_type=authorization_code
        curl_setopt($ch, CURLOPT_URL, "https://api.weixin.qq.com/sns/jscode2session?appid=".$this->appId."&secret=".$this->secret."&js_code=".$code."&grant_type=authorization_code");//设置你所需要抓取的URL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置curl参数，要求结果是否输出到屏幕上，为true的时候是不返回到网页中,假设上面的0换成1的话，那么接下来的$data就需要echo一下。
        $data = json_decode(curl_exec($ch),true);
        return $data;
    }


    /**
     * @param code
     * @param iv
     * @param encryptedData
     * 解密encryptedData
     */
    public function decodeEncryptedData($code,$iv,$encryptedData){
        $result = $this->getOpenIdByCode($code);

        if(!isset($result['session_key'])){
            throw  new Exception($result['errmsg']);
        }
        $sessionKey = $result['session_key'];

        $pc = new WXBizDataCrypt($this->appId, $sessionKey);
        $errCode = $pc->decryptData($encryptedData, $iv, $data);
        if($errCode == 0){
            $data =  json_decode($data,true);
        }
        $result = compact('errCode','data');
        return $result;
    }


    /**
     * @param $access
     * @param $data
     * @param $templateId
     */
    public function sendTemplateMsg($access,$data){
        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=".$access;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        //执行命令
        $data = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        //显示获得的数据
        return $data;
    }
}