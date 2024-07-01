@extends('front.layouts.default')
@section('content')
@if(!empty($aboutUs))
<section class="about-us padding-section">
        <div class="container">
            <div class="heading py-lg-5 pb-3  text-center">

                <h4>{{!empty($aboutUs->name) ? $aboutUs->name:''}}</h4>

            </div>
            <div class="row align-items-center pb-lg-5">
                    <div class="col-md-12 text-center">
                        <div class="paragraph-block">
                        <p>{!! !empty($aboutUs->description) ? $aboutUs->description:'' !!}</p>
                         </div>
                    </div>
            </div>
        </div>
    </section>    
    @endif

    @if(!empty($testimomials))
    <section class="aboutus testimonials-sec py-md-5">
        <div class="container pt-5">
            <div class="fix-container">
                <div class="heading text-center pb-5">
                    <h5>Testimonials</h5>
                    <h3>{{!empty($testimomialsHeading->name) ? $testimomialsHeading->name:''}}</h3>
                    <p>{!! !empty($testimomialsHeading->description) ? $testimomialsHeading->description:'' !!}</p>
                </div>


                <div class="row">
                     @foreach($testimomials as $testimomial)
                    
                    <div class="col-md-6">
                        
                        <div class="text-block pr-2">
                            <div class="text-wall">
                                <p>{!! !empty($testimomial->description) ? strip_tags($testimomial->description):''  !!}</p>
                            </div>
                            <div class="triangle"></div>

                            <div class="d-flex align-items-center py-4">
                                @if(!empty($testimomial->image))
                                <div class="img-wall  pl-4">
                                    <img src="{{ WEBSITE_URL.'image.php?width=80px&height=80px&image='.$testimomial->image }}" alt="">
                                </div>
                                @else
                                <div class="img-wall  pl-4">
                                    <img src="{{WEBSITE_IMG_URL}}no-image.png" class="img-fluid" width="80" height="80">
                                </div>
                                @endif
                                

                                <div class="pl-3">

                                    <h2> {{!empty($testimomial->name) ? $testimomial->name:''}}</h2>
                                    <span> {{!empty($testimomial->designation) ? $testimomial->designation:''}}</span>
                                </div>



                            </div>
                        </div>
                    </div>
                    @endforeach


                    <div class="btn-block mb-5 my-md-5 text-center">
                        <a href="{{route('user.testimonials')}}" class="btn-theme">
                            View all Testimonials
                        </a>
                    </div>
                    <div class="testimonials-img">
                        <img src="{{WEBSITE_IMG_URL}}balloon.PNG" class="ab-img balloon">
                        <img src="{{WEBSITE_IMG_URL}}balloon.PNG" class="ab-img balloon-left">
                        <img src="{{WEBSITE_IMG_URL}}balloon.PNG" class="ab-img balloon-right">
                        <img src="{{WEBSITE_IMG_URL}}cart.png" class="ab-img cart">
                        <img src="{{WEBSITE_IMG_URL}}butterfly.png" class="ab-img butterfly">
                        <img src="{{WEBSITE_IMG_URL}}test-1.png" class="ab-img ribbon">
                    </div>

                </div>
            </div>
        </div>
    </section>
    @endif
@endsection