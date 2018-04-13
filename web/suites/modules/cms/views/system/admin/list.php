<!-- Main content -->

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">

		<div class="row">
			<ol class="breadcrumb">
				<li><a href="<?php echo home_page();?>">首页</a></li>
                <li><a href="<?php echo site_url('admin/get_list');?>">系统管理员</a></li>
                <li class="active"><?php echo $channel;?></li>
			</ol>
            <div class="col-md-5 charts" style="margin-left:-25px;text-align:right;">

            </div>
		</div><!--/.row-->


    <div class="row">
			<div class="col-lg-12">
				<div class="panel panel-default">
					<div class="panel-heading"><?php echo $channel;?>
					     <div class="pull-right">
                           <a class="btn btn-success" href="<?php echo site_url('_cms/admin/edit')?>">新增</a>
                        </div>
					</div>
					<div class="panel-body">
                      <table class="table table-striped table-bordered datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>管理员</th>
                                <th>邮箱</th>
                                <th>角色</th>
                                <th>创建时间</th>
                                <th>最近更新</th>
                                <th>最近登录</th>
                                <th>最近登录IP</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach( $admins as $val ) {?>
                            <tr>
                                <td><?php echo $val['id']?></td>
                                <td><?php echo $val['name']?></td>
                                <td><?php echo $val['email']?></td>
                                <td><?php echo $val['role_name']?></td>
                                <td><?php echo $val['created_at']?></td>
                                <td><?php echo $val['updated_at']?></td>
                                <td><?php echo $val['last_login_at']?></td>
                                <td><?php echo $val['last_login_ip']?></td>
                                <td>
                                    <a class="btn btn-info btn-sm glyphicon glyphicon-pencil" title="编辑" href="<?php echo site_url('_cms/admin/edit?id='.$val['id'])?>">

                                    </a>
<!--                                    <a class="del btn btn-danger btn-sm" title="删除" data-id="--><?php //echo $val['id'];?><!--">-->
<!--                                       Delete-->
<!--                                    </a>-->
                                    <a class="freeze btn btn-warning btn-sm" title="" data-freeze="<?php echo $val['freeze']?>" data-id="<?php echo $val['id'];?>">
                                        <?php echo empty($val['freeze']) ? "冻结" : "解冻"?>
                                    </a>
                                </td>
                            </tr>
						<?php }?>
                        </tbody>
                    </table>
                    <nav id="pagination">
                    </nav>
					</div>
				</div>
			</div>
		</div><!--/.row-->


    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="card">
                <div class="card-block">

                </div>
            </div>
        </div>
    </div>
    <!-- /.conainer-fluid -->
</div>
<script>

    //删除数据
    $('table').on('click', '.del', function(){
        if(confirm('确定删除?')){
            var id = $(this).attr('data-id');
            $.post("<?php echo site_url('_cms/admin/del')?>", {id:id}, function(data) {
                if(data.errCode==0){
                    toastr.success(data.errMsg);
                    location.reload();
                }else{
                    toastr.error(data.errMsg);
                }
            });
        }
    });

    // 冻结 & 解冻
    $('table').on('click', '.freeze', function(){
		var id = $(this).attr('data-id');
		var freeze = $(this).attr('data-freeze');
		var that = $(this);
		var update_freeze;

		if (freeze == 0) {
		    update_freeze = 1;
        } else {
		    update_freeze = 0;
        }

		$.post("<?php echo site_url('_cms/admin/freeze')?>", {id:id, freeze: update_freeze}, function(data) {
			if(data.errCode==0){
			    that.attr('data-freeze', update_freeze);
			    that.html(data.data);
				toastr.success(data.errMsg);
			}else{
				toastr.error(data.errMsg);
			}
		});
    });

</script>