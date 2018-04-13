<style type="text/css">
    .statistics{
        padding-left: 0px;
    }
    .statistics li{
        list-style: none;
        padding-left: 40px;
    }
</style>
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
        <div class="col-sm-12">
            <h3>选项详情</h3>
            <hr />
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th class="text-center" style="width: 60px;">序号</th>
                    <th class="text-center" style="min-width:200px;">主食</th>
                    <th class="text-center" style="min-width:100px;">附加</th>
                    <th class="text-center" style="max-width: 250px;">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($option as $index => $item):?>
                    <tr>
                        <td class="text-center"><?php echo $index+1 ?></td>
                        <td class="text-center option"><?php echo $item['option'] ?></td>
                        <td class="text-center addition"><?php echo $item['addition'] ?></td>
                        <td class="text-center">
                            <a href="javascript:;" class="changeOptionBtn" data-optionId="<?php echo $item['id']  ?>">变更菜单</a>
                            &nbsp;&nbsp;
                            <a href="javascript:;" class="cancleOptionBtn" data-optionId="<?php echo $item['id']  ?>">删除菜单</a>
                            &nbsp;&nbsp;
                            <a href="javascript:;" class="changeLog" data-optionId="<?php echo $item['id']  ?>">代点菜单</a>
                        </td>
                    </tr>
                <?php endforeach;?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12" style="padding-top: 15px;">
            <h3>点餐详情</h3>
            <hr />
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th class="text-center" style="width: 60px;">序号</th>
                    <th class="text-center" style="width: 80px;">头像</th>
                    <th class="text-center">微信昵称</th>
                    <th class="text-center" style="min-width:200px;">选项</th>
                    <th class="text-center" style="min-width:100px;">附加</th>
                    <th class="text-center" style="min-width: 300px;">特殊要求（双击栏目修改）</th>
                    <th style="width: 180px;">订餐时间</th>
                    <th class="text-center">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($userLog as $index => $item):?>
                    <tr>
                        <td class="text-center"><?php echo $index+1 ?></td>
                        <td><img src="<?php echo $item['head_img'] ?>" class="img-responsive"> </td>
                        <td class="text-center"><?php echo $item['nickname'] ?></td>
                        <td class="text-center"><?php echo $item['option'] ?></td>
                        <td class="text-center"><?php echo $item['addition'] ?></td>
                        <td class="text-center description">
                            <p class="descriptionP"><?php echo $item['description'] ?></p>
                            <form class="form1 hide form-inline">
                                <div class="form-group">
                                    <input name="description" type="text" class="form-control" value="" />
                                    <input name="logId" type="hidden" value="<?php echo $item['logId'] ?>">
                                </div>
                                <button type="button" class="btn btn-primary formSubmitBtn">提交</button>
                                <button type="button" class="btn btn-primary formCancleBtn">取消</button>
                            </form>
                        </td>
                        <td><?php echo $item['create_time'] ?></td>
                        <td class="text-center"><a data-logId="<?php echo $item['logId'] ?>" class="removeLogBtn" href="javascript:;">取消菜单</a></td>
                    </tr>
                <?php endforeach;?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <h3>统计</h3>
            <hr />
            <ul class="statistics">
                <h4>主食：</h4>
                <?php foreach ($statistics['optionStatistics'] as $index => $item):?>
                    <li><?php echo $item['option'] ?> : <?php echo $item['total'] ?>份</li>
                <?php endforeach;?>
            </ul>
            <ul class="statistics">
                <h4>附加：</h4>
                <?php foreach ($statistics['additionStatistics'] as $index => $item):?>
                    <li><?php echo $item['addition'] ?> : <?php echo $item['total'] ?>份</li>
                <?php endforeach;?>
            </ul>
        </div>
    </div>
</div>

<div class="modal fade" id="changeOptionModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form id="changeOptionForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">更改菜单</h4>
                    <small style="color: red;">注意：此操作相当于删除了菜单再创建一份新的菜单,请谨慎操作！！！</small>
                </div>
                <div class="modal-body">
                    <div>
                        <h4>旧数据：</h4>
                        <p >主食：<span id="oldMain">A南瓜焖排骨饭</span>	</p>
                        <p >附加：<span id="oldAddition">配汤</span></p>
                    </div>
                    <hr />

                    <h4>新数据：</h4>
                    <div class="form-group">
                        <label for="option">主食</label>
                        <input type="text" class="form-control" name="option" id="option">
                    </div>
                    <div class="form-group">
                        <label for="addition">附加</label>
                        <input type="text" class="form-control" name="addition" id="addition">
                    </div>

                </div>
                <div class="modal-footer">
                    <input type="hidden" name="optionId" value="">
                    <input type="hidden" name="action"   value="changeOption">
                    <button id="submitChangeOptionBtn" type="button" class="btn btn-primary">确认</button>
                    <button id="cancleChangeOptionBtn" type="button" class="btn btn-default">取消</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="changeLogModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form id="changeLogForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">代点菜单</h4>
                    <!--                    <small style="color: red;">注意：此操作相当于删除了菜单再创建一份新的菜单,请谨慎操作！！！</small>-->
                </div>
                <div class="modal-body">
                    <p>菜式：<span id="oldLog">A辣子鸡饭 配汤</span></p>
                    <table class="table table-border">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th width="80px;">头像</th>
                            <th>昵称</th>
                            <th>原选择</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>

                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="optionId">
                    <input type="hidden" name="action" value="saveUserLog">
                    <input type="hidden" name="voteId" value="<?php echo  $voteId ?>">
                    <button id="submitChangeLogBtn" type="button" class="btn btn-primary">确认</button>
                    <button id="cancleChangeLogBtn" type="button" class="btn btn-default">取消</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $(".changeLogBtn").click(function(){
            $("#myModal").modal('show');
        }) ;

        $(".description").dblclick(function(){
            if(!$(this).find('.form1').hasClass('hide') && $(".form1:not('.hide')").length > 0){
                alert("请先保存");
                return false;
            }
            $(this).find("[name='description']").val($(this).find(".descriptionP").text());
            $(this).find("p").addClass('hide');
            $(this).find(".form1").removeClass('hide');
        })

        $(".formSubmitBtn").click(function(){
            var obj = $(this).closest('.form1');
            var logId = obj.find("[name='logId']").val();
            var description = obj.find("[name='description']").val();
            $.ajax({
                url: "<?php echo site_url('_cms/vote/actionByAdmin'); ?>",
                data:{'logId':logId,'description':description,'action':'saveLogDescription'},
                dataType:'json',
                type:'post',
                success:function (response) {
                    window.location.reload();
                },
                error:function (e) {
                    console.log(e);
                    alert("系统繁忙，请稍后再试");
                    return false;
                }
            });
        })
        //取消特殊要求
        $(".formCancleBtn").click(function(){
            var tr = $(this).closest('tr');
            tr.find('.form1').addClass('hide');
            tr.find('.descriptionP').removeClass('hide');
        });

        //
        $(".changeOptionBtn").click(function(){
            var tr = $(this).closest('tr');
            $("#changeOptionModal").find("#oldMain").text(tr.find(".option").text());
            $("#changeOptionModal").find("#oldAddition").text(tr.find(".addition").text());
            $("#changeOptionModal").find("[name='option']").val('');
            $("#changeOptionModal").find("[name='addition']").val('');
            $("#changeOptionModal").find("[name='optionId']").val($(this).attr('data-optionId'));
            $("#changeOptionModal").modal('show');
        });

        //取消菜单
        $(".cancleOptionBtn").click(function(){
            if(!confirm('此操作会让已经选择该菜单的用户需要重新选择，请确认')){
                return false;
            }
            $.ajax({
                url:"<?php echo site_url('_cms/vote/actionByAdmin'); ?>",
                data:{'optionId':$(this).attr('data-optionId'),'action':'cacnleOption'},
                dataType:'json',
                type:"post",
                success:function(response){
                    window.location.reload();
                },

            });
        });

        //
        $("#cancleChangeOptionBtn").click(function(){
//           if(!confirm('确认操作会让填写的内容全部丢失')){
//               return false;
//           }
            $("#changeOptionModal").modal('hide');
        });
        //更改菜单
        $("#submitChangeOptionBtn").click(function(){
            $.ajax({
                url:"<?php echo site_url('_cms/vote/actionByAdmin'); ?>",
                data:$("#changeOptionForm").serialize(),
                dataType:"json",
                type:"post",
                success:function(response){
                    window.location.reload();
                }
            });
        });

        //代点菜单弹窗
        $(".changeLog").click(function(){
            var tr = $(this).closest('tr');
            $("#changeLogForm [name='optionId']").val($(this).attr('data-optionId'));
            $("#changeLogForm #oldLog").text(tr.find('.option').text()+" "+tr.find('.addition').text());
            $.ajax({
                url:"<?php echo site_url('_cms/vote/actionByAdmin'); ?>",
                data:{'voteId':"<?php echo $voteId ?>","action":"getUserLog"},
                dataType:"json",
                type:"post",
                success:function(response){
                    var data = response.data;

                    var tbodyHtml = '';
                    $(data).each(function(){
                        tbodyHtml += "<tr>";
                        tbodyHtml += '<td><input name="openId[]" type="checkbox" value="'+this.openId+'"></td>';
                        tbodyHtml += "<td><img src='"+this.head_img+"' class='img-responsive' /></td>";
                        tbodyHtml += "<td>"+this.nickname+"</td>";
                        if(this.addition_option != null){
                            tbodyHtml += "<td>"+this.addition_option+"</td>";
                        }else{
                            tbodyHtml += "<td></td>";
                        }

                        tbodyHtml += "</tr>";
                    });
                    $("#changeLogForm tbody").html(tbodyHtml);

                }
            })
            $("#changeLogModal").modal('show');
        });
        //代点菜单弹窗关闭
        $("#cancleChangeLogBtn").click(function(){
            $("#changeLogModal").modal('hide');
        });

        //提交代点菜单表单
        $("#submitChangeLogBtn").click(function(){

            $.ajax({
                url:"<?php echo site_url('_cms/vote/actionByAdmin'); ?>",
                data:$("#changeLogForm").serialize(),
                dataType:"json",
                type:"post",
                success:function(response){
                    if(response.errCode!=0){
                        alert(response.errMsg);
                        return false;
                    }
                    window.location.reload();
                }
            })
        });

        $(".removeLogBtn").click(function(){
            if(!confirm('确认取消？！'))  return false;

            var logId = $(this).attr('data-logId');


            $.ajax({
                url:"<?php echo site_url('_cms/vote/actionByAdmin'); ?>",
                data:{"action":"removeLog","logId":logId},
                dataType:"json",
                type:"post",
                success:function(response){
                    if(response.errCode!=0){
                        alert(response.errMsg);
                        return false;
                    }
                    window.location.reload();
                }
            })
        });
    });




    document.onkeyup = function (e) {
        var code = e.charCode || e.keyCode;
        switch (code){
            case 13 :
                break;
        }
    }
</script>