@extends('front.layouts.default')
@section('content')
<section class="login pb-5">
    <div class="container">
        <div class="login-form">
            <div class="row pb-md-5 pb-3 flex-row">
                <div class="col">
                    <div class="heading">
                    <h3 style="text-align:center">Forget Password ?</h3>
                        
                   </div>
                    </div>

            </div>
            <div class="form-bx">
                 <!-- <div class="col-md-auto mb-2" style="text-align:center">
                    <div class="create-account"> Login here <a href="{{ route('user.login') }}">Login</a></div>
                 </div>
                 <div class="social-login mt-4">
                    <a href="{{ url('/login/google') }}" class="g-login">
                       <img src="{{WEBSITE_IMG_URL}}google-icon.jpg">  Login with Google
                   </a>
                   <a href="{{ url('/login/facebook') }}" class="fb-login">
                       <img src="{{WEBSITE_IMG_URL}}fb-icon.PNG"> Login with Facebook
                  </a>
               </div> -->
                {{ Form::open(array('id' => 'user-forgotpass-form', 'class' => 'form')) }}
                <input name="_token" type="hidden" value="{{ csrf_token() }}"/>
                <div class="forgot-form">
               
                    <div class="form-group">
                            {{ Form::text('forgot_email', (! empty(Request::old('forgot_email'))) ? Request::old('forgot_email') : '', ['placeholder' => 'Email', 'class' => 'form-control form-cs']) }}
                        <span id="forgot_email_error" class="help-inline error"></span>
                        </div>
                                     
                    </div>
                <div class="btn-block">
                    {{ Form::button('Submit', ['type' => 'button', 'class' => 'btn-theme', 'id' => 'user-forgotpass']) }}
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

@endsection
@section('scripts')
<script type="text/javascript">
  $(function() {
        $('#user-forgotpass-form').keypress(function(e) { //use form id
            if (e.which == 13) {
               //-- to validate form 
              $.ajax({
                url: "{{ route('user.forgot_password_send')}}",
                method: 'post',
                data: $('#user-forgotpass-form').serialize(),
                beforeSend: function() {
                    $("#loader_img").show();
                },
                success: function(response){
                    $("#loader_img").hide();
                
                    if(response.success) {
                      //console.log(response.);
                        //window.location.href="{{url('/')}}";
                       location.reload();
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
                return false;
            }
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#user-forgotpass').click(function() {
            $.ajax({
                url: "{{ route('user.forgot_password_send')}}",
                method: 'post',
                data: $('#user-forgotpass-form').serialize(),
                beforeSend: function() {
                    $("#loader_img").show();
                },
                success: function(response){
                    $("#loader_img").hide();
                
                    if(response.success) {
                      //console.log(response.);
                        //window.location.href="{{url('/')}}";
                       location.reload();
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