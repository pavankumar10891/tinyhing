
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
						{{ $subscribersNameSingular }} </h5>
					<!--end::Page Title-->

					<!--begin::Breadcrumb-->
					<ul
						class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
						<li class="breadcrumb-item">
							<a href="{{ route('dashboard')}}" class="text-muted">Dashboard</a>
						</li>
						<li class="breadcrumb-item">
							<a href="{{URL::to('adminpnlx/news-letter/newsletter-templates')}}" class="text-muted"> Newsletter Templates</a>
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
			{{ Form::open(['method' => 'get','role' => 'form','url' => 'adminpnlx/news-letter/subscriber-list','class' => 'kt-form kt-form--fit mb-0','autocomplete'=>"off"]) }}
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
								
								<a href="{{URL::to('adminpnlx/news-letter/add-subscriber')}}"  class="btn btn-primary">{{ trans("Add Subscriber") }} </a>&nbsp;&nbsp;

								<a href="{{ route('Subscriber.export') }}"  class="btn btn-primary">{{ trans("Export CSV ") }} </a>
				
							
							</div>
						</div>
						<div class="card-body">
							<div class="accordion accordion-solid accordion-toggle-plus"
								id="accordionExample6">
								<div id="collapseOne6" class="collapse <?php echo !empty($searchVariable) ? 'show' : ''; ?>" data-parent="#accordionExample6">
									<div>
											<div class="row mb-6">
												<div class="col-lg-3 mb-lg-5 mb-6">
													<label>Email</label>
													{{ Form::text('email',((isset($searchVariable['email'])) ? $searchVariable['email'] : ''), ['class' => 'form-control','placeholder'=>'Email']) }}
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
													
													<a href="{{URL::to('adminpnlx/news-letter/subscriber-list')}}" class="btn btn-secondary btn-secondary--icon">
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
												<th class="{{(($sortBy == 'email' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'email' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
													{{
														link_to_route(
															'Subscriber.subscriberList',
															trans("Email"),
															array(
															'sortBy' => 'email',
															'order' => ($sortBy == 'email' && $order == 'desc') ? 'asc' : 'desc',
															$query_string
															)
														)
													}}
												</th>
												<th class="{{(($sortBy == 'created_at' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'created_at' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
													{{
														link_to_route(
															'Subscriber.subscriberList',
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
										<tbody id="powerwidgets">
											@if(!$results->isEmpty())
												@foreach($results as $record)
													<tr>
														<td>
															<div class="text-dark-75 mb-1 font-size-lg">
																{{ $record->email }}
															</div>
														</td>
														<td>
															<div class="text-dark-75 mb-1 font-size-lg">
																{{ date(Config::get("Reading.date_format"),strtotime($record->created_at)) }}
															</div>
														</td>
																										
														
														<td class="text-right pr-2">
														<a href="{{URL::to('adminpnlx/news-letter/subscriber-delete/'.$record->id)}}" 
																class="btn btn-icon btn-light btn-hover-danger btn-sm confirmDelete"
																data-toggle="tooltip" data-placement="top"
																data-container="body" data-boundary="window" title=""
																data-original-title="Delete">
																<span class="svg-icon svg-icon-md svg-icon-danger">
																	<svg xmlns="http://www.w3.org/2000/svg"
																		xmlns:xlink="http://www.w3.org/1999/xlink"
																		width="24px" height="24px" viewBox="0 0 24 24"
																		version="1.1">
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
												@endforeach  
											@else
												<tr>
													<td colspan="6" style="text-align:center;"> {{ trans("Record not found.") }}</td>
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
		
		$(".confirmDelete").click(function (e) {
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
			}).then(function (result) {
				if (result.value) {
					window.location.replace(url);
				}else if (result.dismiss === "cancel") {
					Swal.fire(
						"Cancelled",
						"Your imaginary file is safe :)",
						"error"
					)
				}
			});
			e.preventDefault();
		});
	});
	
	function page_limit() {
		$("form").submit();
	}
</script>

@stop