
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
                        Add New <?php echo e($sectionNameSingular); ?> </h5>
                    <!--end::Page Title-->

                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
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
        <?php echo e(Form::open(['role' => 'form','route' => "$modelName.add",'class' => 'mws-form', 'files' => true,"autocomplete"=>"off"])); ?>

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-1"></div>
                        <div class="col-xl-10">
                            <h3 class="mb-10 font-weight-bold text-dark">
                                <?php echo e($sectionNameSingular); ?> Information</h3>

                            <div class="row">
                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        <?php echo HTML::decode( Form::label('name', trans("Name").'<span
                                            class="text-danger"> * </span>')); ?>

                                        <?php echo e(Form::text('name','', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('name') ? 'is-invalid':'')])); ?>

                                        <div class="invalid-feedback"><?php echo $errors->first('name'); ?></div>
                                    </div>
                                    <!--end::Input-->
                                </div>
                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        <?php echo HTML::decode( Form::label('email', trans("Email").'<span
                                            class="text-danger"> * </span>')); ?>

                                        <?php echo e(Form::text('email','', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('email') ? 'is-invalid':'')])); ?>

                                        <div class="invalid-feedback"><?php echo $errors->first('email'); ?></div>
                                    </div>
                                    <!--end::Input-->
                                </div>
                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        <?php echo HTML::decode( Form::label('phone_number', trans("Phone No.").'<span
                                            class="text-danger"> * </span>')); ?>

											<?php echo e(Form::text('phone_number',isset($model->phone_number) ? $model->phone_number : '', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('phone_number') ? 'is-invalid':'')])); ?>

                                        <div class="invalid-feedback"><?php echo $errors->first('phone_number'); ?></div>
                                    </div>
                                    <!--end::Input-->
                                </div>
                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        <?php echo HTML::decode( Form::label('password', trans("Password").'<span
                                            class="text-danger"> * </span>')); ?>

                                        <?php echo e(Form::password('password', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('password') ? 'is-invalid':'')])); ?>

                                        <div class="invalid-feedback"><?php echo $errors->first('password'); ?></div>
                                    </div>
                                    <!--end::Input-->
                                </div>

                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        <?php echo HTML::decode( Form::label('confirm_password', trans("Confirm Password").'<span
                                            class="text-danger"> * </span>')); ?>

                                        <?php echo e(Form::password('confirm_password', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('confirm_password') ? 'is-invalid':'')])); ?>

                                        <div class="invalid-feedback"><?php echo $errors->first('confirm_password'); ?></div>
                                    </div>
                                    <!--end::Input-->
                                </div>

                             
                                </div>
                            <div class="d-flex justify-content-between border-top mt-5 pt-10">
                                <div>
									<a href="<?php echo e(route($modelName.'.add')); ?>" class="btn btn-danger font-weight-bold text-uppercase px-9 py-4"><?php echo e(trans('Clear')); ?></a>
									<a href="<?php echo e(route($modelName.'.index')); ?>" class="btn btn-info font-weight-bold text-uppercase px-9 py-4"><?php echo e(trans('Cancel')); ?></a>
									</div>
                                <div>
                                    <button button type="submit"
                                        class="btn btn-success font-weight-bold text-uppercase px-9 py-4">
                                        Submit
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo e(Form::close()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\tinyhugs\resources\views/admin/Support/add.blade.php ENDPATH**/ ?>