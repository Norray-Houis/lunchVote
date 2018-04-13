<?php
if (! defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * 权限模型
 */
    
class Role_mdl extends CI_Model
{

    /*
     * 构造函数
     */
    function __construct()
    {
        parent::__construct();
    }
    
    // ---------------------------------------------------------------------------------------------
    
    /*
     * 权限结果集
     */
    public function get_roles($offset = 0, $per_page = 15)
    {
        $app_id = $this->session->userdata('app_id');
        $query = $this->db->select( 'id, name, created_at, updated_at' )
                          ->from( 'role' )
                          ->where( 'app_id',  $app_id)
                          ->order_by( 'id desc' )
                          ->limit($per_page, $offset)
                          ->get();

        return $query->result_array();
    }
    
    
    // ---------------------------------------------------------------------------------------------
    
    /*
     * 加载详细项
     * @param $id int 角色id
     */
    public function load($id)
    {
        if(empty($id)) {
            return array();
        }
        $query = $this->db->select( 'id, name, action_list' )
                          ->from( 'role' )
                          ->where( 'id', $id )
                          ->get();
        
        return $query->row_array();
    }
    
    
    // ---------------------------------------------------------------------------------------------
    
    /*
     * 加载角色选项
     */
    public function roles_option()
    {
        $query = $this->db->select('id, name')
                          ->from('role')
                          ->where('app_id', $this->session->userdata('app_id'))
                          ->get();
        return $query->result_array();
    }
    
    
    // ---------------------------------------------------------------------------------------------
    
    /*
     * 添加角色
     * @param $data array 更新字段数据
     */
    public function create($data)
    {
        $this->db->set($data);
        $this->db->insert('role');
        return $this->db->insert_id();
    }
    
    
    // ---------------------------------------------------------------------------------------------
    
    /*
     * 更新角色
     * @param $data array 更新字段数据
     */
    public function update($id, $data)
    {
        $this->db->set($data);
        $this->db->where('id', $id);
        $this->db->update('role');
        return $this->db->affected_rows();
    }
    
    
    // ---------------------------------------------------------------------------------------------
    
    
    /*
     * 删除角色
     * @param $id int 角色id
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('role');
        return $this->db->affected_rows();
    }
    
    
    // ---------------------------------------------------------------------------------------------
    
    /*
     * 数据总量
     */
    public function total_rows()
    {
        return $this->db->count_all('role');
    }
    
}