<?php
if (! defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 操作日志模型
 */

class Admin_log_mdl extends CI_Model
{

    var $user_id;

	var $log_info;


	function __construct()
    {
        parent::__construct();
    }

	// --------------------------------------------------------------------

    /**
	 * load by id
	 *
	 *
	 */
    public function load($id)
    {
        if (!$id){
            return array();
        }

        $query = $this->db->get_where('admin_log',array('log_id' => $id));

        if ($row = $query->row_array()){
            return $row;
        }

        return array();
    }

	// --------------------------------------------------------------------

    /**
	 * 创建
	 *
	 *
	 */
    public function create($user = 0, $log_info = '')
    {
    	$this->load->helper('date');

        $this->db->set('log_time', now());
		$this->db->set('user_id', $user);
		$this->db->set('log_info', $log_info);
		$this->db->set('ip_address', $this->input->ip_address());

        return $this->db->insert('admin_log');
    }

	// --------------------------------------------------------------------

    /**
	 * 结果集
	 *
	 *
	 */
    public function find_logs($options = array(), $count=20, $offset=0)
	{
		if (!is_array($options)){
            return array();
        }
        // 补充查询字段
        $this->db->select('l.*, u.name');
        
        if ($count){
            $this->db->limit((int)$count, (int)$offset);
        }

        $query = $this->_query_logs($options);

        $rows = array();
        foreach ($query->result_array() as $row){
            $rows[] = $row;
        }
        return $rows;
	}


    // --------------------------------------------------------------------

    /**
	 * 获取最新添加的数据
	 *
	 *
	 */
	public function get_newly_one()
    {
        $this->db->from('admin_log');
        $this->db->order_by("id", "desc");
        $this->db->limit('1');
        $query =  $this->db->get();
        return $query->row_array();
    }

    // --------------------------------------------------------------------

    /**
	 * 私有函数
	 *
	 *
	 */
	public function _query_logs($options = null)
    {
        $this->db->from('admin_log as l');
        $this->db->join('admin_user as u', 'u.id = l.user_id', 'left outer');

		if (!empty($options['conditions'])){
            $this->db->where($options['conditions']);
        }
        
        if(!empty($options['like'])){
            $this->db->like('name', $options['like']);
            $this->db->or_like('log_info', $options['like']);
        }

        if (isset($options['order'])){
            $this->db->order_by($options['order']);
        } else {
            $this->db->order_by('l.id DESC');
        }

        return $this->db->get();
    }

    // ---------------------------------------------------------------------------------------------
    
    /**
	 * 总数
	 *
	 *
	 */
	public function count_logs($options = array())
    {
        $this->db->select('COUNT(DISTINCT(l.id)) as total');

        $query = $this->_query_logs($options);

        $total = 0;
        if ($row = $query->row_array()){
            $total = (int)$row['total'];
        }
        return $total;
    }

}