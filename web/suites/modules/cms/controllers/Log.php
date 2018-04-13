<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Log
 * 日志管理
 */
class Log extends NLF_Controller
{

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
	    $this->session->set_userdata('set_id', 2);
	    require_once(getcwd(). '/suites/config/configurations.php');
    }
    
    public function listing()
    {
        // 检验操作权限
         if (! admin_priv('log')) {
             return error_msg();
         }

	    $data['channel'] = '系统日志';

        load_view("system/log/list", $data);
    }

	/**
	 * ajax 数据
	 */
	public function listing_data()
	{
		$params = $this->pub_query();

		// 分页配置
		$config['cur_page'] = (int) $this->input->post('cur_page') > 0 ? (int) $this->input->post('cur_page') : 1;

		$pages['limit'] = $config['limit'] = 10;
		$pages['offset'] = ($config['cur_page'] - 1) * $pages['limit'];


		$this->load->model('log_mdl');
		$this->data['listing'] = $this->log_mdl->listing($params, $pages);

		$config['total_rows'] = $this->log_mdl->total_rows($params);

		$this->load->library('pages', $config);
		$this->data['pages'] = $this->pages->create_link();

		json_response(0, 'success', $this->data);
	}

	/**
	 * 参数处理
	 * @return mixed
	 */
	public function pub_query()
	{
		$params = $this->input->post();
		$need = ['date', 'user', 'cur_page', 'is_search'];

		foreach ($params as $k => $v) {
			if (! in_array($k, $need) || empty($v)) {
				unset($params[$k]);
			} else if ($k == 'date') {
				$time_arr = explode('-', $params['date']);
				$params['start_time'] = date('Y-m-d H:i:s', strtotime(trim($time_arr[0])));
				$params['end_time'] = date('Y-m-d H:i:s', strtotime(trim($time_arr[1]). " 23:59:59"));
				unset($params[$k]);
			}
		}

		if (empty($params['is_search'])) {
			$return = $params['cur_page'];
		} else {
			$return = $params;
		}

		return $return;
	}
}