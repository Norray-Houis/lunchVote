<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 模板调试
 * @author william
 *
 */
class Templet extends NLF_Controller {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        require_once(getcwd(). '/suites/config/configurations.php');
        $this->load->library('unit_test');
    }
    
    /**
     * 模板示范页
     * 
     * @access public
     * @return void
     */
    public function index()
    {
        $this->data['channel'] = '默认空白页';
        load_view('templet/index', $this->data);
    }
    
}
