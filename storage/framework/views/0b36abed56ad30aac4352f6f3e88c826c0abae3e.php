
<?php $__env->startSection('content'); ?>
<div class="main-workspace">


	<div class="rating-block backg-img">
		<div class="container">
			<div class="heading pt-lg-5 pb-3  pt-3   ">
				<h4>My Plan</h4>
			</div>
			<div class="pricing-table mb-5">

				<div class="active-plans"> 
					<div class="row">
						<?php if(!empty($package)): ?>

						<div class="col-md-6">
							<div class="plans-right-border" >

								<div class="cards-heading">
									<h5 class="card-title text-uppercase text-center"><?php echo e($package->name); ?></h5>
									<h6 class="h1  text-center">$<?php echo e(number_format($package->price, 2)); ?>

										<span class="h6 ">
											/<?php echo e($package->no_of_month); ?>

											<?php echo e(($package->no_of_month > 1) ? 'Months' :'Month'); ?></span></h6>
										</div>

										<div class="py-2 py-md-3">
											<div class="active-plan"><label>Starting Date:</label> <strong><?php echo e(date('m/d/Y', strtotime($package->plan_start_date))); ?></strong></div> 
											<div class="active-plan "><label>Ending Date:</label> <strong><?php echo e(date('m/d/Y', strtotime($package->plan_end_date))); ?></strong></div> 

										</div>
									</div>  
								</div>
								<div class="col-md-6">
									<!-- <?php echo $package->description; ?> -->
									<div class="plan-right-block">
										<ul class="list-unstyled mb-4">
											<?php echo $package->description; ?>

										</ul>

									</div>       </div>

									<div class="btn-block text-right px-5 pb-4 pb-md-0">
										<?php if($package->status==1): ?>
										<button id="cancel_plan" value="<?php echo e($package->id); ?>" class="btn btn-red loadMoreBtn">
										Cancel Plan</button>
										<?php endif; ?>
										<!-- <a href="javascript:void(0)" id="<?php echo e($package->id); ?>" class="btn-theme mw-100 plan_id">Buy now</a>  -->
									</div>

								</div>

							</div>




                <!-- <div class="col-md-4 mb-4">
                    <div class="card mb-5 mb-lg-0 rounded-lg shadow">
                        <div class="card-header">
                            <h5 class="card-title text-uppercase text-center"><?php echo e($package->name); ?></h5>
                            <h6 class="h3 text-white text-center">$<?php echo e(number_format($package->price, 2)); ?>

                                <span class="h6 text-white-50">
                                    /<?php echo e($package->no_of_month); ?>

                                    <?php echo e(($package->no_of_month > 1) ? 'Months' :'Month'); ?></span>
                                </h6>
                                <h6 class="h6 text-white text-center">Start Date : <span
                                    class="h6 text-white-50"><?php echo e(date('m/d/Y', strtotime($package->plan_start_date))); ?>

                                </span></h6>
                                <h6 class="h6 text-white text-center">Expiry Date : <span
                                    class="h6 text-white-50"><?php echo e(date('m/d/Y', strtotime($package->plan_end_date))); ?>

                                </span></h6>
                            </div>
                            <div class="card-body bg-light rounded-bottom">
                                <?php echo $package->description; ?>

                                <div class="btn-block">
                                    <?php if($package->status==1): ?>
                                    <button id="cancel_plan" value="<?php echo e($package->id); ?>" class="form-control btn-danger">
                                    Cancel Plan</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="col-md-4 mb-4">
                        <div class="card mb-5 mb-lg-0 rounded-lg shadow">
                            <div class="card-header">
                                <span class="text-white">No Plan Found</span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                </div> -->






            </div></div>
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <?php if(Auth::user()): ?>
    <script>


    	$(document).on('click','#cancel_plan',function(){
    		$id = $(this).attr('value');
    		
    		Swal.fire({
    			title: "Are you sure?",
    			text: "Want to Cancel Plan",
    			icon: "warning",
    			showCancelButton: true,
    			confirmButtonText: "Yes",
    			cancelButtonText: "No",
    			reverseButtons: true
    		}).then(function (result) {
    			if (result.value) {
    				$("#loader_img").show();
    				$elem=$(this);
    				$.ajax({
    					headers: {
    						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    					},
    					url: '<?php echo e(route("user.changeUserPlanStatus")); ?>',
    					data: {
    						'id': $id,

    					},
    					type: "POST",
    					success: function(res) {
    						if(res.success==1){
    							$("#loader_img").hide();

    							location.reload();
    						}else{
    							$("#loader_img").hide();
    						}
    					}
    				});
    			}
    		});

    	});


    </script>
    <?php endif; ?>
    <?php $__env->stopSection(); ?>
<?php echo $__env->make('front.dashboard.layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\xampp\htdocs\tinyhugs\resources\views/front/dashboard/my_plan_detail.blade.php ENDPATH**/ ?>