@extends('front.dashboard.layouts.default')
@section('content')

    <div class="main-workspace">
        <div class="container">
            <div class="total-client">
                <div class="client-block">
                    <div class="dashboard-heading-head">Notifications</div>
                </div>

                <div class="notification_box">
                    <ul class="notificationBox_list list-unstyled mb-0">
                    @if($results->isNotEmpty())
                        @foreach($results as $result)
                        <li data-id="{{$result->id}}">
                            <a href="javascript:void(0);" class="{{($result->read_status==0?'unread':'')}}">
                                <div class="notiwall">
                                    @if($result->read_status==0)
                                    <svg aria-hidden="true" focusable="false" data-prefix="fas"
                                        data-icon="calendar-alt" role="img" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 448 512" class="svg-inline--fa fa-calendar-alt fa-w-14 fa-2x">
                                        <path fill="currentColor"
                                            d="M0 464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V192H0v272zm320-196c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12h-40c-6.6 0-12-5.4-12-12v-40zm0 128c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12h-40c-6.6 0-12-5.4-12-12v-40zM192 268c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12h-40c-6.6 0-12-5.4-12-12v-40zm0 128c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12h-40c-6.6 0-12-5.4-12-12v-40zM64 268c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12H76c-6.6 0-12-5.4-12-12v-40zm0 128c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12H76c-6.6 0-12-5.4-12-12v-40zM400 64h-48V16c0-8.8-7.2-16-16-16h-32c-8.8 0-16 7.2-16 16v48H160V16c0-8.8-7.2-16-16-16h-32c-8.8 0-16 7.2-16 16v48H48C21.5 64 0 85.5 0 112v48h448v-48c0-26.5-21.5-48-48-48z"
                                            class=""></path>
                                    </svg>
                                    @else
                                    <svg aria-hidden="true" focusable="false" data-prefix="fas"
                                        data-icon="check-square" role="img" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 448 512" class="svg-inline--fa fa-check-square fa-w-14 fa-2x">
                                        <path fill="currentColor"
                                            d="M400 480H48c-26.51 0-48-21.49-48-48V80c0-26.51 21.49-48 48-48h352c26.51 0 48 21.49 48 48v352c0 26.51-21.49 48-48 48zm-204.686-98.059l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.248-16.379-6.249-22.628 0L184 302.745l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.25 16.379 6.25 22.628.001z"
                                            class=""></path>
                                    </svg>
                                    @endif
                                </div>

                                <p class="notidesc">{{$result->message}}
                                <?php
                                    $currentDate=date('m-d-Y');
                                    $dataDate=date('m-d-Y',strtotime($result->created_at));
                                    $currentHour=date('H'); 
                                    $dataHour=date('H',strtotime($result->created_at)); 
                                ?>
                                @if($currentDate == $dataDate)
                                    <span>{{$currentHour - $dataHour}} Hrs ago</span>
                                @else
                                <span>{{date(Config::get('Reading.date_time_format'),strtotime($result->created_at))}}</span>
                                @endif
                                </p>

                            </a>
                        </li>
                        @endforeach
                    @endif
                        
                    </ul>
                </div>

                <div class="text-center mt-4">
               
                @if($results->isNotEmpty())
                <button class="btn btn-theme loadMoreBtn" type="button" onclick="showMore($(this))">
                        Load More
                    </button>
                    <button class="btn btn-theme loadMoreSpinner" type="button" style="display:none">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Loading...
                    </button>
                    @else
                    <span class='text-center'>No Notifications Found </span>
                @endif
                </div>

            </div>
        </div>

    </div>







<script>

var offset = 1;
function showMore($elem){
        $elem.hide();
 		$(".loadMoreSpinner").show();
 		$.ajax({
 			headers: {
 				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
 			},
 			url: '{{route("user.nannyNotificationList")}}',
 			data: {
 				'offset': offset
 			},
 			type: "POST",
 			success: function(res) {
 				if(res!=''){
                    $elem.show();
 		            $(".loadMoreSpinner").hide();
 					$('.notificationBox_list').append(res);
 					offset++;
 				}else{
                    $elem.hide();
 		            $(".loadMoreSpinner").hide();
 				}
 			}
 		});

 	}
     $(document).on('click','.notificationBox_list li',function(){
        $("#loader_img").show();
        $elem=$(this);
        $id = $(this).attr('data-id');
        $.ajax({
 			headers: {
 				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
 			},
 			url: '{{route("user.changeNotificationReadStatus")}}',
 			data: {
 				'id': $id
 			},
 			type: "POST",
 			success: function(res) {
 				if(res.success==1){
                    $("#loader_img").hide();
                    $elem.find('a').removeClass('unread');
                    $html  =  '<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="check-square" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-inline--fa fa-check-square fa-w-14 fa-2x"><path fill="currentColor" d="M400 480H48c-26.51 0-48-21.49-48-48V80c0-26.51 21.49-48 48-48h352c26.51 0 48 21.49 48 48v352c0 26.51-21.49 48-48 48zm-204.686-98.059l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.248-16.379-6.249-22.628 0L184 302.745l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.25 16.379 6.25 22.628.001z" class=""></path></svg>';
                    $elem.find('svg').replaceWith($html);
 				}else{
                    $("#loader_img").hide();
                 }
 			}
 		});
     });
$(document).ready(function () {
    $(".partner-owl").owlCarousel({
        nav: false,
        dots: false,
        loop: false,
        autoplay: true,
        autoplayTimeout: 2000,
        autoplayHoverPause: true,
        stagePadding: 0,
        margin: 0,
        items: 6,
        navText: ["<i class='fa fa-chevron-left'></i>", "<i class='fa fa-chevron-right'></i>"],
        responsive: {
            0: {
                items: 2
            },
            575: {
                items: 3
            },
            767: {
                items: 4
            },
            991: {
                items: 6
            }
        }
    });
});
</script>
@stop