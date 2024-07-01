@extends('front.dashboard.layouts.default')
@section('content')
<div class="main-workspace">


    <div class="container">

        <div class="total-client">
            <div class="client-block">
                <div class="dashboard-heading-head">Dashboard</div>
                @if(Auth::user() && Auth::user()->stripe_user_id == '')
                <div class="alert alert-warning" role="alert">
                  
                  To receive automatic payout to your bank as soon as an order is complete, please <a href="{{ route('user.nanny-payment-setting') }}">click here</a> to connect to Stripe.
                </div>
                @endif
                <div class="row pb-3">

                    <div class="col-md-4">
                        <div class="bg-offwhite d-flex client-post">

                            <div class="row align-items-center ">
                                <div class="col">
                                    <div class="">
                                        <img src="img/dashboard-1.jpg" alt="">
                                    </div>
                                </div>
                                <div class="col-auto">

                                    <div class="request-text px-md-2">
                                        <div class="num">@if($totalNannies > 0) {{ $totalNannies}} @else 0 @endif</div>
                                        <h3>Total No. of <br /> Clients
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">

                        <div class="bg-green d-flex client-post">

                            <div class="row align-items-center ">
                                <div class="col">
                                    <div class="">
                                        <img src="img/dashboard-2.jpg" alt="">
                                    </div>
                                </div>
                                <div class="col-auto">

                                    <div class="request-text px-md-2">
                                        <div class="num">{{ !empty($totalEarnings) ? $totalEarnings : 0 }} USD</div>
                                        <h3> Total <br /> Earnings
                                        </h3>



                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">

                        <div class="bg-blue d-flex client-post">

                            <div class="row align-items-center ">
                                <div class="col">
                                    <div class="">
                                        <img src="img/dashboard-3.jpg" alt="">
                                    </div>
                                </div>
                                <div class="col-auto">

                                    <div class="request-text px-md-2">
                                        <div class="num">@if($totalInterviews > 0) {{ $totalInterviews}} @else 0 @endif</div>
                                        <h3>Scheduled <br />Interviews
                                        </h3>



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
                        <div class="event-block"><span class="scheduled-block"></span>Scheduled Interviews</div>
                        <div class="event-block"><span class="available-block"></span>Holiday Dates</div>
                        <div class="event-block"><span class="confirmed-block"></span>Confirmed Bookings</div>

                    </div>
                </div>
            </div>


        </div>
        <div class="stats-block">
            <div class="row py-4">
                <div class="col-xl-8 col-md-7">
                    <div class="dashboard-heading-head">Stats</div>
                    <div class=" chart-dot">
                      <div class="row">
                          <div class="col-auto">
                           <strong>   Earning</strong>
                       </div>
                       <div class="col text-right">
                          <div class="d-inline-flex align-items-center">
                              <div class="pr-2">
                                  <span class="blue"></span>Top
                              </div>
                              <div class="pr-2">
                                  <span class="blue-light"></span>Current Month
                              </div>
                          </div>
                      </div>
                  </div>
                  
                  <div id="chart"></div>
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
                    <a href="{{route('user.nannyNotificationList')}}" class="btn-theme mw-100">
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

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
        var options = {
            series: [{
                name: 'Top',
                data: [31, 40, 28, 51, 42, 109, 100]
            }, {
                name: 'Current Month',
                data: [11, 32, 45, 32, 34, 52, 41]
            }],
            chart: {
                height: 350,
                type: 'area'
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth'
            },
            xaxis: {
                type: 'datetime',
                categories: ["2018-09-19T00:00:00.000Z", "2018-09-19T01:30:00.000Z", "2018-09-19T02:30:00.000Z",
                    "2018-09-19T03:30:00.000Z", "2018-09-19T04:30:00.000Z", "2018-09-19T05:30:00.000Z",
                    "2018-09-19T06:30:00.000Z"
                ]
            },
            tooltip: {
                x: {
                    format: 'dd/MM/yy HH:mm'
                },
            },
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();
    </script>
@endsection