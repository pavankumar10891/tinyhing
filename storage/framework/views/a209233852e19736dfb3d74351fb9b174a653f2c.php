
<?php $__env->startSection('content'); ?>
<!--begin::Content-->
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
	<!--begin::Subheader-->
	<div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
		<div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
			<!--begin::Info-->
			<div class="d-flex align-items-center flex-wrap mr-1">
				<!--begin::Page Heading-->
				<div class="d-flex align-items-baseline flex-wrap mr-5">
					<!--begin::Page Title-->
					<h5 class="text-dark font-weight-bold my-1 mr-5">
						<?php echo e('Add New '.ucwords(str_replace("-"," ",$type))); ?></h5>
					<!--end::Page Title-->

					<!--begin::Breadcrumb-->
					<ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
						<li class="breadcrumb-item">
							<a href="<?php echo e(route('dashboard')); ?>" class="text-muted">Dashboard</a>
						</li>
						<li class="breadcrumb-item">
							<a href="<?php echo e(URL::to('adminpnlx/lookups-manager/'.$type)); ?>" class="text-muted"><?php echo e(ucwords(str_replace("-"," ",$type))); ?></a>
						</li>
					</ul>
					<!--end::Breadcrumb-->
				</div>
				<!--end::Page Heading-->
			</div>
			<!--end::Info-->

			<?php echo $__env->make("admin.elements.quick_links", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
		</div>
	</div>
	<!--end::Subheader-->

	<!--begin::Entry-->
	<div class="d-flex flex-column-fluid">
		<!--begin::Container-->
		<div class=" container ">
			<?php echo e(Form::open(['role' => 'form','url' => 'adminpnlx/lookups-manager/add-lookups/'.$type,'class' => 'mws-form','files' => true])); ?>	
			
			<div class="card card-custom gutter-b">
				<div class="card-header card-header-tabs-line">
					<div class="card-toolbar border-top">
						<ul class="nav nav-tabs nav-bold nav-tabs-line">
							<?php if(!empty($languages)): ?>
								<?php $i = 1 ; ?>
								<?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<li class="nav-item">
										<a class="nav-link <?php echo e(($i ==  $language_code )?'active':''); ?>" data-toggle="tab" href="#<?php echo e($language->title); ?>">
											<span class="symbol symbol-20 mr-3">
												<img src="<?php echo e($language->image); ?>" alt="">
											</span>
											<span class="nav-text"><?php echo e($language->title); ?></span>
										</a>
									</li>
									<?php $i++; ?>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							<?php endif; ?>
						</ul>
					</div>
				</div>
				<div class="card-body">
					<div class="tab-content">
						<?php if(!empty($languages)): ?>
							<?php $i = 1 ; ?>
							<?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<div class="tab-pane fade <?php echo e(($i ==  $language_code )?'show active':''); ?>" id="<?php echo e($language->title); ?>" role="tabpanel" aria-labelledby="<?php echo e($language->title); ?>">
									<div class="row">
										<div class="col-xl-12">	
											<div class="row">
												<div class="col-xl-6">
													<!--begin::Input-->
													<div class="form-group">
														<div id="kt-ckeditor-1-toolbar<?php echo e($language->id); ?>"></div>
														<?php if($i == 1): ?>
															<?php echo HTML::decode( Form::label($language->id.'.code',trans("Title").'<span class="text-danger"> * </span>')); ?>

															
															<?php echo e(Form::text("data[$language->id][code]",'', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('code') ? 'is-invalid':''),'id' => 'code_'.$language->id])); ?>

															<div class="invalid-feedback"><?php echo ($i ==  $language_code ) ? $errors->first('code') : ''; ?></div>
														<?php else: ?> 
															<?php echo HTML::decode( Form::label($language->id.'.code',trans("Title").'<span class="text-danger">  </span>')); ?>

															<?php echo e(Form::text("data[$language->id][code]",'', ['class' => 'form-control form-control-solid form-control-lg','id' => 'code_'.$language->id])); ?>

														<?php endif; ?>
													</div>
												</div>
												
											</div>
										</div>
									</div>
								</div>
								<?php $i++; ?>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						<?php endif; ?>
					</div>

					<?php if(!empty($type == 'crime-category')): ?>	
					<div class="tab-content">
							<div class="row">
								<div class="col-xl-12">	
									<div class="row">
										<div class="col-xl-6">
		                                    <!--begin::Input-->
		                                    <div class="form-group">
		                                        <?php echo HTML::decode( Form::label('image', trans("Image").'<span
		                                            class="text-danger"> * </span>')); ?>

		                                        <?php echo e(Form::file('image', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('image') ? 'is-invalid':'')])); ?>

		                                        <div class="invalid-feedback"><?php echo $errors->first('image'); ?></div>
		                                    </div>
		                                    <!--end::Input-->
		                                </div>	
									</div>
								</div>
							</div>
					</div>
					<?php endif; ?>

					<?php if(!empty($type == 'standard') || !empty($type == 'pro') || !empty($type == 'advanced')): ?>
					<div class="tab-content">
							<div class="row">
								<div class="col-xl-12">	
									<div class="row">
										<div class="col-xl-6">
		                                    <!--begin::Input-->
		                                    <div class="form-group">
		                                        <?php echo HTML::decode( Form::label('is_featute', trans("Feature").'<span
		                                            class="text-danger">  </span>')); ?>

		                                        <?php echo e(Form::checkbox('is_featute', true, '', ['class' => '  '.($errors->has('is_featute') ? 'is-invalid':'')])); ?>

		                                        <div class="invalid-feedback"><?php echo $errors->first('is_featute'); ?></div>
		                                    </div>
		                                    <!--end::Input-->
		                                </div>	
									</div>
								</div>
							</div>
					</div>
					<?php endif; ?>



					<div class="d-flex justify-content-between border-top mt-5 pt-10">
						<div>
							<a href="<?php echo e(URL::to('adminpnlx/lookups-manager/add-lookups/'.$type)); ?>" class="btn btn-danger font-weight-bold text-uppercase px-9 py-4"><?php echo e(trans('Clear')); ?></a>
							
							<a href="<?php echo e(URL::to('adminpnlx/lookups-manager/'.$type)); ?>" class="btn btn-info font-weight-bold text-uppercase px-9 py-4"><?php echo e(trans('Cancel')); ?></a>
						</div>
						<div>
							<button	button type="submit" class="btn btn-success font-weight-bold text-uppercase px-9 py-4">
								Submit
							</button>
						</div>
					</div>
					
				</div>
			</div>

			
			
			<?php echo e(Form::close()); ?>

		</div>
	</div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\xampp\htdocs\tinyhugs\resources\views/admin/Lookups/add.blade.php ENDPATH**/ ?>