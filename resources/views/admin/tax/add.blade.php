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
            {{ Form::open(['role' => 'form','route' => "$modelName.add",'class' => 'mws-form taxForm', 'files' => true,"autocomplete"=>"off"]) }}
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
                                        {!! HTML::decode( Form::label('city', trans("City").'<span
                                        class="text-danger"> * </span>')) !!}
                                        {{ Form::text('city','', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('city') ? 'is-invalid':''),'id'=>'city','autocomplete'=>'off']) }}
                                        <div class="invalid-feedback"><?php echo $errors->first('city'); ?></div>
                                    </div>
                                    <!--end::Input-->
                                </div>
                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        {!! HTML::decode( Form::label('tax', trans("Tax(%)").'<span
                                        class="text-danger"> * </span>')) !!}
                                        {{ Form::text('tax','', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('tax') ? 'is-invalid':'')]) }}
                                        <div class="invalid-feedback"><?php echo $errors->first('tax'); ?></div>
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
        {{ Form::close() }}
    </div>
</div>
</div>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyAzwqCO7sws38TcKsEqh5ke7pIN_ER2UZM"></script>

<script>


    $('.taxForm').keydown(function (e) {
        if (e.keyCode == 13) {
            e.preventDefault();
            return false;
        }
    });
    var options = {
      types: ['(cities)']
  };

  var input=document.getElementById('city');
  var autocomplete= new google.maps.places.Autocomplete(input,options);
  google.maps.event.addListener(autocomplete, 'place_changed', function () {
    var place = autocomplete.getPlace();
    var address = place.formatted_address;
    var addressArray=address.split(',');
    document.getElementById('city').value = addressArray[0];
    var latitude = place.geometry.location.lat();
    var longitude = place.geometry.location.lng();
    var latlng = new google.maps.LatLng(latitude, longitude);
    var geocoder = geocoder = new google.maps.Geocoder();

    geocoder.geocode({ 'latLng': latlng }, function (results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            if (results[0]) {
                var state = results[0].address_components[results[0].address_components.length - 3].long_name;                        
                document.getElementById('state').value = state;
            }
        }
    });
});

</script>

@stop