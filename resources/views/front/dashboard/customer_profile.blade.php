@extends('front.dashboard.layouts.default')
@section('content')
<div class="main-workspace">
   <div class="container">
      <div class="dashboard-profile">
                    <div class="dashboard-block">
                        <div class="row pb-4 flex-row">
                            <div class="col">
                                <div class="heading">
                            
                                    <h4>Edit Information</h4>
                                </div>
                            </div>
                            <!-- <div class="col-md-auto">
                                <div class="create-account">Already have an account, <a href="">
                                        Log In </a></div>
                            </div> -->
                        </div>
                        <div class="signup-cont">
                        {{ Form::open(array('id' => 'customer-edit-form', 'class' => 'form')) }}
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <div class="img-wall1">
                                    @if(!empty($user_data->photo_id) )
                                        @php($profile_img =  USER_IMAGE_URL.Auth::user()->photo_id) 
                                    @else
                                        @php($profile_img =  WEBSITE_IMG_URL.'no-image.png' ) 
                                    @endif
                                        <img id="previewImg" src="{{ $profile_img  }}" class="img-fluid">
                                    </div>
                                </div>
                                <div class="col pl-3">
    
                             
                                    <div class="signup-detail">
                                        <h5>Upload you Photo ID</h5>
                                        <div class="upload-img">

                                            <label for="profile_pic">
                                                <a class="btn-theme text-white" for="profile_pic1">
                                                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="cloud-upload-alt" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" class="svg-inline--fa fa-cloud-upload-alt fa-w-20 fa-2x">
                                                        <path fill="currentColor" d="M537.6 226.6c4.1-10.7 6.4-22.4 6.4-34.6 0-53-43-96-96-96-19.7 0-38.1 6-53.3 16.2C367 64.2 315.3 32 256 32c-88.4 0-160 71.6-160 160 0 2.7.1 5.4.2 8.1C40.2 219.8 0 273.2 0 336c0 79.5 64.5 144 144 144h368c70.7 0 128-57.3 128-128 0-61.9-44-113.6-102.4-125.4zM393.4 288H328v112c0 8.8-7.2 16-16 16h-48c-8.8 0-16-7.2-16-16V288h-65.4c-14.3 0-21.4-17.2-11.3-27.3l105.4-105.4c6.2-6.2 16.4-6.2 22.6 0l105.4 105.4c10.1 10.1 2.9 27.3-11.3 27.3z" class=""></path>
                                                    </svg>Upload</a>
                                            </label>
    
                                            <input formcontrolname="image" name="photo_id"  type="file" id="profile_pic" ng-reflect-name="image" class="form-control" hidden="">
                                        </div>
                                    </div>
                                </div>
    
    
                            </div>
    
                         <div class="edit-profile-heading py-0 my-4">User Information</div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group input-form mw-100">
                                    {{Form::text('name', ( !empty($user_data->name)  ? $user_data->name : '' ), ['class' => 'form-control', 'placeholder' => 'Name'])}}
                                    <span id="name_error" class="help-inline error"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group input-form mw-100">
                                       <input class="form-control" name="email" type="email" value="<?php  echo  !empty($user_data->email) ? $user_data->email  : ''  ?>" placeholder="Email" >
                                    </div>
                                </div>
                               
                            </div>
                            <div class="row">
    
                                <div class="col-md-6">
                                    <div class="form-group input-form mw-100">
                                    <input class="form-control" type="text" name="phone_number" value="<?php  echo  !empty($user_data->phone_number) ? $user_data->phone_number  : ''  ?>" placeholder="Phone Number" >
                                        <span id="phone_number_error" class="help-inline error"></span>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group input-form mw-100">
                                    {{Form::text('postcode', ( !empty($user_data->postcode)  ? $user_data->postcode : '' ), ['class' => 'form-control', 'placeholder' => 'Zipcode'])}}
                                    <span id="postcode_error" class="help-inline error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
    
                                <div class="col-md-6">
                                    <div class="form-group input-form mw-100">
                                    {{Form::text('city', ( !empty($user_data->city)  ? $user_data->city : '' ), ['class' => 'form-control', 'placeholder' => 'City'])}}
                                    <span id="city_error" class="help-inline error"></span>
                                    </div>
                                </div> <div class="col-md-6">
                                    <div class="form-group input-form mw-100">
                                    {{Form::text('state', ( !empty($user_data->state)  ? $user_data->state : '' ), ['class' => 'form-control', 'placeholder' => 'State'])}}
                                    <span id="state_error" class="help-inline error"></span>
                                    </div>
                                </div>
                            </div>
    

                        <button type="button"  id="save-customer-data"  class="btn-theme text-white">Save Changes</button>
                        {{Form::close()}}
                    </div> </div>
                    <div class="dashboard-block">
                        <div class="row pb-md-4 pb-3 flex-row">
                            <div class="col">
                                <div class="heading">
                                    <h4>Reset Password</h4>
                               </div>
                                </div>
                                <!-- <div class="col-md-auto">
                                   <div class="create-account">   Already have an account,<a href="">Log In   </a></div>
                                </div> -->
                        </div>
                        <div class="form-bx">             
                        {{ Form::open(array('id' => 'change-password', 'class' => 'form')) }}
                        <div class="pass-block">
                            <div class="form-group input-form mw-100">
                                {{Form::password('old_password', ['class' => 'form-control ', 'placeholder' => 'Old Password'])}}
                                <span id="old_password_error" class="help-inline error"></span>
                            </div>
                            <div class="form-group input-form mw-100">
                            {{Form::password('new_password', ['class' => 'form-control', 'placeholder' => 'New Password'])}}
                            <span id="new_password_error" class="help-inline error"></span>
                            </div>

                            <div class="form-group input-form mb-1 mw-100">
                            {{Form::password('new_password_confirmation', ['class' => 'form-control', 'placeholder' => 'Confirm Password'])}}
                            <span id="new_password_confirmation_error" class="help-inline error"></span>	
                            </div>
                        
                        </div>
                        <div class="btn-block">
                            <button type="button"  id="password-reset"  class="btn-theme">
                            Submit
                            </button>
                        </div>
                    
                        {{Form::close()}}
                        </div>
                    </div>
                </div>
            </div>

        </div>
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<script type="text/javascript">
function signUp(){
    var form = $("#customer-edit-form").closest("form");
    var formData = new FormData(form[0]);
    $.ajax({
                url: "{{ route('nanny.profile.update')}}",
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
                        location.reload();
                      /*   window.location.href=response.page_redirect; */
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
        $('#customer-edit-form').keypress(function(e) { //use form id
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
        $('#save-customer-data').click(function() {
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
                  console.log(file);
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
      

        $('#password-reset').click(function() {
            $.ajax({
                url: "{{ url('password-update/')}}",
                method: 'post',
                data: $('#change-password').serialize(),
                beforeSend: function() {
                    $("#loader_img").show();
                },
                success: function(response){
                    $("#loader_img").hide();
                
                    if(response.success) {
                      //console.log(response.);
                        //window.location.href="{{url('/')}}";
                        location.reload();
                       //location.reload();
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
        });
    });

    
</script>

@endsection