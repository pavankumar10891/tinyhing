<?php $newsletterHeading = CustomHelper::get_newsletter_heading();  ?>
<section class="counter-sec newletter_wrap">
    <div class="container">
        <div class=" newletter_inner">
            <div class="pr-3">
                <h2> <?php echo !empty($newsletterHeading->description) ? $newsletterHeading->description:''; ?></h2>
                <h5><?php echo e(!empty($newsletterHeading->name) ? $newsletterHeading->name:''); ?></h5>
            </div>

          
          
            <div class="newletter-input-wrap">
                <div class="newletter-input">
                   <input name="_token" type="hidden" value="<?php echo e(csrf_token()); ?>"/>
                    <?php echo e(Form::text('email','', [ 'id' =>'newletter_email', 'class' => ''.($errors->has('email') ? 'is-invalid':'') , 'placeholder'=>'Enter Your Email Id'])); ?>  
                    <span id="email_error" class="help-inline error"></span>
                </div>
                    
                <?php echo e(Form::button('Subscribe Now', ['type' => 'button', 'class' => 'newletter-sub-btn', 'id' => 'newsletter'])); ?>

               
            </div>
            
          <!--  <div class="newletter-input-wrap">
                <div class="newletter-input">
                    <input type="text" name="email" id="" placeholder="Enter Your Email Id"> 
                   
                </div>
               
               <button class="newletter-sub-btn ">Subscribe Now</button> 
              
            </div>   -->
        </div>
    </div>
</section>

<footer class="footer_wrapper">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="navbar-footer text-center pb-md-4">
                   <?php echo $__env->make('front.footer_links.footer_links', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
        <ul class="list-unstyled social_link_bar py-md-4 ">
            <li>
                <a href="<?php echo e(Config::get('Social.facebook_url')); ?>" target="_blank" class="facebook-bx">
                    <i class="fab fa-facebook-f"></i>
                </a>
            </li>
            <li>
                <a href="<?php echo e(Config::get('Social.youtube_url')); ?>" target="_blank" class="youtube-bx">
                    <i class="fab fa-youtube"></i>
                </a>
            </li>
            <li>
                <a href="<?php echo e(Config::get('Social.twitter_url')); ?>" target="_blank" class="twitter-bx">

                    <i class="fab fa-twitter"></i>
                </a>
            </li>

        </ul>
    </div>

    <div class="container">
        <div class="copyright">
            <span>
                <a href="<?php echo e(url('/')); ?>"> tinyhugs.com</a> © <?php echo date("Y");?>. All Rights Reserved </span>
        </div>
    </div>


    </div>
</footer>





<a href="javascript:void(0);" class="back_top">
    <i class="ion-ios-arrow-thin-up"></i>
</a>

<script type="text/javascript">
    $(document).ready(function() {
        $('#newsletter').click(function() {
            var newsletteremail = $('#newletter_email').val();
           
            $.ajax({
                url: "<?php echo e(url('newsletter/')); ?>",
                method: 'post',
                data: { email : newsletteremail } ,
                headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                beforeSend: function() {
                    $("#loader_img").show();
                },
                success: function(response){
                    $("#loader_img").hide();
                
                    if(response.success) {
                      //console.log(response.);
                        window.location.href="<?php echo e(url('/')); ?>";
                        // window.location.href=response.page_redirect;
                       //location.reload();
                    }else {

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
        });


       $('#zipcode_search').click(function() {
            var zipcode = $('#zipcode_val').val();
           
            window.location.href="<?php echo e(url('/our-nannies/?zipcode=')); ?>"+zipcode;
           // location.reload();

        });

    });
        
</script><?php /**PATH C:\xampp\htdocs\tinyhugs\resources\views/front/elements/footer.blade.php ENDPATH**/ ?>