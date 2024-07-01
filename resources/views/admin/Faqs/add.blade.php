@extends('admin.layouts.default')
@section('content')
<script src="{{ WEBSITE_JS_URL }}ckeditor/ckeditor.js"></script>
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
						Add New {{ $sectionNameSingular }} </h5>
					<!--end::Page Title-->

					<!--begin::Breadcrumb-->
					<ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
						<li class="breadcrumb-item">
							<a href="{{ route('dashboard')}}" class="text-muted">Dashboard</a>
						</li>
						<li class="breadcrumb-item">
							<a href="{{  route($modelName.'.index')}}"  class="text-muted">{{ $sectionName }}</a>
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
			{{ Form::open(['role' => 'form','route' => "$modelName.add",'class' => 'mws-form', 'files' => true,"autocomplete"=>"off"]) }}
			
			<div class="card card-custom gutter-b">
				<!--<div class="card-header card-header-tabs-line">
					<h3 class="card-title font-weight-bolder text-dark"> {{ $sectionNameSingular }} Information</h3>
				</div>-->
				<div class="card-header card-header-tabs-line">
					<div class="card-toolbar border-top">
						<ul class="nav nav-tabs nav-bold nav-tabs-line">
							@if(!empty($languages))
								<?php $i = 1 ; ?>
								@foreach($languages as $language)
									<li class="nav-item">
										<a class="nav-link {{ ($i ==  $language_code )?'active':'' }}" data-toggle="tab" href="#{{$language->title}}">
											<span class="symbol symbol-20 mr-3">
												<img src="{{$language->image}}" alt="">
											</span>
											<span class="nav-text">{{$language->title}}</span>
										</a>
									</li>
									<?php $i++; ?>
								@endforeach
							@endif
						</ul>
					</div>
				</div>
				<div class="card-body">
					<div class="tab-content">
						@if(!empty($languages))
							<?php $i = 1 ; ?>
							@foreach($languages as $language)
								<div class="tab-pane fade {{ ($i ==  $language_code )?'show active':'' }}" id="{{$language->title}}" role="tabpanel" aria-labelledby="{{$language->title}}">
									<div class="row">
										<div class="col-xl-12">	
											<div class="row">
												<div class="col-xl-12">
													<!--begin::Input-->
													<div class="form-group">
														<div id="kt-ckeditor-1-toolbar{{$language->id}}"></div>
														@if($i == 1)
															{!! HTML::decode( Form::label($language->id.'.question',trans("Question").'<span class="text-danger"> * </span>')) !!}
															
															{{ Form::textarea("data[$language->id][question]",'', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('question') ? 'is-invalid':''),'id' => 'question_'.$language->id]) }}
															<div class="invalid-feedback"><?php echo ($i ==  $language_code ) ? $errors->first('question') : ''; ?></div>
															
														@else 
															{!! HTML::decode( Form::label($language->id.'.question',trans("question").'<span class="text-danger">  </span>')) !!}
															{{ Form::textarea("data[$language->id][question]",'', ['class' => 'form-control form-control-solid form-control-lg','id' => 'question_'.$language->id]) }}
														@endif
													</div>
													<script>
														/* CKEDITOR for description */
														CKEDITOR.replace( <?php echo 'question_'.$language->id; ?>,
														{
															filebrowserUploadUrl : '<?php echo URL::to('base/uploder'); ?>',
															enterMode : CKEDITOR.ENTER_BR
														});
														CKEDITOR.config.allowedContent = true;	
														
													</script>
													<!--end::Input-->
												</div>
												<div class="col-xl-12">
													<!--begin::Input-->
													<div class="form-group">
														<div id="kt-ckeditor-1-toolbar{{$language->id}}"></div>
														@if($i == 1)
															{!! HTML::decode( Form::label($language->id.'.answer',trans("Answer").'<span class="text-danger"> * </span>')) !!}
															
															{{ Form::textarea("data[$language->id][answer]",'', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('answer') ? 'is-invalid':''),'id' => 'answer_'.$language->id]) }}
															<div class="invalid-feedback"><?php echo ($i ==  $language_code ) ? $errors->first('answer') : ''; ?></div>
															
														@else 
															{!! HTML::decode( Form::label($language->id.'.answer',trans("answer").'<span class="text-danger">  </span>')) !!}
															{{ Form::textarea("data[$language->id][answer]",'', ['class' => 'form-control form-control-solid form-control-lg','id' => 'answer_'.$language->id]) }}
														@endif
													</div>
													<script>
														/* CKEDITOR for description */
														CKEDITOR.replace( <?php echo 'answer_'.$language->id; ?>,
														{
															filebrowserUploadUrl : '<?php echo URL::to('base/uploder'); ?>',
															enterMode : CKEDITOR.ENTER_BR
														});
														CKEDITOR.config.allowedContent = true;	
														
													</script>
													<!--end::Input-->
												</div>
											</div>
										</div>
									</div>
								</div>
								<?php $i++; ?>
							@endforeach
						@endif
					</div>

					<div class="d-flex justify-content-between border-top mt-5 pt-10">
						<!-- <div>
							<a href="{{ route("$modelName.add")}}" class="btn btn-danger font-weight-bold text-uppercase px-9 py-4">{{ trans('Clear') }}</a>
		
							<a href="{{ route("$modelName.index")}}" class="btn btn-info font-weight-bold text-uppercase px-9 py-4">{{ trans('Cancel') }}</a>
						</div> -->
						<div>
							<button	button type="submit" class="btn btn-success font-weight-bold text-uppercase px-9 py-4">
								Submit
							</button>
						</div>
					</div>
					
				</div>
			</div>

			
			
			{{ Form::close() }}
		</div>
	</div>
</div>
@stop