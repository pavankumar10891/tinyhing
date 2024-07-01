

<?php $__env->startSection('content'); ?>
<?php $__env->startSection('title', 'User Sign Up'); ?> 

<section class="login pb-5">
	<div class="container">
		<div class="">
			<div class="login-form signup-form plan-detail-form">
				<div class="row pb-5 flex-row">
					<div class="col">
						<div class="heading">
							<!-- <h5>Looking for care?</h5> -->
							<h3>Hello, User</h3>
						</div>
					</div>
					<div class="col-md-auto">
						<div class="create-account">Already have an account, <a href="<?php echo e(route('user.login')); ?>">
						Log In </a> 
					</div>
				</div>

			</div>

			<div class="plan-details">
				<div class="plans">

					<div class=" pb-2">
						<div class="">
							<label >
								<strong>Selected Plan :</strong>  </label>
								<span > <?php echo e(isset($planinfo->name) ? $planinfo->name:''); ?> </span> 
								<?php 
								$final_price = 0;
								$plan_price  = (isset($planinfo->price))?  $planinfo->price : 0  ;

								if(!empty($coupenData)){

									if($coupenData->coupon_type =='fixed_amount'){

										if($coupenData->amount> $plan_price ){
											$final_price = 0;
										}else{
											$final_price =  $plan_price - $coupenData->amount ; 
										}


									}else{

										$percentAmmount =  $plan_price*$coupenData->amount/100 ; 
										if($percentAmmount >  $plan_price   ){
											$final_price = 0;
										}else{
											$final_price = $plan_price - $plan_price*$coupenData->amount/100 ; 
										}

									}

								}else{

									$final_price =  $plan_price ;
								}

								?>
							</div>
							<div class="">
								<label><strong>Price :</strong>  </label>
								<span class=" text-center"><?php echo e('$'.number_format($final_price, 2)); ?>

									<span class="h6 t">
										/<?php echo e($planinfo->no_of_month); ?>  <?php echo e(($planinfo->no_of_month > 1) ? 'Months' :'Month'); ?></span>
									</span>
								</div>
								<?php  if(!empty($coupenData)){  ?>
									<p style="color:green;">Coupen Code Applied( <?php echo e($coupenData->coupon_code); ?> )</p>
								<?php   }  ?>
							</div>

									<?php echo $planinfo->description; ?>


								</div>
							</div>
							<div class="plan-detail-input">
								<?php echo e(Form::open(array('id' => 'user-registration-form', 'class' => 'form validation','data-cc-on-file'=>"false",'data-stripe-publishable-key'=> env('STRIPE_PUBLISHER')))); ?>

								<div class="row">
									<div class="col-md-4">
										<div class="form-group input-form mw-100 my-2">
											<label>Name</label>
											<?php echo e(Form::text('first_name','' , ['placeholder' => trans("Name"), 'class'=>'form-control'])); ?>

											<span id="first_name_error" class="help-inline error"></span>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group input-form mw-100 my-2">
											<label>Email</label>
											<?php echo e(Form::text('email','' , ['placeholder' => trans("Email"), 'class'=>'form-control'])); ?>

											<span id="email_error" class="help-inline error"></span>

										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group input-form mw-100 my-2">
											<label>Phone Number (Optional)</label>
											<?php echo e(Form::text('phone_number','', ['placeholder' => trans("Phone Number"), 'class'=>'form-control'])); ?>

											<span id="phone_number_error" class="help-inline error"></span>
										</div>
									</div>
								</div>   
								&nbsp;&nbsp;&nbsp;
								<h5>Enter Card Detail</h5>
								<div class="row">
									<div class="col-md-4">
										<div class="form-group input-form mw-100 my-2">
											<label>Name</label>
											<?php echo e(Form::text('name','' , ['placeholder' => trans("Name"), 'class'=>'form-control'])); ?>

											<span id="first_name_error" class="help-inline error"></span>
										</div>
									</div>
									<div class='col-md-4'>
										<div class='form-group input-form mw-100 my-2'>
											<label class='control-label'>Card Number</label>
											<?php echo e(Form::text('card-number','' , ['class'=>'form-control card-number','autocomplete'=>'off','size'=>'20'])); ?>

											<span id="message" class="help-inline error"></span>

										</div>
									</div>

									<div class='col-md-4'>
										<div class='form-group input-form mw-100 my-2'>
											<label class='control-label'>CVC</label> 
											

											<?php echo e(Form::text('cvc','' , ['placeholder' => 'ex. 311','class'=>'form-control card-cvc','autocomplete'=>'off', 'size'=>'4'])); ?>

											<span id="cvc" class="help-inline error"></span>

										</div>
									</div>
									<div class='col-md-4'>

										<div class='form-group input-form mw-100 my-2 expiration required'>
											<label class='control-label'>Expiration Month</label> 
											

											<?php echo e(Form::text('card-expiry-month','' , ['placeholder' => 'MM','class'=>'form-control card-expiry-month', 'size'=>'2'])); ?>

											<span id="card-expiry-month" class="help-inline error"></span>

										</div>
									</div>
									<div class='col-md-4'>

										<div class='form-group input-form mw-100 my-2 expiration required'>
											<label class='control-label'>Expiration Year</label> 

											<?php echo e(Form::text('card-expiry-year','' , ['placeholder' => 'YYYY','class'=>'form-control card-expiry-year', 'size'=>'4'])); ?>

											<span id="card-expiry-year" class="help-inline error"></span>

										</div>
									</div>
								</div> 
								<?php echo e(Form::hidden('stripe_token', '',['class'=>'form-control stripe_token'])); ?>


								<div class="btn-block pt-3"> 
									<?php echo e(Form::button(trans("Submit"), ['class' => 'btn-theme text-white', 'type' => 'button', 'id' => 'user-register'])); ?>

									<!--<button type="button"  class="btn-theme text-white">Submit</button>   -->
								</div> 
								<div class="login-img">
									<img src="<?php echo e(WEBSITE_IMG_URL); ?>line.png" class="line-img ab-img">
									<img src="<?php echo e(WEBSITE_IMG_URL); ?>triangle.png" class="triangle-img ab-img"> 
									<img src="<?php echo e(WEBSITE_IMG_URL); ?>line1.png" class="line1-img ab-img">
									<img src="<?php echo e(WEBSITE_IMG_URL); ?>close.png" class="close-img ab-img"> 
								</div>
								<?php echo e(Form::close()); ?> 
							</div>      
						</div>  
					</div>
				</div>
			</div>
		</section>

		<?php $__env->stopSection(); ?>
		<?php $__env->startSection('scripts'); ?>
		<script type="text/javascript" src="https://js.stripe.com/v2/"></script>

		<script type="text/javascript">

			
			var $form         = $("#user-registration-form");
			$('#user-register').click(function(e) { 
				var $form         = $("#user-registration-form"),
				inputSelector = ['input[type=email]', 'input[type=password]',
				'input[type=text]', 'input[type=file]',
				'textarea'].join(', '),
				$inputs       = $form.find('.required').find(inputSelector),
				$errorMessage = $form.find('div.error'),
				valid         = true;
				$errorMessage.addClass('hide');

				$('.has-error').removeClass('has-error');
				$inputs.each(function(i, el) {
					var $input = $(el);
					if ($input.val() === '') {
						$input.parent().addClass('has-error');
						$errorMessage.removeClass('hide');
						e.preventDefault();
					}
				});

				if (!$form.data('cc-on-file')) {
					e.preventDefault();
					Stripe.setPublishableKey($form.data('stripe-publishable-key'));
					Stripe.createToken({
						number: $('.card-number').val(),
						cvc: $('.card-cvc').val(),
						exp_month: $('.card-expiry-month').val(),
						exp_year: $('.card-expiry-year').val()
					}, stripeResponseHandler);
				}


				function stripeResponseHandler(status, response) {
					if (response.error) {
						$('.error')
						.removeClass('hide')
						.find('.alert')
						.text(response.error.message);
					} else {
						var token = response['id'];
						console.log(token);
						$('.stripe_token').val(token);
					// $form.find('input[type=text]').empty();
					// $form.append("<input type='hidden' name='stripe_token' value='" + token + "'/>");
					// $form.get(0).submit();
				}

				signUp();

			}


		});

			



			

			

			function signUp(){

				var form = $("#user-registration-form").closest("form");
				console.log(form);
				var formData = new FormData(form[0]);
				$.ajax({
					url: "<?php echo e(route('user.user.signup')); ?>",
					method: 'post',
                //data: $('#user-registration-form').serialize(),
                data: formData,
                contentType: false,       
                cache: false,             
                processData:false, 
                beforeSend: function() {
                	$("#loader_img").show();
                },
                success: function(response){
                	$("#loader_img").hide();

                	
                	if(response.err){
                		show_message(response.err, "error");

                	}
                	if(response.success) {
                		window.location.href=response.page_redirect;
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
			}

/*   function signUp(){
    var form = $("#user-registration-form").closest("form");
    var formData = new FormData(form[0]);
    $.ajax({
                url: "<?php echo e(route('user.user.signup')); ?>",
                method: 'post',
                //data: $('#user-registration-form').serialize(),
                data: formData,
                contentType: false,       
                cache: false,             
                processData:false, 
                beforeSend: function() {
                    $("#loader_img").show();
                },
                success: function(response){
                    $("#loader_img").hide();
                
                    if(response.success == true) {
                      alert('sdfsdfdfdsf');
                        window.location.href=response.page_redirect;
                      
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
        } */
        $(function() {
        $('#user-registration-form').keypress(function(e) { //use form id
        	if (e.which == 13) {
               //-- to validate form 
               signUp();
               return false;
           }
       });
    });
</script>
<script>
	$(document).ready(function () {

		$('#user-register').click(function() {

			// signUp(); 
		});
	});
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('front.layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\xampp\htdocs\tinyhugs\resources\views/front/users/usersignup.blade.php ENDPATH**/ ?>