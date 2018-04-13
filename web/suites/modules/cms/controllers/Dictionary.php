<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 系统管理
 * @author william
 *
 */
class Dictionary extends NLF_Controller
{
	/**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
        require_once(getcwd(). '/suites/config/configurations.php');
        
        // 地区
        $this->load->model('region_mdl', 're');
        // 所有省份
        $this->provinces = $this->re->areas(1, 0);
    }

    public function index($page = 1)
    {
        $data= array();
    	$this->session->set_userdata('set_id', 20);
        // 检验操作权限
        if (! admin_priv('role')) {
            return error_msg();
        }

        // 处理页码
        $page = $page > 1 ? $page: 1;

        $per_page = 10;
        $offset = ($page - 1) * $per_page;

        // 数据总数
        $this->load->model('Dictionary_mdl');
        $total_rows = $this->Dictionary_mdl->total_rows(1);
        $data['total_pages'] = ceil($total_rows / $per_page);
        if ($page > $data['total_pages'] && $page != 1) {
            redirect(site_url('brand/listing'));
        }

        // 获取权限结果集
        $data['brands'] = $this->Dictionary_mdl->getList(1,$offset, $per_page);

        $data['channel'] = '品牌列表';

        load_view("brand/list", $data);
    }
}