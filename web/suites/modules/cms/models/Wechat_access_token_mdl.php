<?php

/**
 * Created by PhpStorm.
 * User: dev_001
 * Date: 18/3/27
 * Time: 下午4:43
 */
class Wechat_access_token_mdl extends CI_Model
{
    private $table = 'wechat_access_token';

    /**
     * @param $appId
     * @return mixed
     * 根据APPID获取微信公众号信息
     */
    public function getRecord($appId){
        $this->db->where('appid',$appId);
        $query = $this->db->get($this->table);
        $result = $query->result_array();
        if(!$result){
            $this->createToken(compact('appId'));
            $this->db->where('appid',$appId);
            $query = $this->db->get($this->table);
            $result = $query->result_array();
        }else{
            $result = $result[0];
        }
        return $result;
    }

    public function createToken($data){
        $this->db->insert($this->table,$data);
    }

    /**
     * @param $appId
     * @param $access_token
     * @param $valid_date
     * 更新access_token到数据库
     */
    public function updateToken($appId,$access_token,$valid_date){
        $data = array(
            "appid" => $appId,
            "access_token" => $access_token,
            "valid_date" => $valid_date,
        );
        $this->db->replace($this->table, $data);
    }
}