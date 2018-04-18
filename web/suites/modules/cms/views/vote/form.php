<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="<?php echo home_page();?>">首页</a></li>
            <li><a href="<?php echo site_url('_cms/vote/listing');?>">点餐管理</a></li>
            <li class="active"><?php echo $channel;?></li>
        </ol>
        <div class="col-md-5 charts" style="margin-left:-25px;text-align:right;">

        </div>
    </div><!--/.row-->

    <div class="row">
        <div class="col-xs-12" style="padding-top: 15px;">
            <form id="form1" action="<?php echo my_site_url('Vote/actionByAdmin'); ?>" class="form-horizontal col-sm-9" method="post">
                <div class="form-group">
                    <label for="date" class="col-sm-2 control-label">日期：</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="date" id="date" placeholder="请输入日期" readonly required value="<?php echo $endDay ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="title" class="col-sm-2 control-label">标题：</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="title" placeholder="title" value="玖晔午餐" name="title">
                    </div>
                </div>
                <div class="form-group">
                    <label for="title" class="col-sm-2 control-label">描述：</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" rows="3" name="description" style="resize:none">温馨提示：凡是请假、补休的同事，若12:30前没到公司，务必在11：00前，找我取消当天的午餐！</textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label for="single_cost" class="col-sm-2 control-label">单项餐费：</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="single_cost" placeholder="10.00" value="10.00" name="single_cost">
                    </div>
                </div>

                <hr />

                <div class="form-group">
                    <h4>投票选项</h4>
                    <label for="option" class="col-sm-2 control-label">选项：</label>
                    <div class="col-sm-10">
                        <textarea id="option" class="form-control" rows="3" name="option" style="resize:none" required></textarea>
                        <p style="color: #d43f3a">注：如需新增选项，换行添加</p>
                    </div>
                </div>

                <hr />

                <div class="form-group">
                    <h4>附加选项</h4>
                    <label for="additionOption" class="col-sm-2 control-label">附加：</label>
                    <div class="col-sm-10">
                        <textarea id="additionOption" class="form-control" rows="3" name="addition_option" style="resize:none" required>
                        </textarea>
                        <p style="color: #d43f3a">注：如需新增选项，换行添加</p>
                    </div>
                </div>

                <hr />

                <div class="form-group">
                    <h4>投票设置</h4>
                    <label for="title" class="col-sm-2 control-label">截止时间：</label>
                    <div class="col-sm-10">
                        <input type="text" name="endTime" class="form-control" id="endTime" placeholder="" readonly required value="<?php echo $endDay ?> 10:00:00">
                    </div>
                </div>
                <hr />
                <div class="col-sm-offset-2 col-sm-10" style="padding: 0px;">
                    <input type="hidden" name="action" value="saveVote">
                    <button id="formSubmitBtm" type="button" class="btn btn-primary btn-block">提交</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var additionOptionVal = "配汤\r配柠檬茶（饮料）\r无";
        $("#additionOption").val(additionOptionVal);
        $("#date").datetimepicker({
            language:  'zh-CN',
            format: "yyyy-mm-dd",
            autoclose: true,
            todayBtn: true,
            weekStart:1,
            minView:2,
        });
        $('#endTime').datetimepicker({
            language:  'zh-CN',
            format: "yyyy-mm-dd hh:ii:ss",
            autoclose: true,
            todayBtn: true,
            minuteStep: 10,
            weekStart:1,
            pickerPosition:'top-right',
        });
        $("#formSubmitBtm").click(function() {

            var regex=/^[0-9]+.?[0-9]*$/;
            var b = regex.test($("#single_cost").val());
            if(!regex.test($("#single_cost").val()) || $("#single_cost").val() == '' || $("#single_cost").val() <= 0){
                alert("请填写正确单项餐费");
                return false;
            }



            if ($("#option").val() == '') {
                alert('请输入选项');
                return false;
            }
            if ($("#additionOption").val() == '') {
                alert('请输入附加选项');
                return false;
            }

            $.ajax({
                url: $("#form1").attr('action'),
                data: $("#form1").serialize(),
                dataType: "json",
                type: "post",
                success: function (response) {
                    if (response.errCode != 0) {
                        alert(response.errMsg);
                        return false;
                    }
                    window.location.href = "<?php echo my_site_url('vote/listing') ?>";
                },
                error: function () {
                    alert("Server Error");
                },
            });
        });
    })
</script>