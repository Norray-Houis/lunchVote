<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Log_mdl
 * 日志
 */
class Log_mdl extends CI_Model
{
	/**
	 * 表名
	 * @var string
	 */
	private $table = 'log';

	/**
	 * 日志列表
	 * @param array $params
	 * @param array $pages
	 * @return mixed
	 */
	public function listing($params = [], $pages = [])
	{
		$this->db->select('l.*, a.name');
		$this->db->from("{$this->table} as l");
		$this->db->join("admin_user as a", "l.user_id = a.id", 'left');

		$this->query_cond($params);

		if (isset($pages['limit'], $pages['offset'])) {
			$this->db->limit($pages['limit'], $pages['offset']);
		}
		$this->db->order_by('id', 'desc');

		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * 日志总数
	 * @param array $params
	 * @return int
	 */
	public function total_rows($params = [])
	{
		$this->db->select('count(l.id) as c');
		$this->db->from("{$this->table} as l");
		$this->db->join("admin_user as a", "l.user_id = a.id", 'left');

		$this->query_cond($params);

		$query = $this->db->get();

		$result = 0;
		if ($rows = $query->row_array()) {
			$result = $rows['c'];
		}

		return $result;
	}

	/**
	 * 查询条件
	 * @param array $params
	 */
	private function query_cond($params = [])
	{
		if (isset($params['start_time'])) {
			$this->db->where('l.log_time >=', $params['start_time']);
		}

		if (isset($params['end_time'])) {
			$this->db->where('l.log_time <=', $params['end_time']);
		}

		if (isset($params['user'])) {
			$this->db->where('a.name', $params['user']);
		}
	}

	/**
	 * 日志记录
	 * @param array $set
	 * @return mixed
	 */
	public function create($set = [])
	{
		if (! empty($set)) {
			$this->db->set($set);
			return $this->db->insert($this->table);
		}
	}
}