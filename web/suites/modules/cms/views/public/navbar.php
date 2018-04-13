<body>
	<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#sidebar-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="#"><span>Procter &amp; Gamble</span></a>
				
				
				
				<ul class="user-menu">
					<li class="dropdown pull-right">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><svg class="glyph stroked male-user"><use xlink:href="#stroked-male-user"></use></svg> <?php echo $this->session->userdata('account_info')['name']?> <span class="caret"></span></a>
						<ul class="dropdown-menu" role="menu">

							<li><a href="<?php echo site_url('_cms/logout');?>"><svg class="glyph stroked cancel"><use xlink:href="#stroked-cancel"></use></svg> Logout</a></li>
						</ul>
					</li>
				</ul>
			</div>
							
		</div><!-- /.container-fluid -->
	</nav>
    
    <div id="sidebar-collapse" class="col-sm-3 col-lg-2 sidebar">
        <?php  $tree_data = $this->session->userdata ( 'tree_data' );
        
        if (count($tree_data) === 0) {
            $this->session->sess_destroy();
            redirect('_cms/login');
        }

        $set_id = $this->session->userdata('set_id') !== null ? $this->session->userdata('set_id') : 0;
        ?>
		<!--左侧菜单 s-->
		<ul class="nav menu">
            <?php foreach($tree_data as $key => $val) {?>
            <?php foreach($val as $key_one => $val_one) { if ($val_one['pid'] == 0 && $val_one['id'] != 1) {?>
            <li class="parent ">
                <a data-toggle="collapse" href="#sub-item-<?php echo $val_one['id']?>"><svg class="glyph stroked <?php echo $val_one["logo_url"]?>"><use xlink:href="#stroked-<?php echo str_replace(" ","-",$val_one["logo_url"])?>"></use></svg></span> <?php echo $val_one['module_name']?> </a>
                <ul data-set_id="<? echo $val_one['id'] ?>" class="children collapse sub-item-1<?php echo $val_one['id'] == $set_id ? ' in' : ''?>" id="sub-item-<?php echo $val_one['id']?>">
                	<?php foreach($tree_data[$val_one['id']] as $key_two => $val_two) { if (isset($tree_data[$val_two['id']])) {?>
                    <li>
                        <a data-toggle="collapse" href="#sub-item-<?php echo $val_two['id']?>"> <span data-toggle="collapse" href="#sub-item-<?php echo $val_two['id']?>"><svg class="glyph stroked <?php echo $val_two["logo_url"]?>"><use xlink:href="#stroked-<?php echo str_replace(" ","-",$val_two["logo_url"])?>"></use></svg></span> <?php echo $val_two['module_name']?> </a>
                        <ul class="children collapse sub-item-2<?php echo $val_one['id'] == $set_id ? ' in' : ''?>" id="sub-item-<?php echo $val_two['id']?>">
                        	<?php foreach($tree_data[$val_two['id']] as $key_three => $val_three) {?>
                            <li>
                                <a class="" href="<?php echo site_url($val_three['url'])?>">
                                    <svg class="glyph stroked <?php echo $val_three["logo_url"]?>">
                                        <use xlink:href="#stroked-<?php echo str_replace(" ","-",$val_three["logo_url"])?>"></use>
                                    </svg> 
                                    <?php  $arr = explode("/", $val_three['module_name']);
                                           if (count($arr) > 1) {
                                                echo $arr[0]."<i class='navbar-two-text'>".$arr[1]."</i>";
                                           } else {
                                                echo $arr[0];   
                                           }
                                    ?>
                                </a>
                            </li>
                            <?php }?>
                        </ul>
                    </li>
                    <?php } else {?>
                    <li>
                        <a class="" href="<?php echo site_url($val_two['url'])?>">
                            <svg class="glyph stroked <?php echo $val_two["logo_url"]?>">
                                <use xlink:href="#stroked-<?php echo str_replace(" ","-",$val_two["logo_url"])?>"></use>
                            </svg>
							<?php  $arr = explode("/", $val_two['module_name']);
                                   if (count($arr) > 1) {
                                        echo $arr[0]."<i class='navbar-two-text'>".$arr[1]."</i>";
                                   } else {
                                        echo $arr[0];   
                                   }
                            ?>
						</a>
                    </li>
                    <?php } }?>
                </ul>
            </li>
            <?php } }?>
            <?php }?>
		</ul><!--/.sidebar-->
    </div>
		
    		
        
    		
		

<script>

	// -----------------------------------------------------------------------------
    //菜单栏效果
    $(".nav-item>a").each(function(){
        if($(this).hasClass("active")){
            $(this).parent().parent().parent().addClass("open");
        }
    });

</script>