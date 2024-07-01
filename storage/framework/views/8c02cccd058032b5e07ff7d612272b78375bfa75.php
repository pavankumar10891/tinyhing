
<?php $__env->startSection('content'); ?>


<!--begin::Content-->
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
	<!--begin::Subheader-->
	<div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
		<div
			class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
			<!--begin::Info-->
			<div class="d-flex align-items-center flex-wrap mr-1">
				<!--begin::Page Heading-->
				<div class="d-flex align-items-baseline flex-wrap mr-5">
					<!--begin::Page Title-->
					<h5 class="text-dark font-weight-bold my-1 mr-5">
						View <?php echo e($sectionNameSingular); ?> </h5>
					<!--end::Page Title-->

					<!--begin::Breadcrumb-->
					<ul
						class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
						<li class="breadcrumb-item">
							<a href="<?php echo e(route('dashboard')); ?>" class="text-muted">Dashboard</a>
						</li>
						<li class="breadcrumb-item">
							<a href="<?php echo e(route($modelName.'.index')); ?>" class="text-muted"><?php echo e($sectionName); ?></a>
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
			<div class="card card-custom gutter-b">
				<!--begin::Header-->
				<div class="card-header card-header-tabs-line">
					<div class="card-toolbar">
						<ul class="nav nav-tabs nav-tabs-space-lg nav-tabs-line nav-bold nav-tabs-line-3x"
							role="tablist">
							<li class="nav-item">
								<a class="nav-link active" data-toggle="tab"
									href="#kt_apps_contacts_view_tab_1">
									<span class="nav-text">
									   <?php echo e($sectionNameSingular); ?> Information
									</span>
								</a>
							</li>
						</ul>
					</div>
				</div>
				<!--end::Header-->

				<!--begin::Body-->
				<div class="card-body px-0">
					<div class="tab-content px-10">
						<!--begin::Tab Content-->
						<div class="tab-pane active" id="kt_apps_contacts_view_tab_1" role="tabpanel">
							
							<div class="form-group row my-2">
								<label class="col-4 col-form-label">Name:</label>
								<div class="col-8">
									<span class="form-control-plaintext font-weight-bolder"><?php echo e(isset($model->name) ? $model->name :''); ?></span>
								</div>
							</div>

							<div class="form-group row my-2">
								<label class="col-4 col-form-label">Price:</label>
								<div class="col-8">
									<span class="form-control-plaintext font-weight-bolder"><?php echo e(isset($model->price) ? number_format($model->price, 2) :''); ?></span>
								</div>
							</div>

							<div class="form-group row my-2">
								<label class="col-4 col-form-label">Number Of Month:</label>
								<div class="col-8">
									<span class="form-control-plaintext font-weight-bolder"><?php echo e(isset($model->no_of_month) ? $model->no_of_month :''); ?></span>
								</div>
							</div>

							<div class="form-group row my-2">
								<label class="col-4 col-form-label">Order:</label>
								<div class="col-8">
									<span class="form-control-plaintext font-weight-bolder"><?php echo e(isset($model->order_type) ? $model->order_type :''); ?></span>
								</div>
							</div>


							<div class="form-group row my-2">
								<label class="col-4 col-form-label">Registered On:</label>
								<div class="col-8">
									<span class="form-control-plaintext font-weight-bolder">
									<?php echo e(date(config::get("Reading.date_format"),strtotime($model->created_at))); ?>

								</span>
								</div>
							</div> 
						</div>
						<!--end::Tab Content-->
					</div>
				</div>
				<!--end::Body-->
			</div>
		</div>
		<!--end::Container-->
	</div>
	<!--end::Entry-->
</div>
<!--end::Content-->
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\xampp\htdocs\tinyhugs\resources\views/admin/Package/view.blade.php ENDPATH**/ ?>