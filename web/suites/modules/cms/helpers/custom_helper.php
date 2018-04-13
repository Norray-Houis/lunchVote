<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 视图加载
 * @param string $view_path
 * @param array $data
 * @return void
 */
function load_view($view_path = '', $data = [])
{
//    exit($view_path);
    $CI = &get_instance();
    $CI->load->view('public/header');
    $CI->load->view('public/navbar');
    $CI->load->view($view_path, $data);
    $CI->load->view('public/footer');

}


/**
 * 调试函数
 * @return void
 */
function p()
{
    $params = func_get_args();
    
    echo "<pre>";
    foreach ($params as $v) {
        print_r($v);
        echo "\n";
    }
    echo "</pre>";
}


/**
 * 打印上一条sql
 */
function psql()
{
    $CI = &get_instance();
    p($CI->db->last_query());
}


/**
 * 返回json格式数据
 * @param number $err_code
 * @param string $err_msg
 * @param array $data
 * @return void
 */
function json_response($err_code = 0, $err_msg = '', $data = [])
{
    header('Content-Type: application/json; charset=utf-8');
    $response = [
        'errCode' => $err_code,
        'errMsg' => $err_msg,
        'data' => $data
    ];
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}


/**
 * 功能：检查权限
 * @param string $priv_str
 * @return bool
 */
function admin_priv($priv_str)
{
    $CI = &get_instance();
    $action_list = $CI->session->userdata('account_info')['action_list'];
    if (strpos(',' . $action_list . ',', ',' . $priv_str . ',') === false)
    {
        return false;
    }

    return true;
}


/**
 * 错误跳转
 * @param  $message  string  提示信息
 * @param  $goto     string  重定向url
 * @return void
 */
function error_msg($message="权限不足", $goto = '_cms/templet')
{
	$CI = &get_instance();

	$account_info = $CI->session->userdata('account_info');
	if (! $account_info['logged_in']) {
		redirect('_cms/login/index');
	}

	$CI->session->set_flashdata('message', array('type'=>'error','content' => $message));

	if ($goto == '_cms/templet' && isset($_SERVER['HTTP_REFERER'])) {
	    redirect($_SERVER['HTTP_REFERER']);
	} else {
	    redirect($goto);
	}
}


/**
 * 复制文件(夹)
 * @param unknown $src
 * @param unknown $des
 */
function recurse_copy($src,$des)
{
    $dir = opendir($src);
    @mkdir($des, 0777, true);
    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if ( is_dir($src . '/' . $file) ) {
                recurse_copy($src . '/' . $file,$des . '/' . $file);
            } else {
                @copy($src . '/' . $file,$des . '/' . $file);
            }
        }
    }
    closedir($dir);
}


/**
 * 压缩
 * @param unknown $folder
 * @param unknown $zipFile
 * @param unknown $subfolder
 */
function folderToZip($folder, $zipFile, $subfolder = null) {
    if ($zipFile == null) {
        return false;
    }
    $folder .= @end(str_split($folder)) == "/" ? "" : "/";
    $subfolder .= @end(str_split($subfolder)) == "/" ? "" : "/";
    
    $handle = opendir($folder);
    while ($f = readdir($handle)) {
        if ($f != "." && $f != "..") {
            if (is_file($folder . $f)) {
                if ($subfolder != null) {
                    $zipFile->addFile($folder . $f, $subfolder . $f);
                } else {
                    $zipFile->addFile($folder . $f);
                }
            } elseif (is_dir($folder . $f)) {
                $zipFile->addEmptyDir($folder. $f);
                folderToZip($folder . $f, $zipFile, $f);
            }
        }
    }
}


/*压缩多级目录 
    $openFile:目录句柄 
    $zipObj:Zip对象 
    $sourceAbso:源文件夹路径 
*/  
function createZip($openFile,$zipObj,$sourceAbso,$newRelat = '')  
{  
    while(($file = readdir($openFile)) != false)  
    {  
        if($file=="." || $file==".." || $file == 'slider' || strpos($file, '.') === 0)
            continue;
          
        /*源目录路径(绝对路径)*/  
        $sourceTemp = $sourceAbso.'/'.$file;  
        /*目标目录路径(相对路径)*/  
        $newTemp = $newRelat==''?$file:$newRelat.'/'.$file;  
        if(is_dir($sourceTemp))  
        {  
            //echo '创建'.$newTemp.'文件夹<br/>';  
            $zipObj->addEmptyDir($newTemp);/*这里注意：php只需传递一个文件夹名称路径即可*/  
            createZip(opendir($sourceTemp),$zipObj,$sourceTemp,$newTemp);  
        }  
        if(is_file($sourceTemp))  
        {  
            //echo '创建'.$newTemp.'文件<br/>';  
            $zipObj->addFile($sourceTemp,$newTemp);  
        }  
    }  
}  


/**
 * 递归创建文件夹
 * @param unknown $param
 * @return string
 */
function mkdirsByPath($param)
{
    if(! file_exists($param)) {
        mkdirsByPath(dirname($param));
        @mkdir($param);
    }
    return realpath($param);
}


/**
 * 删除目录及其子文件夹和文件
 * @param unknown $dirName
 */
function delDirAndFile( $dirName )
{
    if ( $handle = opendir( "$dirName" ) ) {
        while ( false !== ( $item = readdir( $handle ) ) ) {
            if ( $item != "." && $item != ".." ) {
                if ( is_dir( "$dirName/$item" ) ) {
                    delDirAndFile( "$dirName/$item" );
                } else {
                    @unlink( "$dirName/$item" );
                }
            }
        }
        closedir( $handle );
        @rmdir( $dirName );
    }
}


/**
 * 后台主页
 */
function home_page()
{
    return site_url('_cms/templet/index');
}


/**
 * 当前页码
 */
function current_page()
{
    $_ci = &get_instance();
    $cur_page = $_ci->uri->segment(4);
    $cur_page = is_int($cur_page) && $cur_page > 0 ? $cur_page : 1;
    return $cur_page;
}


/**
 * 自定义site_url
 * @param string $uri
 * @param unknown $protocol
 */
function my_site_url($uri = '', $protocol = NULL)
{
    return site_url('_cms/'. $uri, $protocol);
}


/**
 * 自定义base_url
 * @param string $uri
 * @param unknown $protocol
 */
function my_base_url($uri = '', $protocol = NULL)
{
    return base_url('_cms/'. $uri, $protocol);
}

/**
 * 日志记录
 * @param string $operation
 * @param int $operator
 */
function operation_log($operation = '', $operator = 0)
{
	if (! empty($operation)) {
		$ci = get_instance();
		$ci->load->model('log_mdl');
		$set = [
			"user_id" => empty($operator) ? $ci->session->userdata('id') : $operator,
			"log_time" => date('Y-m-d H:i:s'),
			"ip" => $ci->input->ip_address(),
			"log_content" => $operation,
		];

		$ci->log_mdl->create($set);
	}
}