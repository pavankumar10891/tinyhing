@extends('front.dashboard.layouts.default')
@section('content')


<div class="main-workspace">


    <div class="rating-block backg-img">
        <div class="container">
            <div class="heading pt-lg-5 pb-3  pt-3   ">
                <h4>Star Ratings</h4>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="avg-rating-text text-center">
                        @if(!empty($averageRating))
                        <h3>{{number_format($averageRating,1)}}</h3>                       
                        <span>
                            @for($i = 0; $i < 5; $i++)
                            @if($i >= $averageRating)
                            <i class="far fa-star"></i>
                            @else
                            <i class="fas fa-star"></i>
                            @endif
                            @endfor
                        </span>
                        @else
                        <h3>0</h3>
                        <span>
                            <i class="far fa-star"></i>
                            <i class="far fa-star"></i>
                            <i class="far fa-star"></i>
                            <i class="far fa-star"></i>
                            <i class="far fa-star"></i>
                        </span>

                        @endif
                        <h5>Average Rating</h5>
                    </div>
                </div>
                <div class="col-md-9">
                    <?php 
                    $excellentPercent       = 0;
                    $goodPercent            = 0;
                    $averagePercent         = 0;
                    $belowaveragePercent    = 0;
                    $poorPercent            = 0;

                    if($totalRatingCount > 0){
                       $excellentPercent    =   ($excellentRatingCount/$totalRatingCount)*100;
                       $goodPercent         =   ($goodRatingCount/$totalRatingCount)*100;
                       $averagePercent      =   ($averageRatingCount/$totalRatingCount)*100;
                       $belowaveragePercent =   ($belowaverageRatingCount/$totalRatingCount)*100;
                       $poorPercent         =   ($poorRatingCount/$totalRatingCount)*100;
                   }

                   ?>
                   <div class="progress-bar-block ">
                    <div class="progress-div">
                        <span> excellent</span>
                        <div class="progress">


                            <div class="progress-bar bg-success" role="progressbar" style="width: {{$excellentPercent}}%"
                            aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="progress-div">
                        <span> Good </span>
                        <div class="progress">

                            <div class="progress-bar bg-info" role="progressbar" style="width: {{$goodPercent}}%"
                            aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="progress-div">
                        <span> Average</span>
                        <div class="progress">

                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{$averagePercent}}"
                            aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="progress-div">
                        <span> Below Average</span>
                        <div class="progress">

                            <div class="progress-bar bg-Poor" role="progressbar" style="width: {{$belowaveragePercent}}"
                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="progress-div">
                        <span> Poor</span>
                        <div class="progress">

                            <div class="progress-bar bg-below-average" role="progressbar" style="width: {{$poorPercent}}"
                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="review-sec">
            <div class="heading pt-lg-4 pb-3  pt-3   ">
                <h4>Reviews</h4>
            </div>
            <div class=" reviews pb-lg-4">
                @if($results->isNotEmpty()) 
                @foreach($results as $result)
                <div class="row py-3">
                    <div class="col-auto">
                        <div class="Post-date">
                            Posted on: {{date('d/m/Y',strtotime($result->created_at))}}
                        </div>
                        <h3>{{$result->name}}</h3>

                        <?php
                        $currentDate=date('m-d-Y');
                        $dataDate=date('m-d-Y',strtotime($result->created_at));
                        $currentHour=date('H'); 
                        $dataHour=date('H',strtotime($result->created_at)); 
                        $currentMinute=date('i'); 
                        $dataMinute=date('i',strtotime($result->created_at)); 
                        ?>

                        <div class="Post-date">
                            <?php
                            if($currentDate == $dataDate){
                                if($currentHour - $dataHour >=1){
                                   echo $currentHour - $dataHour." Hrs ago"; 
                               }else{
                                echo $currentMinute - $dataMinute." mins ago";
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="col">
                    <div class="review-block pl-md-3">

                        <span>
                            @for($i = 0; $i < 5; $i++)
                            @if($i >= $result->rating)
                            <i class="far fa-star"></i>
                            @else
                            <i class="fas fa-star"></i>
                            @endif
                            @endfor
                        </span>

                        <div class="text-block ">
                            <p>
                                {{$result->review}}</p>
                            </div>

                        </div>
                    </div>

                </div>
                @endforeach
                @else
                <div class="row py-3">
                    <div class="col-auto">
                        No reviews found.
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="btn-block py-3 " data-toggle="modal" data-target="#exampleModal9">
            <a class="btn-theme text-white ">Give Ratings</a>
        </div>



        <!-- Modal -->
        <div class="modal fade give-feedback show pr-0" id="exampleModal9" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                    <h3>Give Feedback</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {!! Form::open(['method' => 'POST','route' => "reviewRating",'class'=>'validate','id'=>'reviewRatingForm']) !!}
                    {{ csrf_field() }}
                    <div class="text-block text-center">
                      <h2>Author Name</h2>
                      <span>Rate the Provided by {{date("D M j")}} </span>
                  </div>



                  <div class="star-rating">
                    <fieldset>
                      <input type="radio" id="star5" name="rating" value="5" /><label for="star5" title="Outstanding">5 stars</label>
                      <input type="radio" id="star4" name="rating" value="4" /><label for="star4" title="Very Good">4 stars</label>
                      <input type="radio" id="star3" name="rating" value="3" /><label for="star3" title="Good">3 stars</label>
                      <input type="radio" id="star2" name="rating" value="2" /><label for="star2" title="Poor">2 stars</label>
                      <input type="radio" id="star1" name="rating" value="1" /><label for="star1" title="Very Poor">1 star</label>
                  </fieldset>
                  <small class="form-text text-danger1" style="color:#dc3545"></small>

              </div>


              <textarea class="w-100" rows="5" name="review" id="review" placeholder="Review"></textarea>
              <small class="form-text text-danger" style="color:#dc3545"></small>
              <input type="hidden" name="nanny_id" value="{{$nanny_id}}" />
              <input type="hidden" name="user_id" value="{{$user_id}}" />

              <div class="btn-block text-center pt-3">

                <button type="button" class="btn-theme submit_review" onclick="reviewrating();">Submit Review</button>


            </div>
            {{ Form::close() }}
        </div>

    </div>
</div>
</div>
</div>
</div>

</div>


<script>

    function reviewrating(){
        if($.trim($("#review").val()) == "" && $.trim($("#rating").val()) == ""){
            $(".text-danger1").html("This field is required");
            $(".text-danger").html("This field is required");

            return false;
        }
        

        $("#reviewRatingForm").submit();
    }

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
@endsection