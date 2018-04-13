<?php

/**
 * Created by PhpStorm.
 * User: Houis
 * Date: 18/3/28
 * Time: 下午3:32
 */
class Wechat_user_mdl extends CI_Model
{
    private $table = 'wechat_user';
    private $openId;
    private $nickName;
    private $userInfo;
    private $head_img;
    private $create_time;

    public function getWechatUserByOpenId($openId){
        $this->db->where('openId',$openId);
        $query = $this->db->get('wechat_user');
        return $query->row_array();
    }

    public function addUser($data){

        if(isset($data['watermark'])) unset($data['watermark']);

        $user['openId'] = $data['openId'];
        $user['nickName'] = $data['nickName'];
        $user['head_img'] = $data['avatarUrl'];
        $user['userInfo'] = json_encode($data);
        $user['create_time'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table,$user);
    }

    /**
     *
     */
    public function getUserList($nickName = FALSE){
        if($nickName){
            $this->db->like('nickName',$nickName);
        }
        $this->db->from($this->table);
        $this->db->order_by('create_time','desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * @param $openId
     * @param $data
     */
    public function updateUser($openId,$data){
        $this->db->where('openId',$openId);
        $this->db->update($this->table,$data);
    }

}