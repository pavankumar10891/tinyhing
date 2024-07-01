<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
    <div class="sidebar-area sticky">
        <div class="single-sidebar-widget widget_search">
            <h4>Search</h4>

            <form id="blog-form" action="<?php echo e(route('user.blog')); ?>" method="get">
                <input type="text" name="search" id="blog-search" value="<?php echo e(isset($_REQUEST['search']) ? $_REQUEST['search']:''); ?>" placeholder="Search Here...">
                <input type="hidden" name="category_id" value="" id="category_id" >
                <button type="submit"><i class="fa fa-search"></i></button>
            </form>
        </div>
        <?php $categories  = CustomHelper::getblogCategory(); ?>
        <?php if(count($categories) > 0): ?>
        <div class="single-sidebar-widget widget_categories">
            <h4>Categories</h4>
            <ul>
               <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li  value="<?php echo e($category->id); ?>"  id="abc"><a href="javascript:void(0)"><?php echo e($category->code); ?></a></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>
        <?php $latestPost  = CustomHelper::getLatestBlogPost(); ?>
        <?php if(count($latestPost) > 0): ?>
        <!-- <div class="single-sidebar-widget widget_recent_entries">
            <h4>Latest Post</h4>
            <ul>
                <?php $__currentLoopData = $latestPost; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li>
                    <?php $image = isset($blog->banner_image) ? WEBSITE_URL.'image.php?width=80px&image='.BLOG_IMAGE_URL.$blog->banner_image:WEBSITE_IMG_URL.'profile.jpg'
                   // WEBSITE_URL.'image.php?width=490px&height=490px&image='.$whychooseUsHeading->image
                     ?>
                    <div class="alignleft"><img src="<?php echo e($image); ?>" alt=""></div>
                    <div>
                    <a href="#"><?php echo e($value->title); ?></a>
                    <time><small><?php echo e(date('M', strtotime($value->created_at))); ?></small><?php echo e(date('d, Y', strtotime($value->created_at))); ?></time>
                    </div>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                
            </ul>
        </div> -->


        <section class="single-sidebar-widget widget_recent_entries banner">
          
            <div class="form-block p-0">
                            <div class="d-flex pb-3 align-items-center">
                                <div class="text-block">
                                   <h4 class="mb-0"> Get a Quote!</h4>
                                   <span> Connect with us</span>
                                    </div>
                                    <!-- <div class="icon-block">
                                        <img src="http://tinyhugspanel.stage02.obdemo.com/img/form-icon.PNG">
                                    </div> -->
                            </div>
                            <form   id="user-quote-form" class = "form">
                              <div class="form-group">
                                <select class="custom-select" id="children" name="children">
                                    <option value="">Number of children</option>
                                    <?php for($i=1;$i<=5;$i++): ?>
                                    <option value="<?php echo e($i); ?>"><?php echo e($i); ?></option>
                                    <?php endfor; ?>
                                  </select> 
                                  <span  class="children_error help-inline error"></span>
                                </div>
                                
                                <div class="form-group">
                            
                                    <select class=" custom-select" name="weeks" id="weeks">
                                      <option value="">Number of Hours per week</option>
                                        <?php for($i=1;$i<=10;$i++): ?>
                                        <option value="<?php echo e($i); ?>"><?php echo e($i); ?></option>
                                        <?php endfor; ?>
                                    </select>
                                    <span  class="weeks_error help-inline error"></span>
                                  </div>
                                  <a href="javascript:void(0);" class="btn-theme " id="getquote">
                                    SUBMIT NOW
                                   </a>
                                </form>
                            </div>
        </div>
        <?php endif; ?>
    </div>

    <form id="quoteform">
      <input type="hidden" name="children_value" id="children_value" value="">
      <input type="hidden" name="week_value" id="week_value" value=""> 
     </form>
</div>

<div class="modal fade price-moodel" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog  modal-dialog-centered" role="document">
    <div class="modal-content">
      <!-- <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div> -->
        <div class="modal-body">
        <div class="text-center py-4">
        <div class="price-block">    Estimated price: <span class="h5 text-center estimateprice">$100.00
        <span class="h6">
        \per week</span>
        <a  data-toggle="tooltip" data-placement="bottom" title="Tooltip on bottom">
        <svg aria-hidden="true" focusable="false" data-prefix="fal" data-icon="info-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-info-circle fa-w-16 fa-2x"><path fill="currentColor" d="M256 40c118.621 0 216 96.075 216 216 0 119.291-96.61 216-216 216-119.244 0-216-96.562-216-216 0-119.203 96.602-216 216-216m0-32C119.043 8 8 119.083 8 256c0 136.997 111.043 248 248 248s248-111.003 248-248C504 119.083 392.957 8 256 8zm-36 344h12V232h-12c-6.627 0-12-5.373-12-12v-8c0-6.627 5.373-12 12-12h48c6.627 0 12 5.373 12 12v140h12c6.627 0 12 5.373 12 12v8c0 6.627-5.373 12-12 12h-72c-6.627 0-12-5.373-12-12v-8c0-6.627 5.373-12 12-12zm36-240c-17.673 0-32 14.327-32 32s14.327 32 32 32 32-14.327 32-32-14.327-32-32-32z" class=""></path></svg>
        </a>
        </span></div>


        <div class="btn-block mt-4">
        <a href="<?php echo e(route('user.pricing')); ?>" class="btn-theme">
        Proceed Now
        </a>
        </div>
        </div>
        </div>
    
    </div>
  </div>
</div>

<script type="text/javascript">


  $(function() {
        $('#user-quote-form').keypress(function(e) { //use form id
            if (e.which == 13) {
              login();
            }
        });
    });
    function quote(){
    
    var child = $("#children_value").val();
    var week = $("#week_value").val();
      $.ajax({
            url: "<?php echo e(route('user.quote')); ?>",
            method: 'post',
            data: { children: child , weeks :  week },
            headers: {
             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
            beforeSend: function() {
                $("#loader_img").show();
            },
            success: function(response){
                $("#loader_img").hide();
            
                if(response.success == true) {
                    $('.estimateprice').html(response.data.price);
                    $('#exampleModal').modal('show');

                   // window.location.href=response.page_redirect;
                   //location.reload();
                }else if(response.success == 2){
                  show_message(response.message,'error');
                }else if(response.success == false){
                   $('.children_error').html(response.errors.children_error);
                   $('.weeks_error').html(response.errors.children_error);
                  //location.reload();
                }  else {

                    $('span[id*="_error"]').each(function() {
                        var id = $(this).attr('id');

                        if(id in response.errors) {
                            $("#"+id).html(response.errors[id]);
                        } else {
                            $("#"+id).html('');
                        }
                    });
                }
            }
        });
    }
</script>
<script>

$(document).ready(function() {
    $(document).on('click','#abc',function(){
             
              var cat_id = $(this).val();
              var catid   = btoa(cat_id);
              $("#category_id").val(catid) ;
              $("#blog-form").submit(); 
            
            });

            $('#getquote').click(function() {
            quote();
        });

        $('#children').change(function() {
            var chldval= $(this).val();
            $("#children_value").val(chldval);
        });

        $('#weeks').change(function() {
            var weekdval= $(this).val();
           $("#week_value").val(weekdval);
       });

    });


</script>
<?php $__env->startSection('scripts'); ?>


<?php $__env->stopSection(); ?><?php /**PATH F:\xampp\htdocs\tinyhugs\resources\views/front/blog/blog_sidebar.blade.php ENDPATH**/ ?>