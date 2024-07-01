
<?php $__env->startSection('content'); ?>
<!--begin::Content-->
<script src="<?php echo e(WEBSITE_JS_URL); ?>ckeditor/ckeditor.js"></script>
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
                        Add New <?php echo e($sectionNameSingular); ?> </h5>
                    <!--end::Page Title-->

                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="<?php echo e(URL::to('adminpnlx/dashboard')); ?>" class="text-muted">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
							<a href="<?php echo e(URL::to('adminpnlx/news-letter/newsletter-templates')); ?>" class="text-muted">Newsletter Templates</a>
                        </li>
                    </ul>
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Page Heading-->
            </div>
            <!--end::Info-->

            <?php echo $__env->make("admin.elements.quick_links", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
    </div>
    <!--end::Subheader-->

    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class=" container ">
        <?php echo e(Form::open(['role' => 'form','url' => 'adminpnlx/news-letter/add-template/','class' => 'mws-form', 'files' => true,"autocomplete"=>"off"])); ?>

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-1"></div>
                        <div class="col-xl-10">
                            <h3 class="mb-10 font-weight-bold text-dark">
                                <?php echo e($sectionNameSingular); ?> Information</h3>

                            <div class="row">
                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        <?php echo HTML::decode( Form::label('subject', trans("Subject").'<span
                                            class="text-danger"> * </span>')); ?>

                                        <?php echo e(Form::text('subject','', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('subject') ? 'is-invalid':'')])); ?>

                                        <div class="invalid-feedback"><?php echo $errors->first('subject'); ?></div>
                                    </div>
                                    <!--end::Input-->
                                </div>

                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group <?php echo ($errors->first('constant')) ? 'has-error' : ''; ?>">	
										<?php echo HTML::decode( Form::label('constant', trans("Constants").'<span
                                            class="text-danger"> </span>')); ?>

										<!-- <div class="mws-form-item">
											<div class="col-md-6"> -->
												<?php $constantArray = Config::get('newsletter_template_constant'); ?>
												<?php echo e(Form::select('constant', $constantArray,'', ['id' => 'constants','empty' => 'Select one','class' => 'form-control form-control-solid form-control-lg'])); ?>

												<div class="error-message help-inline">
													<?php echo $errors->first('constant'); ?>
												</div>
											<!--</div> -->
											<!-- <div class="col-md-6"> -->
												<span>
													<a onclick = "return InsertHTML()" href="javascript:void(0)" class="btn  btn-success no-ajax"><i class="icon-white "></i><?php echo e(trans("Insert Variable")); ?> </a>
												</span>
											<!-- </div> -->
										<!-- </div>	 -->
                                    </div>
                                    <!--end::Input-->
                                </div>

                                <div class="col-xl-12">
                                    <!--begin::Input-->
                                    <div class="form-group">
										<?php echo HTML::decode( Form::label('body', trans("Email Body").'<span
                                            class="text-danger"> </span>')); ?>

										<!-- <div class="mws-form-item"> -->
											<?php echo e(Form::textarea("body",'<p>Hi {USER_NAME}!</p>
												<p>Greetings of the Day..<br /><br />
													ENTER YOUR TEXT HERE </p>
												<p>&nbsp;</p>
												<p>See you soon on <?php echo e(Config::get("Site.title")); ?>.
												<br />
												&nbsp;</p>
												<p><?php echo e(Config::get("Site.title")); ?></p>
												<br />
												<span style="background-color:rgb(239, 239, 239); font-family:arial,sans-serif; font-size:10px">
													You&#39;re receiving this because you have recently signed up on our website or subscribed our newsletter
												</span>
												<br />
												<span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">You can unsubscribe from the <?php echo e(Config::get("Site.title")); ?>&nbsp;</span>
												<span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">newsletter</span>
												<span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">&nbsp;via&nbsp;</span><br />
												<br />&nbsp;', ['class' => 'small','id' => 'body'])); ?>

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
										<!-- </div> -->
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
            <?php echo e(Form::close()); ?>

        </div>
    </div>
</div>
<script type='text/javascript'>
/* this  function is use for insert constant in ckeditor */
	function InsertHTML() {
		var strUser = document.getElementById("constants").value;
		if(strUser != ''){
			var newStr = '{'+strUser+'}';
			var oEditor = CKEDITOR.instances["body"] ;
			oEditor.insertHtml(newStr) ;	
		}
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\xampp\htdocs\tinyhugs\resources\views/admin/newsletter/add_newsletter_templates.blade.php ENDPATH**/ ?>