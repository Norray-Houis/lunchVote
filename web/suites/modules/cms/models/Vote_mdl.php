<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: Houis
 * Date: 18/3/27
 * Time: 下午12:23
 */
class Vote_mdl extends CI_Model
{
    public $id;
    public $title;
    public $description;
    public $date;
    public $endTime;
    public $option;
    public $addition_option;
    public $status;
    public $create_time;
    public $update_time;


    /**
     * 表名
     * @var string
     */
    private $table = 'vote';

    /**
     * @param $data  投票数据包
     * @return mixed 新增记录的id
     */
    public function create($data){
        $data['create_time'] = $data['update_time'] = date('Y-m-d H:i:s');
        if(isset($data['single_cost']) && is_numeric($data['single_cost']))  $data['single_cost'] = sprintf('%2f',$data['single_cost']);
        $data['status'] = 1;

        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function getListByDesc(){
        $this->db->select('v.*');
        $this->db->from('tbl_vote as v');

        $this->db->order_by('v.date','desc');
        $query = $this->db->get();

//        exit($this->db->last_query());
        return $query->result_array();
    }

    public function getVoteDetailsByDate($date){
        $this->db->where('date',$date);
        $query = $this->db->get($this->table);
        return $query->row_array();
    }



    /**
     * @param $voteId
     * @return mixed
     * 通过投票Id获取记录
     */
    public function getVoteDetailsById($voteId){
        $this->db->where('id',$voteId);

        $query = $this->db->get($this->table);
        return $query->row_array();
    }

    /**
     * @param $voteId
     * @param $data
     */
    public function updateVote($voteId,$data){
        $this->db->where('id',$voteId);
        $this->db->update($this->table,$data);
    }

    /**
     * @param $voteId
     * 获取该投票记录
     */
    public function getVoteLog($voteId){
        //获取用户投票记录
        $this->db->select("vl.create_time,wu.nickname,wu.head_img,vo.*,vl.description,vl.id as logId,wu.openId,wu.department,wu.position,vl.scene");
        $this->db->from('vote_log AS vl');
        $this->db->join("wechat_user as wu", "wu.openId = vl.openId", 'left');
        $this->db->join("vote_option as vo", "vo.id = vl.option_id", 'left');
        $this->db->where('vl.vote_id = '.$voteId);
        $this->db->where('vo.is_del = 0');
        $this->db->where('vl.is_del = 0');
        $query = $this->db->get();
        $userLog = $query->result_array();

        return $userLog;
    }

    public function getVoteLog2($voteId){
        //获取用户投票记录
        $this->db->select("vl.create_time,wu.nickname,wu.head_img,vo.*,vl.description,vl.id as logId,wu.openId,wu.department,wu.position,vl.scene");
        $this->db->from('wechat_user AS wu');
        $this->db->join("vote_log as vl", "wu.openId = vl.openId AND vl.is_del = 0 AND vl.vote_id = ".$voteId, 'left');
        $this->db->join("vote_option as vo", "vo.id = vl.option_id", 'left');
        $query = $this->db->get();
        $userLog = $query->result_array();

        return $userLog;
    }

    public function getVoteStatistics($voteId,$isShow = false){
        $this->db->select('option,sum(num) as total');
        $this->db->from('vote_option');
        $this->db->group_by('option');
        $this->db->where('voteId',$voteId);
        $this->db->where('is_del = 0');
        if($isShow) $this->db->where('is_show',$isShow);
        $query = $this->db->get();
        $optionStatistics = $query->result_array();

        $this->db->select('addition , sum(num) as total');
        $this->db->from('vote_option');
        $this->db->group_by('addition');
        $this->db->where('voteId',$voteId);
        $this->db->where('is_del = 0');
        if($isShow) $this->db->where('is_show',$isShow);
        $query = $this->db->get();
        $additionStatistics = $query->result_array();

        $array = compact('optionStatistics','additionStatistics');
        return $array;
    }


    public function getNesestVote(){
        $this->db->order_by('id','desc');
        $this->db->from($this->table);
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row_array();
    }

}