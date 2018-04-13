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
									<label>角色名称</label>
									<input type="text" name="name" class="form-control" placeholder="角色名称" value="<?php echo $detail['name']; ?>" >
                                        <input type="hidden" name="id" class="form-control" placeholder="id" value="<?php echo $detail['id']; ?>" >
								    </div>
                                   
                                   
                                    <div class="form-group">
									<label>功能权限</label>
									
								</div>
                                
                               
                                
                                <!--  输出分组  -->
                                <?php foreach($action_arr as $arr_val){?>
                                <div class="row">
                                    <div class="col-md-5">
										<input type="checkbox" class="checkArr" name="action_code[]" <?php echo !empty($arr_val['cando']) ? 'checked="checked"' : '';?> value="<?php echo $arr_val['action_code'];?>"> <?php echo $arr_val['cn_name']?>
									</div>
									
									<?php if(isset($arr_val['action_list'])){?>
                                    <div class="col-md-5">
                                    <!-- 输出组成员 -->
									<?php foreach($arr_val['action_list'] as $act_val){?>
                                    	<div class="checkbox">
                                    		<label><input type="checkbox" class="checkList" name="action_code[]" <?php echo !empty($act_val['cando']) ? 'checked="checked"' : ''; ?> value="<?php echo $act_val['action_code'];?>"> <?php echo $act_val['cn_name'];?> </label>
                                    	</div>
                                    <?php }?>
                                    </div>
                                    <?php }?>
                                    
                                </div>
                                <?php }?>

                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">
                                        <input type="checkbox" class="checkArr">品牌选择
                                    </label>
                                    <div class="col-md-5">
                                        <div class="checkbox" style="text-align:right">
                                            <?php foreach($brands as $item):?>
                                                <label><input type="checkbox" class="checkList" name="brands[]" value="<?php echo $item['id'];?>" <?php echo !empty($item['own']) ? 'checked="checked"  selected="selected"' :'';?>> <?php echo $item['keyname'];?></label>
                                            <?php endforeach;?>

                                            <input type="hidden" class="delete" name="delete" value="">
                                        </div>
                                    </div>

                                <div class="form-group row">
                                	
                                    <div class="col-md-12">
                                    	<div class="checkbox" >
                                    		<label><input type="checkbox" id="checkAll" name="checkAll" value="1"> 全选 </label>
                                    	</div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="button" id="submit" class="btn btn-primary"><i class="fa fa-dot-circle-o"></i> 提交 </button>
                            		<button type="button" class="btn btn-danger" onclick="history.back();"><i class="fa fa-ban"></i> 取消</button>
                                </div>
                              
                            </form>
                        </div>
                       
                    </div>
                </div>
                <!--/col-->
            </div>
            <!--/row-->
        </div>
    <!-- /.conainer-fluid -->
</div>


<script>

    //分组选择
    $('.checkArr').on('click', function(){
			if( this.checked ) {
				console.log($(this).parent().siblings('.col-md-9').find('input:checkbox').html());
				$(this).parent().siblings('.col-md-5').find('input:checkbox').each( function(){this.checked=true;} );
			} else {
				$(this).parent().siblings('.col-md-5').find('input:checkbox').each( function(){this.checked=false;} );
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
    
	// 表单提交
	$('#submit').on('click', function(){
		var submit = $(this);
		submit.attr('disabled', true);
		$.post('<?php echo site_url("_cms/role/save")?>', $('#form').serialize(), function(data){
			if(data.errCode == 0){
				toastr.success(data.errMsg);
				location.href=document.referrer;
			}else{
				toastr.error(data.errMsg);
				submit.removeAttr('disabled');
    		}
		});
    });

	// -------------------------------------------------------------------------------------
	
	// 取消选择
	$('input:checkbox').click( function() { 
		$(this).each(function(){
			if(!this.checked) {
				$('#checkAll').attr('checked',false);
			}
		});
	});


    $(".checkList").on("change", function () {
        var deleteString = $('.delete').val();


        //点击之后如果取消打钩时触发
        if ($(this).attr("selected") == "selected") {
            if ($(this).attr("checked") == "checked") {
                if($(".delete").val()==""){
                    $(".delete").val($(this).val());
                }else{
                    $(".delete").val($(".delete").val()+","+$(this).val());
                }

                $(this).removeAttr("checked");
            } else {
                var deleteArr = deleteString.split(',');
                for(i=0;i<deleteArr.length;i++){
                    if($(this).val()==deleteArr[i]){
                        deleteArr.splice(i,1);
                    }
                }
                $('.delete').val(deleteArr.join(','));
                $(this).attr("checked","checked");
            }
        }
    });
	
</script> 