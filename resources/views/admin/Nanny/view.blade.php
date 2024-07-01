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
						View {{ $sectionNameSingular }} </h5>
					<!--end::Page Title-->

					<!--begin::Breadcrumb-->
					<ul
						class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
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
				<div class="card-body px-0">
					<div class="tab-content px-10">
						<!--begin::Tab Content-->
						<div class="tab-pane active" id="kt_apps_contacts_view_tab_1" role="tabpanel">
							
							<div class="form-group row my-2">
								<label class="col-4 col-form-label">Name:</label>
								<div class="col-8">
									<span class="form-control-plaintext font-weight-bolder">{{ isset($model->name) ? $model->name :'' }}</span>
								</div>
							</div>
							
							<div class="form-group row my-2">
								<label class="col-4 col-form-label">Email:</label>
								<div class="col-8">
									<span class="form-control-plaintext font-weight-bolder">{{ isset($model->email) ? $model->email :'' }}</span>
								</div>
							</div>
							<div class="form-group row my-2">
								<label class="col-4 col-form-label">Phone:</label>
								<div class="col-8">
									<span class="form-control-plaintext font-weight-bolder">{{ isset($model->phone_number) ? $model->phone_number :'' }}</span>
								</div>
							</div>

							<div class="form-group row my-2">
								<label class="col-4 col-form-label">Age:</label>
								<div class="col-8">
									<span class="form-control-plaintext font-weight-bolder">{{ isset($model->age) ? $model->age :'' }}</span>
								</div>
							</div>

							<div class="form-group row my-2">
								<label class="col-4 col-form-label">Experience:</label>
								<div class="col-8">
									<span class="form-control-plaintext font-weight-bolder">{{ isset($model->experience) ? $model->experience :'' }}</span>
								</div>
							</div>

							<div class="form-group row my-2">
								<label class="col-4 col-form-label">Description:</label>
								<div class="col-8">
									<span class="form-control-plaintext font-weight-bolder">{{ isset($model->description) ? $model->description :'' }}</span>
								</div>
							</div>
							
							<div class="form-group row my-2">
								<label class="col-4 col-form-label">Registered On:</label>
								<div class="col-8">
									<span class="form-control-plaintext font-weight-bolder">
									{{ date(config::get("Reading.date_format"),strtotime($model->created_at)) }}
								</span>
								</div>
							</div>
	
							<div class="form-group row my-2">
								<label class="col-4 col-form-label">Zip Code:</label>
								<div class="col-8">
									<span class="form-control-plaintext font-weight-bolder">
										{{ isset($model->postcode) ? $model->postcode :'' }}
									</span>
                                </div>
                             </div>

							 <div class="form-group row my-2">
								<label class="col-4 col-form-label">Photo ID:</label>
									<div class="col-8">
										<span class="form-control-plaintext font-weight-bolder">
											@if($model->photo_id != "")
                                                <a class="fancybox-buttons" data-fancybox-group="button" href="{{ USER_IMAGE_URL.$model->photo_id }}" >
                                                    <img class="" src="{{ USER_IMAGE_URL.$model->photo_id }}" width="60px" height="60px">
                                                </a>
                                            @endif
                                    </div>
                                </div>
								<div class="form-group row my-2">
								<label class="col-4 col-form-label">Identification Type:</label>
								<div class="col-8">
									<span class="form-control-plaintext font-weight-bolder">
										{{ (isset($model->identification_type)&& $model->identification_type==1) ? 'Passport' :'Driving License' }}
									</span>
                                </div>
                             </div>

								<div class="form-group row my-2">
								<label class="col-4 col-form-label">Uploaded {{(isset($model->identification_type)&& $model->identification_type==1) ? 'Passport' :'Driving License'}}:</label>
									<div class="col-8">
										<span class="form-control-plaintext font-weight-bolder">
                                            @if($model->identification_file != "")
                                            	<a href="{{ CERTIFICATES_AND_FILES_URL.$model->identification_file }}" download="{{ $model->identification_file }}" title="Click To Download">
													<img src="{{ WEBSITE_IMG_URL.'download.png' }}" width="5%" height="5%">
												</a>
                                            @endif
                                    </div>
                                </div> 

								<div class="form-group row my-2">
								<label class="col-4 col-form-label">CPR Certificate:</label>
									<div class="col-8">
										<span class="form-control-plaintext font-weight-bolder">
                                            @if($model->cpr_certificate != "")
                                            	<a href="{{ CERTIFICATES_AND_FILES_URL.$model->cpr_certificate }}" download="{{ $model->cpr_certificate }}" title="Click To Download">
													<img src="{{ WEBSITE_IMG_URL.'download.png' }}" width="5%" height="5%">
												</a>
                                            @endif
                                    </div>
                                </div> 

								<div class="form-group row my-2">
								<label class="col-4 col-form-label">Resume:</label>
									<div class="col-8">
										<span class="form-control-plaintext font-weight-bolder">
                                            @if($model->resume != "")
                                            	<a href="{{ CERTIFICATES_AND_FILES_URL.$model->resume }}" download="{{ $model->resume }}" title="Click To Download">
													<img src="{{ WEBSITE_IMG_URL.'download.png' }}" width="5%" height="5%">
                                            	</a>
                                            @endif
                                    </div>
                                </div> 

							<div class="form-group row my-2">
								<label class="col-4 col-form-label">Other Certificates:</label>
									<div class="col-8">
										<span class="form-control-plaintext font-weight-bolder">
										@if(!empty($userCertificateData))
											@foreach($userCertificateData as $data)
                                            	<a href="{{ OTHER_CERTIFICATES_DOCUMENT_URL.$data['other_certificates'] }}" download="{{ $data['other_certificates'] }}" title="Click To Download" >
													<img src="{{ WEBSITE_IMG_URL.'download.png' }}" width="5%" height="5%">
                                            	</a>
											@endforeach
										@endif
										</span>
									</div>
                                <!--end::Input-->
                            </div>

							<div class="form-group row my-2">
								<label class="col-4 col-form-label">Status:</label>
								<div class="col-8">
									<span class="form-control-plaintext font-weight-bolder">
										@if($model->is_active	== 1)
											<span class="label label-lg label-light-success label-inline">Activated</span>
										@else
											<span class="label label-lg label-light-danger label-inline">Deactivated</span>
										@endif
									</span>
								</div>
							</div>

							<div class="form-group row my-2">
								<label class="col-4 col-form-label">Verification Status:</label>
								<div class="col-8">
									<span class="form-control-plaintext font-weight-bolder">
										@if($model->verified == 1)
											<span class="label label-lg label-light-success label-inline">Verified</span>
										@else
											<span class="label label-lg label-light-danger label-inline">Verification Pending</span>
										@endif
									</span>
								</div>
							</div>	

						</div>
						
						<!--end::Tab Content-->
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
