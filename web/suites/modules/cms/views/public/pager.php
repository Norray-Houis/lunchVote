<nav aria-label="Page navigation" class="pull-right">
    <ul class="pagination">
        <li>
            <a href="<?php echo $url."/1";?>" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>

        <?php $totalPage = ceil($totalRow/$pageInfo['limit'])?>

        <?php for ($i=1;$i<=$totalPage;$i++){?>
            <li class="<?php echo $i==$page?'active':''; ?>"><a href="<?php echo $url."/".$i; ?>" ><?php echo $i ?></a></li>
        <?php }?>
        <li>
            <a href="<?php echo $url."/".$totalPage;?>" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    </ul>
</nav>