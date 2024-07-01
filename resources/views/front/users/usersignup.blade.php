@extends('front.layouts.default')

@section('content')
@section('title', 'User Sign Up') 

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
						<div class="create-account">Already have an account, <a href="{{ route('user.login') }}">
						Log In </a> 
					</div>
				</div>

			</div>

			<?php 
				$final_price = 0;
				$plan_price  = (isset($planinfo->price))?  $planinfo->price : 0  ;
				

				?>

			<div class="plan-details">
				<div class="plans">

					<div class=" pb-2">
						<div class="">
							<label >
								<strong>Selected Plan :</strong>  </label>
								<span > {{isset($planinfo->name) ? $planinfo->name:''}} </span> 
								
							</div>
							<div class="">
								<label><strong>Price :</strong>  </label>
								<span class=" text-center">{{ '$'.number_format($plan_price, 2) }}
									<span class="h6 t">
										/Per Month</span>
									</span>
								</div>
								<?php  /* if(!empty($coupenData)){ 
									<p style="color:green;">Coupen Code Applied( {{ $coupenData->coupon_code }} )</p>
								   } */ ?>
							</div>

							{!! $planinfo->description !!}

						</div>
					</div>
					<?php $userData =  Session::get('user_data'); ?>
					<div class="plan-detail-input">
						{{ Form::open(array('id' => 'user-registration-form', 'class' => 'form validation','data-cc-on-file'=>"false",'data-stripe-publishable-key'=> env('STRIPE_PUBLISHER'))) }}
						<input type="hidden" name="provider_id" value="{{isset($userData['provider_id']) ?$userData['provider_id']:''}}">
						<input type="hidden" name="image" value="{{isset($userData['image']) ?$userData['image']:''}}">
						<div class="row">
							<div class="col-md-4">
								<div class="form-group input-form mw-100 my-2">
									<label>Name</label>
									{{ Form::text('first_name',isset($userData['name']) ?$userData['name']:''  , ['placeholder' => trans("Name"), 'class'=>'form-control']) }}
									<span id="first_name_error" class="help-inline error"></span>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group input-form mw-100 my-2">
									<label>Email</label>
									{{ Form::text('email',isset($userData['email']) ?$userData['email']:'' , ['placeholder' => trans("Email"), 'class'=>'form-control']) }}
									<span id="email_error" class="help-inline error"></span>

								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group input-form mw-100 my-2">
									<label>Phone Number (Optional)</label>
									{{ Form::text('phone_number','', ['placeholder' => trans("Phone Number"), 'class'=>'form-control']) }}
									<span id="phone_number_error" class="help-inline error"></span>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group input-form mw-100 my-2">
									<label>Referral Code (Optional)</label>
									{{ Form::text('referral_code','', ['placeholder' => trans("Referral Code "), 'class'=>'form-control']) }}
									<span id="referral_code_error" class="help-inline error"></span>
								</div>
							</div>
						</div>   
						<div class="row">
							<div class="col-md-12">
			                    <div class="coupon-code w-100">
			                        <div class=" coupon-inner">
			                            <div class="pr-3">
			                                <h2> Add Coupon Code</h2>

			                            </div>
			                            <div class="coupon-input-wrap">
			                                <div class="coupon-input">
			                                    <input type="text" name="coupen_code" value="" id="coupen_code" placeholder="Coupon Code">
			                                    <span id="coupen_error" class="help-inline error"></span>
			                                </div>
			                                <button class="coupon-sub-btn" id="coupen_check" type="button">Apply Code</button>
			                                <button class="btn btn-danger ml-4" id="coupon_remove"  type="button" style="display:none;">Remove</button>
			                            </div>
			                        </div>
			                    </div>
			                </div>
			            </div>
			            <div class="row">
			            	<div class="col-md-12">
								<div class="planCodeBox">

									<div class="row mb-2">
										<div class="col-6">
											Plan:
										</div>
										<div class="col-6 text-right">
											{{isset($planinfo->name) ? $planinfo->name:''}}
										</div>
									</div>

									<div class="row mb-2">
										<div class="col-6">
											Price:
										</div>
										<div class="col-6 text-right">
											{{ '$'.number_format($plan_price, 2) }}
										</div>
									</div>
										<div class="mb-3" style="color:green;" id="coupen_code_applied"> </div>
									<div class="row mb-2">
										<div class="col-6">
										Total Amount:
										</div>
										<div class="col-6 text-right">
										{{ '$'.number_format($plan_price, 2) }}
										</div>
									</div>
								</div>
			            	</div>
			            </div>
		                &nbsp;&nbsp;&nbsp;
						<h5>Enter your card information</h5>
						<div class="row">
							<div class="col-md-4">
								<div class="form-group input-form mw-100 my-2">
									<label>Name on Card</label>
									{{ Form::text('name','' , ['placeholder' => trans("Name on Card"), 'class'=>'form-control']) }}
									<span id="name_error" class="help-inline error"></span>
								</div>
							</div>
							<div class='col-md-4'>
								<div class='form-group input-form mw-100 my-2'>
									<label class='control-label'>Card Number</label>
									{{ Form::text('card-number','' , ['class'=>'form-control card-number','autocomplete'=>'off','size'=>'20']) }}
									<span id="card-number_error" class="help-inline error"></span>

								</div>
							</div>

							<div class='col-md-4'>
								<div class='form-group input-form mw-100 my-2'>
									<label class='control-label'>CVC</label> 


									{{ Form::text('cvc','' , ['placeholder' => 'ex. 311','class'=>'form-control card-cvc','autocomplete'=>'off', 'size'=>'4']) }}
									<span id="cvc_error" class="help-inline error"></span>

								</div>
							</div>
							<div class='col-md-4'>

								<div class='form-group input-form mw-100 my-2 expiration required'>
									<label class='control-label'>Expiration Month</label> 


									{{ Form::text('card-expiry-month','' , ['placeholder' => 'MM','class'=>'form-control card-expiry-month', 'size'=>'2']) }}
									<span id="card-expiry-month_error" class="help-inline error"></span>

								</div>
							</div>
							<div class='col-md-4'>

								<div class='form-group input-form mw-100 my-2 expiration required'>
									<label class='control-label'>Expiration Year</label> 

									{{ Form::text('card-expiry-year','' , ['placeholder' => 'YYYY','class'=>'form-control card-expiry-year', 'size'=>'4']) }}
									<span id="card-expiry-year_error" class="help-inline error"></span>

								</div>
							</div>
						</div> 
						{{ Form::hidden('stripe_token', '',['class'=>'form-control stripe_token']) }}
						{{ Form::hidden('plan_amount', $final_price, ['id' => 'plan_amount']) }}

						<div class="btn-block pt-3"> 
							{{ Form::button(trans("Submit"), ['class' => 'btn-theme text-white', 'type' => 'button', 'id' => 'user-register']) }}
							<!--<button type="button"  class="btn-theme text-white">Submit</button>   -->
						</div> 
						<div class="login-img">
							<img src="{{WEBSITE_IMG_URL}}line.png" class="line-img ab-img">
							<img src="{{WEBSITE_IMG_URL}}triangle.png" class="triangle-img ab-img"> 
							<img src="{{WEBSITE_IMG_URL}}line1.png" class="line1-img ab-img">
							<img src="{{WEBSITE_IMG_URL}}close.png" class="close-img ab-img"> 
						</div>
						{{Form::close()}} 
					</div>      
				</div>  
			</div>
		</div>
	</div>
</section>

@endsection
@section('scripts')
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.5.2/bootbox.min.js" integrity="sha512-RdSPYh1WA6BF0RhpisYJVYkOyTzK4HwofJ3Q7ivt/jkpW6Vc8AurL1R+4AUcvn9IwEKAPm/fk7qFZW3OuiUDeg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
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
				if(response.error.code == 'invalid_number') {
					$("#card-number_error").html(response.error.message);
					return false;

				} 
				else if(response.error.code == 'incorrect_number') {
					$("#card-number_error").html(response.error.message);
					return false;
					
				}
				else if(response.error.code == 'invalid_cvc') {
					$("#cvc_error").html(response.error.message);
					$("#card-number_error").html('');

					return false;
					
				}
				else if(response.error.code == 'invalid_expiry_year') {
					$("#card-expiry-year_error").html(response.error.message);
					$("#card-number_error").html('');
					$("#card-expiry-month_error").html('');
					
					return false;

				}
				else if(response.error.code == 'invalid_expiry_month') {
					$("#card-expiry-month_error").html(response.error.message);
					$("#card-number_error").html('');
					$("#cvc_error").html('');
					return false;

				}
				else if(response.error.code == 'missing_payment_information'){
					show_message(response.error.message, "error"); 
				}
				else{
					show_message(response.error.message, "error"); 
					return false;
				}
			} else {
				var token = response['id'];
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
		var formData = new FormData(form[0]);
		$.ajax({
			url: "{{ route('user.user.signup')}}",
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
                url: "{{ route('user.user.signup')}}",
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

		$('#coupen_check').click(function() {
                $("#coupen_error").html('');
                var planid = {{ $planinfo->id }}
                if(planid !='' &&  planid !=undefined){
                    var coupenCode = $("#coupen_code").val();
                    if(coupenCode==''){
                        $("#coupen_error").html('Please add coupen code');              
                    }else{

                        $.ajax({
                            url: "{{ route('user.checkCoupenCode')}}",
                            method: 'post',
                            data: { coupen_code: coupenCode, planid:planid},
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            beforeSend: function() {
                                $("#loader_img").show();
                            },
                            success: function(response){
                                $("#loader_img").hide();
                                if(response.success == true) {
                                	
                                	//coupen_code_applied
									
                                $('#coupen_code_applied').html('Coupen Code Applied $' + response.data.coupon_code);	
                                $('#total_plan_amount').html('Total Amount $' + response.final_price);
                                $('#plan_amount').val(response.final_price);
                                
                                 var codeid = btoa(response.data.id);
                                 $("#coupen_code_id").html(coupen_code); 

                                 show_message(response.mesg , 'success');
                                 $('#coupen_code').attr('readonly', true);
                                 $("#coupen_check").hide();
                                 $("#coupon_remove").show();


                             } else{
                               $("#coupen_error").html(response.mesg);
                           }
                       }
                   });
                    }
                }else{
                  var mesg = 'Please select a plan to proceed';
                  show_message(mesg , 'error');
                  return false;
              }

          });

		$('#coupon_remove').click(function() {
			   bootbox.confirm({
			    message: "Are you sure want to remove coupan?",
			    buttons: {
			        confirm: {
			            label: 'Yes',
			            className: 'btn-success'
			        },
			        cancel: {
			            label: 'No',
			            className: 'btn-danger'
			        }
			    },
			    callback: function (result) {
			    	if(result == true){
			    		$('#coupen_code_id').val('');
		                $('#coupen_code').attr('readonly', false).val('');
		                $("#coupen_check").show();
		                $("#coupon_remove").hide();
		                var mesg = 'Coupon code removed';
		                show_message(mesg , 'success');		
			    	}
			        //console.log('This was logged in the callback: ' + result);
			    
			    }
			});
                

            });
	});
</script>

@endsection
