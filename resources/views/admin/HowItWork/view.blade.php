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
								<label class="col-4 col-form-label">Image:</label>
								<div class="col-8">
                                <span class="form-control-plaintext font-weight-bolder">
                                     @if($model->image != "")	
										<br />
										<a class="fancybox-buttons" data-fancybox-group="button" href="{{HOWITWORK_IMAGE_URL.$model->image}}"><img height="50" width="50" src="{{HOWITWORK_IMAGE_URL.$model->image}}" /></a>
									@endif
                                </span>
								</div>
							</div>


							<div class="form-group row my-2">
								<label class="col-4 col-form-label">Title:</label>
								<div class="col-8">
									<span class="form-control-plaintext font-weight-bolder">{{ isset($model->title) ? ucfirst($model->title) :'' }}</span>
								</div>
							</div>
							
							<div class="form-group row my-2">
								<label class="col-4 col-form-label">Description:</label>
								<div class="col-8">
									<span class="form-control-plaintext font-weight-bolder">{{ isset($model->description) ? $model->description :'' }}</span>
								</div>
							</div>
							<div class="form-group row my-2">
								<label class="col-4 col-form-label">Order:</label>
								<div class="col-8">
									<span class="form-control-plaintext font-weight-bolder">{{ isset($model->work_order) ? $model->work_order :'' }}</span>
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
