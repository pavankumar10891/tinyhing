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
                       {{-- <a class="btn-theme text-white giveRating" data-id="{{$result->data_id}}" data-name="{{$result->name}}" data-date="{{date('D,M d',strtotime($result->created_at))}}">Give Ratings</a> --}}
                    </div>
                    @endforeach
                    @endif