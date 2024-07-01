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
                        {{ $sectionName }} </h5>
                    <!--end::Page Title-->

                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard')}}" class="text-muted">Dashboard</a>
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
            {{ Form::open(['method' => 'get','role' => 'form','route' => "$modelName.index",'class' => 'kt-form kt-form--fit mb-0','autocomplete'=>"off"]) }}
            {{ Form::hidden('display') }}
            <div class="row">
                <div class="col-12">
                    <div class="card card-custom card-stretch card-shadowless">
                        <div class="card-header">
                            <div class="card-title">
                            </div>
                            <div class="card-toolbar">
                                <a href="javascript:void(0);" class="btn btn-primary dropdown-toggle mr-2"
                                    data-toggle="collapse" data-target="#collapseOne6">
                                    Search
                                </a>
                             
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample6">
                                <div id="collapseOne6"
                                    class="collapse <?php echo !empty($searchVariable) ? 'show' : ''; ?>"
                                    data-parent="#accordionExample6">
                                    <div>
                                        <div class="row mb-6">
                                           
                                         
                                            <div class="col-lg-4 mb-lg-5 mb-6">
                                                <label>Name</label>
                                                {{ Form::text('name',((isset($searchVariable['name'])) ? $searchVariable['name'] : ''), ['class' => ' form-control','placeholder'=>'Name']) }}
                                            </div>

                                            <div class="col-lg-4 mb-lg-5 mb-6">
                                                <label>Email</label>
                                                {{ Form::text('email',((isset($searchVariable['email'])) ? $searchVariable['email'] : ''), ['class' => ' form-control','placeholder'=>'Email']) }}
                                            </div>

                                            <div class="col-lg-4 mb-lg-5 mb-6">
                                                <label>Date From</label>
                                                <div class="input-group date" id="datepickerfrom"
                                                    data-target-input="nearest">
                                                    {{ Form::text('date_from',((isset($searchVariable['date_from'])) ? $searchVariable['date_from'] : ''), ['class' => ' form-control datetimepicker-input','placeholder'=>'Date To','data-target'=>'#datepickerfrom','data-toggle'=>'datetimepicker']) }}
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">
                                                            <i class="ki ki-calendar"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 mb-lg-5 mb-6">
                                                <label>Date To</label>
                                                <div class="input-group date" id="datepickerto"
                                                    data-target-input="nearest">
                                                    {{ Form::text('date_to',((isset($searchVariable['date_to'])) ? $searchVariable['date_to'] : ''), ['class' => ' form-control  datetimepicker-input','placeholder'=>'Date To','data-target'=>'#datepickerto','data-toggle'=>'datetimepicker']) }}
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">
                                                            <i class="ki ki-calendar"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div> 
                                         
                                        </div>

                                        <div class="row mt-8">
                                            <div class="col-lg-12">
                                                <button class="btn btn-primary btn-primary--icon" id="kt_search">
                                                    <span>
                                                        <i class="la la-search"></i>
                                                        <span>Search</span>
                                                    </span>
                                                </button>
                                                &nbsp;&nbsp;

                                                <a href='{{ route("$modelName.index")}}'
                                                    class="btn btn-secondary btn-secondary--icon">
                                                    <span>
                                                        <i class="la la-close"></i>
                                                        <span>Clear Search</span>
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
                                        <!--begin: Datatable-->
                                        <hr>
                                    </div>
                                </div>
                            </div>
                            <div class="dataTables_wrapper ">
                                <div class="table-responsive">
                                    <table
                                        class="table dataTable table-head-custom table-head-bg table-borderless table-vertical-center"
                                        id="taskTable">
                                        <thead>
                                            <tr class="text-uppercase">
                                                <th
                                                    class="{{(($sortBy == 'name' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'name' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
                                                    {{
														link_to_route(
															"$modelName.index",
															trans("Name"),
															array(
															'sortBy' => 'name',
															'order' => ($sortBy == 'name' && $order == 'desc') ? 'asc' : 'desc',
															$query_string
															)
														)
													}}
                                                </th>
                                                <th
                                                    class="{{(($sortBy == 'email' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'email' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
                                                    {{
														link_to_route(
															"$modelName.index",
															trans("Email"),
															array(
															'sortBy' => 'email',
															'order' => ($sortBy == 'email' && $order == 'desc') ? 'asc' : 'desc',
															$query_string
															)
														)
													}}
                                                </th>

                                                 <th
                                                    class="{{(($sortBy == 'subject' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'subject' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
                                                    {{
                                                        link_to_route(
                                                            "$modelName.index",
                                                            trans("Subject"),
                                                            array(
                                                            'sortBy' => 'subject',
                                                            'order' => ($sortBy == 'subject' && $order == 'desc') ? 'asc' : 'desc',
                                                            $query_string
                                                            )
                                                        )
                                                    }}
                                                </th>


                                                

                                                <th
                                                    class="{{(($sortBy == 'created_at' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'created_at' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
                                                        {{
                                                            link_to_route(
                                                                "$modelName.index",
                                                                trans("Created On"),
                                                                array(
                                                                'sortBy' => 'created_at',
                                                                'order' => ($sortBy == 'created_at' && $order == 'desc') ? 'asc' : 'desc',
                                                                $query_string
                                                                )
                                                            )
                                                        }}
                                                    </th>
                                            
                                                <th class="text-right">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(!$results->isEmpty())
                                            @foreach($results as $result)
                                            <tr>
                                                <td>
                                                    <div class="text-dark-75 mb-1 font-size-lg">
                                                        {{ $result->name }}
                                                    </div>
                                                </td>
                                                
                                                <td>
                                                    <div class="text-dark-75 mb-1 font-size-lg">
                                                        {{ $result->email }}
                                                    </div>
                                                </td>

                                                <td>
                                                    <div class="text-dark-75 mb-1 font-size-lg">
                                                        {{ !empty($result->subject) ? $result->subject:''}}
                                                    </div>
                                                </td>
                                                
                                                <td>
                                                    <div class="text-dark-75 mb-1 font-size-lg">
                                                    {{ date(config::get("Reading.date_format"),strtotime($result->created_at)) }}
                                                    </div>
                                                </td>
                                                
                                                <td class="text-right pr-2">
                                                   
                                                    <a href='{{route("$modelName.view","$result->id")}}'
                                                        class="btn btn-icon btn-light btn-hover-primary btn-sm"
                                                        data-toggle="tooltip" data-placement="top" data-container="body"
                                                        data-boundary="window" title="" data-original-title="View">
                                                        <span class="svg-icon svg-icon-md svg-icon-primary">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                height="24px" viewBox="0 0 24 24" version="1.1">
                                                                <g stroke="none" stroke-width="1" fill="none"
                                                                    fill-rule="evenodd">
                                                                    <rect x="0" y="0" width="24" height="24" />
                                                                    <path
                                                                        d="M12.8434797,16 L11.1565203,16 L10.9852159,16.6393167 C10.3352654,19.064965 7.84199997,20.5044524 5.41635172,19.8545019 C2.99070348,19.2045514 1.55121603,16.711286 2.20116652,14.2856378 L3.92086709,7.86762789 C4.57081758,5.44197964 7.06408298,4.00249219 9.48973122,4.65244268 C10.5421727,4.93444352 11.4089671,5.56345262 12,6.38338695 C12.5910329,5.56345262 13.4578273,4.93444352 14.5102688,4.65244268 C16.935917,4.00249219 19.4291824,5.44197964 20.0791329,7.86762789 L21.7988335,14.2856378 C22.448784,16.711286 21.0092965,19.2045514 18.5836483,19.8545019 C16.158,20.5044524 13.6647346,19.064965 13.0147841,16.6393167 L12.8434797,16 Z M17.4563502,18.1051865 C18.9630797,18.1051865 20.1845253,16.8377967 20.1845253,15.2743923 C20.1845253,13.7109878 18.9630797,12.4435981 17.4563502,12.4435981 C15.9496207,12.4435981 14.7281751,13.7109878 14.7281751,15.2743923 C14.7281751,16.8377967 15.9496207,18.1051865 17.4563502,18.1051865 Z M6.54364977,18.1051865 C8.05037928,18.1051865 9.27182488,16.8377967 9.27182488,15.2743923 C9.27182488,13.7109878 8.05037928,12.4435981 6.54364977,12.4435981 C5.03692026,12.4435981 3.81547465,13.7109878 3.81547465,15.2743923 C3.81547465,16.8377967 5.03692026,18.1051865 6.54364977,18.1051865 Z"
                                                                        fill="#000000" />
                                                                </g>
                                                            </svg>
                                                        </span>
                                                    </a>
                                              
                                                   <?php /*<a href='{{route("$modelName.view","$result->id")}}#reply' class="btn btn-icon btn-light btn-hover-primary btn-sm"
                                                        data-toggle="tooltip" data-placement="top" data-container="body"
                                                        data-boundary="window" title="" data-original-title="Reply">
                                                        <span class="svg-icon menu-icon">
                                                                <!--begin::Svg Icon | path:assets/media/svg/icons/Design/Layers.svg-->
                                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                    <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                                                    <path d="M12.9336061,16.072447 L19.36,10.9564761 L19.5181585,10.8312381 C20.1676248,10.3169571 20.2772143,9.3735535 19.7629333,8.72408713 C19.6917232,8.63415859 19.6104327,8.55269514 19.5206557,8.48129411 L12.9336854,3.24257445 C12.3871201,2.80788259 11.6128799,2.80788259 11.0663146,3.24257445 L4.47482784,8.48488609 C3.82645598,9.00054628 3.71887192,9.94418071 4.23453211,10.5925526 C4.30500305,10.6811601 4.38527899,10.7615046 4.47382636,10.8320511 L4.63,10.9564761 L11.0659024,16.0730648 C11.6126744,16.5077525 12.3871218,16.5074963 12.9336061,16.072447 Z" fill="#000000" fill-rule="nonzero"></path>
                                                                    <path d="M11.0563554,18.6706981 L5.33593024,14.122919 C4.94553994,13.8125559 4.37746707,13.8774308 4.06710397,14.2678211 C4.06471678,14.2708238 4.06234874,14.2738418 4.06,14.2768747 L4.06,14.2768747 C3.75257288,14.6738539 3.82516916,15.244888 4.22214834,15.5523151 C4.22358765,15.5534297 4.2250303,15.55454 4.22647627,15.555646 L11.0872776,20.8031356 C11.6250734,21.2144692 12.371757,21.2145375 12.909628,20.8033023 L19.7677785,15.559828 C20.1693192,15.2528257 20.2459576,14.6784381 19.9389553,14.2768974 C19.9376429,14.2751809 19.9363245,14.2734691 19.935,14.2717619 L19.935,14.2717619 C19.6266937,13.8743807 19.0546209,13.8021712 18.6572397,14.1104775 C18.654352,14.112718 18.6514778,14.1149757 18.6486172,14.1172508 L12.9235044,18.6705218 C12.377022,19.1051477 11.6029199,19.1052208 11.0563554,18.6706981 Z" fill="#000000" opacity="0.3"></path>
                                                                </g>
                                                            </svg>
                                                            <!--end::Svg Icon-->
                                                        </span>
                                                        <span class="menu-text"></span>
                                                    </a> */ ?>
                                                  
                                                </td>
                                            </tr>
                                            @endforeach
                                            @else
                                            <tr><td colspan="6" style="text-align:center;">{{ trans("Record not found.") }}</td></tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                @include('pagination.default', ['results' => $results])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{ Form::close() }}
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
<!--end::Content-->

<script>
$(document).ready(function() {
    $('#datepickerfrom').datetimepicker({
        format: 'YYYY-MM-DD'
    });
    $('#datepickerto').datetimepicker({
        format: 'YYYY-MM-DD'
    });

    $(".confirmDelete").click(function(e) {
        e.stopImmediatePropagation();
        url = $(this).attr('href');
        Swal.fire({
            title: "Are you sure?",
            text: "Want to delete this ?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it",
            cancelButtonText: "No, cancel",
            reverseButtons: true
        }).then(function(result) {
            if (result.value) {
                window.location.replace(url);
            } else if (result.dismiss === "cancel") {
                Swal.fire(
                    "Cancelled",
                    "Your imaginary file is safe :)",
                    "error"
                )
            }
        });
        e.preventDefault();
    });

    $(".status_any_item").click(function(e) {
        e.stopImmediatePropagation();
        url = $(this).attr('href');
        Swal.fire({
            title: "Are you sure?",
            text: "Want to change status this ?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, change it",
            cancelButtonText: "No, cancel",
            reverseButtons: true
        }).then(function(result) {
            if (result.value) {
                window.location.replace(url);
            }
        });
        e.preventDefault();
    });
});

function page_limit() {
    $("form").submit();
}
</script>
<style>
.label.label-inline.label-lg {
    padding: 1.1rem 0.75rem;
}
</style>
@stop