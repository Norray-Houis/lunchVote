<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 角色权限
 * @author william
 *
 */
class Role extends NLF_Controller {

    /**
     * 构造函数
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        $this->session->set_userdata('set_id', 2);
        //加载品牌
        $this->load->model('Dictionary_mdl');
        $this->load->model('Role_brand_mdl');
        require_once(getcwd() . '/suites/config/configurations.php');
    }

    // ---------------------------------------------------------------------------------------------

    /**
     * 角色列表
     */
    public function listing() {
        // 检验操作权限
        if (!admin_priv('role')) {
            return error_msg();
        }


        // 处理页码
        $page = isset($_GET['p']) && $_GET['p'] > 0 ? (int)$this->input->get()['p'] : 1;

        $per_page = 10;
        $offset = ($page - 1) * $per_page;

        // 数据总数
        $this->load->model('role_mdl');
        $total_rows = $this->role_mdl->total_rows();
        $data['total_pages'] = ceil($total_rows / $per_page);
        if ($page > $data['total_pages'] && $page != 1) {
            redirect(site_url('role/listing'));
        }

        // 获取权限结果集
        $data['roles'] = $this->role_mdl->get_roles($offset, $per_page);

        $data['channel'] = '角色列表';

        load_view("system/role/list", $data);
    }

    // ---------------------------------------------------------------------------------------------

    /**
     * 编辑/添加角色
     */
    public function edit() {
        // 检验操作权限
        if (!admin_priv('role_edit')) {
            return error_msg();
        }

        // 获取参数
        $params = $this->input->get();

        //获取品牌列表
        $type = 1;
        $data['brands'] = $this->Dictionary_mdl->getList($type);

        // 加载详细项
        if (!empty($params['id']) && $params['id'] > 0) {

            $id = intval($params['id']);
            $this->load->model('role_mdl');
            $data['detail'] = $this->role_mdl->load($id);

            // 判断id是否有效
            if (empty($data['detail'])) {
                json_response(1, '无效ID');
            }

            $data['channel'] = '编辑角色';

            //获取此角色的品牌权限
            $roleBrand = $this->Role_brand_mdl->getBrandid($id);


            $idArr = array();
            foreach ($roleBrand as $item) {
                $idArr[] = $item['brandid'];
            }

            foreach ($data['brands'] as $k => $item) {
                if (in_array($item['id'], $idArr)) {
                    $data['brands'][$k]['own'] = 1;
                } else {
                    $data['brands'][$k]['own'] = 0;
                }
            }

        } else {
            $data['detail'] = array(
                'id' => null,
                'name' => null,
                'action_list' => null
            );

            $data['channel'] = '新增角色';
        }

        $app_id = $this->session->userdata('app_id');
        $this->load->model('admin_action_mdl');

        // 获取 id_parent 为 0 且属于当前 app_id 的权限信息
        $action_arr = $this->admin_action_mdl->get_modules($app_id);

        // 获取 id_parent 不为 0 且属于当前 app_id 的权限信息
        $actions = $this->admin_action_mdl->get_actions($app_id);

        // 遍历 parent_id 不为0 的权限，为每个 id_parent 为 0 的权限分组添加子权限
        foreach ($actions as $val) {
            $action_arr[$val['parent_id']]['action_list'][$val['action_code']] = $val;
        }
//         p($action_arr);

        // 当前角色所有的权限，拆分成数组
        $detail_action_list = explode(',', $data['detail']['action_list']);

//         p($detail_action_list);

        // 编辑模式下，判断角色是否有该权限，如果有，设置键值对 cando => 1,否则设置 cando => 0
        if (!empty($id)) {

            // $arr_id 为 id_parent 为 0 的权限的 id, 即分组 id
            foreach ($action_arr as $arr_id => $arr_val) {

                if (isset($arr_val['action_list'])) {
                    // 遍历某分组的权限列表，$key为权限代码，$val为具体权限信息
                    foreach ($arr_val['action_list'] as $key => $val) {
                        // 判断id所属角色的 action_list 中是否有该权限代码，如果有，则标记 cando 值为 1，反之为0
                        $action_arr[$arr_id]['action_list'][$key]['cando'] = (in_array($val['action_code'], $detail_action_list) || $data['detail']['action_list'] == 'all') ? 1 : 0;
                        // 如果分组有权限代码，同上
                        if (isset($action_arr[$arr_id]['action_code'])) {
                            $action_arr[$arr_id]['cando'] = (in_array($action_arr[$arr_id]['action_code'], $detail_action_list) || $data['detail']['action_list'] == 'all') ? 1 : 0;
                        }
                    }
                } else {
                    if (isset($action_arr[$arr_id]['action_code'])) {
                        $action_arr[$arr_id]['cando'] = (in_array($action_arr[$arr_id]['action_code'], $detail_action_list) || $data['detail']['action_list'] == 'all') ? 1 : 0;
                    }
                }
            }
        }
//         p($action_arr);

        // 按模块分好的操作权限
        $data['action_arr'] = $action_arr;
        load_view('system/role/edit', $data);
    }

    // ---------------------------------------------------------------------------------------------

    /**
     * 保存
     */
    public function save() {
        // 检验操作权限
        if (!admin_priv('role_edit')) {
            return error_msg();
        }

        // 角色控制
        $id = $this->input->post('id');

        $this->load->model('Role_brand_mdl');

        // 接收数据
        $name = $this->input->post('name');
        $action_code = $this->input->post('action_code');

        // 处理权限代码，用“,”连接
        $action_list = @implode(',', $action_code);

        // 数据整理
        $data = array(
            'name' => $name,
            'action_list' => $action_list,
            'updated_at' => date('Y-m-d H:i:s')
        );

        $this->load->model('role_mdl');
        $brands = $this->input->post('brands');

        // 更新角色
        if ($id) {
            $info = $this->role_mdl->load($id);
            if (!$info) {
                json_response(2, '无效ID');
            }
            $delete = $this->input->post('delete');
            $delete = explode(",", $delete);

            //获取此用户的权限id
            $brandids = $this->Role_brand_mdl->getBrandid($id);

            //筛选出最终需要插入的品牌权限
            $Array = array_map('current', $brandids);

            $end = array();

            //存在的品牌权限不添加，不存在的就插入
            if (!empty($brands) && !empty($id)) {
                //插入用户品牌权限
                foreach ($brands as $k => $item) {
                    if (!in_array($item, $Array)) {
                        $arr['roleid'] = $id;
                        $arr['brandid'] = $item;
                        $end[$k] = $arr;
                    }
                }
                $result = $this->Role_brand_mdl->insert($end);
            }

            //删除权限
            foreach ($delete as $item) {
                $rs = $this->Role_brand_mdl->deleteRows(['roleid' => $id, 'brandid' => $item]);
            }

            $res = $this->role_mdl->update($id, $data);

            // 更新session，记录日志、返回更新成功信息
            if ($res) {
                // 动态更新管理员的session
                if ($this->session->userdata('account_info')['role_id'] == $id) {
                    $_SESSION['account_info']['action_list'] = $action_list;
                }

                operation_log("编辑角色[ID:{$id}]");
                json_response(0, '更新成功');
            } else {
                json_response(1, '更新失败');
            }

            // 新增角色
        } else {
            $this->load->model('Role_brand_mdl');
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['app_id'] = $this->session->userdata('app_id');

            $id = $this->role_mdl->create($data);

            $end = array();
            if (!empty($brands) && !empty($id)) {
                //插入用户品牌权限
                foreach ($brands as $k => $item) {
                    $arr['roleid'] = $id;
                    $arr['brandid'] = $item;
                    $end[$k] = $arr;
                }
                $result = $this->Role_brand_mdl->insert($end);
            }

            // 记录日志，返回成功信息
            if ($id) {
            	operation_log("新增角色[ID:{$id}]");
                json_response(0, '保存成功');
            } else {
                json_response(1, '保存失败');
            }
        }
    }

    // ---------------------------------------------------------------------------------------------

    /**
     * 角色删除
     */
    public function del() {

        // 检验操作权限
        if (!admin_priv('role_del')) {
            return error_msg();
        }

        // 获取参数
        $id = (int)$this->input->post()['id'];

        if ($id > 0) {
            // 判断role_id是否与当前登录者一致
            if ($id == $this->session->userdata('account_info')['role_id']) {
                json_response(1, '禁止删除当前管理员所属角色');
            }

            // 判断角色有没有管理员
            $this->load->model('admin_user_mdl');
            $admin = $this->admin_user_mdl->role_user($id);
            if (!empty($admin)) {
                json_response(2, '存在属于该角色的管理员，禁止删除');
            }

            // 删除角色
            $this->load->model('role_mdl');
            $name = $this->role_mdl->load($id)['name'];
            $row = $this->role_mdl->delete($id);
            if (!empty($row)) {
                // 记录日志
                operation_log("删除角色[ID:{$id}]");

                json_response(0, '删除角色成功');
            } else {
                json_response(3, '无效ID');
            }
        }
    }
}