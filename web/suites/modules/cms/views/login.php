<body>
<div class="container">
    <div class="row">
		<div class="col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-md-4 col-md-offset-4">
			<div class="login-panel panel panel-default">
                <form id="form">
				<div class="panel-heading">Log in</div>
				<div class="panel-body">
					<form role="form">
						<fieldset>
							<div class="form-group">
								<input class="form-control" placeholder="Username" name="name" type="text" autofocus="" value="admin">
							</div>
							<div class="form-group">
								<input class="form-control" placeholder="Password" name="password" type="password" value="123456">
                                <input type="hidden" name="act" value="signin" />
							</div>
							<div class="checkbox">
								<label>
									<input name="remember" type="checkbox" value="Remember Me">Remember Me
								</label>
							</div>
							<a type="button" class="btn btn-primary" id="sbm">Login</a>
						</fieldset>
					</form>
				</div>
                </form>
			</div>
		</div><!-- /.col-->
	</div><!-- /.row -->	
    
        
</div>
</body>
</html>

<script>

	// 居中
    $(document).ready(function()
    {
    	verticalAlignMiddle();
        $(window).bind('resize', verticalAlignMiddle);
        
    	function verticalAlignMiddle()
        {
            var bodyHeight = $(window).height();
            var formHeight = $('.vamiddle').height();
            var marginTop = (bodyHeight / 2) - (formHeight / 2);
            if (marginTop > 0)
            {
                $('.vamiddle').css('margin-top', marginTop);
            }
        }
        
    });

    
    // 登录
    $('#sbm').click( function(){
    	$(this).html('正在登录...');
    	var login = $(this);
        $.post("<?php echo my_site_url('login/signin')?>", $('#form').serialize(), function(data) {
            if(data.errCode==0){
                location.href = "<?php echo my_site_url('admin/listing')?>";
            }else{
                toastr.error(data.errMsg);
                login.html('登录');
            }
        });
    });
    

    // 回车登录
    document.onkeydown = function(event) {
		var e = event || window.event || arguments.callee.caller.arguments[0];
		if(e.keyCode == 13) {
			$('#sbm').click();
		}
    }
    

</script>