@extends('admin.layouts.default')
@section('content')
<!--begin::Content-->
<script src="{{ WEBSITE_JS_URL }}ckeditor/ckeditor.js"></script>
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
                        Edit Template </h5>
                    <!--end::Page Title-->

                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="{{URL::to('adminpnlx/dashboard')}}" class="text-muted">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
							<a href="{{URL::to('adminpnlx/news-letter/newsletter-templates')}}" class="text-muted">Newsletter Templates</a>
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
        {{ Form::open(['role' => 'form','url' => 'adminpnlx/news-letter/edit-newsletter-templates/'.$result->id,'class' => 'mws-form', 'files' => true,"autocomplete"=>"off"]) }}
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-1"></div>
                        <div class="col-xl-10">
                            <h3 class="mb-10 font-weight-bold text-dark">
								Edit Template Information</h3>

                            <div class="row">           
								<div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        {!! HTML::decode( Form::label('subject', trans("Subject").'<span
                                            class="text-danger"> * </span>')) !!}
                                        {{ Form::text('subject', $result->subject, ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('subject') ? 'is-invalid':'')]) }}
                                        <div class="invalid-feedback"><?php echo $errors->first('subject'); ?></div>
                                    </div>
                                    <!--end::Input-->
                                </div>

                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group <?php echo ($errors->first('constant')) ? 'has-error' : ''; ?>">	
										{!! HTML::decode( Form::label('constant', trans("Constants").'<span
                                            class="text-danger"> </span>')) !!}
										<!-- <div class="mws-form-item">
											<div class="col-md-6"> -->
												<?php $constantArray = Config::get('newsletter_template_constant'); ?>
												{{ Form::select('constant', $constantArray,'', ['id' => 'constants','empty' => 'Select one','class' => 'form-control form-control-solid form-control-lg']) }}
												<div class="error-message help-inline">
													<?php echo $errors->first('constant'); ?>
												</div>
											<!--</div> -->
											<!-- <div class="col-md-6"> -->
												<span>
													<a onclick = "return InsertHTML()" href="javascript:void(0)" class="btn  btn-success no-ajax"><i class="icon-white "></i>{{ trans("Insert Variable") }} </a>
												</span>
											<!-- </div> -->
										<!-- </div>	 -->
                                    </div>
                                    <!--end::Input-->
                                </div>

                                <div class="col-xl-12">
                                    <!--begin::Input-->
                                    <div class="form-group">
										{{  Form::label('body', trans("Body"), ['class' => 'mws-form-label']) }}
										{{ Form::textarea("body",$result->body, ['class' => 'small','id' => 'body']) }}
										<span class="error-message help-inline">
											<?php echo $errors->first('body'); ?>
										</span>
										<script type="text/javascript">
											/* For CKeditor */
												// <![CDATA[
												CKEDITOR.replace( 'body',
											{
												height: 350,
												width: 600,
												filebrowserUploadUrl : '<?php echo URL::to('base/uploder'); ?>',
												filebrowserImageWindowWidth : '640',
												filebrowserImageWindowHeight : '480',
												enterMode : CKEDITOR.ENTER_BR
											});
											//]]>		
										</script>
                                    </div>
                                    <!--end::Input-->
                                </div>
							</div>
							
                            <div class="d-flex justify-content-between border-top mt-5 pt-10">
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
            {{ Form::close() }}
        </div>
    </div>
</div>
<script type='text/javascript'>
	/* this function insert define constant on ckeditor*/
	function InsertHTML() {
		var strUser = document.getElementById("constants").value;
		if(strUser != ''){
			var newStr = '{'+strUser+'}';
			var oEditor = CKEDITOR.instances["body"] ;
			oEditor.insertHtml(newStr) ;	
		}
    }
</script>
@stop