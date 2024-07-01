<?php
	$link_limit = 6; 
?>
<?php if($paginator->lastPage() > 1): ?>
<ul class="pagination custom_pagination">
    <li class="<?php echo e(($paginator->currentPage() == 1) ? ' disabled' : ''); ?>">
        <a href="<?php echo e(($paginator->currentPage() == 1) ? 'javascript:void(0)' : $paginator->url($paginator->currentPage()-1)); ?>">Prev</a>
    </li>
    <?php for($i = 1; $i <= $paginator->lastPage(); $i++): ?>
          <?php
            $half_total_links = floor($link_limit / 2);
            $from = $paginator->currentPage() - $half_total_links;
            $to = $paginator->currentPage() + $half_total_links;
            if ($paginator->currentPage() < $half_total_links) {
               $to += $half_total_links - $paginator->currentPage();
            }
            if ($paginator->lastPage() - $paginator->currentPage() < $half_total_links) {
                $from -= $half_total_links - ($paginator->lastPage() - $paginator->currentPage()) - 1;
            }
            ?>
            <?php if($from < $i && $i < $to): ?>
                <li class="<?php echo e(($paginator->currentPage() == $i) ? ' active' : ''); ?>">
                    <a href='<?php echo e($paginator->url("$i")); ?>'><?php echo e($i); ?></a>
                </li>
            <?php endif; ?>
     <?php endfor; ?>
    <li class="<?php echo e(($paginator->currentPage() == $paginator->lastPage()) ? ' disabled' : ''); ?>">
        <a href="<?php echo e(($paginator->currentPage() == $paginator->lastPage()) ? 'javascript:void(0)' : $paginator->url($paginator->currentPage()+1)); ?>" >Next</a>
    </li>
</ul>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\tinyhugs\resources\views/pagination/front.blade.php ENDPATH**/ ?>