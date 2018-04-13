<?php
/*
 * 系统模块模型
 */
class System_module_mdl extends CI_Model {
	function __construct() {
		parent::__construct ();
	}
	
	// ---------------------------------------------------------------------------------------------

	/**
	 * 获取单节列表
	 *
	 * @param $id 父类ID
	 * @param $app_id 应用ID
	 */
	function get_list($id = 0, $app_id = 0) {
		if ($app_id == 0) {
			return NULL;
		} else {
			$this->db->select ( 'system_module.*, app_module.app_id, app_module.enabledflag, app_module.sequence' );
			$this->db->from ( 'app_module' );
			$this->db->join ( 'system_module', 'app_module.module_id = system_module.id', 'left' );
			$this->db->where ( array (
					'pid' => $id,
					'app_id' => $app_id
			) );
			$this->db->order_by ( 'sequence', 'asc' );
			$query = $this->db->get ();
			return $query->result_array ();
		}
	}
	
	// ---------------------------------------------------------------------------------------------

	/**
	 * 获取非默认列表
	 *
	 * @param $id 父类ID
	 * @param $app_id 应用ID
	 */
	function get_no_default_list($id = 0, $app_id = 0) {
		if ($app_id == 0) {
			return NULL;
		} else {
			$this->db->select ( 'system_module.*, app_module.app_id, app_module.enabledflag, app_module.sequence' );
			$this->db->from ( 'app_module' );
			$this->db->join ( 'system_module', 'app_module.module_id = system_module.id', 'left' );
			$this->db->where ( array (
					'pid' => $id,
					'app_id' => $app_id,
					'default_content = ' => 0
			) );
			$this->db->order_by ( 'sequence', 'asc' );
			$query = $this->db->get ();
			return $query->result_array ();
		}
	}
	
	// ---------------------------------------------------------------------------------------------

	/**
	 * 获取默认列表
	 *
	 * @param $id 父类ID
	 * @param $app_id 应用ID
	 */
	function get_default_list($id = 0, $app_id = 0) {
		if ($app_id == 0) {
			return NULL;
		} else {
			$this->db->select ( 'system_module.*, app_module.app_id, app_module.enabledflag, app_module.sequence' );
			$this->db->from ( 'app_module' );
			$this->db->join ( 'system_module', 'app_module.module_id = system_module.id', 'left' );
			$this->db->where ( array (
					'pid' => $id,
					'app_id' => $app_id,
					'default_content > ' => 0
			) );
			$this->db->order_by ( 'sequence', 'asc' );
			$query = $this->db->get ();
			return $query->result_array ();
		}
	}

	
	// ---------------------------------------------------------------------------------------------
	
	/**
	 * 获取全部列表
	 *
	 * @param $app_id 应用ID
	 */
	function get_all_list($app_id = 0) {
		if ($app_id === 0) {
			return NULL;
		} else {
			$this->db->select ( 'system_module.*, app_module.app_id, app_module.enabledflag, app_module.sequence' );
			$this->db->from ( 'app_module' );
			$this->db->join ( 'system_module', 'app_module.module_id = system_module.id' );
			$this->db->where ( array (
					'app_module.app_id' => $app_id,
					"app_module.enabledflag" => 1
			) );
			$this->db->order_by ( 'app_module.sequence asc, system_module.id asc' );
			$query = $this->db->get ();
            
//            echo $this->db->last_query();exit;
			return $query->result_array ();
		}
	}

	
	// ---------------------------------------------------------------------------------------------
	
	/**
	 * 获取父类ID
	 */
	function get_parent($id) {
		$this->db->select ( 'a.*' );
		$this->db->from ( 'system_module a' );
		$this->db->join ( 'system_module b', 'a.id = b.pid', 'left' );
		$this->db->where ( 'b.id', $id );
		$query = $this->db->get ();
		return $query->row_array ();
	}
	function getById($id) {
		$query = $this->db->get_where ( 'system_module', array (
				'id' => $id
		) );
		return $query->row_array ();
	}
}

?>