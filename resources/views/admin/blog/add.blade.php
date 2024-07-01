@extends('admin.layouts.default')
@section('content')
<!-- CKeditor start here-->
<script src="{{ WEBSITE_JS_URL }}ckeditor/ckeditor.js"></script>

<?php $blogcategory = ['1' => 'DIABETES MANAGEMENT', '2' => 'WEIGHT MANAGEMENT']; ?>
<!-- CKeditor ends-->
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
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
	                    <a href="{{ route($modelName.'.listBlog')}}" class="text-muted">{{ $sectionName }}</a>
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

	<div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class=" container ">
        {{ Form::open(['role' => 'form','route' => "$modelName.add",'class' => 'mws-form', 'files' => true,"autocomplete"=>"off"]) }}
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-1"></div>
                        <div class="col-xl-10">
                            <h3 class="mb-10 font-weight-bold text-dark">
                                {{ $sectionNameSingular }} Information</h3>

                            <div class="row">
                            	<div class="col-md-6">	
									<div class="form-group <?php echo ($errors->first('category_id')) ? 'has-error' : ''; ?>">
										{{ Form::label('category_id',trans("Blog Category"), ['class' => 'mws-form-label']) }}<span class="text-danger"> * </span>
										<div class="mws-form-item">
											{{ Form::select('category_id',$blogCategory,'',['class' => 'form-control','placeholder'=>'Choose Blog Category']) }}
											<div class="invalid-feedback" style="color:#F64E60;display:block"><?php echo $errors->first('category_id'); ?></div>
										</div>
									</div>
								</div>
                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        {!! HTML::decode( Form::label('title', trans("Title").'<span
                                            class="text-danger"> * </span>')) !!}
                                        {{ Form::text('title','', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('title') ? 'is-invalid':'')]) }}
                                        <div class="invalid-feedback"><?php echo $errors->first('title'); ?></div>
                                    </div>
                                    <!--end::Input-->
                                </div>
                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        {!! HTML::decode( Form::label('posted_by', trans("Posted By").'<span
                                            class="text-danger"> * </span>')) !!}
                                        {{ Form::text('posted_by','', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('posted_by') ? 'is-invalid':'')]) }}
                                        <div class="invalid-feedback"><?php echo $errors->first('posted_by'); ?></div>
                                    </div>
                                    <!--end::Input-->
                                </div>
                                {{ Form::radio('blog_type', 'image',true,['class'=>'blog_type', 'checked' => 'checked','style'=>'display:none']) }}
                                <?php /*
                                <div class="col-md-6">		
									<div class="form-group <?php echo ($errors->first('blog_type')) ? 'has-error' : ''; ?>">
										<div class="mws-form-row">
											{{ Form::label('blog_type', trans("Blog Type"), ['class' => 'mws-form-label']) }}<!-- <span class="requireRed"> * </span> -->
											<div class="mws-form-item">
												<span class="styled-selectors">
													{{ Form::radio('blog_type', 'image','',['class'=>'blog_type']) }}
													<label for="confrm1">Banner Image</label>
												</span>
												<span class="styled-selectors">
													{{ Form::radio('blog_type', 'embedded','',['class'=>'blog_type']) }}
													<label for="confrm1"> Video Embedded Url</label>
												</span>
												<div class="error-message help-inline">
													<?php echo $errors->first('blog_type'); ?>
												</div>
											</div>
										</div>
									</div> */ ?>
								    <div class="col-md-12">
								    	<?php /*
										<div class="form-group embedded_div <?php echo ($errors->first('embedded_url')) ? 'has-error' : ''; ?>" style="display:none;"> 
											<div class="mws-form-row">
												{{ Form::label('embedded_url', trans("Video Embedded Url"), ['class' => 'mws-form-label']) }}<span class="requireRed"> * </span>
												<div class="mws-form-item">
													{{ Form::text('embedded_url','',['class'=>'form-control']) }}
													<span>Example: https://www.youtube.com/watch?v=RT3fmiODWxI</span>
													<div class="error-message help-inline">
														<?php echo $errors->first('embedded_url'); ?>
													</div>
												</div>
											</div>
										</div> */ ?>
										<div class="form-group embedded_image <?php echo ($errors->first('banner_image')) ? 'has-error' : ''; ?>">
											{{ Form::label('banner_image', trans("Banner Image"),  ['class' => 'mws-form-label floatleft']) }}<!-- <span class="requireRed"> * </span> -->
											<span class='tooltipHelp' title="" data-html="true" data-toggle="tooltip" data-placement="right" data-original-title="<?php echo "The attachment must be a file of type:".IMAGE_EXTENSION; ?>" style="cursor:pointer;">
												<i class="fa fa-question-circle fa-2x"> </i>
											</span>
											<div class="mws-form-item">
												{{ Form::file('banner_image') }}
											</div>
											<div class="error-message help-inline">
													<?php echo $errors->first('banner_image'); ?>
											</div>
										</div>
									</div>
									<!-- <div class="col-xl-12">
										<div>
											{{ Form::label('description', trans("Article"), ['class' => 'mws-form-label']) }}<span class="requireRed"> * </span>
											<div class="mws-form-item">
												
												{{ Form::textarea("description",'', ['class' => 'form-control form-control-solid form-control-lg','id' => 'Description']) }}
												<script type="text/javascript">
												/* For CKEDITOR */
													CKEDITOR.replace( <?php echo 'Description'; ?>,
													{
														height: 250,
														width: 550,
														filebrowserUploadUrl : '<?php echo URL::to('base/uploder'); ?>',
														filebrowserImageWindowWidth : '640',
														filebrowserImageWindowHeight : '480',
														enterMode : CKEDITOR.ENTER_BR
													});
													CKEDITOR.config.allowedContent = true;				
												</script>
												<div class="error-message help-inline">
													<?php echo $errors->first('description'); ?>
												</div>
											</div>
										</div>
									</div> -->
									<div class="col-xl-12">
													<!--begin::Input-->
										<div class="form-group">
											<div id="kt-ckeditor-1-toolbar"></div>
											
												{!! HTML::decode( Form::label('description',trans("Article").'<span class="text-danger">  </span>')) !!}
												{{ Form::textarea("description",'', ['class' => 'form-control form-control-solid form-control-lg','id' => 'Description']) }}
										</div>
										<script>
											/* CKEDITOR for description */
											CKEDITOR.replace( <?php echo 'Description'; ?>,
											{
												filebrowserUploadUrl : '<?php echo URL::to('base/uploder'); ?>',
												enterMode : CKEDITOR.ENTER_BR
											});
											CKEDITOR.config.allowedContent = true;	
											
										</script>
										<!--end::Input-->
									</div>
								</div>
	                           	<div class="d-flex justify-content-between border-top mt-5 pt-10">
	                                <div>
										<a href="{{ route($modelName.'.add') }}" class="btn btn-danger font-weight-bold text-uppercase px-9 py-4">{{ trans('Clear') }}</a>
										<a href="{{ route($modelName.'.listBlog') }}" class="btn btn-info font-weight-bold text-uppercase px-9 py-4">{{ trans('Cancel') }}</a>
										</div>
	                                <div>
	                                    <button button type="submit"
	                                        class="btn btn-success font-weight-bold text-uppercase px-9 py-4">
	                                        Submit
	                                    </button>
	                                </div>
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


<script>
	
	$(".blog_type").click(function(){
		var type = $(".blog_type:checked").val();
		if(type == 'embedded'){
			$(".embedded_div").show();
			$(".embedded_image").hide();
		}
		if(type == 'image'){
			$(".embedded_div").hide();
			$(".embedded_image").show();
		}
	});
</script>
@stop