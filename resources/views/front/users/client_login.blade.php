@extends('front.layouts.default')
@section('content')
<section class="login pb-5">
    <div class="container">
        <div class="login-form">
            <div class="row pb-md-5 pb-3 flex-row">
                <div class="col">
                    <div class="heading">
                        <h5>Welcome!</h5>
                        <h3>Hello Client, Log into  your Account</h3>
                   </div>
                    </div>
                    <div class="col-md-auto">
                       <div class="create-account"> Not a member? <a href="{{ route('user.pricing') }}" style="color: #ffd500;">Sign up   </a></div>
                    </div>
            </div>
            <div class="form-bx">
   
             <div class="social-login ">
                 <a href="{{ url('/login/client/google') }}" class="g-login">
                    <img src="{{WEBSITE_IMG_URL}}google-icon.jpg">  Login with Google
                </a>
                <a href="{{ url('/login/client/facebook') }}" class="fb-login">
                    <img src="{{WEBSITE_IMG_URL}}fb-icon.PNG"> Login with Facebook
               </a>
            </div>
       
            {{ Form::open(array('id' => 'user-login-form', 'class' => 'form')) }}
             <input type="hidden" name="url" value="{{url()->previous()}}">
                <div class="forgot-form">
                    <div class="form-group">
                       {{ Form::text('email', (! empty(Request::old('email'))) ? Request::old('email') : '', ['placeholder' => 'Email', 'class' => 'form-control']) }}
                       <span id="email_error" class="help-inline error"></span>
                    </div>
                    <div class="form-group ">
                        {{Form::password('password', ['class' => 'form-control', 'placeholder' => 'Password'])}}
                        <span id="password_error" class="help-inline error"></span>
                    </div>
                    
                    <div class="form-group ">
                        @if(config('services.recaptcha.key'))
                            <div class="g-recaptcha" data-sitekey="{{config('services.recaptcha.key')}}"> </div>
                        @endif
                        <span id="g-recaptcha-response_error" class="help-inline error"></span>
                     </div>  
                    <div class="form-group text-right">
                        <a href="{{ route('user.forgot_password')}}">Forget Password?</a>
                    </div>
                   
                </div>
                <div class="btn-block">
                    {{ Form::button('Log In', ['type' => 'button', 'class' => 'btn-theme', 'id' => 'user-login']) }}
                    </div>
              
            {{Form::close()}}
        </div>
    </div>
    <div class="login-img">
        <img src="{{WEBSITE_IMG_URL}}line.png" class="line-img ab-img">
        <img src="{{WEBSITE_IMG_URL}}triangle.png" class="triangle-img ab-img">
          <img src="{{WEBSITE_IMG_URL}}line1.png" class="line1-img ab-img">
        <img src="{{WEBSITE_IMG_URL}}close.png" class="close-img ab-img">
        </div>
    </div>
</section>

<?php /*
<section class="m-login login" style="background-image: url(img/login-bg.jpg);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="body-content">
                    <div class="clearfix">
                       <div class="col-md-12 pd-0 mb-login-height">
                          <div class="row align-items-center justify-content-between">
                             
                             <div class="col-md-6">
                                <div class="form-sec">
                                   <h3 class="login-heading font24">Login in to Myndaro</h3>
                                   <!-- <div class="s-social">
                                       <a href="#">
                                           <i class="fab fa-facebook-f"></i>
                                       </a>
                                       <a href="#">
                                           <i class="fab fa-twitter"></i>
                                       </a>
                                       <a href="#">
                                           <i class="fab fa-google-plus-g"></i>
                                       </a>
                                      
                                   </div> -->
                                    {{ Form::open(array('id' => 'user-login-form', 'class' => 'form')) }}
                                    <input name="_token" type="hidden" value="{{ csrf_token() }}"/>
                                    <input type="hidden" name="url" value="{{url()->previous()}}">

                                      <div class="form-group">
                                         {{ Form::text('email', (! empty(Request::old('email'))) ? Request::old('email') : '', ['placeholder' => 'Email', 'class' => 'form-control form-cs']) }}
                                        <span id="email_error" class="help-inline error"></span>
                                      </div>
                                      <span id="email_error" class="help-block"></span>
                                      <div class="form-group">
                                         {{Form::password('password', ['class' => 'form-control form-cs', 'placeholder' => 'Password'])}}
                                          <span id="password_error" class="help-inline error"></span>
                                      </div>
                                      <div class="checkbox signup-terms d-flex justify-content-center">
                                         <a href="{{ route('user.forgot_password') }}"  >Forgot password?</a>
                                      </div>
                                      <p class="clearfix text-center">
                                        {{ Form::button('Log In', ['type' => 'button', 'class' => 'w-100 btn btn btn-primary', 'id' => 'user-login']) }}
                                        </p>
                                   {{Form::close()}}
                                   <!-- <p class="text-center mb40">Not a member? <a data-toggle="modal"
                                      data-target="#register-modal" data-dismiss="modal"
                                      class="text-blue font-medium"  href="javascript:void(0);">Register</a></p> -->
                                </div>
                             </div>
                             <div class="col-md-6 pd-0 signup-left-img-height hidden-xs">
                                <div>
                                   <div class="signup-left-img"></div>
                                   <div class="loginleft-text">
                                      <a href="javascript:void(0)" class="btn btn-primary start_signup">START FREE TRIAL</a>
                                      <div class="d-flex align-items-center justify-content-center">
                                        <img src="{{ WEBSITE_IMG_URL.'login.png' }}" alt="">
                                     </div>
                                   </div>
                                </div>
                             </div>
                          </div>
                       </div>
                    </div>
                 </div>
            </div>
        </div>
    </div>
</section> */ ?>

@endsection

@section('scripts')
<script src='https://www.google.com/recaptcha/api.js'></script>
<script type="text/javascript">
  $(function() {
        $('#user-login-form').keypress(function(e) { //use form id
            if (e.which == 13) {
              login();
            }
        });
    });
	function login(){
		$.ajax({
			url: "{{ route('web.login.submit')}}",
			method: 'post',
			data: $('#user-login-form').serialize(),
			beforeSend: function() {
				$("#loader_img").show();
			},
			success: function(response){
                grecaptcha.reset();
				$("#loader_img").hide();
			
				if(response.success == true) {
				  //console.log(response.);
					window.location.href=response.page_redirect;
				   //location.reload();
				}else if(response.success == 2){
				  show_message(response.message,'error');
				}else if(response.success == false){
				  $('span[id*="_error"]').each(function() {
						var id = $(this).attr('id');
						if(id in response.errors) {
							$("#"+id).html(response.errors[id]);
						} else {
							$("#"+id).html('');
						}
					});
				  //location.reload();
				}  else {
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
        $('#user-login').click(function() {
            login();
        });
    });
</script>
@endsection