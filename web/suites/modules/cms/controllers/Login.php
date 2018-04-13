<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 登录模块
 * @author william
 *
 */
class Login extends CI_Controller
{

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
        require_once(getcwd(). '/suites/config/configurations.php');

        $account_info = $this->session->userdata('account_info');
        if ($account_info['logged_in']) {
            redirect('_cms/admin/listing');
            exit();
        }
    }
    
    
    /**
     * 登陆界面
     */
    public function index()
    {
        $this->load->view("public/header");
        $this->load->view("login");
    }
    
    
    /**
     * 登陆检验
     */
    public function signin()
    {

        try {

            if ($this->input->post('act') == 'signin') {
//                exit('x12');
                // 接受客户端数据
                $name = $this->input->post('name');
                $password = $this->input->post('password');
                
                // 把数据提交给模型
                $this->load->model('admin_user_mdl');
                $this->admin_user_mdl->name = $name;
                $this->admin_user_mdl->password = $password;
                
                $this->load->model('app_info_mdl');
                // 根据 base_url 获取 app_info
//                exit(base_url());
                $app_info = $this->app_info_mdl->get_app_info_by_url(base_url());

                // 保存 app_id 到 session 中
                $this->session->set_userdata('app_id', $app_info['id']);
                // 没有获取 app_info 信息，终止
                if (count($app_info) == 0) {
//                    exit('x123');
                    show_404();
                    exit();
                }
                // 获取登录者的所有用户表信息、角色名和权限列表
                $this->admin_user_mdl->app_id = $app_info['id'];
                $user = $this->admin_user_mdl->signin();
//                var_dump($user);
//                exit();
                if (count($user) > 0) {

                	// 判断是否锁定
	                if (time() - strtotime($user['lock_time']) < 7200 || $user['freeze'] == 1) {
	                	json_response(104, '用户已锁定，请联系管理员');
	                }

                    // 获取当前IP
                    require_once ("util/ip_location.php");
                    $ip = GetIP();
                    // $ipresult = getIPLoc_QQ ( $ip ); // 根据ip地址获得地区 getlocation("ip地区")
                    
                    // session记录登录者信息(其中包括关键的角色 ID 和权限列表)
                    $users = array(
                        'name' => $user['name'],
                        'id' => $user['id'],
                        'role_id' => $user['role_id'],
                        'action_list' => $user['action_list'],
                        'logged_in' => TRUE,
                        'location' => '', // $ipresult,
                        'ip' => $ip,
                        'is_first_login' => $user['is_first_login']
                    ) // 判断是否第一次登录
;
                    
                    // 把用户核心信息和id存入session
                    $this->session->set_userdata('account_info', $users);
                    $this->session->set_userdata('id', $user['id']);
                    
                    $this->load->model('system_module_mdl');
                    $this->load->model('admin_action_mdl');
                    $this->load->model('role_brand_mdl');
                    
                    // 获取属于当前 app_id 的所有模块信息，包括 app_id, sequence
                    $list_all = $this->system_module_mdl->get_all_list($user['app_id']);
                    $list_tree = array();
                    
                    // 查询用户所属角色的所有权限模块的 ID
                    $models = $this->admin_action_mdl->getModuleList($user["role_id"]);
                    
                    $ms = array();
                    foreach ($models as $key => $m) {
                        $ms[$key] = $m["module_id"]; // 对用户可操作模块 ID 数组降为一维数组
                    }
                    
                    // 按应用的 parent_id 进行分组，再把各应用的索引改为应用的 id
                    foreach ($list_all as $list_cell) {
                        $list_tree[$list_cell['pid']][$list_cell['id']] = $list_cell;
                    }
                    
                    $this->session->set_userdata('tree_data', $list_tree);
    

                    //数据权限设置
                   $brandlist = $this->role_brand_mdl->brands();
        
                    $brand_str = "0";
                    foreach($brandlist as $brand)
                    {
                        $brand_str = $brand_str.",".$brand["brandid"];
                    }
                    $this->session->set_userdata('brand_data', $brand_str);
                    
                    
                    $this->admin_user_mdl->last_login_ip = $ip;
                    
                    // 更新 is_first_login ，last_login_at 和 last_login_ip，并且清除冻结记录
                    $this->admin_user_mdl->last_update($user['id']);

                    operation_log('登录', $user['id']);
//                    exit('123xas');
                    json_response(0, '登录成功!');

                } else {

                	$user_id = $this->admin_user_mdl->check_name($name);

                    // 记录密码错误，冻结
	                if (! empty($user_id)) {
	                	$user_id = $user_id['id'];
	                	$user_info = $this->admin_user_mdl->load($user_id);

						if ((int) $user_info['error_times'] < 4) {
							$this->admin_user_mdl->freezing($user_id);
						} else {
							$this->admin_user_mdl->freezing($user_id, true);
							operation_log('用户冻结', $user_id);
						}

	                }

                    json_response(101, '用户名称和密码不匹配!');
                }
            } else {
                json_response(102, '非法登录!');
            }
        } catch (Exception $e) {
            json_response(103, '系统出错!');
        }
    }
}
