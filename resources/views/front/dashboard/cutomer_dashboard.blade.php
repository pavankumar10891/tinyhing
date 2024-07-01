@extends('front.dashboard.layouts.default')
@section('content')
<div class="main-workspace">


    <div class="container">

        <div class="total-client">
            <div class="client-block">
                <div class="dashboard-heading-head">Dashboard</div>
                <div class="row pb-3">

                    <div class="col-md-6">
                        <div class="bg-offwhite client-post px-3">

                            <div class="row align-items-center ">
                                <div class="col-sm-3 text-center text-md-left">
                                    <div class="">
                                        <img src="img/dashboard-1.jpg" alt="">
                                    </div>
                                </div>
                                <div class="col">

                                    <div class="request-text row px-md-2">
                                        <div class="col text-md-right text-lg-left">
                                            <div class="num">{{ $totalNannies }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <h3>Total No. of </br>
                                                Nannies
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">

                        <div class="bg-blue client-post px-3">

                            <div class="row align-items-center ">
                                <div class="col-sm-3 text-center text-md-left">
                                    <div class="">
                                        <img src="img/dashboard-3.jpg" alt="">
                                    </div>
                                </div>
                                <div class="col text-md-right text-lg-left">

                                    <div class="request-text row px-md-2">
                                        <div class="col">
                                            <div class="num">{{ $totalInterviews }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <h3>Scheduled <br />Interviews
                                            </h3>
                                        </div>



                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="calendar-block">
                <div class="dashboard-heading-head">My Calendar</div>
                <div class="row align-items-center pb-3">
                    <div class="col-md-9">
                        <div id='calendar'></div>
                    </div>
                    <div class="col-md-3">
                        <div class="calendar-events">
                            <div class="event-block"><span class="scheduled-block"></span>Scheduled Interviews
                            </div>
                            <div class="event-block"><span class="Booked-block"></span>Booked Nanny</div>

                        </div>
                    </div>
                </div>


            </div>
            <div class="review-block stats-block">
                <div class="row py-4">
                    <div class="col-xl-8 col-md-7">
                        <div class="dashboard-heading-head">My Nannies</div>
                        <div class="row">
                         @if(!empty($myNannies))
                         @foreach($myNannies as $nannie)
                         <div class="col-sm-4">
                            <div class="img-wall text-center">
                                @if(!empty($nannie['photo_id']) && file_exists(USER_IMAGE_ROOT_PATH.$nannie['photo_id']))
                                    <img src="{{!empty($nannie['photo_id']) ? WEBSITE_URL.'image.php?width=153px&width=153px&image='.USER_IMAGE_URL.$nannie['photo_id'] : ''}}">
                                @else
                                    <img src="{{ WEBSITE_URL.'image.php?width=153px&width=153px&image='.WEBSITE_IMG_URL.'no-female.jpg' }}">
                                @endif
                            </div>
                            <div class="text-block text-center">

                                <h3>{{ !empty($nannie['name']) ? $nannie['name']:'' }}</h3>

                                <ul class="">
                                    <li> <label>age:</label><strong>{{ !empty($nannie['age']) ? $nannie['age']:'' }}</strong> </li>
                                    <li> <label> Exp:</label><strong>{{ !empty($nannie['experience']) ? $nannie['experience']:0 }} Years</strong></li>
                                </ul>
                            </div>
                        </div>
                        @endforeach
                        @else
                        <div class="col-sm-12">
                            <p class="text-center">No Record Found.</p>
                        </div>
                        @endif
                        
                    </div>

                </div>
                <div class="col-xl-4 col-md-5">
                    <div class="dashboard-heading-head">Notifications</div>
                    <div class="notification-block">
                        @if($notifications->isNotEmpty())
                        @foreach($notifications as $result)
                        <div class="row no-gutters pb-4" data-id="{{$result->id}}">
                            <div class="col-auto">
                                <div class="icon-block {{($result->read_status==1)?'green':''}}">
                                    <img src="{{($result->read_status==1)?'img/eye1.png':'img/job-seeker.png'}}" class="img-fluid">
                                </div>
                            </div>
                            <div class="col pl-3">
                                <div class="text-block">
                                    <h4 class="card-title">{{$result->message}}</h4>
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
                                    
                                </div>
                            </div>

                        </div>
                        @endforeach
                        @else
                        <div class="row no-gutters pb-4">
                            <span>No Notifications Found</span>
                        </div>    
                        @endif

                        

                        <div class="btn-block pt-2">
                            <a href="{{route('user.clientNotificationList')}}" class="btn-theme mw-100">
                                View all Notifications
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

</div>
<script type="text/javascript">
    $(function () {

        $('#calendar').fullCalendar({
            themeSystem: 'bootstrap4',
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay,listMonth'
            },
            weekNumbers: true,
            eventLimit: true,
            eventRender: function(event, element) {
                if(event.description){
                  $(element).tooltip({title: event.description});             

              }
          },
          events: {!! $bookings !!},
      });

    });

    $(document).on('click','.notification-block .row',function(){
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
                $elem.find('div.icon-block').addClass('green');
                $elem.find('img').attr('src','img/eye1.png');
            }else{
                $("#loader_img").hide();
            }
        }
    });
    });


</script>
@endsection