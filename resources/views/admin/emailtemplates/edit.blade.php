@extends('admin.layouts.default')
@section('content')
<!--begin::Content--><script src="{{ WEBSITE_JS_URL }}ckeditor/ckeditor.js"></script>
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
						   Edit {{ $sectionNameSingular }}  </h5>
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
			{{ Form::open(['role' => 'form','url' => 'adminpnlx/email-manager/edit-template/'.$emailTemplate->id,'class' => 'mws-form']) }}
			
			<div class="card card-custom gutter-b">
				<!--<div class="card-header card-header-tabs-line">
					<h3 class="card-title font-weight-bolder text-dark"> Information</h3>
				</div>-->
				@if(count($languages) > 1)
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
				@endif
				<div class="card-body">
					<div class="tab-content">
						@if(!empty($languages))
							<?php $i = 1 ; ?>
							@foreach($languages as $language)
								<div class="tab-pane fade {{ ($i ==  $language_code )?'show active':'' }}" id="{{$language->title}}" role="tabpanel" aria-labelledby="{{$language->title}}">
									<div class="row">
										<div class="col-xl-12">	
											<div class="row">
												<div class="col-xl-6">
													<!--begin::Input-->
													<div class="form-group">
														@if($i == 1)
															{!! HTML::decode( Form::label($language->id.'.name',trans("Name").'<span class="text-danger"> * </span>')) !!}
															
															{{ Form::text("data[$language->id][name]",$emailTemplate->name, ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('name') ? 'is-invalid':'')]) }}
															<div class="invalid-feedback"><?php echo ($i ==  $language_code ) ? $errors->first('name') : ''; ?></div>
															
														@else
															{!! HTML::decode( Form::label($language->id.'.name',trans("Name").'<span class="text-danger">  </span>')) !!}
															{{ Form::text("data[$language->id][name]",$emailTemplate->name, ['class' => 'form-control form-control-solid form-control-lg ']) }}
														@endif
													</div>
													<!--end::Input-->
												</div>

												<div class="col-xl-6">
													<!--begin::Input-->
													<div class="form-group">
														@if($i == 1)
															{!! HTML::decode( Form::label($language->id.'.subject',trans("Subject").'<span class="text-danger"> * </span>')) !!}
															
															{{ Form::text("data[$language->id][subject]",$emailTemplate->subject, ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('subject') ? 'is-invalid':'')]) }}
															<div class="invalid-feedback"><?php echo ($i ==  $language_code ) ? $errors->first('subject') : ''; ?></div>
															
														@else
															{!! HTML::decode( Form::label($language->id.'.subject',trans("Subject").'<span class="text-danger">  </span>')) !!}
															{{ Form::text("data[$language->id][subject]",$emailTemplate->subject, ['class' => 'form-control form-control-solid form-control-lg ']) }}
														@endif
													</div>
													<!--end::Input-->
												</div>

												<div class="col-xl-6" style="display:none;">
													<!--begin::Input-->
													<div class="form-group">
														{!! HTML::decode( Form::label('action',trans("Action").'<span class="text-danger"> * </span>')) !!}
																
															{{ Form::select(
																'action',
																 [null => 'Select Action'] + $Action_options,
																 $emailTemplate->action,
																['class' => 'form-control form-control-solid form-control-lg '.($errors->has('action') ? 'is-invalid':''),'onchange'=>"constant($i)",'id'=>"action"]
																) 
															}}
															<div class="invalid-feedback"><?php echo $errors->first('action'); ?></div>
													</div>
													<!--end::Input-->
												</div>
												 
												<div class="col-xl-6">
													<div class="form-group">
														{!! HTML::decode( Form::label('Constants',trans("Constants").'<span class="text-danger"> * </span>')) !!}
														<div class="row">
															<div class="col">
																{{ Form::select('constants', [null => '-- Select One --'] + array(),'', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('constants') ? 'is-invalid':''),"id"=>"constants"]) }}
																<div class="invalid-feedback"><?php echo $errors->first('constants'); ?></div>
															</div>
															<div class="col-auto">
																<a onclick = "return InsertHTML({{$i}})" href="javascript:void(0)" class="btn btn-lg btn-success no-ajax pull-right"><i class="icon-white "></i>{{  trans("Insert Variable") }} </a>
															</div>
														</div>
													</div>
												</div>
												
												<div class="col-xl-12">
													<!--begin::Input-->
													<div class="form-group">
														<div id="kt-ckeditor-1-toolbar{{$language->id}}"></div>
														@if($i == 1)
															{!! HTML::decode( Form::label($language->id.'.body',trans("Email Body").'<span class="text-danger"> * </span>')) !!}
															
															{{ Form::textarea("data[$language->id][body]",$emailTemplate->body, ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('body') ? 'is-invalid':''),'id' => 'body']) }}
															<div class="invalid-feedback"><?php echo ($i ==  $language_code ) ? $errors->first('body') : ''; ?></div>
															
														@else
															{!! HTML::decode( Form::label($language->id.'.body',trans("Email Body").'<span class="text-danger">  </span>')) !!}
															{{ Form::textarea("data[$language->id][body]",$emailTemplate->body, ['class' => 'form-control form-control-solid form-control-lg','id' => 'body']) }}
														@endif
													</div>
													
													<script>
														/* CKEDITOR for description */
														CKEDITOR.replace( <?php echo 'body'; ?>,
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
							<a href='{{ route("EmailTemplate.edit",$emailTemplate->id)}}' class="btn btn-danger font-weight-bold text-uppercase px-9 py-4">{{ trans('Clear') }}</a>
										
							<a href="{{ route("EmailTemplate.index") }}" class="btn btn-info font-weight-bold text-uppercase px-9 py-4">{{ trans('Cancel') }}</a>
						</div> -->
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
<?php  $constant = ''; ?>
<script type='text/javascript'>
var myText = '<?php  echo $constant; ?>';
	$(function(){
		constant();
	});
	/* this function used for  insert contant, when we click on  insert variable button */
    function InsertHTML() {
		
		var strUser = document.getElementById("constants").value;
		
		if(strUser != ''){
			var newStr = '{'+strUser+'}';
			var oEditor = CKEDITOR.instances["body"] ;
			oEditor.insertHtml(newStr) ;	
		}
    }
	/* this function used for get constant,define in email template*/
	function constant() {
		var constant = document.getElementById("action").value;
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
				url: "<?php echo URL::to('adminpnlx/email-manager/get-constant')?>",
				type: "POST",
				data: { constant: constant},
				dataType: 'json',
				success: function(r){
					$('#constants').empty();
					$('#constants').append( '<option value="">-- Select One --</option>' );
					$.each(r, function(val, text) {
						var sel ='';
						if(myText == text)
						 {
						   sel ='selected="selected"';
						 }
						 
						$('#constants').append( '<option value="'+text+'" '+sel+'>'+text+'</option>');
					});	
			   }
			});
		return false; 
	}	
</script>
<style>
	.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {
		font-size: 14px !important;
		padding: 0px !important;
	}
	.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {
		vertical-align: top !important;
	}
	.table-bordered > thead > tr > th, .table-bordered > tbody > tr > th, .table-bordered > tfoot > tr > th, .table-bordered > thead > tr > td, .table-bordered > tbody > tr > td, .table-bordered > tfoot > tr > td {
		border: 0px !important;
	}
	.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {
		border-top: 0px !important;
		padding: 0px !important;
	}
	.table-bordered {
		border: 0px !important;
	}
</style>
@stop