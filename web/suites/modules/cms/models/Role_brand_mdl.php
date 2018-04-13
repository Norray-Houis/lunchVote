<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 角色品牌模型
 * @author william
 *
 */
class Role_brand_mdl extends CI_Model
{
    /**
     *
     * @var string
     */
    private $table = 'role_brand';

    /**
     *
     * @param number $test
     */
    public function brands()
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('roleid', $this->session->userdata('account_info')['role_id']);
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     */
    public function getBrandid($id)
    {
        $this->db->select('brandid');
        $this->db->from($this->table);
        $this->db->where('roleid', $id);
        $query = $this->db->get();

        return $query->result_array();
    }

    /**
     */
    public function getBrands($id)
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('roleid', $id);
        $query = $this->db->get();

        return $query->result_array();
    }


    /**
     */
    public function insert($arr)
    {
        if(empty($arr)){
            return false;
        }
        $result = $this->db->insert_batch($this->table,$arr);

        return $result;
    }

    /**
     */
    public function deleteRows($where)
    {
        $this->db->where($where);
        $result = $this->db->delete($this->table);

        return $result;
    }

    /**
     * 列表
     */
    public function getRoleBrands($offset=0,$limit=0)
    {
        $this->db->select('datadictionary.*');
        $this->db->from('role_brand as rb');
        $this->db->join('datadictionary',' rb.brandid = datadictionary.id','left');
        $this->db->where('rb.roleid',$this->session->userdata('account_info')['role_id']);
        if(!empty($limit)){
            $this->db->limit( $limit,$offset );
        }

        $query = $this->db->get();
        return $query->result_array();
    }

}