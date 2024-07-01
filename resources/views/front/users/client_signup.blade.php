@extends('front.layouts.default')
@section('content')
@section('title', 'User Sign Up') 

<section class="login pb-5">
        <div class="container">
            <div class="">
                <div class="login-form signup-form plan-detail-form">
                    <div class="row pb-5 flex-row">
                        <div class="col">
                            <div class="heading">
                                <!-- <h5>Looking for care?</h5> -->
                                <h3>Hello, Client</h3>
                            </div>
                        </div>
                        <div class="col-md-auto">
            <div class="create-account">Already have an account, <a href="{{ route('client.login') }}">
                              Log In </a> 
            </div>
          </div>
                    
                    </div>

                  
                    <div class="plan-detail-input">
                        {{ Form::open(array('id' => 'user-registration-form', 'class' => 'form')) }}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group input-form mw-100 my-2">
                                <label>Name</label>
                                   {{ Form::text('first_name','' , ['placeholder' => trans("Name"), 'class'=>'form-control']) }}
                                    <span id="first_name_error" class="help-inline error"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group input-form mw-100 my-2">
                                <label>Email</label>
                                    {{ Form::text('email','' , ['placeholder' => trans("Email"), 'class'=>'form-control']) }}
                                    <span id="email_error" class="help-inline error"></span>

                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group input-form mw-100 my-2">
                                    <label>Phone Number (Optional)</label>
                                    {{ Form::text('phone_number','', ['placeholder' => trans("Phone Number"), 'class'=>'form-control']) }}
                                    <span id="phone_number_error" class="help-inline error"></span>
                                </div>
                            </div>
                        </div>   
                        <div class="btn-block pt-3"> 
                        {{ Form::button(trans("Submit"), ['class' => 'btn-theme text-white', 'type' => 'button', 'id' => 'user-register']) }}
                            <!--<button type="button"  class="btn-theme text-white">Submit</button>   -->
                        </div> 
                         <div class="login-img">
                           <img src="{{WEBSITE_IMG_URL}}line.png" class="line-img ab-img">
                            <img src="{{WEBSITE_IMG_URL}}triangle.png" class="triangle-img ab-img"> 
                            <img src="{{WEBSITE_IMG_URL}}line1.png" class="line1-img ab-img">
                            <img src="{{WEBSITE_IMG_URL}}close.png" class="close-img ab-img"> 
                        </div>
                        {{Form::close()}} 
                    </div>      
                </div>  
                </div>
            </div>
        </div>
</section>

@endsection
@section('scripts')
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

/*   function signUp(){
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
                
                    if(response.success == true) {
                      alert('sdfsdfdfdsf');
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
        } */
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
<script>
        $(document).ready(function () {

         $('#user-register').click(function() {
           
           signUp(); 
           });
        });
    </script>

@endsection
