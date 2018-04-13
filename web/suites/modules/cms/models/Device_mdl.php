<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 设备模型
 * @author william
 *
 */
class Device_mdl extends CI_Model
{
    /**
     * 表名
     * @var string
     */
    private $table = 'device';
    
    /**
     * 设备列表
     * @param unknown $params
     * @params int $device_status
     * @param array $pages
     */
    public function listing($params, $device_status, $pages = [])
    {
        $this->db->select("d.*, dd.keyname as brand, de.keyname as channel, df.keyname as batch, r.region_name as province, ra.region_name as city");
        $this->db->from("{$this->table} as d");
//         $this->db->join('activity as a', 'd.activityid = a.id', 'left');
//         $this->db->join('datadictionary as dd', 'dd.id = a.brand and dd.keytype = 1', 'left');
        $this->db->join('datadictionary as dd', 'dd.id = d.brand and dd.keytype = 1', 'left');
        $this->db->join('datadictionary as de', 'de.id = d.customerid and de.keytype = 2', 'left');
        $this->db->join('datadictionary as df', 'df.id = d.batch and df.keytype = 3', 'left');
        $this->db->join('region as r', 'r.region_id = d.provinceid', 'left');
        $this->db->join('region as ra', 'ra.region_id = d.cityid', 'left');
        $this->db->order_by('d.id desc');
        
        if (empty($pages)) {
//             $this->db->limit(10);
        } else {
            $this->db->limit($pages['limit'], $pages['offset']);
        }
        
        if ($device_status !== '') {
            $this->db->where('d.device_status', $device_status);
        }
        
        $this->listing_query($params);
        
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     * 数据量
     * @param unknown $params
     * @param unknown $device_status
     */
    public function total_rows($params, $device_status)
    {
        $this->db->select('count(*) as c');
        $this->db->from("{$this->table} as d");
        
        $this->listing_query($params);
        
        if ($device_status !== '') {
            $this->db->where('d.device_status', $device_status);
        }
        
        $query = $this->db->get();
        return $query->row_array()['c'];
    }
    
    /**
     * 设备健康状态
     * @param unknown $params
     */
    public function get_device_status($params){
        $this->db->select("count(d.id) as sum_num ,d.status");
        $this->db->from('device as d');
        $this->db->group_by('status');

        $this->report_query($params);
        
        return $this->db->get()->result_array();
    }
    
    /**
     * 设备状态城市分布
     * @param unknown $params
     */
    public function get_device_city_status($params)
    {
//         $this->db->select("count(d.id) as sum_num ,r.region_name as cityid, d.status");
//         $this->db->from('device as d');
//         $this->db->join('region as r', 'd.cityid = r.region_id', 'left');
//         $this->db->group_by('r.region_name, d.status');
//         $this->db->order_by('r.region_name');
        
        $this->db->select('count(d.id) as sum_num, r.region_name as city, cityid, d.address, d.status');
        $this->db->from("{$this->table} as d");
        $this->db->join('region as r', 'd.cityid = r.region_id', 'left');
        $this->db->group_by('d.address, d.status, d.cityid, r.region_name');
        $this->db->order_by('r.region_name');

        $this->report_query($params);
    
        return $this->db->get()->result_array();
    }
    
    /**
     * 报表查询
     * @param unknown $params
     */
    private function report_query($params)
    {
        if ($params['city']) {
            $city = implode(',', $params['city']);
            $this->db->where("d.cityid in ({$city})");
        }
        
        if ($params['brand']) {
            $brand = implode(',', $params['brand']);
//            $this->db->join('activity as a','a.id = d.activityid','inner');
            $this->db->where("d.brand in ($brand)");
        }
        
        if ($params['channel']) {
            $channel = implode(',', $params['channel']);
            $this->db->where("d.customerid in ({$channel})");
        }
        
        if ($params['activity']) {
            $this->db->where('d.activityid', $params['activity']);
        }
        
        if ($params['deviceid']) {
            $this->db->where('d.deviceid', $params['deviceid']);
        }
        
        if ($params['shop']) {
            $this->db->where_in('d.shop', $params['shop']);
        }
    }
    
    /**
     * 列表查询
     * @param unknown $params
     */
    private function listing_query($params)
    {
        if (isset($params['brand'])) {
            $this->db->where_in('d.brand', $params['brand']);
        }
        
        if (isset($params['channel'])) {
            $this->db->where_in('d.customerid', $params['channel']);
        }
        
        if (isset($params['batch'])) {
            $this->db->where_in('d.batch', $params['batch']);
        }
        
        if (isset($params['status'])) {
            $this->db->where_in('d.status', $params['status']);
        }
        
        if (isset($params['keyword'])) {
            $this->db->like('d.devicename', $params['keyword']);
        }
    }
    
    /**
     * 设备信息保存
     * @param array $set
     */
    public function save($set = [])
    {
        if (empty($set)) {
            return false;
        }
        
        return $this->db->insert_batch($this->table, $set);
    }

	/**
	 * 设备批量更新
	 * @param array $set
	 * @param string $index
	 * @return bool
	 */
    public function ud_batch($set = [], $index= '')
    {
    	$res = false;

        if (is_array($set) && is_string($index) && ($indexs = array_column($set, $index))) {
        	$fields = array_keys($set[0]);

			foreach ($set as $group) {
				$new_set[$group[$index]] = $group;
			}

			$sql = "UPDATE `pg_device` SET ";
			foreach ($fields as $field) {
				if ($field != $index) {

					$sql .= "`{$field}` = CASE ";
					foreach ($indexs as $index_value) {
						$sql .= "WHEN {$index} = '". $index_value. "' THEN '". $new_set[$index_value][$field]. "' ";
					}
					$sql .= "ELSE `{$field}` END" . ($field == end($fields) ? " " : ", ");

				}
			}
			$sql .= "WHERE `{$index}` IN ('". (implode("', '", $indexs)). "')";

			$res = $this->db->query($sql);
		}

		return $res;
    }
    
    /**
     * 设备更新
     * @param unknown $id
     * @param array $set
     */
    public function update($id = 0, $set = [])
    {
        $this->db->where('id', $id);
        $this->db->set($set);
        $this->db->update($this->table);
        
        return $this->db->affected_rows();
    }
    
    /**
     * 通过设备id获取设备信息
     * @param number $deviceid
     * @return unknown
     */
    public function load($deviceid = 0)
    {
        $this->db->select('deviceid');
        $this->db->from($this->table);
        
        if (! empty($deviceid)) {
            $this->db->where('deviceid', $deviceid);
            $this->db->or_where('imei', $deviceid);
        }
        
        $query = $this->db->get();
        
        return $query->result_array();
    }
    
    /**
     * 设备详情
     * @param number $id
     */
    public function load_detail_by_id($id = 0)
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('id', $id);
        
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     * 删除设备
     * @param number $id
     */
    public function delete($id = 0)
    {
        return $this->db->delete($this->table, ['id' => $id]);
    }
    
    /**
     * 设备健康状态
     * @param unknown $params
     */
    public function get_devices_by_activityid($activityid)
    {
        $this->db->select("id");
        $this->db->from('device as d');
        $this->db->where('activityid',$activityid);
        
        return $this->db->get()->result_array();
    }
    
    /**
     * 修改设备活动id
     * @param number $batch
     * @param number $activity_id
     */
    public function update_activityid_by_device_batch($batch = 0, $activity_id = 0)
    {
        $this->db->set('activityid', $activity_id);
        $this->db->where('batch', $batch);
        $this->db->like('devicename', '测试');
        return $this->db->update($this->table);
    }

    /**
     * 获取所有设备id
     * @return mixed
     */
    public function get_deviceid()
    {
        $this->db->select('deviceid');
        $this->db->from($this->table);
        $query = $this->db->get();

        return $query->result_array();
    }

    /**
     * 自定义更新
     * @return mixed
     */
    public function update_customize($where = [], $set = [])
    {
        if (! empty($where)) {
            $this->db->where($where);
        }

        if (! empty($set)) {
            $this->db->set($set);
            return $this->db->update($this->table);
        } else {
            return false;
        }
    }

	/**
	 * 设备部分历史记录
	 *
	 * @param array $set
	 * @return mixed
	 */
    public function device_log($set = [])
    {
	    return $this->db->insert_batch('device_log', $set);
    }
}