/**
 * 共公js函数
 */

/**
 * 获取报表公共查询参数
 * @returns
 */
function get_common_params()
{
	return {
		"date": $("#dateTimeRange").val(),
		"brand": $("#brand").val(),
		"channel": $("#channel").val(),
		"activity": $('#activity').val(),
		"city": $("#city").val(),
		"deviceid": $('#deviceid').val(),
		"shop": $('#shop').val(),
	};
}

/**
 * 活动、设备列表查询参数
 * @returns {___anonymous342_505}
 */
function get_params()
{
	return {
		"brand": $("#brand").val(),
		"channel": $("#channel").val(),
		"batch": $("#batch").val(),
		"status": $("#status").val(),
		"keyword": $("#keyword").val(),
	};
}

/**
 * 需要设置传入元元素唯一标识，标签添加data-id属性
 * @param url
 * @param ele
 */
function del(url, ele)
{
	if (confirm('确定删除？')) {
		var id = $(ele).attr('data-id');
		$.post(url, {"id":id}, function(data){
			if (data.errCode == 0) {
				toastr.success(data.errMsg);
				location.reload();
			} else {
				toastr.info(data.errMsg);
			}
		});
	}
}

/*
 * 列出搜索参数
 */
function report_list_request_params(params){
	var request_params = '';
	request_params += '<span class="search_params">时间:'+params.date+'</span>';
	request_params += '<span class="search_params">品牌:'+params.brand+'</span>';
	request_params += '<span class="search_params">渠道:'+params.channel+'</span>';
	request_params += '<span class="search_params">活动:'+params.activity+'</span>';
	request_params += '<span class="search_params">地区:'+params.city+'</span>';
	request_params += '<span class="search_params">门店:'+params.shop+'</span>';
	request_params += '<span class="search_params">设备ID:'+params.deviceid+'</span>';
	$('#params_choice').html(request_params);
}

/**
 * 分页
 */
$('#pagination').on('click', 'a', function(){
	var page = $(this).attr('data-page');
	
	if (page != cur_page) {
		cur_page = $(this).attr('data-page');
		search_data();
	}
});

/**
 * 列表搜索
 */
$('#search_list').click(function(){
	cur_page = 1;
	search_data();
});
