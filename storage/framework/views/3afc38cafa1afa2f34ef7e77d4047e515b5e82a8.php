<?php if($results->isNotEmpty()): ?>
<?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="col-md-12">
	<div class="bg-white block-inner ">
		<div class="col">

			<div class="text-block ">
				<div class="d-flex align-items-center">
					<span>
						<?php if(!empty($result->photo_id)): ?>
						<img src="<?php echo e(WEBSITE_URL.'image.php?width=148&height=148&cropratio=3:3&image='.USER_IMAGE_URL.$result->photo_id); ?>" height="148" width="148">
						<?php else: ?>
						<img src="<?php echo e(WEBSITE_URL.'image.php?width=148&height=148&cropratio=3:3&image='.WEBSITE_IMG_URL.'no-image.png'); ?>" alt="">
						<?php endif; ?>

					</span>
					<div class="pl-4">
						<h2> <?php echo e($result->name); ?></h2>
						<div class="date">
							<?php echo e(date(Config::get('Reading.date_format'),strtotime($result->booking_date))); ?>

						</div>
						<div class="sdate"> Start Date:
							<span class="badge ">
								<?php echo e(date('m/d/Y', strtotime($result->start_date))); ?>

							</span>

						</div>
						<div class="sdate"> End Date:
							<span class="badge ">
								<?php echo e(date('m/d/Y', strtotime($result->end_date))); ?>

							</span>

						</div>

						<div class="status"> status:
							<?php if($result->status == 0): ?>
							<span class="badge badge-warning">
								Approval Pending 
							</span>
							<?php elseif($result->status == 1): ?>
							<span class="badge badge-success">
								Booking  Accepted
							</span>
							<?php elseif($result->status == 2): ?>
							<span class="badge badge-info">
								Booking  Stopped
							</span>
							<?php elseif($result->status == 3): ?>
							<span class="badge badge-danger">
								Booking Rejected
							</span>
							<?php endif; ?>
						</div>
					</div>
				</div>

			</div>

		</div>
		<div class="col-sm-auto">
			<div class="btn-block pt-3 pt-md-0">

				<?php if($type=='nanny'): ?>
				<?php if($result->status==0): ?>
				<a class=" btn btn-theme btn-view" onclick="statusApproved(<?php echo e($result->id); ?>)">Approve</a>
				<a class=" btn btn-theme btn-stop" onclick="statusRejected(<?php echo e($result->id); ?>)">Reject </a>
				<?php endif; ?>
				<a class=" btn btn-theme btn-view " class="btn-theme mw-100" data-toggle="modal"
				data-target="#exampleModalLong12">View</a>
				<?php if($result->status==1): ?>    
				<a class=" btn btn-theme btn-stop">Stop </a>
				<?php endif; ?>
				<?php if($result->status==2): ?> 
				<a class=" btn btn-theme btn-start">Restart</a>
				<?php endif; ?>

				<?php elseif($type=='client'): ?>
				<?php if($result->status==0): ?>
				<a class=" btn btn-theme btn-view nanny_details" class="btn-theme mw-100 "
				id="<?php echo e($result->nanny_id); ?>">View</a>
				<?php endif; ?>
				<?php if($result->status == 1): ?>    
				<a class=" btn btn-theme btn-stop">Stop </a>
				<?php endif; ?>
				<?php if($result->status == 2): ?>
				<a class=" btn btn-theme btn-start">Restart</a>
				<?php endif; ?>
				<?php endif; ?>    
			</div>


		</div>
	</div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php /**PATH F:\xampp\htdocs\tinyhugs\resources\views/front/dashboard/booking_data.blade.php ENDPATH**/ ?>