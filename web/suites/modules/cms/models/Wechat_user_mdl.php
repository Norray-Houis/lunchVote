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
    public function getUserList($nickName = FALSE,$pageinfo = FALSE){
        if($nickName){
            $this->db->like('nickName',$nickName);
        }
        $this->db->from($this->table);
        $this->db->order_by('create_time','desc');



        $query = $this->db->get();


        $data['data'] = $query->result_array();

        return $data;
    }

    /**
     * @param $openId
     * @param $data
     */
    public function updateUser($openId,$data){
        $this->db->where('openId',$openId);
        $this->db->update($this->table,$data);
    }

    /**
     * @param array $params
     * @param array $pages
     * @return mixed
     * 获取用户列表
     */
    public function getListing($params = [],$pages = []){

        $this->db->from($this->table);
        $this->db->order_by('create_time','desc');

        $this->query_cond($params);
        if (isset($pages['limit'], $pages['offset'])) {
            $this->db->limit($pages['limit'], $pages['offset']);
        }
        $query = $this->db->get();
//        exit($this->db->last_query());
        return $query->result_array();
    }

    /**
     * @param $params
     * 查询条件
     */
    private function query_cond($params){
        foreach ($params as $k => $v){
            switch ($k){
                case 'where':
                        if(isset($v['nickName']))   $this->db->where('nickName',$v['nameName']);
                    break;
                case 'like':
                        if(isset($v['nickName']))   $this->db->like('nickName',$v['nameName']);
                    break;
            }
        }
    }


    public function total_row($params){
        $this->query_cond($params);
        $this->db->select('count(openId) as c');
        $this->db->from($this->table);
        $query = $this->db->get();

        $result = 0;
        if ($rows = $query->row_array()) {
            $result = $rows['c'];
        }

        return $result;
    }
}