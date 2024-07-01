
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
                        Edit <?php echo e($sectionNameSingular); ?> </h5>
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
          <?php echo e(Form::open(['role' => 'form','url' =>  route("$modelName.edit",$model->id),'class' => 'mws-form', 'files' => true,"autocomplete"=>"off","enctype"=>"multipart/form-data"])); ?>

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
                                        <?php echo HTML::decode( Form::label('name', trans("Name").'<span class="text-danger">
                                            * </span>')); ?>

                                        <?php echo e(Form::text('name', isset($model->name) ? $model->name : '', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('name') ? 'is-invalid':'')])); ?>

                                        <div class="invalid-feedback"><?php echo $errors->first('name'); ?></div>
                                    </div>
                                    <!--end::Input-->
                                </div>
                                <?php /*
                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        {!! HTML::decode( Form::label('last_name', trans("Last Name").'<span class="text-danger">
                                            * </span>')) !!}
                                        {{ Form::text('last_name', isset($model->last_name) ? $model->last_name : '', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('last_name') ? 'is-invalid':'')]) }}
                                        <div class="invalid-feedback"><?php echo $errors->first('last_name'); ?></div>
                                    </div>
                                    <!--end::Input-->
                                </div> */ ?>
                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        <?php echo HTML::decode( Form::label('email', trans("Email").'<span
                                            class="text-danger"> * </span>')); ?>

                                        <?php echo e(Form::text('email', isset($model->email) ? $model->email : '', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('email') ? 'is-invalid':'')])); ?>

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

											<?php echo e(Form::text('age',isset($model->age) ? $model->age : '', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('age') ? 'is-invalid':'')])); ?>

                                        <div class="invalid-feedback"><?php echo $errors->first('age'); ?></div>
                                    </div>
                                    <!--end::Input-->
                                </div>

                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        <?php echo HTML::decode( Form::label('experience', trans("Experience").'<span
                                            class="text-danger"> * </span>')); ?>

											<?php echo e(Form::text('experience',isset($model->experience) ? $model->experience : '', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('experience') ? 'is-invalid':'')])); ?>

                                        <div class="invalid-feedback"><?php echo $errors->first('experience'); ?></div>
                                    </div>
                                    <!--end::Input-->
                                </div>


                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        <?php echo HTML::decode( Form::label('password', trans("Password"))); ?>

                                        <?php echo e(Form::password('password', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('password') ? 'is-invalid':'')])); ?>

                                        <div class="invalid-feedback"><?php echo $errors->first('password'); ?></div>
                                    </div>
                                    <!--end::Input-->
                                </div>

                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        <?php echo HTML::decode( Form::label('confirm_password', trans("Confirm Password"))); ?>

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

											<?php echo e(Form::text('postcode',isset($model->postcode) ? $model->postcode : '', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('postcode') ? 'is-invalid':'')])); ?>

                                        <div class="invalid-feedback"><?php echo $errors->first('postcode'); ?></div>
                                    </div>
                                    <!--end::Input-->
                                </div>

                                 <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        <?php echo HTML::decode( Form::label('nanny_price', trans("Fees").'<span
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

                                            <?php if($model->photo_id != ""): ?>
                                                <br />
                                                <a class="fancybox-buttons" data-fancybox-group="button" href="<?php echo e(USER_IMAGE_URL.$model->photo_id); ?>" >
                                                    <img class="" src="<?php echo e(USER_IMAGE_URL.$model->photo_id); ?>" width="60px" height="60px">
                                                </a>
                                            <?php endif; ?>
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

                                            <?php if($model->resume != ""): ?>
                                                <br />
                                                <a href="<?php echo e(CERTIFICATES_AND_FILES_URL.$model->resume); ?>" download="<?php echo e($model->resume); ?>" title="Click To Download" >
													<img src="<?php echo e(WEBSITE_IMG_URL.'download.png'); ?>" width="12%" height="12%">
                                            	</a>
                                            <?php endif; ?>
                                    </div>
                                    <!--end::Input-->
                                </div>

                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        <?php echo HTML::decode( Form::label('identification_type', trans("Identification Type").' <span
                                            class="text-danger">*</span>')); ?><br>
                                            Passport <input class="identification_type mr-3 form-control-solid" type="radio" name="identification_type" value="1" <?php echo e(($model->identification_type==1)?'checked':''); ?>>
                                            Driving License <input class="identification_type form-control-solid" type="radio" name="identification_type" value="2" <?php echo e(($model->identification_type==2)?'checked':''); ?>>
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
                                        <?php if($model->identification_file != ""): ?>
                                                <br />
                                                <a href="<?php echo e(CERTIFICATES_AND_FILES_URL.$model->identification_file); ?>" download="<?php echo e($model->identification_file); ?>" title="Click To Download" >
													<img src="<?php echo e(WEBSITE_IMG_URL.'download.png'); ?>" width="12%" height="12%">
                                            	</a>
                                        <?php endif; ?>
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

                                            <?php if($model->cpr_certificate != ""): ?>
                                                <br />
                                                <a href="<?php echo e(CERTIFICATES_AND_FILES_URL.$model->cpr_certificate); ?>" download="<?php echo e($model->cpr_certificate); ?>" title="Click To Download">
													<img src="<?php echo e(WEBSITE_IMG_URL.'download.png'); ?>" width="12%" height="12%"> 
                                            	</a>
                                            <?php endif; ?>
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

                                        <br />

                                        <ul class="list-unstyled mb-0 editDelete_list">
                                            <?php if(!empty($userCertificateData)): ?>
                                                <?php $__currentLoopData = $userCertificateData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $certificate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li class="mb-2 mr-2 position-relative">
                                                    <a href="<?php echo e(OTHER_CERTIFICATES_DOCUMENT_URL.$certificate['other_certificates']); ?>" download="<?php echo e($certificate['other_certificates']); ?>" title="Click To Download">
                                                        <img src="<?php echo e(WEBSITE_IMG_URL.'download.png'); ?>" class="w-70px">
                                                    </a>                                    
                                                    <i class="far fa-trash-alt remove-certificate" data="<?php echo e($certificate['id']); ?>" title="Click To Delete"></i>
                                                </li>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>
                                        </ul>

                                        <div class="invalid-feedback"><?php echo $errors->first('other_certificates'); ?></div>
                                    </div>

                                <!--end::Input-->
                                </div>
                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        <?php echo HTML::decode( Form::label('description', trans("Description").'<span
                                            class="text-danger"> * </span>')); ?>

                                            <?php echo e(Form::textarea('description',isset($model->description) ? $model->description : '', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('description') ? 'is-invalid':'')])); ?>

                                        <div class="invalid-feedback"><?php echo $errors->first('description'); ?></div>
                                    </div>
                                    <!--end::Input-->
                                </div>
                                </div>
                            <div class="d-flex justify-content-between border-top mt-5 pt-10">
                                <div>
									<a href='<?php echo e(route("$modelName.edit",$model->id)); ?>' class="btn btn-danger font-weight-bold text-uppercase px-9 py-4"><?php echo e(trans('Clear')); ?></a>
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
$value = "<?php echo e((isset($model->identification_type))?$model->identification_type:''); ?>";
if($value==1){
    $(".identification_file1").find('label').html('Upload your Passport <span class="text-danger">*</span>');
}else{
    $(".identification_file1").find('label').html('Upload your Driving License <span class="text-danger">*</span>');
}
$(".identification_type").on('change',function(){
  if($(this).val()==1){
    $(".identification_file1").find('label').html('Upload your Passport <span class="text-danger">*</span>');
  }else{
    $(".identification_file1").find('label').html('Upload your Driving License <span class="text-danger">*</span>');
  }
});
$(document).ready(function() {
    $(document).on('click', '.remove-certificate', function(event) {
        event.preventDefault();
        $imageId = $(this).attr('data');
        var url='<?php echo e(route("$modelName.certificates","id")); ?>';
        url = url.replace('id', $imageId);
        $(this).prev().remove();
        $(this).remove();
        $.ajax({
            url: url,
            type: 'get',
            success: function(response) {
                // console.log(response);
            }
        });
    });
});






</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\xampp\htdocs\tinyhugs\resources\views/admin/Nanny/edit.blade.php ENDPATH**/ ?>