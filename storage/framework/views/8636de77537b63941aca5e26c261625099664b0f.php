
<?php $__env->startSection('content'); ?>
<div class="blog-list  backg-img padding-section">
    <div class="container">
    
        <div class=" list-block  listing padding-bottom">
          
                <div class="heading py-lg-5 pb-3  text-center">

                    <h3>Blog</h3>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="row blogs">
                            <?php if(count($blogs) > 0): ?>
                                <?php $__currentLoopData = $blogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $blog): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-12">
                                <div class=" mr-md-auto">
                                    <div class="row align-items-center no-gutters bg-light-white">
                                        <div class="col-md-4">
                                    <?php $image = isset($blog->banner_image) ? BLOG_IMAGE_URL.$blog->banner_image:WEBSITE_IMG_URL.'listing-img.jpg' ?>
                                    <div class="img-wall no-round" style="background-image: url(<?php echo e($image); ?>)">
                                        <img src="<?php echo e($image); ?>" class="w-100" alt="">
                                    </div>
                                </div>
                                <div class="col-md-8 pl-4">
                                    <div class="text-block">
                                            <h3><?php echo e(isset($blog->title) ? $blog->title:''); ?></h3>
                                       
                                        <div class="">
                                            <p><?php echo isset($blog->description) ? $blog->description:''; ?></p>
                                        </div>
                                        <div class="btn-block mt-3 ">
                                        <a href="<?php echo e(route('user.blogdetail', $blog->slug)); ?>" 
                                            class="btn-theme ">
                                            Read More
                                        </a>
                                    </div>
                                    </div>
                                    
                                 
                                </div>  </div>    </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                             <h3>No Blog Found</h3>
                            <?php endif; ?>                            
                            
                        </div>
                        <input type="hidden" value="<?php  echo isset($offset) ? $offset:7; ?>"   id="offset">
                            <?php if(count($blogs) > 5): ?>
                            <div id="remove-row" class="btn-block text-center pb-5" >
                                <a href="javascript:void(0);" id="btn-more" class="btn-theme">
                                  Load More
                                </a>
                            </div>
                             <?php endif; ?>
                        </div>
                    


                     <!-- Right Sidebar -->
                <?php echo $__env->make('front.blog.blog_sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
              </div>

            </div>
    </div>
</div>
<script>
        $(document).ready(function () {

            $(document).on('click','#btn-more',function(){
             
                var offset = $('#offset').val();
                var search =  $('#blog-search').val();  
                var category_id  =  $('#category_id').val() ; 
                $("#btn-more").html("Loading....");
                $.ajax({
                    url    : '<?php echo e(route("user.loadmoreblog")); ?>',
                    method : "POST",
                    data   : {offset:offset , search:search , category_id:category_id  },
                    headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                     },
                    success : function (res)
                    {
                      
                        if(res.data != '') 
                        {
                            $('.blogs').append(res.data);
                            $('#offset').val(res.offset);
                            if(res.list_count <= 5){
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
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>


<?php $__env->stopSection(); ?>
<?php echo $__env->make('front.layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\xampp\htdocs\tinyhugs\resources\views/front/blog/blog.blade.php ENDPATH**/ ?>