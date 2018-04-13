<?php

/**
 * Created by PhpStorm.
 * User: dev_001
 * Date: 18/3/28
 * Time: 上午9:50
 */
class Wechat extends NLF_Controller
{

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
        $this->appId = $this->config->item('appId');
//        require_once(getcwd(). '/suites/config/configurations.php');
        require_once (getcwd().'/suites/config/wxBizDataCrypt.php');
    }

    /**
     * @param code
     * @param iv
     * @param encryptedData
     * 解密encryptedData
     */
    private function decodeEncryptedData($code,$iv,$encryptedData){
//        var_dump($_POST);exit;
        $this->load->library('wechat_lib');
        $result = $this->wechat_lib->getOpenIdByCode($code);

        if(!isset($result['session_key'])){
            throw  new Exception($result['errmsg']);
        }
        $sessionKey = $result['session_key'];

        $pc = new WXBizDataCrypt($this->appId, $sessionKey);
        $errCode = $pc->decryptData($encryptedData, $iv, $data);
//        $data = json_decode($data,true);
        if($errCode == 0){
            $data =  json_decode($data,true);
        }
        $result = compact('errCode','data');
        return $result;
//        json_response('0','success',$result);
    }

    public function checkUser(){
        $result = $this->decodeEncryptedData($this->input->post('code'),$this->input->post('iv'),$this->input->post('encryptedData'));
//        json_response('0','success',$result);
        $openId = $result['data']['openId'];
//        $openId = 'oWca84l5bKywKDov6v4OVp2aWGAw';
        $voteAccess = 0;
        //用户检验
        $this->load->model('wechat_user_mdl');
        $user = $this->wechat_user_mdl->getWechatUserByOpenId($openId);
        if(!$user){
            $this->wechat_user_mdl->addUser($result['data']);
        }

        json_response('0','success',compact('openId','voteAccess'));
    }

}