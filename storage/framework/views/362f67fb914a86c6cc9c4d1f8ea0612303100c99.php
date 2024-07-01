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
                        <?php echo e($sectionName); ?> </h5>
                    <!--end::Page Title-->

                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="<?php echo e(route('dashboard')); ?>" class="text-muted">Dashboard</a>
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
            <?php echo e(Form::open(['method' => 'get','role' => 'form','route' => "$modelName.index",'class' => 'kt-form kt-form--fit mb-0','autocomplete'=>"off"])); ?>

            <?php echo e(Form::hidden('display')); ?>

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
                                <a href='<?php echo e(route("$modelName.add")); ?>' class="btn btn-primary">
                                    <?php echo e(trans("Add New ")); ?><?php echo e($sectionNameSingular); ?> </a>
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
                                                <label>Status</label>
                                                <?php echo e(Form::select('is_active',array(''=>trans('All'),1=>trans('Active'),0=>trans('Deactive')),((isset($searchVariable['is_active'])) ? $searchVariable['is_active'] : ''), ['class' => 'form-control select2init'])); ?>

                                            </div>
                                            <div class="col-lg-4 mb-lg-5 mb-6">
                                                <label>Name</label>
                                                <?php echo e(Form::text('name',((isset($searchVariable['name'])) ? $searchVariable['name'] : ''), ['class' => ' form-control','placeholder'=>'Name'])); ?>

                                            </div>
                                            <div class="col-lg-4 mb-lg-5 mb-6">
                                                <label>Email</label>
                                                <?php echo e(Form::text('email',((isset($searchVariable['email'])) ? $searchVariable['email'] : ''), ['class' => ' form-control','placeholder'=>'Email'])); ?>

                                            </div>
                                            <div class="col-lg-4 mb-lg-5 mb-6">
                                                <label>Phone Number</label>
                                                <?php echo e(Form::text('phone_number',((isset($searchVariable['phone_number'])) ? $searchVariable['phone_number'] : ''), ['class' => ' form-control','placeholder'=>'Phone Number'])); ?>

                                            </div>
                                            <div class="col-lg-4 mb-lg-5 mb-6">
                                                <label>Date From</label>
                                                <div class="input-group date" id="datepickerfrom"
                                                    data-target-input="nearest">
                                                    <?php echo e(Form::text('date_from',((isset($searchVariable['date_from'])) ? $searchVariable['date_from'] : ''), ['class' => ' form-control datetimepicker-input','placeholder'=>'Date To','data-target'=>'#datepickerfrom','data-toggle'=>'datetimepicker'])); ?>

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
                                                    <?php echo e(Form::text('date_to',((isset($searchVariable['date_to'])) ? $searchVariable['date_to'] : ''), ['class' => ' form-control  datetimepicker-input','placeholder'=>'Date To','data-target'=>'#datepickerto','data-toggle'=>'datetimepicker'])); ?>

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

                                                <a href='<?php echo e(route("$modelName.index")); ?>'
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
                                                    class="<?php echo e((($sortBy == 'name' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'name' && $order == 'asc') ? 'sorting_asc' : 'sorting'))); ?>">
                                                    <?php echo e(link_to_route(
															"$modelName.index",
															trans("Name"),
															array(
															'sortBy' => 'name',
															'order' => ($sortBy == 'name' && $order == 'desc') ? 'asc' : 'desc',
															$query_string
															)
														)); ?>

                                                </th>
                                                <th
                                                    class="<?php echo e((($sortBy == 'email' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'email' && $order == 'asc') ? 'sorting_asc' : 'sorting'))); ?>">
                                                    <?php echo e(link_to_route(
															"$modelName.index",
															trans("Email"),
															array(
															'sortBy' => 'email',
															'order' => ($sortBy == 'email' && $order == 'desc') ? 'asc' : 'desc',
															$query_string
															)
														)); ?>

                                                </th>
                                                <th
                                                    class="<?php echo e((($sortBy == 'phone_number' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'phone_number' && $order == 'asc') ? 'sorting_asc' : 'sorting'))); ?>">
                                                    <?php echo e(link_to_route(
															"$modelName.index",
															trans("Phone Number"),
															array(
															'sortBy' => 'phone_number',
															'order' => ($sortBy == 'phone_number' && $order == 'desc') ? 'asc' : 'desc',
															$query_string
															)
														)); ?>

                                                </th>

                                                <th
                                                    class="<?php echo e((($sortBy == 'created_at' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'created_at' && $order == 'asc') ? 'sorting_asc' : 'sorting'))); ?>">
                                                    <?php echo e(link_to_route(
															"$modelName.index",
															trans("Registered On"),
															array(
															'sortBy' => 'created_at',
															'order' => ($sortBy == 'created_at' && $order == 'desc') ? 'asc' : 'desc',
															$query_string
															)
														)); ?>

                                                </th>
                                                <th
                                                    class="<?php echo e((($sortBy == 'is_active' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'is_active' && $order == 'asc') ? 'sorting_asc' : 'sorting'))); ?>">
                                                    <?php echo e(link_to_route(
															"$modelName.index",
															trans("Status"),
															array(
															'sortBy' => 'is_active',
															'order' => ($sortBy == 'is_active' && $order == 'desc') ? 'asc' : 'desc',
															$query_string
															)
														)); ?>

                                                </th>
                                                <th class="text-right">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(!$results->isEmpty()): ?>
                                            <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td>
                                                    <div class="text-dark-75 mb-1 font-size-lg">
                                                        <?php echo e($result->name); ?>

                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-dark-75 mb-1 font-size-lg">
                                                        <?php echo e($result->email); ?>

                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-dark-75 mb-1 font-size-lg">
                                                        <?php echo e($result->phone_number); ?>

                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-dark-75 mb-1 font-size-lg">
                                                        <?php echo e(date(config::get("Reading.date_format"),strtotime($result->created_at))); ?>

                                                    </div>
                                                </td>
                                                <td>
                                                    <?php if($result->is_active == 1): ?>
                                                    <span
                                                        class="label label-lg label-light-success label-inline">Activated</span>
                                                    <?php else: ?>
                                                    <span
                                                        class="label label-lg label-light-danger label-inline">Deactivated</span>
                                                    <?php endif; ?>

                                                    <!-- <?php if($result->verified == 1): ?>
                                                    <span
                                                        class="label label-lg label-light-success label-inline">Verified</span>
                                                    <?php else: ?>
                                                    <span
                                                        class="label label-lg label-light-danger label-inline">Verification
                                                        Pending</span>
                                                    <?php endif; ?> -->
                                                </td>
                                                <td class="text-right pr-2">
                                                    <?php if($result->is_active == 1): ?>
                                                    <a title="Click To Deactivate"
                                                        href='<?php echo e(route("$modelName.status",array($result->id))); ?>'
                                                        class="btn btn-icon btn-light btn-hover-danger btn-sm status_any_item"
                                                        data-toggle="tooltip" data-placement="top" data-container="body"
                                                        data-boundary="window" data-original-title="Deactivate">
                                                        <span class="svg-icon svg-icon-md svg-icon-danger">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                height="24px" viewBox="0 0 24 24" version="1.1">
                                                                <g stroke="none" stroke-width="1" fill="none"
                                                                    fill-rule="evenodd">
                                                                    <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)"
                                                                        fill="#000000">
                                                                        <rect x="0" y="7" width="16" height="2"
                                                                            rx="1" />
                                                                        <rect opacity="0.3"
                                                                            transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000) "
                                                                            x="0" y="7" width="16" height="2" rx="1" />
                                                                    </g>
                                                                </g>
                                                            </svg>
                                                        </span>
                                                    </a>
                                                    <?php else: ?>
                                                    <a title="Click To Activate"
                                                        href='<?php echo e(route("$modelName.status",array($result->id))); ?>'
                                                        class="btn btn-icon btn-light btn-hover-success btn-sm status_any_item"
                                                        data-toggle="tooltip" data-placement="top" data-container="body"
                                                        data-boundary="window" data-original-title="Activate">
                                                        <span class="svg-icon svg-icon-md svg-icon-success">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                height="24px" viewBox="0 0 24 24" version="1.1">
                                                                <g stroke="none" stroke-width="1" fill="none"
                                                                    fill-rule="evenodd">
                                                                    <polygon points="0 0 24 0 24 24 0 24" />
                                                                    <path
                                                                        d="M9.26193932,16.6476484 C8.90425297,17.0684559 8.27315905,17.1196257 7.85235158,16.7619393 C7.43154411,16.404253 7.38037434,15.773159 7.73806068,15.3523516 L16.2380607,5.35235158 C16.6013618,4.92493855 17.2451015,4.87991302 17.6643638,5.25259068 L22.1643638,9.25259068 C22.5771466,9.6195087 22.6143273,10.2515811 22.2474093,10.6643638 C21.8804913,11.0771466 21.2484189,11.1143273 20.8356362,10.7474093 L17.0997854,7.42665306 L9.26193932,16.6476484 Z"
                                                                        fill="#000000" fill-rule="nonzero" opacity="0.3"
                                                                        transform="translate(14.999995, 11.000002) rotate(-180.000000) translate(-14.999995, -11.000002) " />
                                                                    <path
                                                                        d="M4.26193932,17.6476484 C3.90425297,18.0684559 3.27315905,18.1196257 2.85235158,17.7619393 C2.43154411,17.404253 2.38037434,16.773159 2.73806068,16.3523516 L11.2380607,6.35235158 C11.6013618,5.92493855 12.2451015,5.87991302 12.6643638,6.25259068 L17.1643638,10.2525907 C17.5771466,10.6195087 17.6143273,11.2515811 17.2474093,11.6643638 C16.8804913,12.0771466 16.2484189,12.1143273 15.8356362,11.7474093 L12.0997854,8.42665306 L4.26193932,17.6476484 Z"
                                                                        fill="#000000" fill-rule="nonzero"
                                                                        transform="translate(9.999995, 12.000002) rotate(-180.000000) translate(-9.999995, -12.000002) " />
                                                                </g>
                                                            </svg>
                                                        </span>
                                                    </a>
                                                    <?php endif; ?>

                                                    <a href='<?php echo e(route("$modelName.view","$result->id")); ?>'
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
                                                    <a href='<?php echo e(route("$modelName.edit","$result->id")); ?>'
                                                        class="btn btn-icon btn-light btn-hover-primary btn-sm"
                                                        data-toggle="tooltip" data-placement="top" data-container="body"
                                                        data-boundary="window" title="" data-original-title="Edit">
                                                        <span class="svg-icon svg-icon-md svg-icon-primary">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                height="24px" viewBox="0 0 24 24" version="1.1">
                                                                <g stroke="none" stroke-width="1" fill="none"
                                                                    fill-rule="evenodd">
                                                                    <rect x="0" y="0" width="24" height="24" />
                                                                    <path
                                                                        d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z"
                                                                        fill="#000000" opacity="0.3" />
                                                                    <path
                                                                        d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z"
                                                                        fill="#000000" />
                                                                </g>
                                                            </svg>
                                                        </span>
                                                    </a>

                                                    <a href='<?php echo e(route("$modelName.delete","$result->id")); ?>'
                                                        class="btn btn-icon btn-light btn-hover-danger btn-sm confirmDelete"
                                                        data-toggle="tooltip" data-placement="top" data-container="body"
                                                        data-boundary="window" title="" data-original-title="Delete">
                                                        <span class="svg-icon svg-icon-md svg-icon-danger">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                height="24px" viewBox="0 0 24 24" version="1.1">
                                                                <g stroke="none" stroke-width="1" fill="none"
                                                                    fill-rule="evenodd">
                                                                    <rect x="0" y="0" width="24" height="24" />
                                                                    <path
                                                                        d="M6,8 L6,20.5 C6,21.3284271 6.67157288,22 7.5,22 L16.5,22 C17.3284271,22 18,21.3284271 18,20.5 L18,8 L6,8 Z"
                                                                        fill="#000000" fill-rule="nonzero" />
                                                                    <path
                                                                        d="M14,4.5 L14,4 C14,3.44771525 13.5522847,3 13,3 L11,3 C10.4477153,3 10,3.44771525 10,4 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z"
                                                                        fill="#000000" opacity="0.3" />
                                                                </g>
                                                            </svg>
                                                        </span>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php else: ?>
                                            <tr><td colspan="6" style="text-align:center;"><?php echo e(trans("Record not found.")); ?></td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php echo $__env->make('pagination.default', ['results' => $results], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo e(Form::close()); ?>

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

<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\tinyhugs\resources\views/admin/Support/index.blade.php ENDPATH**/ ?>