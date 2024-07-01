

<?php $__env->startSection('content'); ?>
<?php ( $couponTypeArr = Config::get('coupon_type_arr') ); ?>

<!--begin::Content-->
<script src="<?php echo e(WEBSITE_JS_URL); ?>ckeditor/ckeditor.js"></script>
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
                        Edit <?php echo e($sectionNameSingular); ?> </h5>
                    <!--end::Page Title-->

                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="<?php echo e(URL::to('adminpnlx/dashboard')); ?>" class="text-muted">Dashboard</a>
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
        <?php echo e(Form::open(['role' => 'form','url' =>  route("$modelName.edit",$model->id),'class' => 'mws-form', 'files' => true,"autocomplete"=>"off"])); ?>

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-1"></div>
                        <div class="col-xl-10">
                            <h3 class="mb-10 font-weight-bold text-dark">
                                Edit <?php echo e($sectionNameSingular); ?> Information</h3>

                            <div class="row">
                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        <?php echo HTML::decode( Form::label('coupon_code', trans("Coupon Code").'<span class="text-danger">
                                            * </span>')); ?>

                                        <?php echo e(Form::text('coupon_code', isset($model->coupon_code) ? $model->coupon_code : '', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('coupon_code') ? 'is-invalid':'')])); ?>

                                        <div class="invalid-feedback"><?php echo $errors->first('coupon_code'); ?></div>
                                    </div>
                                    <!--end::Input-->
                                </div>

                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        <?php echo HTML::decode( Form::label('name', trans("Coupon Name").'<span
                                            class="text-danger"> * </span>')); ?>

                                        <?php echo e(Form::text('name',isset($model->name) ? $model->name : '', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('name') ? 'is-invalid':'')])); ?>

                                        <div class="invalid-feedback"><?php echo $errors->first('name'); ?></div>
                                    </div>
                                    <!--end::Input-->
                                </div>

                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        <?php echo HTML::decode( Form::label('start_date', trans("Coupon Valid From").'<span
                                            class="text-danger"> * </span>')); ?>

                                            <div class="input-group date" id="datepickerfrom" data-target-input="nearest">
                                            <?php echo e(Form::text('start_date',isset($model->start_date) ? $model->start_date : '', ['class' => 'form-control form-control-solid form-control-lg datetimepicker-input '.($errors->has('start_date') ? 'is-invalid':''),'placeholder'=>'Valid From','data-target'=>'#datepickerfrom','data-toggle'=>'datetimepicker'])); ?>

                                                <div class="input-group-append">
                                                    <span class="input-group-text">
                                                        <i class="ki ki-calendar"></i>
                                                    </span>
                                                </div>
                                            <div class="invalid-feedback"><?php echo $errors->first('start_date'); ?></div>
                                            </div>
                                    </div>
                                    <!--end::Input-->
                                </div>
                                
                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        <?php echo HTML::decode( Form::label('end_date', trans("Coupon Valid Till").'<span
                                        class="text-danger"> * </span>')); ?>

                                        <div class="input-group date" id="datepickerto" data-target-input="nearest">
                                            <?php echo e(Form::text('end_date',isset($model->end_date) ? $model->end_date : '',['class' => 'form-control form-control-solid form-control-lg datetimepicker-input '.($errors->has('end_date') ? 'is-invalid':''),'placeholder'=>'Valid To','data-target'=>'#datepickerto','data-toggle'=>'datetimepicker'])); ?>

                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <i class="ki ki-calendar"></i>
                                                </span>
                                            </div>
                                            <div class="invalid-feedback"><?php echo $errors->first('end_date'); ?></div>
                                        </div>										
                                    </div>
                                    <!--end::Input-->
                                </div>

                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        <?php echo HTML::decode( Form::label('amount', trans("Amount").'<span
                                            class="text-danger"> * </span>')); ?>

                                        <?php echo e(Form::text('amount', isset($model->amount) ? $model->amount : '', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('amount') ? 'is-invalid':'')])); ?>

                                        <div class="invalid-feedback"><?php echo $errors->first('amount'); ?></div>
                                    </div>
                                    <!--end::Input-->
                                </div>

                                <div class="col-xl-12">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        <?php echo HTML::decode( Form::label('coupon_type', trans("Coupon Type").'<span
                                            class="text-danger"> * </span>')); ?>

                                            <?php if(!empty($couponTypeArr)): ?>
                                                <?php $__currentLoopData = $couponTypeArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $couponKey => $couponVal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php echo e(Form::radio('coupon_type',$couponKey,(!empty($model->coupon_type) && $model->coupon_type == $couponKey) ? true : false,['class'=>'' .($errors->has('coupon_type') ? 'is-invalid':''), 'id' => "coupon_".$couponKey])); ?>

                                                    <label for="<?php echo e('coupon_'.$couponKey); ?>"><?php echo e($couponVal); ?></label>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>
                                            <div class="invalid-feedback"><?php echo $errors->first('coupon_type'); ?></div>
                                    </div>
                                    <!--end::Input-->
                                </div>

							</div>
							
                            <div class="d-flex justify-content-between border-top mt-5 pt-10">
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
<script>
$(document).ready(function() {
    $('#datepickerfrom').datetimepicker({
        format: 'YYYY-MM-DD'
    });
    $('#datepickerto').datetimepicker({
        format: 'YYYY-MM-DD'
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\xampp\htdocs\tinyhugs\resources\views/admin/CouponCodes/edit.blade.php ENDPATH**/ ?>