
<?php $__env->startSection('content'); ?>


<div class="main-workspace mb-4">
<div class=" backg-img padding-section h-100">
	<div class="container">
	<div class="heading py-lg-5 pb-3  text-center">

		<h4>Payment settings</h4>
	</div>

	<div class="">


	<div class="theme-table theme-table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                           
                                    <th class="text-center">Brand</th>
                                    <th class="text-center">Country</th>
                                    <th class="text-center">Expiry Month</th>
									<th class="text-center">Expiry Year </th>
                                    <th class="text-center">Type</th>
                                    <th class="text-center">Card Number</th>
									<th class="text-right">Action</th>
                                </tr>
                            </thead>
							<?php if(!empty($customer)): ?>
			<?php $__currentLoopData = $customer; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tbody>
                                <tr>
                                
									<td data-label="Brand" class="text-center"><?php echo e(!empty($data->brand) ? $data->brand:''); ?></td>
				<td data-label="Country" class="text-center"> <?php echo e(!empty($data->country) ? $data->country:''); ?></td>
				<td data-label="Expiry Month" class="text-center"><?php echo e(!empty($data->exp_month) ? $data->exp_month:''); ?></td>
				<td  data-label="Expiry Year" class="text-center"><?php echo e(!empty($data->exp_year) ? $data->exp_year:''); ?></td>
				<td data-label="Type" class="text-center"><?php echo e(!empty($data->funding) ? $data->funding:''); ?></td>
				<td data-label="Card Number" class="text-center"><?php echo e(!empty($data->last4) ? '************'.$data->last4:''); ?></td>
				<td data-label="Action" class="text-right">
					<input type="hidden" name="card_id" value="<?php echo e($data->id); ?>" class="card_id">
					<input class=' btn btn-theme' type='button' value='Update' id='updateCard'/>
				</td>

                                </tr>
                          
                            </tbody>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			<?php endif; ?>
                        </table>
                    </div>
		<!-- <table style="width:100%">
			<thead>
				<th>Brand</th>
				<th>Country</th>
				<th>Expiry Month</th>
				<th>Expiry Year</th>
				<th>Type</th>
				<th>Card Number</th>
			</thead>
			<?php if(!empty($customer)): ?>
			<?php $__currentLoopData = $customer; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<tbody>
				<td><?php echo e(!empty($data->brand) ? $data->brand:''); ?></td>
				<td> <?php echo e(!empty($data->country) ? $data->country:''); ?></td>
				<td><?php echo e(!empty($data->exp_month) ? $data->exp_month:''); ?></td>
				<td><?php echo e(!empty($data->exp_year) ? $data->exp_year:''); ?></td>
				<td><?php echo e(!empty($data->funding) ? $data->funding:''); ?></td>
				<td><?php echo e(!empty($data->last4) ? '************'.$data->last4:''); ?></td>
				<td>
					<input type="hidden" name="card_id" value="<?php echo e($data->id); ?>" class="card_id">
					<input class='btn btn-primary' type='button' value='Update' id='updateCard'/>
				</td>

			</tbody>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			<?php endif; ?>
		</table> -->

	</div>
	
	<div class="py-3 payment-setting-checkbx">
<!-- 
		Weekly Recurring Schedule -->
		<!-- <input class="" id="styled-checkbox-1" type="checkbox" value="value1" hidden> -->

		<?php echo e(Form::checkbox('weekly_recurring',1,(Auth::user()->weekly_recurring == 1 ? true : false), array('id'=>'styled-checkbox-1','class'=>'styled-checkbox'))); ?>


		Weekly Recurring Schedule 
    <label for="styled-checkbox-1"></label>

<!-- 	
		<?php echo e(Form::checkbox('weekly_recurring',1,(Auth::user()->weekly_recurring == 1 ? true : false), array('id'=>'weekly_recurring'))); ?> -->


	</div>
	</div>
</div>	</div>
<script type="text/javascript">
	
	$('#styled-checkbox-1').click(function() {
		var statusValue = $('#styled-checkbox-1').is(":checked");
		
		$.ajax({
			type: "POST",
			url: '<?php echo e(URL("weekly-recurring-status")); ?>',
			data: {
				"_token": "<?php echo e(csrf_token()); ?>",
				"status": statusValue
			},
			success: function(data) {
				window.location.reload();
				// show_message(data.message,'success');

			},
			error: function() {
			}
		});

		
	});

	$('#updateCard').click(function() {
		var card_id = $('.card_id').val();
		
		$.ajax({
			type: "POST",
			url: '<?php echo e(URL("update-card")); ?>',
			data: {
				"_token": "<?php echo e(csrf_token()); ?>",
				card_id:card_id,
			},
			success: function(data) {
				window.location.reload();
				show_message(data.message,'success');

			},
			error: function() {
			}
		});

		
	});

</script>



<?php $__env->stopSection(); ?>

<?php echo $__env->make('front.dashboard.layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\xampp\htdocs\tinyhugs\resources\views/front/users/payment_setting.blade.php ENDPATH**/ ?>