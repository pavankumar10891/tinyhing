@extends('admin.layouts.default')
@section('content')
<!--begin::Content-->
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
	<!--begin::Subheader-->
	<div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
		<div
			class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
			<!--begin::Info-->
			<div class="d-flex align-items-center flex-wrap mr-1">
				<!--begin::Page Heading-->
				<div class="d-flex align-items-baseline flex-wrap mr-5">
					<!--begin::Page Title-->
					<h5 class="text-dark font-weight-bold my-1 mr-5">
					View {{ $sectionNameSingular }}</h5>
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
			<div class="card card-custom gutter-b">
				<!--begin::Header-->
				<div class="card-header card-header-tabs-line">
					<div class="card-toolbar">
						<ul class="nav nav-tabs nav-tabs-space-lg nav-tabs-line nav-bold nav-tabs-line-3x"
							role="tablist">
							<li class="nav-item">
								<a class="nav-link active" data-toggle="tab"
									href="#kt_apps_contacts_view_tab_1">
									<span class="nav-text">
									{{ $sectionNameSingular }} Information
									</span>
								</a>
							</li>
						</ul>
					</div>
				</div>
				<!--end::Header-->

				<!--begin::Body-->
				<div class="dataTables_wrapper ">
					<div class="table-responsive">
						<table
							class="table dataTable table-head-custom table-head-bg table-borderless table-vertical-center"
							id="taskTable">
							<thead>
								<tr class="text-uppercase">
									<th>
										Date
									</th>
									<th>
										Time
									</th>

									<th>
										Day
									</th>
									
								</tr>
							</thead>
							<tbody>
								@if(!$results->isEmpty())
								@foreach($results as $detail)
								<tr>
									<td>
										<div class="text-dark-75 mb-1 font-size-lg">
										{{ date(Config::get("Reading.date_format"),strtotime($detail->booking_date)) }}
										</div>
									</td>
									<td>
										<div class="text-dark-75 mb-1 font-size-lg">
											{{ !empty($detail->from_time) ? date('h:i a', strtotime($detail->from_time)):'' }} - {{ !empty($detail->to_time) ? date('h:i a', strtotime($detail->to_time)) :'' }}
										</div>
									</td>
									<td>
										<div class="text-dark-75 mb-1 font-size-lg">
										{{ $detail->day }}	
										</div>
									</td>
								</tr>
								@endforeach
								@else
								<tr><td colspan="3" style="text-align:center;">{{ trans("Record not found.") }}</td></tr>
								@endif
							</tbody>
						</table>
					</div>
				</div>
				<!--end::Body-->
			</div>
		</div>
		<!--end::Container-->
	</div>
	<!--end::Entry-->
</div>
<!--end::Content-->
@stop