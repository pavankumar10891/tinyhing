@extends('front.layouts.default')

@section('content')
@section('title', 'Sign Up') 

<section class="login pb-5">
        <div class="container">
            <div class="">
                <div class="login-form signup-form">
                    <div class="row pb-5 flex-row">
                        <div class="col">
                            <div class="heading">
                                <h5>Looking for care?</h5>
                                <h3>Nannies, Create your account</h3>
                            </div>
                        </div>
                        <div class="col-md-auto">
                            <div class="create-account">Already have an account, <a href="{{ route('user.login') }}">  Log In </a></div>
                        </div>
                    </div>
                    {{ Form::open(array('id' => 'user-registration-form', 'class' => 'form')) }}
                    <div class="signup-cont">
                        <div class="row align-items-center">
                            <div class="col-auto">
                            <div class="img-wall1"> <img id="previewImg" src="{{WEBSITE_IMG_URL}}signup-profile.PNG" class="img-fluid"> <a id="removeimg" href="javascript:void(0)"><i class="fa fa-times" aria-hidden="true"></i></a> </div>
                            </div>
                            <div class="col pl-3">
                                <div class="signup-detail">
                                    <h5>Upload you Photo ID</h5>
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

                        <div class="row pb-3">
                            <div class="col-md-4">
                                <div class="form-group input-form">
                                {{ Form::text('first_name','' , ['placeholder' => trans("First Name"), 'class'=>'form-control']) }}
                                <span id="first_name_error" class="help-inline error"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group input-form">
                                {{ Form::text('last_name','' , ['placeholder' => trans("Last Name"), 'class'=>'form-control']) }}
                                <span id="last_name_error" class="help-inline error"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group input-form">
                                {{ Form::text('email','' , ['placeholder' => trans("Email"), 'class'=>'form-control']) }}
                                <span id="email_error" class="help-inline error"></span>
                                </div>
                            </div>
                            
                        </div>
                        <div class="row">

                            <div class="col-md-4">
                                <div class="form-group">
                                {{ Form::text('phone_number','', ['placeholder' => trans("Phone Number"), 'class'=>'form-control']) }}
                                <span id="phone_number_error" class="help-inline error"></span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                {{Form::password('password', ['class' => 'form-control', 'placeholder' => 'New Password'])}}
                                <span id="password_error" class="help-inline error"></span>
                                </div>

                            </div>

                            <div class=" col-md-4">
                                <div class="form-group ml-lg-3">
                                {{Form::password('confirm_password', ['class' => 'form-control', 'placeholder' => 'Confirm Password'])}}
                                <span id="confirm_password_error" class="help-inline error"></span>
                                </div>
                            </div>
                        </div>
                   
                    <div class="paragraph-block">

                        <p>After Submitting, our support team will review it and if approved, you can update your
                            profile in futher steps.</p>
                    </div>
                    <button type="button" class="btn-theme text-white" id="user-register">Submit</button>
                    <div class="login-img">
                        <img src="img/line.png" class="line-img ab-img">
                        <img src="img/triangle.png" class="triangle-img ab-img">
                        <img src="img/line1.png" class="line1-img ab-img">
                        <img src="img/close.png" class="close-img ab-img">
                    </div>
                </div> 
                {{Form::close()}}    
            </div>
        </section>

@endsection
@section('scripts')
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<script type="text/javascript">

  function signUp(){
    var form = $("#user-registration-form").closest("form");
    var formData = new FormData(form[0]);
    $.ajax({
                url: "{{ route('user.user.signup')}}",
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
       

    });

    
</script>
@endsection
