
@if(count($lists) > 0)
<div class="row listingnanny">
    @foreach($lists as $listsk=>$listsv)
    
    <div class="col-sm-6 col-lg-4 mb-md-5 mb-4">
        <a href="{{ route('user.nanny.profile', base64_encode($listsv->id)) }}">
        <div class="bg-white mr-md-auto">
            <?php
                if(!empty($listsv->photo_id) && file_exists(USER_IMAGE_ROOT_PATH.$listsv->photo_id)){
                  $image = USER_IMAGE_URL.$listsv->photo_id;   
                }else{
                    $image =  WEBSITE_IMG_URL.'no-female.jpg';
                } 
            ?>
             <div class="img-wall" style="background-image: url({{$image}})">
            <img src="{{WEBSITE_URL.'image.php?width=253px&height=226px&cropratio=3:2&image='.$image}}" class="w-100" alt="">
            </div>
        <div class="text-block">
            <div class="d-flex align-items-center">
                <h3>{{!empty($listsv->name) ? $listsv->name:''}}</h3>
                <div class="rating-block">
                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="star"
                        role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"
                        class="svg-inline--fa fa-star fa-w-18 fa-2x">
                        <path fill="currentColor"
                            d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z"
                            class=""></path>
                    </svg>
                    @if(!empty($listsv->total_rating))
                    {{ round(($listsv->total_rating / $listsv->total_rating_count), 1) }}
                    @else
                     New Member
                    @endif
                </div></div>
                <ul class="">
                    <li> <label>Age:</label><strong>{{  !empty($listsv->age) ? $listsv->age  : 'N/A' }}</strong> </li>
                    <li> <label>City:</label><strong>{{  !empty($listsv->city) ? $listsv->city  : 'N/A' }}</strong> </li>
                    <li> <label> Exp:</label><strong>{{ !empty($listsv->experience) ? $listsv->experience.' Years'  : 'N/A' }} </strong></li>
                </ul>
            <div class="">
                <p>{{ !empty($listsv->description) ? $listsv->description  : '' }}</p>
            </div>
            </div>
            <input type="hidden" name="nanny" class="nanny" value="{{$listsv->id}}">

       <?php   if(!empty(Auth::user())){   ?>
        <div class="btn-block mt-1 text-center">
            <a href="javascript:void(0);" class="btn-theme mw-100 schedule_interview" id="{{$listsv->id}}" >
                Schedule Interview
            </a>
            <?php /*<p></p>
            <a href="javascript:void(0);" class="btn-theme mw-100 book_nanny" id="{{$listsv->id}}" >
                Book Nanny
            </a>*/?>
        </div>
        <?php   }else{  ?>
           <div class="btn-block mt-1 text-center">
            <a href="javascript:void(0);" class="btn-theme mw-100 nanny_interview" id="{{$listsv->id}}" >
                Schedule Interview
            </a>
            
        </div>
      
        <?php   }   ?>

    </div>
    </a>
    </div>
    
    @endforeach
</div>
@else
    No Record Found
@endif