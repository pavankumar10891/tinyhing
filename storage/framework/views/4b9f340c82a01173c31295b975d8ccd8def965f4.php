
<?php $__env->startSection('content'); ?>
<div class="main-workspace">


    <div class="container">

        <div class="total-client">
            <div class="client-block">
                <div class="dashboard-heading-head">Dashboard</div>
                <?php if(Auth::user() && Auth::user()->stripe_user_id == ''): ?>
                <div class="alert alert-warning" role="alert">
                  
                  Your account not connected with stripe so payout will not received. Please <a href="<?php echo e(route('user.nanny-payment-setting')); ?>">click here</a> to connect your account.
                </div>
                <?php endif; ?>
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
                                        <div class="num"><?php if($totalNannies > 0): ?> <?php echo e($totalNannies); ?> <?php else: ?> 0 <?php endif; ?></div>
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
                                        <div class="num"><?php echo e(!empty($totalEarnings) ? $totalEarnings : 0); ?> USD</div>
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
                                        <div class="num"><?php if($totalInterviews > 0): ?> <?php echo e($totalInterviews); ?> <?php else: ?> 0 <?php endif; ?></div>
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
                <?php if($notifications->isNotEmpty()): ?>
                <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="row no-gutters pb-4" data-id="<?php echo e($result->id); ?>">
                    <div class="col-auto">
                        <div class="icon-block <?php echo e(($result->read_status==1)?'green':''); ?>">
                            <img src="<?php echo e(($result->read_status==1)?'img/eye1.png':'img/job-seeker.png'); ?>" class="img-fluid">
                        </div>
                    </div>
                    <div class="col pl-3">
                        <div class="text-block">
                            <h4 class="card-title"><?php echo e($result->message); ?></h4>
                            <?php
                            $currentDate=date('m-d-Y');
                            $dataDate=date('m-d-Y',strtotime($result->created_at));
                            $currentHour=date('H'); 
                            $dataHour=date('H',strtotime($result->created_at)); 
                            ?>
                            <?php if($currentDate == $dataDate): ?>
                            <span><?php echo e($currentHour - $dataHour); ?> Hrs ago</span>
                            <?php else: ?>
                            <span><?php echo e(date(Config::get('Reading.date_time_format'),strtotime($result->created_at))); ?></span>
                            <?php endif; ?>

                        </div>
                    </div>

                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                <div class="row no-gutters pb-4">
                    <span>No Notifications Found</span>
                </div>    
                <?php endif; ?>



                <div class="btn-block pt-2">
                    <a href="<?php echo e(route('user.nannyNotificationList')); ?>" class="btn-theme mw-100">
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
          events: <?php echo $bookings; ?>,
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
           url: '<?php echo e(route("user.changeNotificationReadStatus")); ?>',
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('front.dashboard.layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\xampp\htdocs\tinyhugs\resources\views/front/dashboard/nanny_dashboard.blade.php ENDPATH**/ ?>