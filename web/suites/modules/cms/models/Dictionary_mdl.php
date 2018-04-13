<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 设备模型
 * @author william
 *
 */
class Dictionary_mdl extends CI_Model
{
    /**
     * 表名
     * @var string
     */
    private $table = 'datadictionary';

    /**
     * 列表
     * @param number $type 1为品牌，2为渠道， 3为批次，0为所有
     * @param number $offset
     * @param number $limit
     */
    public function getList($type = 1, $offset = 0,$limit = 0)
    {
        $this->db->select('*');
        $this->db->from($this->table);
        
        if (! empty($type)) {
            $this->db->where('keytype',$type);
        }
        
        if (! empty($limit) && ! empty($offset)) {
            $this->db->limit( $limit,$offset );
        }

        $query = $this->db->get();
        return $query->result_array();
    }

    public function getChannels($offset,$limit){
        $this->db->select('C.*,P.keyname as parentname');
        $this->db->from('datadictionary as C');
        $this->db->join('datadictionary as P','P.id = C.keyparent','left');
        $this->db->where('C.keytype',2);
        $this->db->limit( $limit,$offset );

        $query = $this->db->get();
        return $query->result_array();
    }



    /*
     * 数据总量
     */
    public function total_rows($type) {
      $this->db->where('keytype',$type);
      return $this->db->count_all($this->table);
    }

    public function load($id){
        if($id<=0){
            return false;
        }
        $query = $this->db->select('*')
            ->from($this->table)
            ->where('id', $id)
            ->get();

        return $query->row_array();

    }

    /*
     * 添加角色
     * @param $data array 更新字段数据
     */
    public function create($data)
    {
        $this->db->set($data);
        $this->db->insert($this->table);
        return $this->db->insert_id();
    }


    /*
     * 更新角色
     * @param $data array 更新字段数据
     */
    public function update($id, $data)
    {
        $this->db->set($data);
        $this->db->where('id', $id);
        $result = $this->db->update($this->table);
        return $result;
    }

    /*
     * 删除角色
     * @param $id int 角色id
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete($this->table);
        return $this->db->affected_rows();
    }

    public function getOther($id,$type){
        if($id<=0||!in_array($type,[1,2])){
            return false;
        }

        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('keytype',$type);
        $this->db->where('id !=', $id);

        $query = $this->db->get();
        return $query->result_array();

    }

    public function getAll($type){
        if(!in_array($type,[1,2])){
            return false;
        }

        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('keytype',$type);

        $query = $this->db->get();
        return $query->result_array();

    }
}