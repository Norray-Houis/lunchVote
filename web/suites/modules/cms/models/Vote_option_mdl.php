<?php

/**
 * Created by PhpStorm.
 * User: dev_001
 * Date: 18/3/27
 * Time: 下午2:18
 */
class Vote_option_mdl extends CI_Model
{
    public $id;
    public $voteId;
    public $option;
    public $num;
    public $addition;
    public $addition_option;
    public $create_time;

    private $table = 'vote_option';

    public function addOption($data){

        foreach ($data as $v){
            $v['create_time'] = date('Y-m-d H:i:s');
            $this->db->insert($this->table, $v);
        }
        return 1;
    }

    public function addOneOption($data){
        $this->db->insert($this->table, $data);
        return $this->db->insert_id;
    }

    /**
     * @param $voteId 投票Id
     * @return mixed
     * 根据投票Id获取所有的选项
     */
    public function getOpptionByVoteId($voteId,$isShow=FALSE){
        $this->db->where('voteId',$voteId);
        if($isShow){
            $this->db->where('is_show',$isShow);
        }
        $this->db->where('is_del = 0');
        $query = $this->db->get($this->table);
//        exit($this->db->last_query());
        return $query->result_array();
    }

    /**
     * @param $optionId
     * @return mixed
     * 根据选项Id获取选项信息
     */
    public function getOpptionById($optionId){
        $this->db->where('id',$optionId);
        $query = $this->db->get($this->table);
        return $query->row_array();
    }

    /**
     * @param $optionId
     * @param $num
     * 更新
     */
    public function updateOpptionNum($optionId,$num){
        $this->db->set('num', $num);
        $this->db->where('id',$optionId);
        $this->db->update($this->table);
    }

    /**
     * @param $optionId
     * @return mixed
     * 选项Num+1
     */
    public function optionNumAddNum($optionId){
        $this->db->where('id',$optionId);
        $query = $this->db->get($this->table);
        $option = $query->row_array();

        $option['num']++;
        $this->db->replace($this->table,$option);
        return $option;
    }

    /**
     * @param $optionId
     * @return mixed
     * 选项Num-1
     */
    public function optionNumRedict($optionId){
        $this->db->where('id',$optionId);
        $query = $this->db->get($this->table);
        $option = $query->row_array();
        $option['num']--;
        $this->db->replace($this->table,$option);
        return $option;
    }

    /**
     * @param $optionId
     * @param $data
     * 更新选项
     */
    public function updateOption($optionId,$data){
        $this->db->where('id',$optionId);
        $this->db->update($this->table,$data);

        $option = $this->getOpptionById($optionId);
        return $option;
    }


    public function getVoteAllOptionByVoteId($voteId){
        $this->db->select("v1.*,v2.addition_option AS old");
        $this->db->from("vote_option AS v1");
        $this->db->join("vote_option AS v2",'v1.is_replace = v2.id','left');

        $this->db->where('v1.voteId = '.$voteId);
        $query = $this->db->get();
        return $query->result_array();
    }
}