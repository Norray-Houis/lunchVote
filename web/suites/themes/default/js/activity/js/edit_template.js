$(function() {
    var global = global || {};
    var active_index = 0;
    var video = {};

    // 判断是修改还是新增，编辑时url没有id/number字符
    var edit_or_add = location.href.match(/id\/(\d+)/);

    // 模板相关
    var temp_name = "WELCOME"; // 当前页面名称
    var temp_brand = edit_or_add == null ? "vs" : 'PG_' + edit_or_add[1]; // 默认品牌名，编辑跟新增不一样
    var temp_brand_html = edit_or_add == null ? 'uploads/templates/html/vs/' : 'uploads/templates/PG_' + edit_or_add[1] + '/html/'; // 模板html文件路径
    //	var temp_brand_video = edit_or_add == null ? 'uploads/templates/html/vs/' : 'uploads/templates/PG_' + edit_or_add[1] + '/video/'; // 模板video文件路径
    var temp_src; // 模板所在路径，完整路径，用于js加载html文件
    var temp_url; // 模板http根地址

    // 调用链接相关
    var link_url;

    // 图片相关
    var img_width;
    var img_height;
    var current_img; // 当前图片
    var replace_img; // 替换图片
    var ajax_flag = false; // 标记图片是否已完成替换
    var img_url; // 图片上传
    var cancel_url; // 撤销图片上传
    
    // 视频替换
    var current_video; // 当前视频
    var replace_video; // 替换视频
    var ajax_video_flat = false; // 标记视频是否已完成替换
    var video_url; // 视频上传url

    var env_flag = /pg_cms/; // 当记当前环境，有pg_cms的是本地环境

    // 本地环境
    if (env_flag.test(location.href)) {
        // 模板相关
        temp_url = location.origin + '/pg_cms/web/';

        // 调用链接相关
        link_url = location.origin + '/pg_cms/web/index.php/_cms/activity/path_json';

        // 图片相关
        img_url = location.origin + '/pg_cms/web/index.php/_cms/activity/img_upload';
        cancel_url = location.origin + '/pg_cms/web/index.php/_cms/activity/cancel_replace';
        
        // 视频相关
        video_url = location.origin + '/pg_cms/web/index.php/_cms/activity/replace_video';
        
        // 模板相关
        switch_temp = location.origin + '/pg_cms/web/index.php/_cms/activity/switch_temp';

        // 线上环境
    } else {
        // 模板相关
        temp_url = location.origin + '/';

        // 调用链接相关
        link_url = location.origin + '/index.php/_cms/activity/path_json';

        // 图片相关
        img_url = location.origin + '/index.php/_cms/activity/img_upload';
        cancel_url = location.origin + '/index.php/_cms/activity/cancel_replace';
        
        // 视频相关
        video_url = location.origin + '/index.php/_cms/activity/replace_video';
        
        // 模板相关
        switch_temp = location.origin + '/index.php/_cms/activity/switch_temp';
    }


    // 加载默认页面
    getTemp();


    // 模板切换
    $(".swiper-container .swiper-wrapper").on("click", ".swiper-slide img", function() {
        active_index = $(this).parents(".swiper-slide").index();
        $(".swiper-slide").css("margin-left", "-22px");
        $(".swiper-slide").eq(active_index + 1).css("margin-left", "0px");
        $(this).addClass("active").closest("div").css("margin-left", "0px")
            .siblings(".swiper-slide").children("img").removeClass("active");
        temp_name = $(this).closest("div").data("type");
        //		temp_brand = $(this).closest("div").data("brand");
        getTemp();
        // $(".set_link_btn").hide();
    });

    // 上一页
    $("#prev_btn").on("click", function() {
        if (active_index != 0) {
            active_index--;
            resetCss(active_index);
        }
    });

    // 下一页
    $("#next_btn").on("click", function() {
        if (active_index != $(".swiper-slide").length - 1) {
            active_index++;
            resetCss(active_index);
        }
    });

    // 重置css
    function resetCss(index) {
        mySwiper.slideTo(index, 1000, false); //切换到第一个slide，速度为1秒
        $(".swiper-slide").css("margin-left", "-22px");
        $(".swiper-slide").eq(index + 1).css("margin-left", "0px");
        $(".swiper-slide").eq(index).find("img").addClass("active").closest("div").css("margin-left", "0px")
            .siblings(".swiper-slide").children("img").removeClass("active");
        temp_name = $(".swiper-slide").eq(index).data("type");
        //		temp_brand = $(".swiper-slide").eq(index).data("brand");
        getTemp();
    }

    // 加载模板
    function getTemp() {
        // 本地环境
        if (env_flag.test(location.href)) {
            temp_src = "/pg_cms/web/" + temp_brand_html + temp_name;
            // 线上环境
        } else {
            temp_src = "/" + temp_brand_html + temp_name;
        }

        //切换则保存已编辑的页面
        $.get(temp_src + '.html', function(data) {
            // $(".replace_img_btn").hide();
            $(".operate_btns").hide();
            // 替换js、css、img、a、video的路径为绝对路径
            var res = data.replace(/(<script\s+?src=\")(.+?\.js)(\"\s*?>)/g, "$1" + temp_url + temp_brand_html + "/$2$3");
            var res = res.replace(/(<img.+?src=\")(.+?)(\".*?>)/g, "$1" + temp_url + temp_brand_html + "/$2$3");
            var res = res.replace(/(<link.+?href=\")(.+?\.css)(\".*?>)/g, "$1" + temp_url + temp_brand_html + "/$2$3");
            var res = res.replace(/(<a.+?href=\")(.+?\.html)(\".*?>)/g, "$1" + temp_url + temp_brand_html + "/$2$3");
            var res = res.replace(/(<video.+?src=\")([^"]+?)(\".+?>)/g, "$1" + temp_url + temp_brand_html + "/$2$3");
            var res = res.replace(/(<video.+?poster=\")([^"]+?)(\".+?>)/g, "$1" + temp_url + temp_brand_html + "/$2$3");
            $('#temp_box').html(res);

            $("#temp_box a").on("click", function(e) {
                e.preventDefault();
            });

//            console.log(temp_src);

            $(".container img").on("click", function() {
                current_img = $(this).attr('src').replace(/(.*\/)?([^/]+?\.(jpeg|jpg|png|gif))/g, "$2");

                $(".set_link_btn").hide();
                $(".replace_vdo_btn").hide();
                $("#temp_box .active").removeClass("active");
                $(this).addClass("active");
                $(".replace_img_btn").toggle();
                global._target = $(this);
                global.src = global._target.attr("src");
                $("#layerMask").find("img").attr("src", global.src);
                img_width = global._target.width();
                img_height = global._target.height();
                $("#recovery_btn").hide();
                var show_height = (img_height * 100) / img_width + "px";
                $("#layerMask").find("img").css("height", show_height);
            });

            $("#assess_lists").on("click", function() {
                $("#temp_box .active").removeClass("active");
                $("#layerMask").hide();
                $(".set_link_btn").show().siblings(".operate_btns").hide();
            });

            $(".container video").on("click", function() {
            	current_video = $(this).attr('src').replace(/(.*\/)?([^/]+?\.(mp4))/g, "$2");
            	
                $("#temp_box .active").removeClass("active");
                $("#layerMask").hide();
                $(".replace_vdo_btn").show().siblings(".operate_btns").hide();
                video['vdoTarget'] = $(this);
            });
            if(temp_brand === "vs"){
            	$("#temp_box").css("fontSize", "45px");
            }else{
            	$("#temp_box").css("fontSize", "84.375px");
            }
            
        });
    }

    // 替换图片图层
    $(".replace_img_btn").on("click", function() {
        layer.open({
            type: 1,
            title: '替换图片',
            skip: 'layui-layer-rim',
            area: ['403px', '228px'],
            content: $("#layerMask"),
            cancel: function(index, layero) {
                $("#layerMask").hide();
            }
        });
        $(this).hide();
    });

    //修改上传图片视频的地址方法
    function uploadMedia(e){
        var obj = {},url = window.URL || window.webkitURL || window.mozURL ,files = e.target.files;
        
        for(var i = 0,len = files.length;i<len;++i){
            obj['file'] = files[i];
            if(url){
                obj['src'] = url.createObjectURL(obj['file']);
            }else{
                obj['src'] = e.target.result;
            }
        }
//        console.log(obj);
        return obj;
    }

    //本地图片和视频上传的url转变
    $("#img_upload").on("change", function(e) {
        // var src, url = window.URL || window.webkitURL || window.mozURL,
        //     files = e.target.files;

        // for (var i = 0, len = files.length; i < len; ++i) {
        //     var file = files[i];
        //     if (url) {
        //         src = url.createObjectURL(file);
        //     } else {
        //         src = e.target.result;
        //     }
        var fileObj = uploadMedia(e);
        
        // 上传文件
        var formData = new FormData();
        formData.append('file', fileObj['file']);
        formData.append('temp_brand_html', temp_brand_html);
        formData.append('temp_path', temp_name + '.html');
        formData.append('current_img', current_img);
        activity_img_upload(img_url, formData);

        if (global._target) {
            if (global._target.attr("src")) {
                global._target.attr("src", fileObj['src']);
            } else {
                global._target.css("background", "url(" + fileObj['src'] + ") 0px 0px / 100% 100% no-repeat");
            }
            $(this).val("");
            $("#layerMask").find("img").attr("src", fileObj['src']);
            $("#recovery_btn").show();
        }
        // }
    });


    // 图片上传方法
    function activity_img_upload(upload_url, formData) {
        $.ajax({
            url: upload_url,
            type: 'post',
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                if (data.errCode == 0) {
                    replace_img = data.errMsg;
                } else {
                    toastr.error(data.errMsg);
                }
            },
            complete: function() {
                ajax_flag = true;
            },
            error: function() {
                toastr.error('上传错误');
            }
        });
    }

    // 撤销替换图片
    $("#recovery_btn").on("click", function() {
        if (ajax_flag == false) {
            toastr.info('请稍等片刻再撤销...');
        } else {
            cancel_replace_img(current_img, replace_img);
        }
    });

    // 撤销替换图片请求
    function cancel_replace_img(current_img, replace_img) {
        $.post(cancel_url, { "temp_brand_html": temp_brand_html, "temp_path": temp_name + '.html', "current_img": current_img, "replace_img": replace_img }, function(data) {
            if (data.errCode == 0) {
                toastr.success(data.errMsg);

                global._target.attr("src", global.src);
                $("#layerMask").find("img").attr("src", global.src);
                $('#recovery_btn').hide();
            } else {
                toastr.error(data.errMsg);

                $('#recovery_btn').hide();
                layer.close(1);
            }
        });
    }

    // 调用链接
    $(".set_link_btn").on("click", function() {
        /*
        layer.open({
        	type: 1,
        	title: '调用链接',
        	skip: 'layui-layer-rim',
        	area:['400px','168px'],
        	content: $("#link"),
        	cancel: function(index,layer){
        		$("#link").hide();
        	}
        });
        $(this).hide();
        */

        var cur_link = get_link(temp_name);

        var url = prompt('请输入调用链接', cur_link);
        if (url) {
            page_url(link_url, temp_name + '.html', url);
        }
    });
    

    //替换视频
    $("input[name='vdoUpload']").on("change", function(e) {
        var fileObj = uploadMedia(e);
        
        var formData = new FormData();
        formData.append('file', fileObj['file']);
        formData.append('temp_brand_html', temp_brand_html);
        formData.append('temp_path', temp_name + '.html');
        formData.append('current_video', current_video);
        
//        console.log(video_url, formData);
//        replace_video(video_url, formData);
        // 暂时不使用函数了
        $.ajax({
            url: video_url,
            type: 'post',
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                if (data.errCode == 0) {
                    replace_video = data.errMsg;
                } else {
                    toastr.error(data.errMsg);
                }
            },
            complete: function() {
                ajax_video_flag = true;
            },
            error: function() {
                toastr.error('上传错误');
            }
        });
        
        
        if (video['vdoTarget']) {
            video['vdoTarget'].attr("src", fileObj['src']);
            $(this).val("");
        }
    });
    
    // 上传视频
    function replace_video(video_url, formData){
    	
    }
    
    /**
     * 查找已有链接
     */
    function get_link(temp_name) {
        return '';
    }

    /**
     * 页面调用链接
     */
    function page_url(request_url, page_name, page_url) {
        $.post(request_url, { "temp_brand_html": temp_brand_html, "page": page_name, "path": page_url }, function(data) {
            if (data.errCode == 0) {
                toastr.success(data.errMsg);
            } else {
                toastr.error(data.errMsg);
            }
        });
    }
    
    
    // 当前项保存
    var last_temp = 'vs';
    $('#template').click(function(){
    	last_temp = $(this).val();
    });
    

    // 切换模板，重新加载
    $("#template").change(function(e) {
    	
        if (confirm('切换模板后会清空当前编辑内容，是否切换？')) {
        	
        	temp_brand = $(this).val();
            temp_brand_html = 'uploads/templates/html/' + temp_brand + '/';
            $('#submit').attr('disabled', true);

	        $.ajax({
	        	url: switch_temp,
	        	method: 'post',
	        	data: {"template": temp_brand},
	        	async: false,
	        	complete: function(res) {
	        		
	        		if (res.responseJSON.errCode == 0) {
	        			toastr.success(res.responseJSON.errMsg);

	        			// 模板预览图
	        			var this_pic;

	        			$.ajax({
                            url: temp_url + temp_brand_html + 'temp_list_pic.json',
                            method: 'get',
                            async: false,
                            complete: function(resp) {

                                if (resp.readyState == 4 && resp.status == 200) {

                                    this_pic = resp.responseJSON.temp_list_pic;
                                    var len = this_pic.length;
                                    var html = '';

                                    for (var i = 0; i < len; i++) {
                                        html += '<div class="swiper-slide" ' + (i < 2 ? 'style=margin-left:0' : '') + ' data-type="' + this_pic[i].data_type + '" data-brand="vs"><img ' + (i < 1 ? 'class="active"' : '') + ' src="'+ temp_url + temp_brand_html + this_pic[i].src + '" /></div>';
                                    }

                                    $('.swiper-container .swiper-wrapper').html(html).css("transform", "translate3d(0px, 0px, 0px)");

                                    temp_name = "WELCOME"; // 切换后默认WELCOME页面

                                    getTemp();

                                    $('#submit').attr('disabled', false);
                                }

                            }
                        });

	        		} else {
	        			toastr.info(res.responseJSON.errMsg);
	        		}
	        	}
	        });
        
        } else {

        	// 取消选中
        	$("#template>option").each(function(a, b){
        		if ($(b)[0].value == last_temp) {
        			$('#template').val(last_temp);
        		}
        	})
        	
        }
        
    })
});