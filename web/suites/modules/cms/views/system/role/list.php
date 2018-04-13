<!-- Main content -->


<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">	
			
		<div class="row">
			<ol class="breadcrumb">
				<li><a href="<?php echo home_page();?>">首页</a></li>
                <li><a href="<?php echo site_url('admin/get_list');?>">权限管理</a></li>
                <li class="active"><?php echo $channel;?></li>
			</ol>
          
		</div><!--/.row-->
    

    <div class="row">
			<div class="col-lg-12">
				<div class="panel panel-default">
					<div class="panel-heading"><?php echo $channel;?>
					    <div class="pull-right">
                           <a  class="btn btn-success" href="<?php echo site_url('_cms/role/edit')?>">新增</a>
                        </div>
					</div>
					<div class="panel-body">
                      <table class="table table-striped table-bordered datatable">

                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>角色</th>
                                <th>创建时间</th>
                                <th>最近更新</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach( $roles as $val ) {?>
                            <tr>
                                <td><?php echo $val['id']?></td>
                                <td><?php echo $val['name']?></td>
                                <td><?php echo $val['created_at']?></td>
                                <td><?php echo $val['updated_at']?></td>
                                <td>
                                    <a class="btn btn-info btn-sm" title="编辑" href="<?php echo site_url('_cms/role/edit?id='.$val['id'])?>">
                                        Edit
                                    </a>
                                    <a class="btn btn-danger btn-sm del" title="删除" data-id="<?php echo $val['id'];?>">
                                        Delete
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
    </div>
    <!-- /.conainer-fluid -->
</div>
<script>
        
    //删除数据
    $('table').on('click', '.del', function(){
        if(confirm('确定删除?')){
            var id = $(this).attr('data-id');
            $.post("<?php echo site_url('_cms/role/del')?>", {id:id}, function(data) {
                if(data.errCode==0){
                    toastr.success(data.errMsg);
                    location.reload();
                }else{
                    toastr.error(data.errMsg);
                }
            });
        }
    });

</script> 