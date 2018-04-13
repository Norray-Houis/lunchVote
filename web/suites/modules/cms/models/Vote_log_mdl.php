<?php

/**
 * Created by PhpStorm.
 * User: dev_001
 * Date: 18/3/28
 * Time: 下午6:00
 */
class Vote_log_mdl extends CI_Model
{
    private $table = "vote_log";

    /**
     * @param $openId
     * @param $voteId
     * @return mixed
     * 通过微信用户openId和投票Id获取用户投票记录
     */
    public function getUserLogById($openId,$voteId){
        $this->db->where('vote_id',$voteId);
        $this->db->where('openId',$openId);
        $query = $this->db->get($this->table);
        return $query->row_array();
    }

    /**
     * @param $openId
     * @param $voteId
     * @param $optionId
     * @return mixed
     * 新增投票记录
     */
    public function addLog($openId,$voteId,$optionId,$scene=0,$formId = ''){
        $data = array(
            "openId" => $openId,
            'vote_id' => $voteId,
            'option_id' => $optionId,
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s'),
            'scene'=>$scene,
            'form_id'=>$formId,
        );

        $this->db->insert($this->table,$data);
        return $this->db->insert_id();
    }

    /**
     * @param $logId
     * @param $optionId
     * 修改用户投票记录
     */
    public function updateLogOption($logId,$optionId,$scene=0){

        $data = array(
            'option_id' => $optionId,
            'update_time' => date('Y-m-d H:i:s'),
            'scene'=>$scene
        );
        $this->db->where('id',$logId);
        $this->db->update($this->table,$data);
    }

    /**
     * @param $logId
     * @param $data
     */
    public function updateLog($logId,$data){
        $this->db->where('id',$logId);
        $this->db->update($this->table,$data);


        $this->db->where('id',$logId);
        $this->db->from($this->table);
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * @param $voteId
     * @return mixed
     */
    public function getLogByVoteId($voteId){
        $this->db->where('vote_id',$voteId);
        $this->db->where('is_del = 0');
        $this->db->from($this->table);
        $query = $this->db->get();
        return $query->result_array();
    }


    public function getLogByVoteIdAndOpenId($openId,$voteId){
        $this->db->select('vl.*,vo.option,vo.addition');
        $this->db->from('vote_log as vl');
        $this->db->join('vote_option as vo','vo.id = vl.option_id','left');
        $this->db->where('vl.vote_id',$voteId);
        $this->db->where('vl.openId',$openId);
        $this->db->where('vl.is_del = 0');
        $query = $this->db->get();
        return $query->row_array();
    }

}