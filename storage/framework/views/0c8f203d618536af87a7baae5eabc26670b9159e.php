
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
        <?php echo e(Form::open(['role' => 'form','route' => "$modelName.add",'class' => 'mws-form', 'files' => true,"autocomplete"=>"off","enctype"=>"multipart/form-data"])); ?>

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
                                <?php /*
                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        {!! HTML::decode( Form::label('last_name', trans("Last Name").'<span
                                            class="text-danger"> * </span>')) !!}
                                        {{ Form::text('last_name','', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('last_name') ? 'is-invalid':'')]) }}
                                        <div class="invalid-feedback"><?php echo $errors->first('last_name'); ?></div>
                                    </div>
                                    <!--end::Input-->
                                </div> */ ?>
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
                                        <?php echo HTML::decode( Form::label('phone', trans("Phone No.").'<span
                                            class="text-danger"> * </span>')); ?>

											<?php echo e(Form::text('phone_number',isset($model->phone_number) ? $model->phone_number : '', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('phone_number') ? 'is-invalid':'')])); ?>

                                        <div class="invalid-feedback"><?php echo $errors->first('phone_number'); ?></div>
                                    </div>
                                    <!--end::Input-->
                                </div>
                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        <?php echo HTML::decode( Form::label('age', trans("Age").'<span
                                            class="text-danger"> * </span>')); ?>

											<?php echo e(Form::text('age','', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('age') ? 'is-invalid':'')])); ?>

                                        <div class="invalid-feedback"><?php echo $errors->first('age'); ?></div>
                                    </div>
                                    <!--end::Input-->
                                </div>

                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        <?php echo HTML::decode( Form::label('experience', trans("Experience").'<span
                                            class="text-danger"> * </span>')); ?>

											<?php echo e(Form::text('experience','', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('experience') ? 'is-invalid':'')])); ?>

                                        <div class="invalid-feedback"><?php echo $errors->first('experience'); ?></div>
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

                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        <?php echo HTML::decode( Form::label('postcode', trans("Zip Code").'<span
                                            class="text-danger"> * </span>')); ?>

											<?php echo e(Form::text('postcode',isset($model->postcode) ? $model->postcode : '', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('phone_number') ? 'is-invalid':'')])); ?>

                                        <div class="invalid-feedback"><?php echo $errors->first('postcode'); ?></div>
                                    </div>
                                    <!--end::Input-->
                                </div>

                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        <?php echo HTML::decode( Form::label('nanny_price', trans("Price").'<span
                                            class="text-danger"> * </span>')); ?>

                                            <?php echo e(Form::text('nanny_price',isset($model->nanny_price) ? $model->nanny_price : '', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('nanny_price') ? 'is-invalid':'')])); ?>

                                        <div class="invalid-feedback"><?php echo $errors->first('nanny_price'); ?></div>
                                    </div>
                                    <!--end::Input-->
                                </div>

                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        <?php echo HTML::decode( Form::label('photo_id', trans("Photo ID").'<span
                                            class="text-danger"> * </span>')); ?>

                                        <?php echo e(Form::file('photo_id', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('photo_id') ? 'is-invalid':'')])); ?>

                                        <div class="invalid-feedback"><?php echo $errors->first('photo_id'); ?></div>
                                    </div>
                                <!--end::Input-->
                                </div>

                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        <?php echo HTML::decode( Form::label('resume', trans("Resume").'<span
                                            class="text-danger"> </span>')); ?>

                                        <?php echo e(Form::file('resume', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('resume') ? 'is-invalid':'')])); ?>

                                        <div class="invalid-feedback"><?php echo $errors->first('resume'); ?></div>
                                    </div>
                                <!--end::Input-->
                                </div>

                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        <?php echo HTML::decode( Form::label('identification_type', trans("Identification Type").' <span
                                            class="text-danger">*</span>')); ?><br>
                                            Passport <input class="identification_type mr-3 form-control-solid" type="radio" name="identification_type" value="1" checked>
                                            Driving License <input class="identification_type form-control-solid" type="radio" name="identification_type" value="2">
                                        <div class="invalid-feedback"><?php echo $errors->first('identification_type'); ?></div>
                                    </div>
                                <!--end::Input-->
                                </div>
                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group identification_file1">
                                        <?php echo HTML::decode( Form::label('identification_file', trans("Upload your Passport").' <span
                                            class="text-danger">*</span>')); ?>

                                        <?php echo e(Form::file('identification_file', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('identification_file') ? 'is-invalid':'')])); ?>

                                        <div class="invalid-feedback"><?php echo $errors->first('identification_file'); ?></div>
                                    </div>
                                <!--end::Input-->
                                </div>
                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        <?php echo HTML::decode( Form::label('cpr_certificate', trans("CPR Certificate").'<span
                                            class="text-danger"> </span>')); ?>

                                        <?php echo e(Form::file('cpr_certificate', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('cpr_certificate') ? 'is-invalid':'')])); ?>

                                        <div class="invalid-feedback"><?php echo $errors->first('cpr_certificate'); ?></div>
                                    </div>
                                <!--end::Input-->
                                </div>

                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        <?php echo HTML::decode( Form::label('other_certificates', trans("Other Certificates").'<span
                                            class="text-danger"> </span>')); ?>

                                        <?php echo e(Form::file('other_certificates', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('other_certificates') ? 'is-invalid':''),
                                        'name'=>'other_certificates'])); ?>

                                        <div class="invalid-feedback"><?php echo $errors->first('other_certificates'); ?></div>
                                    </div>
                                <!--end::Input-->
                                </div>

                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        <?php echo HTML::decode( Form::label('description', trans("Description").'<span
                                            class="text-danger"> * </span>')); ?>

											<?php echo e(Form::textarea('description','', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('description') ? 'is-invalid':'')])); ?>

                                        <div class="invalid-feedback"><?php echo $errors->first('description'); ?></div>
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
<script>
$(".identification_type").on('change',function(){
  if($(this).val()==1){
    $(".identification_file1").find('label').html('Upload your Passport <span class="text-danger">*</span>');
  }else{
    $(".identification_file1").find('label').html('Upload your Driving License <span class="text-danger">*</span>');
  }
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\xampp\htdocs\tinyhugs\resources\views/admin/Nanny/add.blade.php ENDPATH**/ ?>