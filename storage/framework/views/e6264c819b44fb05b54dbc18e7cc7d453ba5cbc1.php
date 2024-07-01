<div class="theme-table theme-table-responsive">
	<table class="table mb-0">
		<thead>
			<tr>
				<th>Date</th>
				<th>Type</th>
				<th>Day</th>
			</tr>
		</thead>
		<tbody>
			<?php if(!empty($bookingDetails)): ?>
			<?php $__currentLoopData = $bookingDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<tr>
				<td data-label="Date">
					<?php echo e(date(Config::get("Reading.date_format"),strtotime($detail->booking_date))); ?>

				</td>
				<td data-label="Time">
					<?php echo e(!empty($detail->from_time) ? date('h:i a', strtotime($detail->from_time)):''); ?> - <?php echo e(!empty($detail->to_time) ? date('h:i a', strtotime($detail->to_time)) :''); ?>

				</td>
				<td data-label="Day">
					<?php echo e($detail->day); ?>

				</td>
			</tr>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			<?php else: ?> 
			<tr>
				<td colspan="3" class="text-center">No Record Found.</td>
			</tr>
			<?php endif; ?>
		</tbody>
	</table>
</div><?php /**PATH F:\xampp\htdocs\tinyhugs\resources\views/front/pages/booking_details.blade.php ENDPATH**/ ?>