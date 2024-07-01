
<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="<?php echo e(WEBSITE_CSS_URL); ?>jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<div class="main-workspace">
    <div class="container">
        <div class="total-client mainEarningShow">
            <div class="client-block">
                <div class="dashboard-heading-head">My Earnings</div>
            </div>

            <div class="row">
                <div class="col-md-8 order-2 order-md-1">
                    <div class="earning_filter">
                        <div class="theme-input position-relative">
                            <i class="fal fa-calendar-alt inputIcon"></i>
                            <input type="text" name="created_at" class="form-control" placeholder="Search by date" id="created_at" /> 
                        </div>
                    </div>
                    <div class="theme-table theme-table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th class="text-right">Earning</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php if(!empty($earnings)): ?>
                                <?php $__currentLoopData = $earnings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $earning): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td data-label="Date">
                                        <?php echo e(!empty($earning['created_at']) ? date('d/m/Y',strtotime($earning['created_at'])) : ''); ?>

                                    </td>
                                    <td data-label="Type">
                                        <?php if($earning['type'] == 1): ?>
                                        <span class="badge-theme"> Booking</span>
                                        <?php elseif($earning['type'] == 2): ?>
                                        <span class="badge-theme"> Tip</span>
                                        <?php else: ?>
                                        <?php endif; ?>
                                    </td>
                                    <td data-label="Earning" class="text-right font-600">
                                        $<?php echo e(!empty($earning['amount']) ? $earning['amount']:0); ?>

                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center">No Record Found.</td>
                                    </tr>
                                <?php endif; ?>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-4 order-1 order-md-2">
                    <div class="totalearn_box">
                        <h4>Total Earnings</h4>

                        <h5>$<?php echo e(!empty($totalEarnings) ? $totalEarnings : 0); ?></h5>

                       <!--  <a href="javascript:void(0);" class="btn btn-theme">
                            Withdraw Now
                        </a> -->
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>


    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>


    <script>

        $('#created_at').datepicker({

        onSelect: function (selectedDate) {
            $("#loader_img").show();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '<?php echo e(route("user.nannyEarningListSearch")); ?>',
                data: {date:selectedDate},
                type: "get",
                success: function(res) {
                    console.log(res);
                    if (res != '') {
                     $("#loader_img").hide();

                     $('.mainEarningShow').html('');
                     $('.mainEarningShow').html(res);


                 } else {
                    $("#loader_img").hide();

                    $html =
                    ' <div class="col-md-12"><div class="bg-white block-inner "><span class="text-center">No Earnings Found</span></div></div>';
                    $('.mainEarningShow').html($html);

                }
            }
        });
        }
    });
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
    <script>
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
                events: 'https://fullcalendar.io/demo-events.json'
            });

        });
    </script>
    <script>
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('front.dashboard.layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\xampp\htdocs\tinyhugs\resources\views/front/dashboard/nanny_myearning.blade.php ENDPATH**/ ?>