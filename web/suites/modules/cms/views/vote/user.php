<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="<?php echo home_page();?>">首页</a></li>
            <li>点餐管理</li>
            <li class="active"><?php echo $channel;?></li>
        </ol>
        <div class="col-md-5 charts" style="margin-left:-25px;text-align:right;">

        </div>
    </div><!--/.row-->
    <div class="row">
        <div class="col-xs-12" style="padding-top: 15px;">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 100px;">序号</th>
                        <th style="width: 80px;">头像</th>
                        <th>昵称</th>
                        <th>备注</th>
                        <th>部门</th>
                        <th>职位</th>
                        <th>联系电话</th>
                        <th class="text-center" style="width: 100px;">点餐权限</th>
                        <th class="text-center" style="width: 300px;">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($userList as $index => $item):?>
                        <tr>
                            <td class="text-center"><?php echo $index+1 ?></td>
                            <td><img src="<?php echo $item['head_img'] ?>" class="img-responsive" ></td>
                            <td class="nickName"><?php echo $item['nickname'] ?></td>
                            <td class="remark"><?php echo $item['remark'] ?></td>
                            <td class="department"><?php echo $item['department'] ?></td>
                            <td class="position"><?php echo $item['position'] ?></td>
                            <td class="telephone"><?php echo $item['telephone'] ?></td>
                            <td class="text-center"><?php echo $item['vote_access']==1?"开":"关"; ?></td>
                            <td class="text-center">
                                <a data-openId="<?php echo $item['openId'] ?>" data-access="1" href="javascript:;" class="changeBtn">开放权限</a>
                                &nbsp;&nbsp;&nbsp;
                                <a data-openId="<?php echo $item['openId'] ?>" data-access="0" href="javascript:;" class="changeBtn">关闭权限</a>
                                &nbsp;&nbsp;
                                <a data-openId="<?php echo $item['openId'] ?>" class="userMsgBtn" href="javascript:;">修改职位信息</a>
                            </td>
                        </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="userMsgModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden=”true” data-backdrop=”static”>
    <div class="modal-dialog" role="document">
        <form id="userMsgForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">用户信息</h4>
                </div>
                <div class="modal-body">

                        <div class="form-group">
                            <label for="nickName">昵称</label>
                            <input type="text" class="form-control" id="nickName" value="" name="nickName" disabled>
                        </div>
                        <div class="form-group">
                            <label for="remark">备注</label>
                            <input type="text" class="form-control" id="remark" value="" name="remark">
                        </div>
                        <div class="form-group">
                            <label for="department">部门</label>
                            <input type="text" class="form-control" id="department" value="" name="department">
                        </div>
                        <div class="form-group">
                            <label for="position">职位</label>
                            <input type="text" class="form-control" id="position" value="" name="position">
                        </div>
                        <div class="form-group">
                            <label for="telephone">联系电话</label>
                            <input type="text" class="form-control" id="telephone" value="" name="telephone">
                        </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="openId">
                    <input type="hidden" name="action" value="saveUserMsg">
                    <button id="submitUserLogBtn" type="button" class="btn btn-primary">确认</button>
                    <button id="cancleUserLogBtn" type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
       $(".changeBtn").click(function(){
           $.ajax({
              url:"<?php echo site_url('_cms/vote/changeAccess') ?>",
              data:{"openId":$(this).attr('data-openId'),'access':$(this).attr('data-access')},
              dataType:"json",
               type:"post",
               success:function (response) {
                   console.log(response);
                   window.location.reload();
               }
           });
       });
       $(".userMsgBtn").click(function(){
           var tr = $(this).closest('tr');
           $("#userMsgModal [name='openId']").val($(this).attr('data-openId'));
           $("#userMsgModal [name='nickName']").val(tr.find('.nickName').text());
           $("#userMsgModal [name='department']").val(tr.find('.department').text());
           $("#userMsgModal [name='position']").val(tr.find('.position').text());
           $("#userMsgModal [name='telephone']").val(tr.find('.telephone').text());
           $("#userMsgModal [name='remark']").val(tr.find('.remark').text());
           $("#userMsgModal").modal('show');
       });

       $("#submitUserLogBtn").click(function(){
           $.ajax({
               url:"<?php echo site_url('_cms/vote/actionByAdmin') ?>",
               data:$("#userMsgForm").serialize(),
               dataType:"json",
               type:"post",
               success:function(response){
                    if(response.errCode != 0){
                       alert(response.errMsg);
                       return false;
                   }
                   window.location.reload();
               },
           });
       });
    });
</script>