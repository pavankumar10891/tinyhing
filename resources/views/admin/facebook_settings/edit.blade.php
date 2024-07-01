@extends('admin.layouts.default')
@section('content') 
<!--begin::Content-->
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
                        Edit {{ $sectionNameSingular }} </h5>
                    <!--end::Page Title-->

                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard')}}" class="text-muted">Dashboard</a>
                        </li>
                        <?php /*
                        <li class="breadcrumb-item">
                            <a href="{{ route($modelName.'.index')}}" class="text-muted">{{ $sectionName }}</a>
                        </li>  */?>
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
          {{ Form::open(['role' => 'form','url' =>  route("$modelName.edit"),'class' => 'mws-form', 'files' => true,"autocomplete"=>"off"]) }}
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-1"></div>
                        <div class="col-xl-10">
                            <h3 class="mb-10 font-weight-bold text-dark">
                                {{ $sectionNameSingular }} Information</h3>

                            <div class="row">
                                <div class="col-xl-6">
                                    <!--begin::Input-->
                                    <div class="form-group">
                                        {!! HTML::decode( Form::label('page_id', trans("Page ID").'<span class="text-danger">
                                            * </span>')) !!}
                                        {{ Form::text('page_id', isset($model->page_id) ? $model->page_id : '', ['class' => 'form-control form-control-solid form-control-lg '.($errors->has('page_id') ? 'is-invalid':'')]) }}
                                        <div class="invalid-feedback"><?php echo $errors->first('page_id'); ?></div>
                                    </div>
                                    <!--end::Input-->
                                </div>
                                @if(!empty($model))
                                    @if(!empty($model->page_token))    
                                        <div class="col-xl-6">
                                        <b>Already Logged In</b>
                                        {{ Form::hidden('user_id',isset($model->user_id) ? $model->user_id : '', ['class' => 'fb_user_id']) }}
                                        {{ Form::hidden('user_token', isset($model->user_token) ? $model->user_token : '', ['class' => 'fb_user_token']) }}
                                        </div>                     
                                    @else
                                        <fb:login-button scope="public_profile,email" data-width="100%" data-size="medium"  class="fb_login" onlogin="checkLoginState();"></fb:login-button>
                                        {{ Form::hidden('user_id', '', ['class' => 'fb_user_id']) }}
                                        {{ Form::hidden('user_token', '', ['class' => 'fb_user_token']) }}
                                        <b class="submit_button"  style="display:none !important"> Logged In</b>
                                    @endif
                                @else
                                    <fb:login-button scope="public_profile,email" data-width="100%" data-size="medium" class="fb_login" onlogin="checkLoginState();"></fb:login-button>

                                    {{ Form::hidden('user_id', '', ['class' => 'fb_user_id']) }}
                                    {{ Form::hidden('user_token', '', ['class' => 'fb_user_token']) }}
                                    <b class="submit_button"  style="display:none !important"> Logged In</b>
                                @endif
                            </div>
                            @if(!empty($model))
                                @if(!empty($model->page_token)) 
                                    <div class="d-flex justify-content-between border-top mt-5 pt-10 submit_button">
                                @else
                                    <div class="d-flex justify-content-between border-top mt-5 pt-10 submit_button" style="display:none !important">
                                @endif
                            @else
                                    <div class="d-flex justify-content-between border-top mt-5 pt-10 submit_button" style="display:none !important">
                            @endif
                                <div>
                                    <button button type="submit" class="btn btn-success font-weight-bold text-uppercase px-9 py-4">
                                    Submit
                                    </button>
                                </div>
                            </div>
                        </div>

                        <script>
                                 function statusChangeCallback(response) {  // Called with the results from FB.getLoginStatus().
                                        console.log('statusChangeCallback');
                                        console.log(response);
                                        if(response.status == 'connected')
                                        {
                                            var token = response.authResponse.accessToken;
                                            var userid = response.authResponse.userID;
                                            $(".fb_user_id").val(userid);
                                            $(".fb_user_token").val(token);
                                            $(".submit_button").show();
                                            $(".fb_login").hide();
                                        }
                                        
                                        // $.ajax({
                                        //     url: "{{ route('Facebook.update.token')}}",
                                        //     method: 'post',
                                        //     data:{userid:userid, token:token}, 
                                        //     beforeSend: function (request) {
                                        //         return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                                        //     },
                                        //     success: function(response){
                                        //         console.log(response); 
                                        //     }
                                        // });
                                        
                                                        // The current login status of the person.
                                       // if (response.status === 'connected') {   // Logged into your webpage and Facebook.
                                        //console.log(response)

                                        //    testAPI();  
                                        //} else {                                 // Not logged into your webpage or we are unable to tell.
                                        //document.getElementById('status').innerHTML = 'Please log ' +'into this webpage.';
                                        //}

                                        
                                    }


                                    function checkLoginState() {        
                                        sessionStorage.removeItem("fbssls_307086770869300"); 

                                        // Called when a person is finished with the Login Button.
                                        FB.getLoginStatus(function(response) {   // See the onlogin handler
                                            statusChangeCallback(response);
                                        });
                                    }


                                        window.fbAsyncInit = function() {
                                            FB.init({
                                                appId      : '307086770869300',
                                                cookie     : true,                     // Enable cookies to allow the server to access the session.
                                                xfbml      : true,                     // Parse social plugins on this webpage.
                                                version    : 'v10.0'           // Use this Graph API version for this call.
                                            });

                                            sessionStorage.removeItem("fbssls_307086770869300"); 
   
                                            // FB.getLoginStatus(function(response) {   // Called after the JS SDK has been initialized.
                                            //     statusChangeCallback(response);        // Returns the login status.
                                            // });
                                        };
                 
                                        function testAPI() {                      // Testing Graph API after login.  See statusChangeCallback() for when this call is made.
                                            console.log('Welcome!  Fetching your information.... ');
                                            FB.api('/me', function(response) {
                                            console.log('Successful login for: ' + response.name);
                                            document.getElementById('status').innerHTML =
                                                'Thanks for logging in, ' + response.name + '!';
                                            });
                                        }


                                 </script>  
                                 
                                <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js"></script>
                    </div>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@stop