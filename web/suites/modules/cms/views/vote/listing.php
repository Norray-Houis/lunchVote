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
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>序号</th>
                    <th>日期</th>
                    <th>截止时间</th>
                    <th>参与人数</th>
                    <th width="150px">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($voteList as $index => $item):?>
                    <tr>
                        <td><?php echo $index+1 ?></td>
                        <td><?php echo $item['date'] ?></td>
                        <td><?php echo $item['endTime'] ?></td>
                        <td><?php echo $item['total'] ?></td>
                        <td>
                            <a href="<?php echo my_site_url('vote/details').'/'.$item['id'] ?>">详情</a>&nbsp;&nbsp;&nbsp;
                            <a href="<?php echo my_site_url('vote/exportVoteDetails').'/'.$item['id'].'/'.$item['date'] ?>" >导出Excel</a>
                        </td>
                    </tr>
                <?php endforeach;?>
                </tbody>
            </table>
        </div>
    </div>
</div>