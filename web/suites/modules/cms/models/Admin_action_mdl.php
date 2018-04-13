<?php
if (! defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 权限模型
 */
    
class Admin_action_mdl extends CI_Model
{

    /*
     * 构造函数
     */
	function __construct()
    {
        parent::__construct();
    }

    // --------------------------------------------------------------------

    /**
	 * 权限分组
	 *
	 *
	 */
	function get_modules($app_id = 0)
	{
		$this->db->select('a.*');
		$this->db->from('admin_action as a');
		$this->db->where('parent_id',0);
		if($app_id != 0)
		{
			$this->db->join("app_module as b"," b.module_id = a.module_id and b.enabledflag =1 and b.app_id = ".$app_id);
		}
        $query = $this->db->get();
        
        // 把分组id赋予索引
		$rows = array();
        foreach ($query->result_array() as $row){
            $rows[$row['id']] = $row;
        }
        return $rows;
	}

    // --------------------------------------------------------------------

    /**
	 * 权限数据
	 *
	 *
	 */
	function get_actions($app_id = 0)
	{
		$this->db->select('a.*');
		$this->db->from('admin_action as a');
		$this->db->where('parent_id !=',0);
		if($app_id != 0)
		{
			$this->db->join("app_module as b"," b.module_id = a.module_id and b.enabledflag =1 and b.app_id = ".$app_id);
		}
		$this->db->order_by('parent_id asc, module_id asc');
        $query = $this->db->get();

        // 把id赋予索引
		$rows = array();
        foreach ($query->result_array() as $row){
            $rows[$row['id']] = $row;
        }
        return $rows;
	}

	// ---------------------------------------------------------------------------------------------
	
	function getModuleList($roleid)
	{
		$role = $this->db->get_where('role',array('id' => $roleid))->row_array();
		if($role != null && $role["action_list"] != null)
		{
			$actions = explode(',',$role["action_list"]);
			$this->db->select("distinct module_id",false);
			$this->db->from('admin_action');
			$this->db->where_in("action_code",$actions);
			$query = $this->db->get();



			return $query->result_array();

		}
		return array();

	}



}