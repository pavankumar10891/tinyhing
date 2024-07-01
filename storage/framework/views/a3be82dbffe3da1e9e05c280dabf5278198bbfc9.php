
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
          <?php echo e(Form::open(['role' => 'form','url' =>  route("$modelName.edit",$model->id),'class' => 'mws-form', 'files' => true,"autocomplete"=>"off"])); ?>

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-1"></div>
                        <div class="col-xl-10">
                            <h3 class="mb-10 font-weight-bold text-dark">
                                <?php echo e($sectionNameSingular); ?> Information</h3>

                            <div class="row">
                                
                                <div class="col-xl-6">
                                        <label>Date From</label>
                                        <div class="input-group date" id="datepickerfrom"
                                            data-target-input="nearest">
                                            <?php echo e(Form::text('checkin_date',((isset($searchVariable['checkin_date'])) ? $searchVariable['checkin_date'] : $model->checkin_date), ['class' => ' form-control form-control-solid form-control-lg datetimepicker-input','placeholder'=>'Date To','data-target'=>'#datepickerfrom','data-toggle'=>'datetimepicker'])); ?>

                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <i class="ki ki-calendar"></i>
                                                </span>
                                            </div>
                                            <div class="invalid-feedback"><?php echo $errors->first('checkin_date'); ?></div>
                                        </div>
                                    </div>

                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <?php echo HTML::decode( Form::label('checkin_time', trans("Start Time").'<span class="text-danger"> * </span>')); ?>

                                    <div class="input-group clockpicker">
                                        
                                           
                                        <?php echo e(Form::text('checkin_time', isset($model->checkin_time) ? $model->checkin_time : '', ['class' => 'form-control form-control-solid form-control-lg datetimepicker-input'.($errors->has('checkin_time') ? 'is-invalid':'')])); ?>

                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-time"></span>
                                        </span>
                                        <div class="invalid-feedback"><?php echo $errors->first('checkin_time'); ?></div>
                                    </div>
                                    <!--end::Input-->
                                </div>



                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                     <?php echo HTML::decode( Form::label('checkout_time', trans("End Time").'<span class="text-danger">
                                            * </span>')); ?>

                                    <div class="input-group endclockpicker">
                                       
                                        <?php echo e(Form::text('checkout_time', isset($model->checkout_time) ? $model->checkout_time : '', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('checkout_time') ? 'is-invalid':'')])); ?>

                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-time"></span>
                                        </span>
                                        <div class="invalid-feedback"><?php echo $errors->first('checkout_time'); ?></div>
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
                                    <button button type="submit" class="btn btn-success font-weight-bold text-uppercase px-9 py-4">
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
<link rel="stylesheet" href="<?php echo e(url('/')); ?>/jquery-clockpicker.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="<?php echo e(url('/')); ?>/jquery-clockpicker.min.js"></script>
<script>

    $('#datepickerfrom').datetimepicker({
        format: 'YYYY-MM-DD'
    });
    $('.clockpicker').clockpicker({
        donetext: 'Done'
    });
    $('.endclockpicker').clockpicker({
        donetext: 'Done'
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\xampp\htdocs\tinyhugs\resources\views/admin/StaffAttendance/edit.blade.php ENDPATH**/ ?>