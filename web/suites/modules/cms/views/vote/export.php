<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="<?php echo home_page();?>">首页</a></li>
            <li class="active">报表导出</li>
        </ol>
        <div class="col-md-5 charts" style="margin-left:-25px;text-align:right;">

        </div>
    </div><!--/.row-->

    <div class="row">
        <div class="col-xs-12">
            <h3>报表导出</h3>
            <hr />
            <form action="<?php echo my_site_url('vote/exportExcel') ?>" class="form-inline" method="post">
                <div class="form-group">
                    <label for="startTime">开始时间</label>
                    <input type="text" class="form-control" id="startTime" name="startTime" value="<?php echo date('Y-m-d')?>">
                </div>
                <div class="form-group" style="margin: 0px 5px;">—</div>
                <div class="form-group">
                    <label for="endTime">结束时间</label>
                    <input type="text" class="form-control" id="endTime" name="endTime" value="<?php echo date('Y-m-d')?>">
                </div>

                <button type="submit" class="btn btn-default">导出</button>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $("#startTime").datetimepicker({
            language:  'zh-CN',
            format: "yyyy-mm-dd",
            autoclose: true,
            todayBtn: true,
            weekStart:1,
            minView:2,
        }).on('changeDate',function(e){
            var date = $(this).val();
            $('#endTime').datetimepicker('setStartDate', date);
        });
        $('#endTime').datetimepicker({
            language:  'zh-CN',
            format: "yyyy-mm-dd",
            autoclose: true,
            todayBtn: true,
            weekStart:1,
            minView:2,
        }).on('changeDate',function(e){
            var date = $(this).val();
            $('#startTime').datetimepicker('setEndDate', date);
        });
    });
</script>