<?php

function get_header($controller, $module_id){
	
	if (! checkLogin($controller)) {
		redirect ( site_url ( 'login' ) );
		return;
	}
	
	$data ['top_module'] = $controller->session->userdata ( 'tree_data' );
	
	$controller->load->model ( 'system_module_mdl' );
	
	$data ['section_id'] = $module_id;
	
	$data['parent'] = $controller->system_module_mdl->get_parent($module_id);
	
	$data ['theme_title'] = $data['parent']['module_name'];
	
	return $data;
	
}


function checkLogin($controller)
{
	$account_info = $controller->session->userdata ( 'account_info' );
	
	if (! $account_info) {
		return false;
	}else
	{
		return true;
	}
}

?>