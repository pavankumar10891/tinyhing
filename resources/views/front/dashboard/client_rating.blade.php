@extends('front.dashboard.layouts.default')
@section('content')
<div class="main-workspace mb-3">


    <div class="rating-block backg-img h-100">
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

                    $excellentPercent   = 0;
                    $goodPercent        = 0;
                    $averagePercent     = 0;
                    $belowaveragePercent= 0;
                    $poorPercent        = 0;

                    if($totalRatingCount > 0){
                        $excellentPercent=($excellentRatingCount/$totalRatingCount)*100;
                        $goodPercent=($goodRatingCount/$totalRatingCount)*100;
                        $averagePercent=($averageRatingCount/$totalRatingCount)*100;
                        $belowaveragePercent=($belowaverageRatingCount/$totalRatingCount)*100;
                        $poorPercent=($poorRatingCount/$totalRatingCount)*100;
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

                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{$averagePercent}}%"
                                aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="progress-div">
                            <span> Below Average</span>
                            <div class="progress">

                                <div class="progress-bar bg-Poor" role="progressbar" style="width: {{$belowaveragePercent}}%"
                                aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="progress-div">
                            <span> Poor</span>
                            <div class="progress">

                                <div class="progress-bar bg-below-average" role="progressbar" style="width: {{$poorPercent}}%"
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
                                  {{$result->review}} </p>
                              </div>

                          </div>
                      </div>

                  </div>
                  <div class="btn-block py-3 "  >
                    <?php  /*<a class="btn-theme text-white giveRating" data-id="{{$result->data_id}}" data-name="{{$result->name}}" data-date="{{date('D,M d',strtotime($result->created_at))}}">Give Ratings</a> */ ?>
                </div>
                @endforeach
                @else
                <div class="row py-3">
                    <div class="col-auto">
                        No reviews found.
                    </div>
                </div>
                @endif
                    <!-- <div class="row py-3">
                        <div class="col-auto">
                            <div class="Post-date">
                                Posted on: 25/10/21
                            </div>
                            <h3>Rob Williamson</h3>

                            <div class="Post-date">
                                14 mins ago
                            </div>
                        </div>
                        <div class="col">
                            <div class="review-block pl-md-3">

                                <span>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                    <i class="far fa-star"></i>
                                    <i class="far fa-star"></i>
                                </span>

                                <div class="text-block ">
                                    <p>
                                        What is Lorem Ipsum Lorem Ipsum is simply dummy text of the printing and
                                        typesetting industry Lorem Ipsum has been the industry's standard dummy text
                                        ever since the 1500s when an unknown printer took a galley of type and scrambled
                                        it to make a type specimen book it has?</p>
                                </div>

                            </div>
                        </div>

                    </div> -->
                </div>
            </div>
            <div class="text-center mt-4">
                @if($results->isNotEmpty())
                <button class="btn btn-theme loadMoreBtn" type="button" onclick="showMore($(this))">
                    Load More
                </button>
                @endif

            </div>

            



            <!-- Modal -->
        <div class="modal fade give-feedback show pr-0" id="exampleModal9" tabindex="-1"
            aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="feedbackForm" data-id="">
                        <div class="modal-header">
                            <h3>Give Feedback</h3>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                            <div class="text-block text-center">
                                <h2 class="clientName">Author Name</h2>
                                <span class="postedDate"> </span>
                            </div>



                            <div class="star-rating">
                                <fieldset>
                                    <input type="radio" id="star5" name="rating" value="5" /><label for="star5"
                                    title="Outstanding">5 stars</label>
                                    <input type="radio" id="star4" name="rating" value="4" /><label for="star4"
                                    title="Very Good">4 stars</label>
                                    <input type="radio" id="star3" name="rating" value="3" /><label for="star3"
                                    title="Good">3 stars</label>
                                    <input type="radio" id="star2" name="rating" value="2" /><label for="star2"
                                    title="Poor">2 stars</label>
                                    <input type="radio" id="star1" name="rating" value="1" /><label for="star1"
                                    title="Very Poor">1 star</label>
                                </fieldset>
                            </div>


                            <textarea class="w-100" rows="5" name="review" placeholder="Review"></textarea>
                            
                            <div class="btn-block text-center pt-3">

                                <input type="submit" href="javascript:void(0);" class="btn-theme" value="Submit Review"/>



                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</div>

<script>
    $(document).on('click','.giveRating',function(){
        $id=$(this).attr('data-id');
        $name=$(this).attr('data-name');
        $date=$(this).attr('data-date');

        $("#feedbackForm").find('.clientName').html($name);
        $("#feedbackForm").attr('data-id',$id);
        $html='Rating Provided by '+$date; 
        $("#feedbackForm").find('.postedDate').html($html);
        $("#feedbackForm")[0].reset();
        $("#exampleModal9").modal('show');
    });

        $("#feedbackForm").on('submit',function(e){
            e.preventDefault();
            var formData= new FormData($("#feedbackForm")[0]);
            formData.append('nanny_id',$(this).attr('data-id'));
            $("#loader_img").show();
            $.ajax({
                headers: {
                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
             },
             url: '{{route("user.clientGiveFeedback")}}',
             data: formData,
             type: "POST",
             contentType: false,
             cache: false,
             processData:false,
             success: function(res) {
                 if(res.success==1){
                    $("#loader_img").hide();
                    $("#exampleModal9").modal('hide');
                    show_message(res.message,'success');
                }else if(res.success==0){
                    $("#loader_img").hide();
                    if(res.errors.rating){
                        show_message(res.errors.rating,'error');
                    }
                    if(res.errors.review){
                        show_message(res.errors.review,'error');
                    }
                }else{
                    $("#loader_img").hide();
                    $("#exampleModal9").modal('hide');
                    show_message(res.message,'error');
                }
            }
        });
    });

    var offset = 1;

    function showMore($elem) {
        $("#loader_img").show();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '{{route("user.clientRatingList")}}',
            data: {'offset': offset},
            type: "POST",
            success: function(res) {
                console.log(res);
                if (res.data != '') {
                    $elem.show();
                    $("#loader_img").hide();
                    $('.reviews').append(res.data);
                    offset++;
                } else {
                    $elem.hide();
                    $("#loader_img").hide();
                }
            }
        });

    }
</script>


@stop