<div class="loader-wrapper" id="loader_img" style="display:none;">
        <div class="loader">
            <img src="{{$logoimage}}" alt="">
            <div class="material-spinner"></div>
        </div>
    </div>

    <div class="overlay" style="display:none"></div>

    <!-- Header -->
    <div class="dashboard-header">
        <div class="container-fluid px-4 px-md-5">
            
            
            <nav class="navbar navbar-expand-lg">
                <a class="navbar-brand" href="{{url('/')}} ">
                    <img src="{{ $logoimage }}" alt="">
                </a>

                <div class="row align-items-center nav-right padding-sec">
                    <div class="d-flex author-detail align-items-center nav-right">
                        <div class="date-header">
                            <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="clock" role="img"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                                class="svg-inline--fa fa-clock fa-w-16 fa-2x">
                                <path fill="currentColor"
                                    d="M256,8C119,8,8,119,8,256S119,504,256,504,504,393,504,256,393,8,256,8Zm92.49,313h0l-20,25a16,16,0,0,1-22.49,2.5h0l-67-49.72a40,40,0,0,1-15-31.23V112a16,16,0,0,1,16-16h32a16,16,0,0,1,16,16V256l58,42.5A16,16,0,0,1,348.49,321Z"
                                    class=""></path>
                            </svg>{{ date('m/d/Y') }}
                        </div>
                        @if(!empty(Auth::user()))
                        <?php  $userData  = Auth::user();    ?>
                        <div class="d-flex align-items-center nav-right pl-3">
                                @if( !empty(Auth::user()->photo_id) && file_exists(USER_IMAGE_ROOT_PATH.Auth::user()->photo_id))
                                    <a href="#"><img src="{{ WEBSITE_URL.'image.php?width=47&height=47&cropratio=3:3&image='.USER_IMAGE_URL.Auth::user()->photo_id }}" alt=""></a> 
                                @else
                                    @if($userData->user_role_id  == NANNY_ROLE_ID  ) 
                                        <a href="#"><img src="{{ WEBSITE_URL.'image.php?width=47&height=47&cropratio=3:3&image='.WEBSITE_IMG_URL.'no-female.jpg' }}" alt=""></a> 
                                    @else
                                        <a href="#"><img src="{{ WEBSITE_URL.'image.php?width=47&height=47&cropratio=3:3&image='.WEBSITE_IMG_URL.'no-image.png' }}" alt=""></a> 
                                    @endif
                                 @endif   
                          
                            <div class="author-name text-left pl-3">
                                <h3>{{ (!empty($userData->name) ?  $userData->name  :  '' )  }}</h3>
                                <span>
                                @if($userData->user_role_id  == NANNY_ROLE_ID  ) 
                                Nanny 
                                @else
                                Customer
                                @endif
                                   </span>
                            </div>
                        </div>
                        @endif
                    </div>

                    <button class=" sidebar-toggle pl-3" type="button">
                        <span class="fa fa-bars"></span>
                    </button>
                </div>

            </nav>
        </div>
    </div>


<div class="dashboard-head">
<?php 
$segment = Request::segment(1);
?>
@if($segment != 'meeting-join')
    @if(!empty(Auth::user()))
        @if($userData->user_role_id  == NANNY_ROLE_ID  ) 
            @include('front.dashboard.elements.nanny_sidebar')
        @elseif($userData->user_role_id  == SUBSCRIBER_ROLE_ID)
            @include('front.dashboard.elements.customer_sidebar')
        @endif
    @endif
@endif