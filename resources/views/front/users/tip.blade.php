@extends('front.layouts.default')
@section('content')

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
						
					</div>

				</div>

				<div class="plan-details">
					<div class="plans">

						<div class=" pb-2">
							<div class="plan-detail-input">
								{{ Form::open(array('id' => 'tip-form', 'class' => 'form validation','data-cc-on-file'=>"false",'data-stripe-publishable-key'=> env('STRIPE_PUBLISHER'))) }}

								<h5>Enter Card Detail</h5>
								<div class="row">
									<div class="col-md-4">
										<div class="form-group input-form mw-100 my-2">
											<label>Name</label>
											{{ Form::text('name','' , ['placeholder' => trans("Name"), 'class'=>'name form-control']) }}
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
											{{ Form::hidden('stripe_token', '',['class'=>'form-control stripe_token']) }}
											<input type="hidden" name="_token" value="{{ csrf_token() }}" />
											<input type="hidden" name="nanny_id" value="{{ $nanny_id }}" />
											<input type="hidden" name="user_id" value="{{ $user_id }}" />

										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-4">
										<div class="floating-label">

											<label>Amount</label>
											{{ Form::text('amount', '', ['class' => 'form-control  amount']) }}
											<span id="amount_error" class="help-inline error"></span>	
											<br>
											<strong>Note: Minimum amount is $50.</strong>
											<br><br>
											{{ Form::button('Save', ['type' => 'button', 'class' => 'btn btn-primary', 'id' => 'tip-submit']) }}

										</div>
									</div>
								</div>
								{{Form::close()}} 
							</div>      
						</div>  
					</div>
				</div>
			</div>
		</section>
		<script type="text/javascript" src="https://js.stripe.com/v2/"></script>

		<script type="text/javascript">


			var $form         = $("#tip-form");
			console.log($form);
			$('#tip-submit').click(function(e) { 
				var $form         = $("#tip-form"),
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
						console.log(token);
						$('.stripe_token').val(token);

					}

					tip();

				}


			});

			function tip(){
				var amount 	= $('.amount').val();
				var name 	= $('.name').val();
				var number 	= $('.card-number').val();
				var cvc 	= $('.card-cvc').val();
				var month 	= $('.card-expiry-month').val();
				var year 	= $('.card-expiry-year').val();


				var form = $("#tip-form").closest("form");
				console.log(form);
				var formData = new FormData(form[0]);
				$("#loader_img").show();

				$.ajax({
					type: "POST",
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					url: '{{route("user.tip-save")}}',
					data: formData,
					contentType: false,       
					cache: false,             
					processData:false,
					success: function(data) {
						$("#loader_img").hide();
						if(data.success==1){
							window.location.data.page_redirect;

						}else{
							console.log(data);

							$("#loader_img").hide();
								// show_message(data.errors,'error');


								$('span[id*="_error"]').each(function() {
									var id = $(this).attr('id');

									if(id in data.errors) {
										$("#"+id).html(data.errors[id]);
									} else {
										$("#"+id).html('');
									}
								});

							}

				// show_message(data.message,'success');

			},
			error: function() {
			}
		});
				

			};


		</script>



		@endsection
