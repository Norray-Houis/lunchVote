<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 地区模型
 * @author william
 *
 */
class Region_mdl extends CI_Model
{
    /**
     * 表名
     * @var string
     */
    private $table = 'region';
    
    /**
     * 区域列表
     * @param number $region_type
     * @param number $parent_id
     */
    public function areas($region_type = 0, $parent_id = 0)
    {
        if (empty($region_type)) {
            return [];
        }
        
        $this->db->select('region_id, region_name, parent_id');
        $this->db->from($this->table);
        $this->db->where('region_type', $region_type);
        
        if (! empty($parent_id)) {
            $this->db->where('parent_id', $parent_id);
        }
        
        $query = $this->db->get();
        return $query->result_array();
    }
}