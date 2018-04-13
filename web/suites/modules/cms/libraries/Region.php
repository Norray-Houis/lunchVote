<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 地区
 * @author william
 *
 */
class Region
{
    private $ci;
    
    private $provinces;
    
    private $cities;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->model('region_mdl', 're');
    }
    
    /**
     * 省份
     * @param number $parent_id
     */
    public function provinces($parent_id = 0)
    {
        if (empty($this->provinces)) {        
            $parent_id = (int) $parent_id;
            $this->provinces = $this->ci->re->areas(1, $parent_id);
        }
        
        return $this->provinces;
    }
    
    /**
     * 城市
     * @param number $parent_id
     */
    public function cities($parent_id = 0)
    {
        $parent_id = (int) $parent_id;
        $this->cities = $this->ci->re->areas(2, $parent_id);
        
        return $this->cities;
    }
}