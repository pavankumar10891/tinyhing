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
						{{ $sectionName }} </h5>
					<!--end::Page Title-->

					<!--begin::Breadcrumb-->
					<ul
						class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
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
			{{ Form::open(['method' => 'get','role' => 'form','route' => "$modelName.index",'class' => 'kt-form kt-form--fit mb-0','id'=>'searchForm','autocomplete'=>"off"]) }}
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
							<div class="accordion accordion-solid accordion-toggle-plus"
								id="accordionExample6">
								<div id="collapseOne6" class="collapse <?php echo !empty($searchVariable) ? 'show' : ''; ?>" data-parent="#accordionExample6">
									<div>
											<div class="row mb-6">
												
												<div class="col-lg-3 mb-lg-5 mb-6">
                                                    <label>Date From</label>
                                                    <div class="input-group date" id="datepickerfrom"
                                                        data-target-input="nearest">
                                                        {{ Form::text('date_from',((isset($searchVariable['date_from'])) ? $searchVariable['date_from'] : ''), ['class' => ' form-control datetimepicker-input','placeholder'=>'Date From','data-target'=>'#datepickerfrom','data-toggle'=>'datetimepicker']) }}
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">
                                                                <i class="ki ki-calendar"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 mb-lg-5 mb-6">
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
													<button class="btn btn-primary btn-primary--icon"
														id="kt_search">
														<span>
															<i class="la la-search"></i>
															<span>Search</span>
														</span>
													</button>
													&nbsp;&nbsp;
													
													<a href='{{ route("$modelName.index")}}'  class="btn btn-secondary btn-secondary--icon">
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
												<th class="{{(($sortBy == 'message' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'message' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
													{{
														link_to_route(
															"$modelName.index",
															trans("Message"),
															array(
															'sortBy' => 'message',
															'order' => ($sortBy == 'message' && $order == 'desc') ? 'asc' : 'desc',
															$query_string
															)
														)
													}}
												</th>
												
												
												<th class="{{(($sortBy == 'created_at' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'created_at' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
													{!!
														html_entity_decode(link_to_route(
															"$modelName.index",
															trans("Date"),
															array(
															'sortBy' => 'created_at',
															'order' => ($sortBy == 'created_at' && $order == 'desc') ? 'asc' : 'desc',
															$query_string
															)
														))
													!!}
												</th>
											</tr>
										</thead>
										<tbody>
											@if(!$results->isEmpty())
												@foreach($results as $result)
													<tr>
														<td>
															<div class="text-dark-75 mb-1 font-size-lg">
																{{ $result->message }}
															</div>
														</td>
														<td>
															<div class="text-dark-75 mb-1 font-size-lg">
															{{ date(config::get("Reading.date_format"),strtotime($result->created_at)) }}
															</div>
														</td>
													</tr>
												@endforeach  
											@else
												<tr>
													<td colspan="3" style="text-align:center;"> {{ trans("Record not found.") }}</td>
												</tr>
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
	$(document).ready(function () {
		$('#datepickerfrom').datetimepicker({
			format: 'YYYY-MM-DD'
		});
		$('#datepickerto').datetimepicker({
			format: 'YYYY-MM-DD'
		});
	});

	function page_limit(){
		$('#searchForm').submit();
	}
</script>

@stop