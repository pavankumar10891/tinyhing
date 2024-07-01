

<?php $__env->startSection('content'); ?>
<?php $__env->startSection('title', 'Generate Password'); ?>

<section class="login pb-5">
    <div class="container">
        <div class="login-form">
            <div class="row pb-md-5 pb-3 flex-row">
                <div class="col">
                    <div class="heading">
                        <h4 style="text-align:center;font-weight: bold;">Generate New Password</h4>
                        
                   </div>
                    </div>
                  
            </div>
            <div class="form-bx">
   
                <form class="form" id="user-passreset-form">
                    <input name="_token" type="hidden" value="<?php echo e(csrf_token()); ?>"/>
                       <input type="hidden" name="validate_string" value="<?php echo e($userDetail->validate_string); ?>">
                       <input type="hidden" name="user_id" value="<?php echo e($userDetail->id); ?>">
            
                <div class="forgot-form">
               
                    <div class="form-group">
                        <?php echo e(Form::password('new_password', ['class' => 'form-control form-cs', 'placeholder' => 'New Password'])); ?>

                       <span id="new_password_error" class="help-inline error"></span>
                     </div>

                     <div class="form-group">
                          <?php echo e(Form::password('new_password_confirmation', ['class' => 'form-control form-cs', 'placeholder' => 'Confirm Password'])); ?>

                       <span id="new_password_confirmation_error" class="help-inline error"></span>	
                     </div>
                                     
                    </div>
                <div class="btn-block">
                    <?php echo e(Form::button('Submit', ['type' => 'button', 'class' => 'btn-theme', 'id' => 'user-resetpass'])); ?>

                    
                    </div>
              
            <?php echo e(Form::close()); ?>

        </div>
    </div>
    <div class="login-img">
        <img src="<?php echo e(WEBSITE_IMG_URL); ?>line.png" class="line-img ab-img">
        <img src="<?php echo e(WEBSITE_IMG_URL); ?>triangle.png" class="triangle-img ab-img">
          <img src="<?php echo e(WEBSITE_IMG_URL); ?>line1.png" class="line1-img ab-img">
        <img src="<?php echo e(WEBSITE_IMG_URL); ?>close.png" class="close-img ab-img">
        </div>
    </div>
</section>


<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<script type="text/javascript">
  $(function() {
        $('#user-passreset-form').keypress(function(e) { //use form id
            if (e.which == 13) {
               //-- to validate form 
              $.ajax({
                url: "<?php echo e(url('generate-password/')); ?>",
                method: 'post',
                data: $('#user-passreset-form').serialize(),
                beforeSend: function() {
                    $("#loader_img").show();
                },
                success: function(response){
                    $("#loader_img").hide();
                
                    if(response.success) {
                      //console.log(response.);
                        //window.location.href="<?php echo e(url('/')); ?>";
                        window.location.href=response.page_redirect;
                       //location.reload();
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
                return false;
            }
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#user-resetpass').click(function() {
            $.ajax({
                url: "<?php echo e(url('generate-password/')); ?>",
                method: 'post',
                data: $('#user-passreset-form').serialize(),
                beforeSend: function() {
                    $("#loader_img").show();
                },
                success: function(response){
                    $("#loader_img").hide();
                
                    if(response.success ==true) {
                      //console.log(response.);
                        //window.location.href="<?php echo e(url('/')); ?>";
                        window.location.href=response.page_redirect;
                       //location.reload();
                    }else 
                   /*  if( response.success == false){
                        window.location.href=response.page_redirect;

                    } else  */
                    {
                       
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




<?php echo $__env->make('front.layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\xampp\htdocs\tinyhugs\resources\views/front/users/generate_password.blade.php ENDPATH**/ ?>