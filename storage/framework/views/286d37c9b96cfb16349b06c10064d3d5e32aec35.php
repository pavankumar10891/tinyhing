

<?php $__env->startSection('content'); ?>
<?php $__env->startSection('title', 'Sign Up'); ?> 
<style>
  .form-group.identification.input-form input {
    height: 12px;
    color:#34363a;
  }
  .form-group.identification.input-form{
    color:#34363a;
  }
</style>
<section class="login pb-5">
  <div class="container">
    <div class="">
      <div class="login-form signup-form">
        <div class="row pb-5 flex-row">
          <div class="col">
            <div class="heading">
              <h5>Looking for care?</h5>
              <h3>Nannies, Create your account</h3> </div>
          </div>
          <div class="col-md-auto">
            <div class="create-account">Already have an account, <a href="<?php echo e(route('user.login')); ?>">
                              Log In </a> 
            </div>
          </div>
        </div>
        <?php echo e(Form::open(array('id' => 'user-registration-form', 'class' => 'form'))); ?>

        <div class="signup-cont">
          <div class="row align-items-center">
            <div class="col-auto">
              <div class="img-wall1" > 
              <?php if(isset($image) && !empty($image)): ?>
                  <img id="previewImg" src="<?php echo e($image); ?>" class="img-fluid"> 
              <?php else: ?> 
                <img id="previewImg" src="<?php echo e(WEBSITE_IMG_URL); ?>signup-profile.PNG" class="img-fluid"> 
              <?php endif; ?>
              <!-- <a id="removeimg" href="javascript:void(0)"><i class="fa fa-times" aria-hidden="true"></i></a>--> </div> 
            </div>
            <div class="col pl-3">
              <div class="signup-detail">
                <h5>Upload Your Photo</h5>
                <div class="upload-img">
                  <label for="profile_pic">
                    <a class="btn-theme text-white" for="profile_pic1">
                      <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="cloud-upload-alt" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" class="svg-inline--fa fa-cloud-upload-alt fa-w-20 fa-2x">
                        <path fill="currentColor" d="M537.6 226.6c4.1-10.7 6.4-22.4 6.4-34.6 0-53-43-96-96-96-19.7 0-38.1 6-53.3 16.2C367 64.2 315.3 32 256 32c-88.4 0-160 71.6-160 160 0 2.7.1 5.4.2 8.1C40.2 219.8 0 273.2 0 336c0 79.5 64.5 144 144 144h368c70.7 0 128-57.3 128-128 0-61.9-44-113.6-102.4-125.4zM393.4 288H328v112c0 8.8-7.2 16-16 16h-48c-8.8 0-16-7.2-16-16V288h-65.4c-14.3 0-21.4-17.2-11.3-27.3l105.4-105.4c6.2-6.2 16.4-6.2 22.6 0l105.4 105.4c10.1 10.1 2.9 27.3-11.3 27.3z" class=""></path>
                      </svg> Upload 
                    </a>
                  </label>
                  <input formcontrolname="image" name="photo_id" type="file" id="profile_pic" ng-reflect-name="image" class="form-control" hidden>
                  
                  <span id="photo_id_error" class="help-inline error"></span>
                  
                </div>
              </div>
            </div>
          </div>

           <div class="d-md-flex">
            <div class="form-group input-form">
              <label>Nanny Type</label><br>
              <input class="nanny_type" type="radio" name="nanny_type" value="1" id="nanny_type" checked><label for="nanny_type"> Nanny </label>
              <input class="nanny_type" type="radio" name="nanny_type" value="2" id="nanny_type"> <label for="nanny_type">Babbysitter </label>
              <input class="nanny_type" type="radio" name="nanny_type" value="3" id="nanny_type"> <label for="nanny_type">Both </label>
            </div>
           </div>
          <div class="d-md-flex">
            <div class="form-group input-form identification">
                <label>Identification Type</label><br>
                <input class="identification_type" type="radio" name="identification_type" value="1" id="identification_type_passport" checked> <label for="identification_type_passport">Passport  </label>
                <input class="identification_type" type="radio" name="identification_type" value="2" id="identification_type_license"> <label for="identification_type_license">Driving License </label>
                  <!-- <label for="profile_pic2"> <a class="btn-theme text-white">Upload</a> </label> -->
                  <span id="identification_type_error" class="help-inline error"></span>
            </div>
            <div class="form-group input-form">
                  <input class="form-control identification_file1" type="text" placeholder="Upload your Passport" readonly>
                  <label for="identification_file">
                  <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="cloud-upload-alt" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" class="svg-inline--fa fa-cloud-upload-alt fa-w-20 fa-2x">
                    <path fill="currentColor" d="M537.6 226.6c4.1-10.7 6.4-22.4 6.4-34.6 0-53-43-96-96-96-19.7 0-38.1 6-53.3 16.2C367 64.2 315.3 32 256 32c-88.4 0-160 71.6-160 160 0 2.7.1 5.4.2 8.1C40.2 219.8 0 273.2 0 336c0 79.5 64.5 144 144 144h368c70.7 0 128-57.3 128-128 0-61.9-44-113.6-102.4-125.4zM393.4 288H328v112c0 8.8-7.2 16-16 16h-48c-8.8 0-16-7.2-16-16V288h-65.4c-14.3 0-21.4-17.2-11.3-27.3l105.4-105.4c6.2-6.2 16.4-6.2 22.6 0l105.4 105.4c10.1 10.1 2.9 27.3-11.3 27.3z" class=""></path>
                  </svg>
                </label>
                <input formcontrolname="image" type="file" id="identification_file" name="identification_file" ng-reflect-name="image" class="form-control identification_file" hidden>
                <span id="identification_file_error" class="help-inline error"></span>
            </div>
          </div>
          <div class="d-md-flex">
            <div class="form-group input-form">
              <input class="form-control cpr_certicate1"  type="text" placeholder="CPR certificate (if applicable)" readonly>
              <label for="CPR">
                <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="cloud-upload-alt" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" class="svg-inline--fa fa-cloud-upload-alt fa-w-20 fa-2x">
                  <path fill="currentColor" d="M537.6 226.6c4.1-10.7 6.4-22.4 6.4-34.6 0-53-43-96-96-96-19.7 0-38.1 6-53.3 16.2C367 64.2 315.3 32 256 32c-88.4 0-160 71.6-160 160 0 2.7.1 5.4.2 8.1C40.2 219.8 0 273.2 0 336c0 79.5 64.5 144 144 144h368c70.7 0 128-57.3 128-128 0-61.9-44-113.6-102.4-125.4zM393.4 288H328v112c0 8.8-7.2 16-16 16h-48c-8.8 0-16-7.2-16-16V288h-65.4c-14.3 0-21.4-17.2-11.3-27.3l105.4-105.4c6.2-6.2 16.4-6.2 22.6 0l105.4 105.4c10.1 10.1 2.9 27.3-11.3 27.3z" class=""></path>
                </svg>
              </label>
              <input formcontrolname="image" type="file" id="CPR" name="cpr_certificate" ng-reflect-name="image" class="form-control cpr_certicate" hidden> 
              <span id="cpr_certificate_error" class="help-inline error"></span>
            </div>
            <div class="form-group input-form">
              <input class="form-control other_certificate1" type="text" placeholder="Other certificates (if applicable)" readonly>
              <label for="Other_crp">
                <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="cloud-upload-alt" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" class="svg-inline--fa fa-cloud-upload-alt fa-w-20 fa-2x">
                  <path fill="currentColor" d="M537.6 226.6c4.1-10.7 6.4-22.4 6.4-34.6 0-53-43-96-96-96-19.7 0-38.1 6-53.3 16.2C367 64.2 315.3 32 256 32c-88.4 0-160 71.6-160 160 0 2.7.1 5.4.2 8.1C40.2 219.8 0 273.2 0 336c0 79.5 64.5 144 144 144h368c70.7 0 128-57.3 128-128 0-61.9-44-113.6-102.4-125.4zM393.4 288H328v112c0 8.8-7.2 16-16 16h-48c-8.8 0-16-7.2-16-16V288h-65.4c-14.3 0-21.4-17.2-11.3-27.3l105.4-105.4c6.2-6.2 16.4-6.2 22.6 0l105.4 105.4c10.1 10.1 2.9 27.3-11.3 27.3z" class=""></path>
                </svg>
              </label>
              <input formcontrolname="image" type="file" id="Other_crp" name="other_certificates" ng-reflect-name="image" class="form-control other_certificate" hidden> 
              <span id="other_certificates_error" class="help-inline error"></span>
            </div>
          </div>
          <div class="upload-resume">
            <div class="d-block d-lg-flex no-gutters">
              <div class="form-group input-form col-md-6">
                  <input class="form-control resume1" type="text" placeholder="Upload you Resume" readonly>
                  <label for="profile_pic3">
                  <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="cloud-upload-alt" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" class="svg-inline--fa fa-cloud-upload-alt fa-w-20 fa-2x">
                    <path fill="currentColor" d="M537.6 226.6c4.1-10.7 6.4-22.4 6.4-34.6 0-53-43-96-96-96-19.7 0-38.1 6-53.3 16.2C367 64.2 315.3 32 256 32c-88.4 0-160 71.6-160 160 0 2.7.1 5.4.2 8.1C40.2 219.8 0 273.2 0 336c0 79.5 64.5 144 144 144h368c70.7 0 128-57.3 128-128 0-61.9-44-113.6-102.4-125.4zM393.4 288H328v112c0 8.8-7.2 16-16 16h-48c-8.8 0-16-7.2-16-16V288h-65.4c-14.3 0-21.4-17.2-11.3-27.3l105.4-105.4c6.2-6.2 16.4-6.2 22.6 0l105.4 105.4c10.1 10.1 2.9 27.3-11.3 27.3z" class=""></path>
                  </svg>
                </label>
                <input formcontrolname="image" type="file" id="profile_pic3" name="resume" ng-reflect-name="image" class="form-control resume" hidden>
                <span id="resume_error" class="help-inline error"></span>
              </div>
              
              <div class="form-group col-md-6">
                <label>Zip/Postal Code</label><br>
                <div class="d-md-inline-flex">
                  <input class="form-control" type="text" placeholder="Zip/Postal Code" name="postcode">
                  <!-- <label for="profile_pic2"> <a class="btn-theme text-white">Upload</a> </label> -->
                  
                  </div><br>
                  <span id="postcode_error" class="help-inline error"></span>
              </div>
            </div>
            
              <div class="d-block d-lg-flex no-gutters">
                  <div class="form-group input-form col-md-6" style="margin:0px 38px 19px 0">
                      <label>Name</label><br>
                        <div class="d-md-inline-flex">
                          <input class="form-control" type="text" placeholder="Name" name="name" value="<?php echo e(isset($name) ? $name:''); ?>">
                        </div>
                        <br>
                        <span id="name_error" class="help-inline error"></span>
                  </div>

                  <div class="form-group input-form col-md-6" style="margin:0px 38px 19px 0">
                      <label>Email</label><br>
                      <div class="d-md-inline-flex">
                        <input type="hidden" name="provider" value="<?php echo e(isset($provider) ? $provider:''); ?>">
                        <input type="hidden" name="provider_id" value="<?php echo e(isset($provider_id) ? $provider_id:''); ?>">
                        <input type="hidden" name="user_image" value="<?php echo e(isset($image) ? $image:''); ?>">
                        <input class="form-control" type="text" placeholder="Email" name="email" value="<?php echo e(isset($email) ? $email:''); ?>">
                        <!-- <label for="profile_pic2"> <a class="btn-theme text-white">Upload</a> </label> -->
                         
                        </div><br>
                        <span id="email_error" class="help-inline error"></span>
                </div>
            </div>
            
              
              
          </div>
          <div class="paragraph-block">
            <p>After Submitting, our support team will review it and if approved, you can update your profile in futher steps.</p>
          </div>
          <button type="button" class="btn-theme text-white" id="user-register">Submit</button>
          <div class="login-img">
           <img src="<?php echo e(WEBSITE_IMG_URL); ?>line.png" class="line-img ab-img">
            <img src="<?php echo e(WEBSITE_IMG_URL); ?>triangle.png" class="triangle-img ab-img"> 
            <img src="<?php echo e(WEBSITE_IMG_URL); ?>line1.png" class="line1-img ab-img">
            <img src="<?php echo e(WEBSITE_IMG_URL); ?>close.png" class="close-img ab-img"> 
          </div>
        </div>
        <?php echo e(Form::close()); ?>

      </div>
    </div>
  </div>
</section>
    
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<script type="text/javascript">
$(".identification_type").on('change',function(){
  if($(this).val()==1){
    $(".identification_file1").attr('placeholder','Upload your Passport');
  }else{
    $(".identification_file1").attr('placeholder','Upload your Driving License');
  }
});

  function signUp(){
    var form = $("#user-registration-form").closest("form");
    var formData = new FormData(form[0]);
    $.ajax({
                url: "<?php echo e(route('web.signup.create')); ?>",
                method: 'post',
                //data: $('#user-registration-form').serialize(),
                data: formData,
                contentType: false,       
                cache: false,             
                processData:false, 
                beforeSend: function() {
                    $("#loader_img").show();
                },
                success: function(response){
                    $("#loader_img").hide();
                
                    if(response.success) {
                        window.location.href=response.page_redirect;
                    } else {
                      
                      $('span[id*="_error"]').each(function() {
                            var id = $(this).attr('id');

                            if(id in response.errors) {

                                $("#"+id).html(response.errors[id]);
                            } else {
                                $("#"+id).html('');
                            }
                        });
                    }
                }
            });
  }
  $(function() {
        $('#user-registration-form').keypress(function(e) { //use form id
            if (e.which == 13) {
               //-- to validate form 
             signUp();
                return false;
            }
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
      $("#removeimg").hide();
        $('#user-register').click(function() {
           signUp(); 
        });

        
       $('#profile_pic').change(function(){
        $('#photo_id_error').html('');
            var fsize = this.files[0].size,
                ftype = this.files[0].type,
                fname = this.files[0].name,
                fextension = fname.substring(fname.lastIndexOf('.')+1);
                validExtensions = ["jpg","jpeg","gif","png"];
                if ($.inArray(fextension, validExtensions) == -1){
                $('#photo_id_error').html('The photo id must be in: jpeg, jpg, png, gif, bmp formats');
                this.value = "";
                return false;
            }else{
                if(fsize > 3145728){
                   $('#photo_id_error').html('File size too large! Please upload less than 3MB');
                   this.value = "";
                   return false;
                }
                const file = this.files[0];

                if (file)
                {
                  let reader = new FileReader();
                  reader.onload = function(event){
                    console.log(event.target.result);
                    $("#removeimg").show();
                    $('#previewImg').attr('src', event.target.result);
                  }
                  reader.readAsDataURL(file);
                }
            }
          
        });
 
        $('#removeimg').click(function(){
           $('#previewImg').attr('src', '<?php echo WEBSITE_IMG_URL; ?>signup-profile.PNG');
           $("#removeimg").hide();
           //$('#previewImg').remove();
        });
       $('.cpr_certicate').on('change',function(){
        $('#cpr_certificate_error').html('');
          var fsize = this.files[0].size,
               ftype = this.files[0].type,
                fname = this.files[0].name,
                fextension = fname.substring(fname.lastIndexOf('.')+1);

                validExtensions = ["jpg","pdf","jpeg","gif","png"];
                $('.cpr_certicate1').val(fname);
                if ($.inArray(fextension, validExtensions) == -1){
                  $('#cpr_certificate_error').html('The cpr certificate must be in: pdf, docx, doc formats');
                  this.value = "";
                  return false;
                }else{
                  return true ; 
                }
        });  

        // $('.other_certificate').change(function(){
          $('.other_certificate').on('change',function(){
            $('#other_certificates_error').html('');
          var fsize = this.files[0].size,
                ftype = this.files[0].type,
                fname = this.files[0].name,
                fextension = fname.substring(fname.lastIndexOf('.')+1);
               
                validExtensions = ["jpg","pdf","jpeg","gif","png"];
                $('.other_certificate1').val(fname);
                if ($.inArray(fextension, validExtensions) == -1){
                  $('#other_certificates_error').html('The other certificates must be in: pdf, docx, doc formats');
                  this.value = "";
                  return false;
                }else{
                  return true ; 
                }
        });

        $('.identification_file').on('change',function(){
            $('#identification_file_error').html('');
          var fsize = this.files[0].size,
                ftype = this.files[0].type,
                fname = this.files[0].name,
                fextension = fname.substring(fname.lastIndexOf('.')+1);
               
                validExtensions = ["jpg","pdf","jpeg","gif","png","doc","docx"];
                $('.identification_file1').val(fname);
                if ($.inArray(fextension, validExtensions) == -1){
                  $('#identification_file_error').html('The uploaded file must be in: jpg,jpeg,pdf,gif,png,bmp,docx,doc formats');
                  this.value = "";
                  return false;
                }else{
                  return true ; 
                }
        });

 
       //  $('.resume').change(function(){
          $('.resume').on('change',function(){
            $('#resume_error').html('');
          var fsize = this.files[0].size,
                ftype = this.files[0].type,
                fname = this.files[0].name,
                fextension = fname.substring(fname.lastIndexOf('.')+1);
                
                validExtensions = ["jpg","pdf","jpeg","gif","png","doc","docx"];
                $('.resume1').val(fname);
                if ($.inArray(fextension, validExtensions) == -1){
                  $('#resume_error').html('The resume must be in: jpeg, jpg, png, gif, bmp, pdf, docx, doc, xls, excel formats');
                  this.value = "";
                  return false;
                }else{
                  return true ; 
                }
        }); 

    });

    
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('front.layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\xampp\htdocs\tinyhugs\resources\views/front/users/signup.blade.php ENDPATH**/ ?>