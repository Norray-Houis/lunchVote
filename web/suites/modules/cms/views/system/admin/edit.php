<!-- Main content -->
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">	
			
    <div class="row">
			<ol class="breadcrumb">
				<li><a href="<?php echo home_page();?>">首页</a></li>
                <li><a href="<?php echo site_url('admin/get_list');?>">系统管理员</a></li>
                <li class="active"><?php echo $channel;?></li>    
			</ol>
            <div>&nbsp;</div>
		</div><!--/.row-->
    
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
				<div class="panel-heading"><?php echo $channel;?></div>
					<div class="panel-body">
						<div class="col-md-6">
							<form action="" method="post" id="form" enctype="multipart/form-data" class="form-horizontal ">
                                   <div class="form-group">
									<label>用户名</label>
									<input type="text" name="name" class="form-control" placeholder="请填写用户名" maxlength='32' value="<?php echo $detail['name']; ?>" >
                                        <input type="hidden" name="id" class="form-control" placeholder="id" value="<?php echo $detail['id']; ?>" >
								</div>
                                  
                                  <div class="form-group">
									<label>邮箱</label>
									 <input type="text" name="email" class="form-control" placeholder="请填写邮箱" maxlength='32' value="<?php echo $detail['email']; ?>" >
								</div>
                                  
                                  <div class="form-group">
									<label>角色</label>
									<select id="role" name="role" class="form-control">
                                        	<option value="0">请选择角色</option>
                                        <?php foreach($roles as $val){
                                            $selected = $val['id'] == $detail['role_id'] ? 'selected="selected"' : '';
                                            echo '<option value="', $val['id'], '" ', $selected, '>', $val['name'], '</option>';
                                        }?>
                                        </select>
								</div>
                                   
                                   
                                   <div class="form-group">
									<label>密码 <span style="color:red">[可选字符仅限大小写字母、数字和 =/ ，至少 8 个字符并且包含至少 1 位数字]</span></label>
									<input type="button" value="修改密码" <?php echo isset($detail['id']) ? '' : 'style="display:none" disabled="disabled"' ;  ?> class="btn btn-primary" id="change_btn"/>
                                    	<input type="password" name="password" maxlength='32' class="change_pwd form-control" <?php echo isset($detail['id']) ? 'style="display:none" disabled="disabled"' : '' ;  ?> placeholder="请填写密码" value="" >
                                    	<br />
                                    	<input type="password" name="confirm_pwd" maxlength='32' class="change_pwd form-control" <?php echo isset($detail['id']) ? 'style="display:none" disabled="disabled"' : '' ;  ?> placeholder="请确认密码" value="" >
								</div>
                                   
                                <div class="form-group">
                                     <button type="button" id="submit" class="btn  btn-primary"><i class="fa fa-dot-circle-o"></i> 提交 </button>
                                     <button type="button" class="btn  btn-danger" onclick="history.back();"><i class="fa fa-ban"></i> 取消</button>
                                </div>
                               
                            </form>
                           
                        </div>
                </div>
            </div>
        </div>
    </div>

    

    <!-- /.conainer-fluid -->
</div>


<script>

    //分组选择
    $('.checkArr').on('click', function(){
			if( this.checked ) {
				$(this).parent().siblings('.col-md-9').find('input:checkbox').each( function(){this.checked=true;} );
			} else {
				$(this).parent().siblings('.col-md-9').find('input:checkbox').each( function(){this.checked=false;} );
			}
    });

    // -------------------------------------------------------------------------------------

    //全选
    $('#checkAll').on('click', function(){
		if( this.checked ) {
			$('input:checkbox').each( function(){this.checked=true;} );
		} else {
			$('input:checkbox').each( function(){this.checked=false;} );
		}
	});

    // -------------------------------------------------------------------------------------

	// 取消全选
	$('input:checkbox').click( function() {
		$(this).each(function(){
			if(!this.checked) {
				$('#checkAll').attr('checked',false);
			}
		});
	});

	// -------------------------------------------------------------------------------------
	
	// 修改密码按钮
	$('#change_btn').on('click', function(){
		$(this).css('display', 'none');
		$(this).attr('disabled', 'disabled');
		$(".change_pwd").css('display', 'block');
		$(".change_pwd").removeAttr('disabled');
		$('#submit').val('change_btn');// 修改密码标记
	});

	// -------------------------------------------------------------------------------------

	// ajax提交
	function sub_ajax()
	{
		var submit = $('#submit');
		$.post('<?php echo site_url("_cms/admin/save")?>', $('#form').serialize(), function(data){	
			if(data.errCode == 0){
				toastr.success(data.errMsg);
				location.href=document.referrer;
			}else{
				toastr.error(data.errMsg);
				submit.removeAttr('disabled');
			}
		});
	}

	// -------------------------------------------------------------------------------------
		
	// 表单验证提交
	$('#submit').on('click', function(){
		var test_name = /[A-Za-z0-9_\-\u4e00-\u9fa5]+/;
	    var test_email = /\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/;
	    var test_pwd = /[a-zA-Z0-9=/]{8,}/;
	    var test_pwd_num = /[0-9]/;
		var id = '<?php echo $detail["id"]?>';
		var name = $('input[name=name]').val();
		var email = $('input[name=email]').val();
		var role = $('#role').val();
		
		var password = $('input[name=password]').val();
		var confirm_pwd = $('input[name=confirm_pwd]').val();
		var change_btn = $(this).val();//修改密码标记
		var submit = $(this);
		submit.attr('disabled', true);

		if(name == '') {
			toastr.error('用户名不能为空');
			submit.removeAttr('disabled');
		} else if (!test_name.test(name)) {
    		toastr.error('请填写正确格式的用户名');
    		submit.removeAttr('disabled');
		} else if( email !== '' && !test_email.test(email)) {
			toastr.error('请填写正确格式的邮箱地址');
			submit.removeAttr('disabled');
		} else if (role == '0') {
    		toastr.error('请选择角色');
    		submit.removeAttr('disabled');
    	// 点击修改密码或新增管理员才验证
		} else if (change_btn || !id){
    		if(password == '') {
				toastr.error('请填写密码');
				submit.removeAttr('disabled');
    		}else if(confirm_pwd == ''){
        		toastr.error('请填写确认密码');
        		submit.removeAttr('disabled');
    		}else if(!test_pwd.test(password) || !test_pwd_num.test(password)){
        		toastr.error('请填写正确格式的密码');
        		submit.removeAttr('disabled');
    		}else if(password != confirm_pwd){
				toastr.error('请确认密码一致');
				submit.removeAttr('disabled');
    		}else{
   				sub_ajax();
    		}
		} else {
			sub_ajax();
		}
		
    });
	
</script> 