@extends('admin.layouts.default')
@section('content') 
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
                        Edit {{ $sectionNameSingular }} </h5>
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
          {{ Form::open(['role' => 'form','url' =>  route("$modelName.edit",$model->id),'class' => 'mws-form', 'files' => true,"autocomplete"=>"off"]) }}
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
                                        {!! HTML::decode( Form::label('name', trans("Name").'<span class="text-danger">
                                            * </span>')) !!}
                                        {{ Form::text('name', isset($model->name) ? $model->name : '', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('name') ? 'is-invalid':'')]) }}
                                        <div class="invalid-feedback"><?php echo $errors->first('name'); ?></div>
                                    </div>
                                    <!--end::Input-->
                                </div>
                                
                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        {!! HTML::decode( Form::label('logo', trans("Logo").'<span
                                            class="text-danger"> * </span>')) !!}
                                        {{ Form::file('logo', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('logo') ? 'is-invalid':'')]) }}
                                        <div class="invalid-feedback"><?php echo $errors->first('logo'); ?></div>
                                        @if($model->logo != "")	
                                            <br />
                                            <a class="fancybox-buttons" data-fancybox-group="button" href="{{PARTNER_LOGO_URL.$model->logo}}">
                                            <img height="50" width="50" src="{{PARTNER_LOGO_URL.$model->logo}}" /></a>
									    @endif
                                    </div>
                                    <!--end::Input-->
                                </div>
                            </div>
                            <div class="d-flex justify-content-between border-top mt-5 pt-10">
                                <div>
									<a href='{{ route("$modelName.edit",$model->id)}}' class="btn btn-danger font-weight-bold text-uppercase px-9 py-4">{{ trans('Clear') }}</a>
									<a href="{{ route($modelName.'.index') }}" class="btn btn-info font-weight-bold text-uppercase px-9 py-4">{{ trans('Cancel') }}</a>
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
            {{ Form::close() }}
        </div>
    </div>
</div>
@stop