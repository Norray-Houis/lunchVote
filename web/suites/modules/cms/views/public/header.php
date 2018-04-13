<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>PG_CMS</title>
<base href="<?php echo THEMEURL;?>" />

<link href="css/bootstrap-responsive.min.css" rel="stylesheet">
<link href="css/bootstrap-modal.css" rel="stylesheet">
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/datepicker3.css" rel="stylesheet">
<link href="css/bootstrap-select.css" rel="stylesheet">
<link href="css/daterangepicker.css" rel="stylesheet">
<link href="css/styles.css" rel="stylesheet">
<link rel="stylesheet" href="css/toastr.css" />



<script src="js/jquery-1.11.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/chart.min.js"></script>
<!--<script src="js/chart-data.js"></script>-->
<!--<script src="js/easypiechart.js"></script>-->
<!--<script src="js/easypiechart-data.js"></script>-->
<script src="js/bootstrap-datepicker.js"></script>
<script src="js/bootstrap-select.js"></script>
<script src="js/moment.js"></script>
<script src="js/daterangepicker.js"></script>
<script src="js/lumino.glyphs.js"></script>
<script src="js/bootstrap-modalmanager.js"></script>
<script src="js/bootstrap-modal.js"></script>
<script src="js/toastr.min.js"></script>

<!-- dateTimePicker -->
<link rel="stylesheet" type="text/css" href=" dateTimePicker/css/bootstrap-datetimepicker.css "/ >
<script type="text/javascript" src="dateTimePicker/js/bootstrap-datetimepicker.js " charset="UTF-8"></script>
<script type="text/javascript" src="dateTimePicker/js/locales/bootstrap-datetimepicker.zh-CN.js "></script>

 <style>
     .table>tbody>tr>td{vertical-align: middle;}
     .sub-item-2 li a{padding-left: 60px!important;}

</style>

<script>

toastr.options = {
    "closeButton": true,
    "debug": false,
    "progressBar": false,
    "positionClass": "toast-bottom-center",
    "onclick": null,
    "showDuration": "1000",
    "hideDuration": "1000",
    "timeOut": "1000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
}

<?php $msg = $this->session->flashdata ( 'message' );if (count ( $msg ) > 0 && ! empty($msg)) {?>
switch("<?php echo $msg['type'];?>"){
    case 'error':{toastr.error("<?php echo $msg['content'];?>");};break;
    case 'success':{toastr.success("<?php echo $msg['content'];?>");};break;
}
<?php }?>

</script>
 
<!--弹窗 s-->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">  
    <div class="modal-dialog" role="document">  
        <div class="modal-content">  
            <div class="modal-header">  
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">  
                    <span aria-hidden="true">×</span>  
                </button>  
                <h4 class="modal-title" id="myModalLabel">标题</h4>  
            </div>  
            <div class="modal-body">  
                <p>内容</p>  
            </div>  
            <div class="modal-footer">  
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>  
                <button type="button" class="btn btn-primary">Save</button>  
            </div>  
        </div>  
    </div>  
</div>  
<!--弹窗 e-->