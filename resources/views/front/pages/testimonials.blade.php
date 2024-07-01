@extends('front.layouts.default')

@section('content')
@section('title', 'Testimonils') 

<section class="testimonials testimonials-sec py-md-5">
        <div class="container pt-5">
            <div class="fix-container">
                <div class="heading text-center py-5">
              
                    <h4>Testimonials</h4>
                   <h3>{{!empty($testimomialsHeading->name) ? $testimomialsHeading->name:''}}</h3>
                    <p>{!! !empty($testimomialsHeading->description) ? $testimomialsHeading->description:'' !!}</p>
                </div>

                <div class="row testimonial">
                    @if(!empty($testimomials))

                     @foreach($testimomials as $key=>$value)
                     @if($key%2 == 0)
                        <div class="row align-items-center mb-md-5 mb-2">
                            <div class="col-md-auto">
                                    <div class="py-2">
                                         <div class="img-wall text-center">
                                            @if(!empty($value->image))
                                            <img src="{{ WEBSITE_URL.'image.php?width=80px&height=80px&image='.$value->image }}" alt="">
                                            @else
                                            <img src="{{WEBSITE_IMG_URL}}no-image.png" alt="">
                                            @endif
                                        </div>
                                                                        
                                        <div class="text-center ">

                                            <h2> {{!empty($value->name) ? $value->name:''}}</h2>
                                            <span> {{!empty($value->designation) ? $value->designation:''}}</span>
                                        </div>
                                    </div>
                            </div>
                            <div class="col">
                                <div class="text-block pr-2">
                                    <div class="text-wall">
                                        <p>{!! !empty($value->description) ? strip_tags($value->description):''  !!}</p>
                                    </div>
                                    <div class="triangleone"></div>
                                </div>
                            </div>                    
                        </div>
                    @else
                    <div class="row align-items-center mb-md-5 mb-2">
                        <div class="col-md-auto order-md-2">
                        <div class="py-2">
                                <div class="img-wall text-center">
                                       @if(!empty($value->image))
                                        <img src="{{ WEBSITE_URL.'image.php?width=80px&height=80px&image='.$value->image }}" alt="">
                                        @else
                                        <img src="{{WEBSITE_IMG_URL}}no-image.png" alt="">
                                        @endif
                                    </div>
                                                                    

                                    <div class="text-center ">

                                        <h2> {{!empty($value->name) ? $value->name:''}}</h2>
                                        <span> {{!empty($value->designation) ? $value->designation:''}}</span>
                                    </div>
                                </div>
                        </div>
                        <div class="col">
                        <div class="text-block pr-2">
                                <div class="text-wall">
                                    <p>{!! !empty($value->description) ? strip_tags($value->description):''  !!}</p>
                                </div>
                                <div class="triangleright"></div>

                          
                            </div>
                        </div>                    
                    </div>
                    @endif
                    @endforeach
                    @endif
                    
                </div>


                
            </div>
            <input type="hidden" value="<?php  echo isset($offset) ? $offset:5; ?>"   id="offset">
                @if(count($testimomials) > 4)
                <div id="remove-row" class="btn-block text-center pb-5 ">
                    <a href="javascript:void(0);" id="btn-more" class="btn-theme">
                      Load More
                    </a>
                </div>
                @endif
        </div>
    </section>
<script>
        $(document).ready(function () {

            $(document).on('click','#btn-more',function(){
             
                var offset = $('#offset').val();
                $("#btn-more").html("Loading....");
                $.ajax({
                    url    : '{{ route("user.loadmortestimonials") }}',
                    method : "POST",
                    data   : {offset:offset },
                    headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                     },
                    success : function (res)
                    {
                      
                        if(res.data != '') 
                        {
                            $('.testimonial').append(res.data);
                            $('#offset').val(res.offset);
                            if(res.list_count <= 4){
                                $('#remove-row').hide();
                              }
                          }
                       else
                        {
                            $('#remove-row').hide();
                        } 
                    }
                });
            });  




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
@section('scripts')

@endsection
