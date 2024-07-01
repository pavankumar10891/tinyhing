@extends('front.layouts.default')
@section('content')
@section('title', 'Sign Up')
<div class="myndaro-profile">
    <div class="container">
        <div class="myndaro-profile-inner">
            <div class="row">
                <div class="col-md-12">
                    <div class="m-profile-section">
                        {{ Form::open(array('id' => 'user-image-form', 'class' => 'form', 'files' => true)) }}
                        <div class="profile-upload-section">
                            <div class="profile-img">
                                <span class="profile-icon">
                                    @if(!empty(Auth::user()->image) &&
                                    file_exists(USER_IMAGE_ROOT_PATH.Auth::user()->image))
                                    <img src="{{ USER_IMAGE_URL.Auth::user()->image }}" alt="">
                                    @else
                                    <img src="{{ WEBSITE_IMG_URL.'user-male.png' }}" alt="">
                                    @endif
                                </span>
                            </div>
                            <div class="upload-section">
                                <div class="upload-btn">
                                    <label>
                                        {{ Form::file('image',['id' => 'profile_pic']) }}
                                        Choose File
                                        <i class="fa fa-long-arrow-right"></i>
                                    </label>

                                </div>
                                <div class="msg-validation file-name">(JPEG Or PNG 500x500px Thumbnail)</div>
                                <span id="image_error" class="help-inline error"></span>
                            </div>
                            <div class="edit-btn-section">
                                {{ Form::button('Update', ['type' => 'button', 'class' => 'edit-btn', 'id' => 'user-image']) }}
                            </div>
                        </div>
                        {{Form::close()}}

                        <div class="form-box user-profilinfo">
                            <h4 class="user-profilinfo-heading">
                                <span>General Info</span>
                               
                            </h4>
                            {{ Form::open(array('id' => 'user-profile-form', 'class' => 'form')) }}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="floating-label">

                                        {{ Form::text('name', (! empty(Auth::user()->name)) ? Auth::user()->name : '', ['placeholder' => ' ', 'class' => 'floating-input']) }}
                                        <span class="highlight"></span>
                                        <label>Name</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="floating-label">
                                        {{ Form::text('email', (! empty(Auth::user()->email)) ? Auth::user()->email : '', ['placeholder' => ' ', 'class' => 'floating-input']) }}
                                        <span class="highlight"></span>
                                        <label>Email</label>
                                    </div>
                                    <span id="email_error" class="help-inline error"></span>
                                </div>
                                <div class="col-md-6">
                                    <div class="floating-label">
                                        {{ Form::text('phone_number', (! empty(Auth::user()->phone_number)) ? Auth::user()->phone_number : '', ['placeholder' => ' ', 'class' => 'floating-input']) }}
                                        <span class="highlight"></span>
                                        <label>Phone Number</label>
                                    </div>
                                    <span id="phone_number_error" class="help-inline error"></span>
                                </div>
                               
                                <div class="col-md-12">
                                    <div class="submit-btn">

                                        {{ Form::button('Save', ['type' => 'button', 'class' => '', 'id' => 'user-profile']) }}
                                    </div>
                                </div>
                            </div>
                            {{Form::close()}}

                            <div class="form-box">
                                <h4><span>Change Password</span></h4>
                                {{ Form::open(array('id' => 'user-chanagepass-form', 'class' => 'form')) }}
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="floating-label">
                                            {{Form::password('oldpassword', ['class' => 'floating-input', 'placeholder' => ' '])}}
                                            <span class="highlight"></span>
                                            <label>Old Password</label>
                                        </div>
                                        <span id="oldpassword_error" class="help-inline error"></span>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="floating-label">
                                            {{Form::password('newpassword', ['class' => 'floating-input', 'placeholder' => ' '])}}
                                            <span class="highlight"></span>
                                            <label>New Password</label>
                                        </div>
                                        <span id="newpassword_error" class="help-inline error"></span>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="submit-btn">

                                            {{ Form::button('Change Password', ['type' => 'button', 'class' => '', 'id' => 'user-chanagepass']) }}
                                        </div>
                                    </div>
                                </div>
                                {{Form::close()}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
@section('scripts')
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
 
<script type="text/javascript">
    function uploadimage(){
		var formData = $('#user-image-form')[0];
        $.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: "{{ route('user.imageupload')}}",
			type: 'POST',
			data: new FormData(formData),
			contentType: false,
			cache: false,
			processData: false,
			beforeSend: function() {
				$("#loader").show();
			},
			success: function(response){
				$("#loader").hide();
			
				if(response.success) {
					location.reload(true);
				}else if(response.success == 2){
					show_message(response.message,'error');
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

    function profile()
    {
      $.ajax({
                url: "{{ route('user.profile.update')}}",
                method: 'post',
                data: $('#user-profile-form').serialize(),
                beforeSend: function() {
                    $("#loader").show();
                },
                success: function(response){
                    $("#loader").hide();
                
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

    function changePass()
    {
      $.ajax({
                url: "{{ route('user.password.update')}}",
                method: 'post',
                data: $('#user-chanagepass-form').serialize(),
                beforeSend: function() {
                    $("#loader").show();
                },
                success: function(response){
                    $("#loader").hide();
                
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

    $(document).ready(function() {
        $('#user-image').click(function() {
            uploadimage();
        });
    });
    $(document).ready(function() {
        $('#user-profile').click(function() {
            profile();
        });
    });
    $(document).ready(function() {
        $('#user-chanagepass').click(function() {
            changePass();
        });
    });
</script>
<style>
.weight input.floating-input.form-cs {
    width: 100%;
}
</style>

@endsection
