@extends('admin.layouts.default')
@section('content')
<script src="{{ WEBSITE_JS_URL }}ckeditor/ckeditor.js"></script>
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
                        Add New {{ $sectionNameSingular }} </h5>
                    <!--end::Page Title-->

                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard')}}" class="text-muted">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route($modelName.'.index')}}" class="text-muted">{{ $sectionName }}</a>
                        </li>
                    </ul>
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Page Heading-->
            </div>
            <!--end::Info-->

            @include("admin.elements.quick_links")
        </div>
    </div>
    <!--end::Subheader-->

    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class=" container ">
            {{ Form::open(['role' => 'form','route' => "$modelName.add",'class' => 'mws-form', 'files' => true,"autocomplete"=>"off"]) }}
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-1"></div>
                        <div class="col-xl-10">
                            <h3 class="mb-10 font-weight-bold text-dark">
                                {{ $sectionNameSingular }} Information</h3>

                            <div class="row">
                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        {!! HTML::decode( Form::label('name', trans("Name").'<span
                                            class="text-danger"> * </span>')) !!}
                                        {{ Form::text('name','', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('name') ? 'is-invalid':'')]) }}
                                        <div class="invalid-feedback"><?php echo $errors->first('name'); ?></div>
                                    </div>
                                    <!--end::Input-->
                                </div>
                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        {!! HTML::decode( Form::label('description', trans("Description").'<span
                                            class="text-danger"> * </span>')) !!}
                                        {{ Form::textarea('description','', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('description') ? 'is-invalid':''),'id' => 'description']) }}
                                        <div class="invalid-feedback"><?php echo $errors->first('description'); ?></div>
                                    </div>
                                    <script>
                                        /* CKEDITOR for description */
                                        CKEDITOR.replace('description',
                                        {
                                            filebrowserUploadUrl : '<?php echo URL::to('base/uploder'); ?>',
                                            enterMode : CKEDITOR.ENTER_BR
                                        });
                                        CKEDITOR.config.allowedContent = true;	
                                                    
                                     </script>
                                    <!--end::Input-->
                                </div>
                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        {!! HTML::decode( Form::label('designation', trans("Designation").'<span
                                            class="text-danger"> * </span>')) !!}
                                        {{ Form::text('designation','', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('designation') ? 'is-invalid':'')]) }}
                                        <div class="invalid-feedback"><?php echo $errors->first('designation'); ?></div>
                                    </div>
                                    <!--end::Input-->
                                </div>

                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        {!! HTML::decode( Form::label('image', trans("Image").'<span
                                            class="text-danger"> </span>')) !!}
                                        {{ Form::file('image', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('image') ? 'is-invalid':'')]) }}
                                        <div class="invalid-feedback"><?php echo $errors->first('image'); ?></div>
                                    </div>
                                    <!--end::Input-->
                                </div>

                            </div>





                            <div class="d-flex justify-content-between border-top mt-5 pt-10">
                                <div>
                                    <a href="{{ route($modelName.'.add')}}"
                                        class="btn btn-danger font-weight-bold text-uppercase px-9 py-4">{{ trans('Clear') }}</a>

                                    <a href="{{ route($modelName.'.index')}}"
                                        class="btn btn-info font-weight-bold text-uppercase px-9 py-4">{{ trans('Cancel') }}</a>
                                </div>
                                <div>
                                    <button button type="submit"
                                        class="btn btn-success font-weight-bold text-uppercase px-9 py-4">
                                        Submit
                                    </button>
                                </div>
                            </div>


                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>


            <script>
            /*  $(document).ready(function() {
 $(".card-header").click(function () {
    $(".card-body").removeClass("show");
    // $(".tab").addClass("active"); // instead of this do the below 
    $(this).next().addClass("show");   
});
}); */
            </script>

            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/fontawesome.min.css"
                rel="stylesheet">

            @stop