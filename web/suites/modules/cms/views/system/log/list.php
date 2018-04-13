<!-- Main content -->

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">

    <div class="row">
        <ol class="breadcrumb">
            <li><a href="<?php echo home_page();?>">首页</a></li>
            <li><a href="<?php echo site_url('_cms/log/listing');?>">系统管理</a></li>
            <li class="active"><?php echo $channel;?></li>
        </ol>
        <div class="col-md-5 charts" style="margin-left:-25px;text-align:right;">

        </div>
    </div><!--/.row-->


    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading"><?php echo $channel;?></div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>时间</label>
                                <input type="text" id="dateTimeRange" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>用户</label>
                                <input class="form-control" name="user" id="user" placeholder="用户">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <span class="input-group-btn">
									<button type="button" class="btn btn-md btn-primary" id="search">搜索</button>
								</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <table class="table table-striped table-bordered datatable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>用户</th>
                            <th>操作时间</th>
                            <th>最近登录 IP</th>
                            <th>操作内容描述</th>
                        </tr>
                        </thead>
                        <tbody id="list_body">

                        </tbody>
                    </table>
                    <nav id="pagination" class="pull-right">
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
    </main>
    <script src="js/common.js"></script>
    <script>
        var cur_page = 1;
        var is_search = '';
        var date = new Date();
        var year = date.getFullYear();
        var month = date.getMonth() + 1 > 9 ? date.getMonth() + 1 : '0' + (date.getMonth() + 1);
        var day = date.getDate() > 9 ? date.getDate() : '0' + date.getDate();
        var begindate = enddate = month + '/' + day + '/' + year;

        function search_data()
        {
            var params = {
                "date": $('#dateTimeRange').val(),
                "user": $('#user').val(),
                "cur_page": cur_page,
                "is_search": is_search
            };

            var site_url = '<?php echo site_url('_cms/activity/edit/id/');?>';
            $.post('<?php echo site_url('_cms/log/listing_data')?>', params, function(data){
                if (data.errCode == 0) {
                    var html = '';
                    var res = data.data.listing;
                    var len = res.length;

                    for(var i = 0; i < len; i ++) {
                        html += '<tr>';
                        html += '<td>' + res[i].id+ '</td>';
                        html += '<td>' + (res[i].name == null ? '' : res[i].name) + '</td>';
                        html += '<td>' + (res[i].log_time == null ? '' : res[i].log_time) + '</td>';
                        html += '<td>' + (res[i].ip == null ? '' : res[i].ip)+ '</td>';
                        html += '<td>' + (res[i].log_content == null ? '' : res[i].log_content) + '</td>';
                        html += '</tr>';
                    }

                    if (! html) {
                        html = '<tr><td colspan="5">没有数据</td></tr>';
                    }

                    $('#list_body').html(html);

                    $('#pagination').html(data.data.pages);
                } else {
                    toastr.error('error');
                }
            });
        }

        $(function(){
            search_data();
        });

        $('#search').click(function() {
            is_search = 'is_search';
            cur_page = 1;
            search_data();
        })

    </script>
    <script src="js/report.js"></script>