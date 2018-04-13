<?php
if (! defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 管理员模型
 */
    
class Admin_user_mdl extends CI_Model
{

	public $name;
	public $email;
	public $password;
	public $role_id;
	public $created_at;
	public $updated_at;
	public $last_login_at;
	public $last_login_ip;
	public $is_first_login;
	public $app_id;

	function __construct()
	{
		parent::__construct();
	}


	// --------------------------------------------------------------------

	/**
	 * 结果集
	 */
	function get_admins($offset = 0, $per_page = 15)
	{
		$this->db->select('u.*, r.name as role_name,a.app_name as app_name');
		$this->db->from('admin_user u');
		$this->db->join('role r', 'u.role_id = r.id', 'left');
		$this->db->join('app_info a', 'a.id = u.app_id', 'left');
		$app_id = $this->session->userdata('app_id');
		if ((int)$app_id !== 0) {
			$this->db->where('u.app_id', $app_id);
		}
		$this->db->order_by('u.id desc');
		$this->db->limit($per_page, $offset);
		$query = $this->db->get();

		return $query->result_array();
	}


	// --------------------------------------------------------------------

	/**
	 * 详细项
	 */
	function load($id)
	{
		if (!$id) {
			return array();
		}

		$query = $this->db->select('id, name, email, role_id, error_times')
			->from('admin_user')
			->where('id', $id)
			->get();

		return $query->row_array();
	}

	// --------------------------------------------------------------------

	/**
	 * 创建
	 */
	function create($data)
	{

		$this->db->set($data);
		$this->db->insert('admin_user');

		return $this->db->insert_id();
	}



	// --------------------------------------------------------------------

	/**
	 * 更新
	 */
	function update($id, $data)
	{
		$this->db->set($data);
		$this->db->where('id', $id);
		return $this->db->update('admin_user');
	}

	// --------------------------------------------------------------------

	/**
	 * 总数
	 */
	function total_rows()
	{
		$app_id = $this->session->userdata('app_id');
		if ((int)$app_id !== 0) {
			$this->db->where('app_id', $app_id);
		}
		return $this->db->count_all_results('admin_user');
	}

	// --------------------------------------------------------------------

	/**
	 * 删除
	 */
	function delete($id)
	{
		$this->db->where('id', $id);

		return $this->db->delete('admin_user');
	}

	// --------------------------------------------------------------------

	/**
	 * 获取最新添加的数据
	 */
	function get_newly_one()
	{
		$this->db->from('admin_user');
		$this->db->order_by("id", "desc");
		$this->db->limit('1');
		$query = $this->db->get();
		return $query->row_array();
	}

	// --------------------------------------------------------------------

	/**
	 * 登陆后获取操作权限
	 */
	function signin()
	{


		$this->db->select('u.*, r.name as role_name, r.action_list as action_list');
		$this->db->from('admin_user u');
		$this->db->join('role r', 'u.role_id = r.id', 'left');

		$this->db->where('u.name', $this->name);

		$this->db->where('u.password', md5($this->password));

		$this->db->where('u.app_id', $this->app_id);

		$this->db->where('r.app_id', $this->app_id);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			return $test = $query->row_array();
		}
//        exit($this->db->last_query());
		return array();
	}

	// --------------------------------------------------------------------

	/**
	 * 获取该用户角色
	 */
	function role_user($role_id)
	{
		if (!$role_id) {
			return array();
		}

		$query = $this->db->get_where('admin_user', array(
			'role_id' => $role_id
		));

		if ($row = $query->row_array()) {
			return $row;
		}

		return array();
	}


	// ---------------------------------------------------------------------------------------------

	/**
	 * 更新用户登录，清除冻结信息
	 */
	function last_update($id)
	{
		$datetime = date('Y-m-d H:i:s');
		$this->db->set('last_login_at', $datetime);
		$this->db->set('last_login_ip', $this->last_login_ip);
		$this->db->set('is_first_login', 1);
		$this->db->set('lock_time', null);
		$this->db->set('error_times', 0);

		$this->db->where('id', $id);
		return $this->db->update('admin_user');
	}


	// ---------------------------------------------------------------------------------------------

	/*
	 * 用户名唯一性验证
	 */
	public function check_name($name)
	{
		$query = $this->db->select('id')
			->from('admin_user')
			->where('name', $name)
			->where('app_id', $this->session->userdata('app_id'))
			->get();
		return $query->row_array();
	}

	/**
	 * 用户冻结
	 *
	 * @param int $user_id
	 * @param bool $the_last
	 * @return mixed
	 */
	public function freezing($user_id = 0, $the_last = false)
	{
		$this->db->set('error_times', 'error_times + 1', false);
		if ($the_last) {
			$this->db->set('lock_time', date('Y-m-d H:i:s'));
		}

		$this->db->where('id', $user_id);
		return $this->db->update('admin_user');
	}
}