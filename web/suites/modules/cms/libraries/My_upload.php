<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 上传类库
 * @author william
 *
 */
class My_upload
{
    /**
     * ci实例
     * @var obj
     */
    private $ci;
    
    /**
     * @var array
     */
    private $config = [];
    
    /**
     * constructor
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->config($config);
        $this->ci = &get_instance();
        $this->ci->load->library('upload', $this->config);
    }
    
    /**
     * 上传配置
     * @param array $config
     * @throws Exception
     */
    public function config($config = [])
    {
        $file_name = uniqid();
        
        $this->config = [
            'upload_path' => 'uploads/default',
            'file_name' => $file_name,
            'max_size' => 10240,
            'allowed_types' => 'jpg|jpeg|png|gif'
        ];
        
        foreach($config as $k => $v) {
            if (isset($this->config[$k])) {
                $this->config[$k] = $v;
            } else {
                $this->config[] = $v;
            }
        }
        
        if (! file_exists($this->config['upload_path'])) {
            if (! mkdir($this->config['upload_path'], 0777, true)) {
                throw new Exception('创建目录失败');
            }
        }
    }
    
    /**
     * 上传文件
     * @param string $widget_key
     * @throws Exception
     */
    public function upload($widget_key = 'file')
    {
        if (! $this->ci->upload->do_upload($widget_key)) {
            throw new Exception('文件上传失败');
        } else {
            return $this->ci->upload->data('file_name');
        }
    }

    /**
     * 返回文件信息
     * @param unknown $index
     */
    public function data($index = null)
    {
        return $this->ci->upload->data($index);
    }
    
    /**
     * 错误显示
     */
    public function display_errors()
    {
        return $this->ci->upload->display_errors();
    }

	/**
	 * 图片压缩
	 * @return string
	 */
    public function create_thumb()
    {
		$image_info = $this->ci->upload->data();

		$config = [
			'image_library' => 'gd2',
			'source_image' => $image_info['full_path'],
			'width' => $image_info['image_width'] * 4 / 5,
			'create_thumb' => TRUE,
			'maintain_ratio' => TRUE,
		];

	    $this->ci->load->library('image_lib', $config);
		$this->ci->image_lib->resize();
		@unlink($image_info['full_path']);

		return $image_info['raw_name']. '_thumb'. $image_info['file_ext'];
    }
}