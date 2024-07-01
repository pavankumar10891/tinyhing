
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
                                
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample6">
                                <div id="collapseOne6"
                                    class="collapse <?php echo !empty($searchVariable) ? 'show' : ''; ?>"
                                    data-parent="#accordionExample6">
                                    <div>
                                        <div class="row mb-6">
                                         <!--   <div class="col-lg-4 mb-lg-5 mb-6">
                                                <label>Status</label>
                                                <?php echo e(Form::select('is_active',array(''=>trans('All'),1=>trans('Active'),0=>trans('Deactive')),((isset($searchVariable['is_active'])) ? $searchVariable['is_active'] : ''), ['class' => 'form-control select2init'])); ?>

                                            </div>  -->
                                            <div class="col-lg-4 mb-lg-5 mb-6">
                                                <label>Nanny Name</label>
                                                <?php echo e(Form::text('nanny_name',((isset($searchVariable['nanny_name'])) ? $searchVariable['nanny_name'] : ''), ['class' => ' form-control','placeholder'=>'Nanny Name'])); ?>

                                            </div>
                                            <div class="col-lg-4 mb-lg-5 mb-6">
                                                <label>Client Name</label>
                                                <?php echo e(Form::text('client_name',((isset($searchVariable['client_name'])) ? $searchVariable['client_name'] : ''), ['class' => ' form-control','placeholder'=>'Client Name'])); ?>

                                            </div>
                                            
                                            <div class="col-lg-4 mb-lg-5 mb-6">
                                                <label>Interview Date</label>
                                                <div class="input-group date" id="datepickerinterview"
                                                    data-target-input="nearest">
                                                    <?php echo e(Form::text('interview_date',((isset($searchVariable['interview_date'])) ? $searchVariable['interview_date'] : ''), ['class' => ' form-control datetimepicker-input','placeholder'=>'Interview Date','data-target'=>'#datepickerinterview','data-toggle'=>'datetimepicker'])); ?>

                                                    <div class="input-group-append">
                                                        <span class="input-group-text">
                                                            <i class="ki ki-calendar"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                           <!-- <div class="col-lg-4 mb-lg-5 mb-6">
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
                                            </div>   -->
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
                                                    class="<?php echo e((($sortBy == 'nanny_name' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'nanny_name' && $order == 'asc') ? 'sorting_asc' : 'sorting'))); ?>">
                                                    <?php echo e(link_to_route(
															"$modelName.index",
															trans("Nanny Name"),
															array(
															'sortBy' => 'nanny_name',
															'order' => ($sortBy == 'nanny_name' && $order == 'desc') ? 'asc' : 'desc',
															$query_string
															)
														)); ?>

                                                </th>
                                                <th
                                                    class="<?php echo e((($sortBy == 'client_name' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'client_name' && $order == 'asc') ? 'sorting_asc' : 'sorting'))); ?>">
                                                    <?php echo e(link_to_route(
															"$modelName.index",
															trans("Client Name"),
															array(
															'sortBy' => 'client_name',
															'order' => ($sortBy == 'client_name' && $order == 'desc') ? 'asc' : 'desc',
															$query_string
															)
														)); ?>

                                                </th>
                                                <th
                                                    class="<?php echo e((($sortBy == 'time_slot' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'time_slot' && $order == 'asc') ? 'sorting_asc' : 'sorting'))); ?>">
                                                    <?php echo e(link_to_route(
															"$modelName.index",
															trans("Time Slot"),
															array(
															'sortBy' => 'time_slot',
															'order' => ($sortBy == 'time_slot' && $order == 'desc') ? 'asc' : 'desc',
															$query_string
															)
														)); ?>

                                                </th>
                                                <th
                                                    class="<?php echo e((($sortBy == 'interview_date' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'interview_date' && $order == 'asc') ? 'sorting_asc' : 'sorting'))); ?>">
                                                    <?php echo e(link_to_route(
															"$modelName.index",
															trans("Interview Date"),
															array(
															'sortBy' => 'interview_date',
															'order' => ($sortBy == 'interview_date' && $order == 'desc') ? 'asc' : 'desc',
															$query_string
															)
														)); ?>

                                                </th>
                                                <th
                                                    class="<?php echo e((($sortBy == 'status' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'status' && $order == 'asc') ? 'sorting_asc' : 'sorting'))); ?>">
                                                    <?php echo e(link_to_route(
															"$modelName.index",
															trans("Status"),
															array(
															'sortBy' => 'status',
															'order' => ($sortBy == 'status' && $order == 'desc') ? 'asc' : 'desc',
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
                                                        <?php echo e($result->nanny_name); ?>

                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-dark-75 mb-1 font-size-lg">
                                                        <?php echo e($result->client_name); ?>

                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-dark-75 mb-1 font-size-lg">
                                                        <?php echo e($result->time_slot); ?>

                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-dark-75 mb-1 font-size-lg">
                                                        <?php echo e(date(config::get("Reading.date_format"),strtotime($result->interview_date))); ?>

                                                    </div>
                                                </td>
                                                <td>
                                                    <?php if($result->status == 1): ?>
                                                    <span
                                                        class="label label-lg label-light-success label-inline">Activated</span>
                                                    <?php else: ?>
                                                    <span
                                                        class="label label-lg label-light-danger label-inline">Deactivated</span>
                                                    <?php endif; ?>
                                                   
                                                </td>
                                                <td class="text-right pr-2">
                                                    <?php if($result->interview_date ==date('Y-m-d')): ?>
                                                    <a href="<?php echo e(route('admin.meeting.join', $result->id)); ?>">Join Now</a>
                                                    <?php endif; ?>
                                                    <a href='<?php echo e(route("$modelName.view",array($result->id))); ?>'
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

    $('#datepickerinterview').datetimepicker({
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
<?php echo $__env->make('admin.layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\xampp\htdocs\tinyhugs\resources\views/admin/ScheduleInterview/index.blade.php ENDPATH**/ ?>