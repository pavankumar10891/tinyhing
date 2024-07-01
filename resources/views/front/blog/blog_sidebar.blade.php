<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
    <div class="sidebar-area sticky">
        <div class="single-sidebar-widget widget_search">
            <h4>Search</h4>

            <form id="blog-form" action="{{ route('user.blog') }}" method="get">
                <input type="text" name="search" id="blog-search" value="{{ isset($_REQUEST['search']) ? $_REQUEST['search']:'' }}" placeholder="Search Here...">
                <input type="hidden" name="category_id" value="" id="category_id" >
                <button type="submit"><i class="fa fa-search"></i></button>
            </form>
        </div>
        <?php $categories  = CustomHelper::getblogCategory(); ?>
        @if(count($categories) > 0)
        <div class="single-sidebar-widget widget_categories">
            <h4>Categories</h4>
            <ul>
               @foreach($categories as $category)
                <li  value="{{ $category->id }}"  id="abc"><a href="javascript:void(0)">{{ $category->code }}</a></li>
                @endforeach
            </ul>
        </div>
        @endif
        <?php $latestPost  = CustomHelper::getLatestBlogPost(); ?>
        @if(count($latestPost) > 0)
        <!-- <div class="single-sidebar-widget widget_recent_entries">
            <h4>Latest Post</h4>
            <ul>
                @foreach ($latestPost as $key => $value)
                <li>
                    <?php $image = isset($blog->banner_image) ? WEBSITE_URL.'image.php?width=80px&image='.BLOG_IMAGE_URL.$blog->banner_image:WEBSITE_IMG_URL.'profile.jpg'
                   // WEBSITE_URL.'image.php?width=490px&height=490px&image='.$whychooseUsHeading->image
                     ?>
                    <div class="alignleft"><img src="{{$image}}" alt=""></div>
                    <div>
                    <a href="#">{{ $value->title }}</a>
                    <time><small>{{ date('M', strtotime($value->created_at)) }}</small>{{ date('d, Y', strtotime($value->created_at)) }}</time>
                    </div>
                </li>
                @endforeach
                
            </ul>
        </div> -->


        <section class="single-sidebar-widget widget_recent_entries banner">
          
            <div class="form-block p-0">
                            <div class="d-flex pb-3 align-items-center">
                                <div class="text-block">
                                   <h4 class="mb-0" style="color:red"> Get a Quote!</h4>
                                   <span> Connect with us</span>
                                    </div>
                                    <!-- <div class="icon-block">
                                        <img src="http://tinyhugspanel.stage02.obdemo.com/img/form-icon.PNG">
                                    </div> -->
                            </div>
                            <form   id="user-quote-form" class = "form">
                              <div class="form-group">
                                <select class="custom-select children" id="children" name="children">
                                    <option value="">Number of children</option>
                                    @for($i=1;$i<=5;$i++)
                                    <option value="{{$i}}">{{$i}}</option>
                                    @endfor
                                  </select> 
                                  <span  class="children_error help-inline error"></span>
                                </div>
                                
                                <div class="form-group">
                            
                                    <select class=" custom-select weeks" name="weeks" id="weeks">
                                      <option value="">Number of Hours per week</option>
                                        @for($i=4;$i<=50;$i++) 
                                        <option value="{{$i}}">{{$i}}</option>
                                        @endfor
                                    </select>
                                    <span  class="weeks_error help-inline error"></span>
                                  </div>
                                  <a href="javascript:void(0);" class="btn-theme getquote" >
                                    SUBMIT NOW
                                   </a>
                                </form>
                            </div>
        </div>
        @endif
    </div>

    <form id="quoteform">
      <input type="hidden" name="children_value" class="children_value" value="">
      <input type="hidden" name="week_value" class="week_value" value=""> 
     </form>
</div>

<script>

$(document).ready(function() {
    $(document).on('click','#abc',function(){
             
              var cat_id = $(this).val();
              var catid   = btoa(cat_id);
              $("#category_id").val(catid) ;
              $("#blog-form").submit(); 
            
            });

    });


</script>
@section('scripts')


@endsection