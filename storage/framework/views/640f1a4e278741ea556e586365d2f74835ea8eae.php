</div>
<footer class="footer_wrapper dashboard-footer">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="navbar-footer text-center pb-lg-4">
                        <?php echo $__env->make('front.footer_links.footer_links', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </div>
                </div>

            </div>

            <ul class="list-unstyled social_link_bar py-lg-4 ">
                <li>
                    <a href="<?php echo e(Config::get('Social.facebook_url')); ?>" class="facebook-bx">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                </li>
                <li>
                    <a href="<?php echo e(Config::get('Social.youtube_url')); ?>" class="youtube-bx">
                        <i class="fab fa-youtube"></i>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0);" class="twitter-bx">

                        <i class="fab fa-twitter"></i>
                    </a>
                </li>

            </ul>
        </div>

        <div class="container">
            <div class="copyright">
                <span>
                    <a href="javascript:void(0);"> tinyhugs.com</a> © 2021. All Rights Reserved </span>
            </div>
        </div>


        </div>
    </footer>


    <a href="javascript:void(0);" class="back_top">
        <i class="ion-ios-arrow-thin-up"></i>
    </a>


    <!-- Optional JavaScript -->
    <script type="text/javascript" src="<?php echo e(WEBSITE_JS_URL); ?>front1/moment.min.js"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.0/dist/jquery.min.js"></script> -->

  
    <script type="text/javascript" src="<?php echo e(WEBSITE_JS_URL); ?>front1/popper.min.js"></script>
    <script type="text/javascript" src="<?php echo e(WEBSITE_JS_URL); ?>front1/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script type="text/javascript" src="<?php echo e(WEBSITE_JS_URL); ?>front1/fullcalendar.min.js"></script>
    <script type="text/javascript" src="<?php echo e(WEBSITE_JS_URL); ?>front1/owl.carousel.min.js"></script>
    <script type="text/javascript" src="<?php echo e(WEBSITE_JS_URL); ?>front1/wow.js"></script>


    <!-- Custom JavaScript -->
    <script type="text/javascript" src="<?php echo e(WEBSITE_JS_URL); ?>front1/script.js"></script>



    <!-- <script>
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
    </script> -->
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

<?php /**PATH C:\xampp\htdocs\tinyhugs\resources\views/front/dashboard/elements/dashboard_footer.blade.php ENDPATH**/ ?>