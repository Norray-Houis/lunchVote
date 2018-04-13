<?php

/**
 * Created by PhpStorm.
 * User: Houis
 * Date: 18/3/27
 * Time: 下午6:27
 */
class Wechat extends NLF_Controller
{

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
//        $this->appId = $this->config->item('appId');
//        require_once(getcwd(). '/suites/config/configurations.php');
//        require_once (getcwd().'/suites/config/wxBizDataCrypt.php');
    }

//    /**
//     * @param code
//     * @param iv
//     * @param encryptedData
//     * 解密encryptedData
//     */
//    public function decodeEncryptedData(){
//
////        $code = $this->input->post('code');
////        $iv = $this->input->post('iv');
////        $encryptedData = $this->input->post('encryptedData');
//
//        $code = '003mC0W52wHvjK0TJMY52tV4W52mC0WN';
//        $iv = 'zIDXZV+iaUxGpxghLUP1wg==';
//        $encryptedData = 'aF1Nai4c/Y+cMalXU1AE/c1TKdkmuS09BIMnK1AjgperRKZAK1KXYI09YWWcxCDwFb/YdIdWS5McdoSRV/kWpVMIt/YNC9W0EQcG6AOk9FqSSXSL5lKtzwqEZA7u4EQHieLWvw+2c1GFKQSCXy1XPg+eqJJj5fsp0DIW2KXZubnBZIGtF9iT3wwX/plx9u2wpy77zz1D7mYv9ccttEfkw/r6nqJKiGLa3tF/2NoFWuZd0R2hnWJQ4mIALzF9hRB7pbug3W6hDuvZN5y7UzfBXH/Wm71Aux+gyJWrJ1nx0nux/HxUDs4LddLBD5nu6latnZ4Z5op9YQDy6hmMy1TbeiAN4aD1bCOiwVHRrCMbr7tADWIPRHLIl9Kq2n2hGAW1IdUnPW/o8w6/R7NIyWgjbo3WkLxQfnuyQwa2LDHqUD7cwIpnyALsUJs20osV1RiY7DRObkuOFJkHLjICugMiIy8za+CuiXbM6ygVqx5xgLg=';
//
//        $this->load->library('wechat');
//        $sessionKey = $this->wechat->getAccessToken();
////        $sessionKey = $this->Wechat->getOpenIdByCode($code)['session_key'];
////
////        $pc = new WXBizDataCrypt($this->appId, $sessionKey);
////        $errCode = $pc->decryptData($encryptedData, $iv, $data );
////
////        return $errCode;
//    }

    public function test(){
        $this->load->library('wechat');
        $access = $this->wechat->getAccessToken();
        p($access);
    }


}