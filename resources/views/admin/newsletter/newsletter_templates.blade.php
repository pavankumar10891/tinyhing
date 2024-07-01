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
			{{ Form::open(['method' => 'get','role' => 'form','url' => 'adminpnlx/news-letter/newsletter-templates','class' => 'kt-form kt-form--fit mb-0','autocomplete'=>"off"]) }}
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
								
								<a href="{{URL::to('adminpnlx/news-letter/add-template')}}" class="btn btn-primary"> {{ trans("Add Template ") }} </a>&nbsp;&nbsp;

								<a href="{{URL::to('adminpnlx/news-letter/subscriber-list')}}" class="btn btn-primary"> {{ trans("Subscriber List ") }} </a>&nbsp;&nbsp;

								<a href="{{URL::to('adminpnlx/news-letter')}}" class="btn btn-primary"> {{ trans("Scheduled Newletter ") }} </a>
						
							
							</div>
						</div>
						<div class="card-body">
							<div class="accordion accordion-solid accordion-toggle-plus"
								id="accordionExample6">
								<div id="collapseOne6" class="collapse <?php echo !empty($searchVariable) ? 'show' : ''; ?>" data-parent="#accordionExample6">
									<div>
											<div class="row mb-6">
												<div class="col-lg-3 mb-lg-5 mb-6">
													<label>Subject</label>
													{{ Form::text('subject',((isset($searchVariable['subject'])) ? $searchVariable['subject'] : ''), ['class' => 'form-control','placeholder'=>'Subject']) }}
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
													
													<a href="{{URL::to('adminpnlx/news-letter/newsletter-templates')}}" class="btn btn-secondary btn-secondary--icon">
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
												<th class="{{(($sortBy == 'subject' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'subject' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
													{{
														link_to_route(
															'NewsTemplates.newsletterTemplates',
															trans("Subject"),
															array(
															'sortBy' => 'subject',
															'order' => ($sortBy == 'subject' && $order == 'desc') ? 'asc' : 'desc',
															$query_string
															)
														)
													}}
												</th>
												<th class="{{(($sortBy == 'created_at' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'created_at' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
													{{
														link_to_route(
															'NewsTemplates.newsletterTemplates',
															trans("Created On"),
															array(
															'sortBy' => 'created_at',
															'order' => ($sortBy == 'created_at' && $order == 'desc') ? 'asc' : 'desc',
															$query_string
															)
														)
													}}
												</th>
												<th class="{{(($sortBy == 'updated_at' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'updated_at' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
													{{
														link_to_route(
															'NewsTemplates.newsletterTemplates',
															trans("Updated On"),
															array(
															'sortBy' => 'updated_at',
															'order' => ($sortBy == 'updated_at' && $order == 'desc') ? 'asc' : 'desc',
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
												@foreach($results as $record)
													<tr>
														<td>
															<div class="text-dark-75 mb-1 font-size-lg">
																{{ $record->subject }}
															</div>
														</td>
														<td>
															<div class="text-dark-75 mb-1 font-size-lg">
																{{ date(Config::get("Reading.date_format"),strtotime($record->created_at)) }}
															</div>
														</td>
														<td>
															<div class="text-dark-75 mb-1 font-size-lg">
																{{ date(Config::get("Reading.date_format"),strtotime($record->updated_at)) }}
															</div>
														</td>
														
														
														<td class="text-right pr-2">
															<a href="{{URL::to('adminpnlx/news-letter/edit-newsletter-templates/'.$record->id)}}"
																class="btn btn-icon btn-light btn-hover-primary btn-sm"
																data-toggle="tooltip" data-placement="top"
																data-container="body" data-boundary="window" title=""
																data-original-title="Edit">
																<span class="svg-icon svg-icon-md svg-icon-primary">
																	<svg xmlns="http://www.w3.org/2000/svg"
																		xmlns:xlink="http://www.w3.org/1999/xlink"
																		width="24px" height="24px" viewBox="0 0 24 24"
																		version="1.1">
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
															<a href="{{URL::to('adminpnlx/news-letter/send-newsletter-templates/'.$record->id)}}"
																class="btn btn-icon btn-light btn-hover-primary btn-sm"
																data-toggle="tooltip" data-placement="top"
																data-container="body" data-boundary="window" title=""
																data-original-title="Send">										
																<span class="svg-icon svg-icon-md svg-icon-primary">
																	<svg xmlns="http://www.w3.org/2000/svg" 
																		xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
																		<g stroke="none" stroke-width="1" 
																			fill="none" fill-rule="evenodd">
																			<rect x="0" y="0" width="24" height="24"/>
																			<path 
																				d="M3,13.5 L19,12 L3,10.5 L3,3.7732928 C3,3.70255344 3.01501031,3.63261921 3.04403925,3.56811047 C3.15735832,3.3162903 3.45336217,3.20401298 3.70518234,3.31733205 L21.9867539,11.5440392 C22.098181,11.5941815 22.1873901,11.6833905 22.2375323,11.7948177 C22.3508514,12.0466378 22.2385741,12.3426417 21.9867539,12.4559608 L3.70518234,20.6826679 C3.64067359,20.7116969 3.57073936,20.7267072 3.5,20.7267072 C3.22385763,20.7267072 3,20.5028496 3,20.2267072 L3,13.5 Z" fill="#000000" />
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
		
		$(".status_any_item").click(function (e) {
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
			}).then(function (result) {
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

@stop