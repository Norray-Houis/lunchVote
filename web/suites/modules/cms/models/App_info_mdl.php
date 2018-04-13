<?php
/**
 * 应用模型
 *
 *
 */
class App_info_mdl extends CI_Model
{

	function __construct()
    {
        parent::__construct();
    }


    //--------------------------------------------------------
    /**
     * 获取单个公司信息
     * @param unknown $app_id
     */
    function get_app_info($app_id){

    	$query = $this->db->get_where('app_info', array('id' => $app_id));

    	return $query->row_array();
    }


    //--------------------------------------------------------
    /**
     * 获取单个公司信息
     * @param unknown $app_id
     */
    function load($app_id){

    	$query = $this->db->get_where('app_info', array('id' => $app_id));

    	return $query->row_array();
    }

    //--------------------------------------------------------

    /**
     *
     * @param unknown $site_url
     * @return unknown
     */
    function get_app_info_by_url($site_url){
//        echo $site_url;
    	$query = $this->db->get_where('app_info', array('admin_url' => $site_url));

    	$row = $query->row_array();

//    	error_log($this->db->last_query());
//        exit($this->db->last_query());
    	return $row;
    }

    //--------------------------------------------------------

    /**
     *
     */
	function findAll($select='')
	{

		if($select != '')
		{
			$this->db->select($select);
		}
		$query = $this->db->from('app_info');

    	return $query->get()->result_array();
	}

    //--------------------------------------------------------

	/**
	 *
	 * @param unknown $data
	 */
	function create($data)
    {
		$datetime = date('Y-m-d H:i:s');
        $this->db->insert('app_info',$data);
        return $this->db->insert_id();
    }

    //--------------------------------------------------------
	/**
	 *
	 * @param unknown $id
	 * @param unknown $data
	 */
	function update($id,$data)
    {
        $this->db->where('id', $id);
        return $this->db->update('app_info',$data);
    }

    //--------------------------------------------------------
    /**
     *
     * @param unknown $id
     */
    function delete($id){
    	$this->db->where('id', $id);
    	return $this->db->delete('app_info');
    }

    //--------------------------------------------------------

    /**
     * 权限列表
     * @param number $id
     */
    function list_permit($id = 0){
    	$this->db->select('s.*, m.id_app as app_id, m.enabledflag as enabledflag, m.sequence as seq');
    	$this->db->from('system_module s');
    	$this->db->join('app_module m', 'm.id_module = s.id and m.id_app = '.$id, 'left');
    	$this->db->order_by('s.id', 'ASC');
		$query = $this->db->get();
    	$rows = $query->result_array();
    	return $rows;
    }


    //--------------------------------------------------------
    /**
     * 设置权限
     * @param unknown $app_id
     * @param unknown $modlue_id
     * @param string $is_set
     * @return boolean
     */
    function set_permit($app_id, $modlue_id, $is_set = true)
    {

    	$query = $this->db->get_where('app_module', array('id_app' => $app_id, 'id_module' => $modlue_id));
    	error_log($is_set);
    	if ($query->num_rows() > 0) {
    		if($is_set == 'false'){
    			$this->db->delete('app_module', array('id_app' => $app_id, 'id_module' => $modlue_id));
    		}
    	}else{
    		if($is_set == 'true'){
    			$this->db->insert('app_module', array('id_app' => $app_id, 'id_module' => $modlue_id));
    		}
    	}
    	//error_log($this->db->last_query());

    	return true;
    }

    //--------------------------------------------------------

    /**
	 * 更新单个公司的access_token
	 * @param unknown $app_id
	 * @param unknown $token
	 * @param unknown $timestamp
	 */
    function update_access_token($app_id, $token, $timestamp){
    	$this->db->where('id', $app_id);
    	$this->db->update('app_info', array('wechat_access_token' => $token, 'wechat_token_timestamp' => $timestamp));
    }
}