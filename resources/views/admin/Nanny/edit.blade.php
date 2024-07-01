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
          {{ Form::open(['role' => 'form','url' =>  route("$modelName.edit",$model->id),'class' => 'mws-form', 'files' => true,"autocomplete"=>"off","enctype"=>"multipart/form-data"]) }}
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
                                        {!! HTML::decode( Form::label('email', trans("Email").'<span
                                            class="text-danger"> * </span>')) !!}
                                        {{ Form::text('email', isset($model->email) ? $model->email : '', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('email') ? 'is-invalid':'')]) }}
                                        <div class="invalid-feedback"><?php echo $errors->first('email'); ?></div>
                                    </div>
                                    <!--end::Input-->
                                </div>
                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        {!! HTML::decode( Form::label('phone', trans("Phone No.").'<span
                                            class="text-danger"> * </span>')) !!}
											{{ Form::text('phone_number',isset($model->phone_number) ? $model->phone_number : '', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('phone_number') ? 'is-invalid':'')]) }}
                                        <div class="invalid-feedback"><?php echo $errors->first('phone_number'); ?></div>
                                    </div>
                                    <!--end::Input-->
                                </div>
                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        {!! HTML::decode( Form::label('age', trans("Age").'<span
                                            class="text-danger"> * </span>')) !!}
											{{ Form::text('age',isset($model->age) ? $model->age : '', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('age') ? 'is-invalid':'')]) }}
                                        <div class="invalid-feedback"><?php echo $errors->first('age'); ?></div>
                                    </div>
                                    <!--end::Input-->
                                </div>

                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        {!! HTML::decode( Form::label('experience', trans("Experience").'<span
                                            class="text-danger"> * </span>')) !!}
											{{ Form::text('experience',isset($model->experience) ? $model->experience : '', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('experience') ? 'is-invalid':'')]) }}
                                        <div class="invalid-feedback"><?php echo $errors->first('experience'); ?></div>
                                    </div>
                                    <!--end::Input-->
                                </div>


                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        {!! HTML::decode( Form::label('password', trans("Password"))) !!}
                                        {{ Form::password('password', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('password') ? 'is-invalid':'')]) }}
                                        <div class="invalid-feedback"><?php echo $errors->first('password'); ?></div>
                                    </div>
                                    <!--end::Input-->
                                </div>

                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        {!! HTML::decode( Form::label('confirm_password', trans("Confirm Password"))) !!}
                                        {{ Form::password('confirm_password', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('confirm_password') ? 'is-invalid':'')]) }}
                                        <div class="invalid-feedback"><?php echo $errors->first('confirm_password'); ?></div>
                                    </div>
                                    <!--end::Input-->
                                </div>

                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        {!! HTML::decode( Form::label('postcode', trans("Zip Code").'<span
                                            class="text-danger"> * </span>')) !!}
											{{ Form::text('postcode',isset($model->postcode) ? $model->postcode : '', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('postcode') ? 'is-invalid':'')]) }}
                                        <div class="invalid-feedback"><?php echo $errors->first('postcode'); ?></div>
                                    </div>
                                    <!--end::Input-->
                                </div>

                                 <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        {!! HTML::decode( Form::label('nanny_price', trans("Fees").'<span
                                            class="text-danger"> * </span>')) !!}
                                            {{ Form::text('nanny_price',isset($model->nanny_price) ? $model->nanny_price : '', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('nanny_price') ? 'is-invalid':'')]) }}
                                        <div class="invalid-feedback"><?php echo $errors->first('nanny_price'); ?></div>
                                    </div>
                                    <!--end::Input-->
                                </div>

                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        {!! HTML::decode( Form::label('photo_id', trans("Photo ID").'<span
                                            class="text-danger"> * </span>')) !!}
                                        {{ Form::file('photo_id', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('photo_id') ? 'is-invalid':'')]) }}
									    <div class="invalid-feedback"><?php echo $errors->first('photo_id'); ?></div>

                                            @if($model->photo_id != "")
                                                <br />
                                                <a class="fancybox-buttons" data-fancybox-group="button" href="{{ USER_IMAGE_URL.$model->photo_id }}" >
                                                    <img class="" src="{{ USER_IMAGE_URL.$model->photo_id }}" width="60px" height="60px">
                                                </a>
                                            @endif
                                    </div>
                                    <!--end::Input-->
                                </div>

                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        {!! HTML::decode( Form::label('resume', trans("Resume").'<span
                                            class="text-danger"> </span>')) !!}
                                        {{ Form::file('resume', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('resume') ? 'is-invalid':'')]) }}
									    <div class="invalid-feedback"><?php echo $errors->first('resume'); ?></div>

                                            @if($model->resume != "")
                                                <br />
                                                <a href="{{ CERTIFICATES_AND_FILES_URL.$model->resume }}" download="{{ $model->resume }}" title="Click To Download" >
													<img src="{{ WEBSITE_IMG_URL.'download.png' }}" width="12%" height="12%">
                                            	</a>
                                            @endif
                                    </div>
                                    <!--end::Input-->
                                </div>

                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        {!! HTML::decode( Form::label('identification_type', trans("Identification Type").' <span
                                            class="text-danger">*</span>')) !!}<br>
                                            Passport <input class="identification_type mr-3 form-control-solid" type="radio" name="identification_type" value="1" {{($model->identification_type==1)?'checked':''}}>
                                            Driving License <input class="identification_type form-control-solid" type="radio" name="identification_type" value="2" {{($model->identification_type==2)?'checked':''}}>
                                        <div class="invalid-feedback"><?php echo $errors->first('identification_type'); ?></div>
                                    </div>
                                <!--end::Input-->
                                </div>

                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group identification_file1">
                                        {!! HTML::decode( Form::label('identification_file', trans("Upload your Passport").' <span
                                            class="text-danger">*</span>')) !!}
                                        {{ Form::file('identification_file', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('identification_file') ? 'is-invalid':'')]) }}
                                        <div class="invalid-feedback"><?php echo $errors->first('identification_file'); ?></div>
                                        @if($model->identification_file != "")
                                                <br />
                                                <a href="{{ CERTIFICATES_AND_FILES_URL.$model->identification_file }}" download="{{ $model->identification_file }}" title="Click To Download" >
													<img src="{{ WEBSITE_IMG_URL.'download.png' }}" width="12%" height="12%">
                                            	</a>
                                        @endif
                                    </div>
                                <!--end::Input-->
                                </div>

                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        {!! HTML::decode( Form::label('cpr_certificate', trans("CPR Certificate").'<span
                                            class="text-danger"> </span>')) !!}
                                        {{ Form::file('cpr_certificate', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('cpr_certificate') ? 'is-invalid':'')]) }}
									    <div class="invalid-feedback"><?php echo $errors->first('cpr_certificate'); ?></div>

                                            @if($model->cpr_certificate != "")
                                                <br />
                                                <a href="{{ CERTIFICATES_AND_FILES_URL.$model->cpr_certificate }}" download="{{ $model->cpr_certificate }}" title="Click To Download">
													<img src="{{ WEBSITE_IMG_URL.'download.png' }}" width="12%" height="12%"> 
                                            	</a>
                                            @endif
                                    </div>
                                    <!--end::Input-->
                                </div>

                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        {!! HTML::decode( Form::label('other_certificates', trans("Other Certificates").'<span
                                            class="text-danger"> </span>')) !!}
                                        {{ Form::file('other_certificates', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('other_certificates') ? 'is-invalid':''),
                                        'name'=>'other_certificates']) }}
                                        <br />

                                        <ul class="list-unstyled mb-0 editDelete_list">
                                            @if(!empty($userCertificateData))
                                                @foreach($userCertificateData as $certificate)
                                                <li class="mb-2 mr-2 position-relative">
                                                    <a href="{{ OTHER_CERTIFICATES_DOCUMENT_URL.$certificate['other_certificates'] }}" download="{{ $certificate['other_certificates']  }}" title="Click To Download">
                                                        <img src="{{ WEBSITE_IMG_URL.'download.png' }}" class="w-70px">
                                                    </a>                                    
                                                    <i class="far fa-trash-alt remove-certificate" data="{{$certificate['id']}}" title="Click To Delete"></i>
                                                </li>
                                                @endforeach
                                            @endif
                                        </ul>

                                        <div class="invalid-feedback"><?php echo $errors->first('other_certificates'); ?></div>
                                    </div>

                                <!--end::Input-->
                                </div>
                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        {!! HTML::decode( Form::label('description', trans("Description").'<span
                                            class="text-danger"> * </span>')) !!}
                                            {{ Form::textarea('description',isset($model->description) ? $model->description : '', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('description') ? 'is-invalid':'')]) }}
                                        <div class="invalid-feedback"><?php echo $errors->first('description'); ?></div>
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
            {{ Form::close() }}
        </div>
    </div>
</div>

<script>
$value = "{{(isset($model->identification_type))?$model->identification_type:''}}";
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
        var url='{{route("$modelName.certificates","id")}}';
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
@stop