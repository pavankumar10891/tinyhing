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
							Add New Word </h5>
						<!--end::Page Title-->

						<!--begin::Breadcrumb-->
						<ul
							class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
							<li class="breadcrumb-item">
								<a href="{{ route('dashboard')}}" class="text-muted">Dashboard</a>
							</li>
							<li class="breadcrumb-item">
								<a href="{{URL::to('adminpnlx/language-settings')}}" class="text-muted">{{ trans("Back To Language Settings") }}</a>
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
				{{ Form::open(['role' => 'form','url' => 'adminpnlx/language-settings/add-setting','class' => 'mws-form']) }}
				<div class="card">
					<div class="card-body">
						<div class="row">
							<div class="col-xl-1"></div>
							<div class="col-xl-10">
								<h3 class="mb-10 font-weight-bold text-dark">
								</h3>

								<div class="row">
									<div class="col-xl-6">
										<!--begin::Input-->
										<div class="form-group">
											{{  Form::label('default', 'Default') }} <span class="asterisk">*</span>
											{{ Form::text('default', '', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('default') ? 'is-invalid':'')]) }}
											<div class="invalid-feedback"><?php echo $errors->first('default'); ?></div>
										</div>
										<!--end::Input-->
									</div>
									@if(!empty($languages))
										@foreach($languages as $key => $val)
											<div class="col-xl-6">
												<!--begin::Input-->
												<div class="form-group">
													{{  Form::label('email', $val->title) }}
													{{ Form::text("language[$val->lang_code]",'', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('email') ? 'is-invalid':'')]) }}
													<div class="invalid-feedback"><?php echo $errors->first('email'); ?></div>
												</div>
												<!--end::Input-->
											</div>
										@endforeach
									@endif		
									
								

								<div class="d-flex justify-content-between border-top mt-5 pt-10">
									<div>
										<a href="{{URL::to('adminpnlx/language-settings/add-setting')}}" class="btn btn-danger font-weight-bold text-uppercase px-9 py-4">{{ trans('Reset') }}</a>
										
									</div>
									<div>
										<button	button type="submit" class="btn btn-success font-weight-bold text-uppercase px-9 py-4">
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
@stop
