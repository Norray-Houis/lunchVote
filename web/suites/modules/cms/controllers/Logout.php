<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 退出登录
 * @author william
 *
 */
class Logout extends CI_Controller
{

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 退出
     * @access public
     * @return void
     */
    public function index()
    {
        $this->session->sess_destroy();
        redirect('_cms/login/index');
    }
}