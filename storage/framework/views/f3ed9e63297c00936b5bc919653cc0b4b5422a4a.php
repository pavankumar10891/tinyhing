
<?php $__env->startSection('content'); ?>

<div class="cont-section backg-img padding-section">
    <div class="container">
  
    <div class="search-block contact-area">

        <div class="heading pb-lg-4 pb-4 text-center">

            <h3>Get In Touch</h3>
        <p>Need any help jusct send a message via our email address</p>
        </div>
        <div class="contact-form-area">
        
              
                <div class="row">
                    <div class="col-md-8 col-lg-8 col-sm-12 col-xs-12">
                        <div class="contact-form">
                          
                            <?php echo e(Form::open(array('id' => 'contact-form', 'class' => 'form'))); ?>

                                <div class="row">
                                    <div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                     <?php echo e(Form::text('name', '', ['placeholder' => 'Name..', 'class' => 'form-control'])); ?>

                                        <span id="name_error" class="help-inline error"></span>
                                          </div>
                                    </div>
                                    <div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                        <?php echo e(Form::text('email','', ['placeholder' => 'Email.', 'class' => 'form-control'])); ?>

                                        <span id="email_error" class="help-inline error"></span>
                                         </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                                        <div class="form-group" >
                                        <?php echo e(Form::text('subject','', ['placeholder' => 'Subject..', 'class' => 'form-control'])); ?>

                                        <span id="subject_error" class="help-inline error"></span>
                                        </div>
                                
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                                        <div class="form-group" >
                                        <?php echo e(Form::textarea('message', '', ['placeholder' => 'Your Message Here...', 'class' => 'form-control'])); ?>

                                        <span id="message_error" class="help-inline error"></span>
                                          </div>
                                     
                                    </div>
                                </div>
                              
                                <div class="row">
                                    <div class="col-md-4 col-lg-4 col-sm-4 col-xs-4">
                                        <?php if(config('services.recaptcha.key')): ?>
                                            <div class="g-recaptcha"
                                                data-sitekey="<?php echo e(config('services.recaptcha.key')); ?>">
                                            </div>
                                        <?php endif; ?>
                                        <span id="g-recaptcha-response_error" class="help-inline error"></span>
                                        
                                    </div>
                                </div>
                             
                                <div class="row">
                                    <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12 pt-2">
                                        <div class="form-group">
                                        <?php echo e(Form::button('Send', ['type' => 'button', 'class' => 'btn-theme', 'id' => 'contact-us'])); ?>

                                          </div>
                                    </div>
                                </div>
                                <?php echo e(Form::close()); ?>

                        </div>
                    </div>
                    <div class="col-md-4 col-lg-4 col-sm-12 col-xs-12">
                        <div class="contact-image">
                            <div class="contact-address">
                                <h4>Address</h4>
                                <p><label>Phone: </label><?php echo e(Config::get('Contact.phone_number')); ?></p>
                                <p><label>Email: </label><a href="mailto:<?php echo e(Config::get('Contact.email')); ?>"><?php echo e(Config::get('Contact.email')); ?></a></p>
                                <address>
                                    <label>   Office  :</label>   <?php echo e(Config::get('Contact.address')); ?>

                                </address>
                            
                   
                                <h4>Follow Us </h4>
                                <ul class="list-unstyled social_link_bar ">
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
                        </div>
                    </div>
                 
                </div>
          
                <div class="contact-img">
                    <img src="img/line.png" class="line-img ab-img">
                    <img src="img/triangle.png" class="triangle-img ab-img">
                    <img src="img/line1.png" class="line1-img ab-img">
                    <img src="img/close.png" class="close-img ab-img">
                </div>
          </div>
       </div>
    </div>
 </div>
<script src='https://www.google.com/recaptcha/api.js'></script>
<script type="text/javascript">
 
    $(document).ready(function() {
        $('#contact-us').click(function() {
            $.ajax({
                url: "<?php echo e(route('user.contact.send')); ?>",
                method: 'post',
                data: $('#contact-form').serialize(),
                beforeSend: function() {
                    $("#loader_img").show();
                },
                success: function(response){
                    $("#loader_img").hide();
                
                    if(response.success) {
                      //console.log(response.);
                        //window.location.href="<?php echo e(url('/')); ?>";
                       location.reload();
                    } else {

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
    });
</script>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('front.layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\tinyhugs\resources\views/front/pages/conatct_us.blade.php ENDPATH**/ ?>