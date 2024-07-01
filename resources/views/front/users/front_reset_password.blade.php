@extends('front.layouts.default')

@section('content')
@section('title', 'Reset Password')


<section class="login pb-5">
    <div class="container">
        <div class="login-form">
            <div class="row pb-md-5 pb-3 flex-row">
                <div class="col">
                    <div class="heading">
                        <h3 style="text-align:center">Reset Your Password</h3>
                        
                   </div>
                    </div>
                  
            </div>
            <div class="form-bx">
   
                <form class="form" id="user-passreset-form">
                    <input name="_token" type="hidden" value="{{ csrf_token() }}"/>
                       <input type="hidden" name="validate_string" value="{{ $validate_string }}">
            
                <div class="forgot-form">
               
                    <div class="form-group">
                        {{Form::password('new_password', ['class' => 'form-control form-cs', 'placeholder' => 'New Password'])}}
                       <span id="new_password_error" class="help-inline error"></span>
                     </div>

                     <div class="form-group">
                        {{Form::password('new_password_confirmation', ['class' => 'form-control form-cs', 'placeholder' => 'Confirm Password'])}}
                     <span id="new_password_confirmation_error" class="help-inline error"></span>	
                   </div>
                                     
                    </div>
                <div class="btn-block">
                    {{ Form::button('Submit', ['type' => 'button', 'class' => 'btn-theme', 'id' => 'user-resetpass']) }}
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


<
@endsection
@section('scripts')
<script type="text/javascript">
  $(function() {
        $('#user-passreset-form').keypress(function(e) { //use form id
            if (e.which == 13) {
               //-- to validate form 
              $.ajax({
                url: "{{ url('reset-password/')}}",
                method: 'post',
                data: $('#user-passreset-form').serialize(),
                beforeSend: function() {
                    $("#loader_img").show();
                },
                success: function(response){
                    $("#loader_img").hide();
                
                    if(response.success) {
                      //console.log(response.);
                        //window.location.href="{{url('/')}}";
                        window.location.href=response.page_redirect;
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
                return false;
            }
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#user-resetpass').click(function() {
            $.ajax({
                url: "{{ url('reset-password/')}}",
                method: 'post',
                data: $('#user-passreset-form').serialize(),
                beforeSend: function() {
                    $("#loader_img").show();
                },
                success: function(response){
                    $("#loader_img").hide();
                
                    if(response.success) {
                      //console.log(response.);
                        //window.location.href="{{url('/')}}";
                        window.location.href=response.page_redirect;
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



