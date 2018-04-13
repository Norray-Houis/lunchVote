<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 管理员
 * @author william
 *
 */
class Admin extends NLF_Controller
{

    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->session->set_userdata('set_id', 2);
        require_once(getcwd(). '/suites/config/configurations.php');
    }
    
    // ---------------------------------------------------------------------------------------------
    
    /**
     * 管理员列表
     */
    public function listing()
    {
//        exit('123x');
        // 检验操作权限
        if (! admin_priv('admin')) {
            return error_msg();
        }
        // 处理页码
        $page = isset($_GET['p']) && $_GET['p'] > 0 ? (int) $this->input->get()['p'] : 1;
        
        $per_page = 10;
        $offset = ($page - 1) * $per_page;
        
        // 数据总数
        $this->load->model('admin_user_mdl');
        $total_rows = $this->admin_user_mdl->total_rows();
        $data['total_pages'] = ceil($total_rows / $per_page);
        if ($page > $data['total_pages'] && $page != 1) {
            redirect(site_url('admin/listing'));
        }
        
        // 获取权限结果集
        $data['admins'] = $this->admin_user_mdl->get_admins($offset, $per_page);
        
        $data['channel'] = '管理员列表';
//        exit('ad');
        load_view("system/admin/list", $data);
    }
    
    // ---------------------------------------------------------------------------------------------
    
    /**
     * 编辑/增加管理员
     */
    public function edit()
    {
        // 检验操作权限
        if (! admin_priv('admin_edit')) {
            return error_msg();
        }
        
        // 获取参数
        $params = $this->input->get();
        
        // 加载详细项
        if (! empty($params['id']) && $params['id'] > 0) {
            
            $id = intval($params['id']);
            $this->load->model('admin_user_mdl');
            $data['detail'] = $this->admin_user_mdl->load($id);
            
            // 判断id是否有效
            if (empty($data['detail'])) {
                json_response(1, '无效ID');
            }
            
            $data['channel'] = '编辑管理员';
        } else {
            $data['detail'] = array(
                'id' => null,
                'name' => null,
                'email' => null,
                'role_id' => null
            );
            
            $data['channel'] = '新增管理员';
        }
        
        // 角色结果集
        $this->load->model('role_mdl');
        $data['roles'] = $this->role_mdl->roles_option();
        
        load_view('system/admin/edit', $data);
    }
    
    // ---------------------------------------------------------------------------------------------
    
    /**
     * 保存
     */
    public function save()
    {
        // 检验操作权限
        if (! admin_priv('admin_edit')) {
            return error_msg();
        }
        
        // 管理员id
        $id = $this->input->post('id');
        // 接收数据
        $name = $this->input->post('name');
        $email = $this->input->post('email');
        $role_id = $this->input->post('role');
        $password = $this->input->post('password') ? md5($this->input->post('password')) : '';
        
        $this->load->model('admin_user_mdl');
        
        // 数据处理
        $data = array(
            'name' => $name,
            'email' => $email,
            'role_id' => $role_id,
            'updated_at' => date('Y-m-d H:i:s')
        );
        
        // 用户名唯一性验证
        $check_id = $this->admin_user_mdl->check_name($name);
        if (isset($check_id['id']) && $id != $check_id['id'] && empty($id)) {
            json_response(1, '用户名已存在');
        }
        
        // 更新管理员信息
        if ($id) {
            
            $admin = $this->admin_user_mdl->load($id);
            if (! $admin) {
                json_response(2, '无效ID');
            }
            
            // 判断是否对密码处理
            if (! empty($password)) {
                $data['password'] = $password;
            }
            
            // 更新数据表
            $res = $this->admin_user_mdl->update($id, $data);
            
            // 记录日志，返回成功信息
            if ($res) {
                // 如果修改当前管理员，更新session
                if ($id == $this->session->userdata('id')) {
                    if ($name != $this->session->userdata('account_info')['name']) {
                        $_SESSION['account_info']['name'] = $name;
                    }
                    if ($role_id != $this->session->userdata('account_info')['role_id']) {
                        // 加载角色权限
                        $this->load->model('role_mdl');
                        $action_list = $this->role_mdl->load($role_id)['action_list'];
                        
                        $_SESSION['account_info']['role_id'] = $role_id;
                        $_SESSION['account_info']['action_list'] = $action_list;
                    }
                }
                
                // 记录日志
                operation_log("编辑管理员[ID:{$id}]");
                
                json_response(0, '更新成功');
            }
            
            // 增加管理员
        } else {
            // 更新数据
            $data['password'] = $password;
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['app_id'] = $this->session->userdata('app_id');
            
            $id = $this->admin_user_mdl->create($data);
            
            // 记录日志，返回成功信息
            if ($id) {
                operation_log("新增管理员[ID:{$id}]");
                
                json_response(0, '保存成功');
            }
        }
    }
    
    // ---------------------------------------------------------------------------------------------
    
    /**
     * 管理员删除
     */
    public function del()
    {
        // 检验操作权限
        if (! admin_priv('admin_del')) {
            return error_msg();
        }
        
        // 获取参数
        $id = (int) $this->input->post()['id'];
        
        if ($id > 0) {
            // 判断id是否与当前登录者一致
            if ($id == $this->session->userdata('id')) {
                json_response(1, '禁止删除当前管理员');
            }
            
            // 删除管理员
            $this->load->model('admin_user_mdl');
            $name = $this->admin_user_mdl->load($id)['name'];
            $row = $this->admin_user_mdl->delete($id);
            if (! empty($row)) {
                // 记录日志
                operation_log("删除管理员[ID:{$id}]");
                
                json_response(0, "删除管理员成功");
            } else {
                json_response(3, '无效ID');
            }
        }
    }

	/**
	 * 冻结 & 解冻
	 */
    public function freeze()
    {
        // 检验操作权限
        if (! admin_priv('admin_del')) {
            return error_msg();
        }

        // 获取参数
        $id = (int) $this->input->post()['id'];
        $freeze = (int) $this->input->post()['freeze'];

        if ($id > 0 && $freeze >= 0) {
            // 判断id是否与当前登录者一致
            if ($id == $this->session->userdata('id')) {
                json_response(1, '禁止对当前管理员进行操作');
            }

            $this->load->model('admin_user_mdl');

            $res = $this->admin_user_mdl->update($id, ['freeze' => $freeze]);
            if ($res) {
            	if ($freeze == 0) {
		            operation_log("解冻管理员[ID:{$id}]");
		            json_response(0, "解冻管理员成功", "冻结");
	            } else {
		            operation_log("冻结管理员[ID:{$id}]");
		            json_response(0, "冻结管理员成功", "解冻");
	            }
            } else {
                json_response(3, '无效ID');
            }
        }
    }
}