@extends('front.dashboard.layouts.default')
@section('content')
<div class="main-workspace">
	<div class="rating-block backg-img">
		<div class="container">
			<div class="heading pt-lg-5 pb-3  pt-3">
				<h4>My Plan</h4>
			</div>
			<div class="pricing-table mb-5">
				<div class="plan-details">
					<div class="plans">
						<div class=" pb-2">
							<div class="">
								<label> <strong>Selected Plan :</strong> </label>
								<span> {{isset($planDeatil->name) ? $planDeatil->name:''}} </span>
							</div>
							<div class="">
								<label><strong>Price :</strong> </label>
								<span class=" text-center">{{ '$'.number_format($planDeatil->price, 2) }}
									<span class="h6 t">/Per Month</span>
								</span>
							</div>
							<?php  /* if(!empty($coupenData)){ 
                        <p style="color:green;">Coupen Code Applied( {{ $coupenData->coupon_code }} )</p>
                          } */ ?>
						</div>
						{!! $planDeatil->description !!}
					</div>
				</div>
			</div>


			<div class="row">
				@if(!empty($customer))
				@foreach($customer as $key=>$customer)
				<?php $checked = ''; ?>
				@if($key == 0)
				<?php $checked = 'checked'; ?>
				@endif
				<div class="col-md-4 mb-4">
					<div class="card paymentCard">
					<input type="radio" name="card_id" value="{{$customer->id }}" class="plan-new select_plan"
								<?php echo $checked; ?>>
						<div class="card-body">
							<h5 class="card-title">Card:{{$customer->brand}}</h5>
							<h6 class="card-subtitle">Country:{{$customer->country}}</h6>
							<h6 class="card-subtitle">Card Number:************{{$customer->last4}}</h6>
							<h6 class="card-subtitle">Expairy:{{$customer->exp_month}} /
								{{$customer->exp_year}}</h6>
						</div>
					</div>
				</div>
				@endforeach
				@endif
				<div class="col-md-4">
					<div class="addNewCard_btn">
						<input type="radio" name="plan" class="select_plan add_new_card" value="1">
						Add New Card
					</div>	
				</div>
			
			</div>


			<?php $final_price  = $planDeatil->price; ?>
			{{ Form::open(array('id' => 'newplan-registration-form', 'class' => 'form validation','data-cc-on-file'=>"false",'data-stripe-publishable-key'=> env('STRIPE_PUBLISHER'))) }}

			<div class="add_new_card_form">
				<h5>Add New card information</h5>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group input-form mw-100 my-2">
							<label>Name on Card</label>
							{{ Form::text('name','' , ['placeholder' => trans("Name on Card"), 'class'=>'form-control', 'id' => 'name']) }}
							<span id="name_error" class="help-inline error"></span>
						</div>
					</div>
					<div class='col-md-6'>
						<div class='form-group input-form mw-100 my-2'>
							<label class='control-label'>Card Number</label>
							{{ Form::text('card-number','' , ['class'=>'form-control card-number','autocomplete'=>'off','size'=>'20', 'id' => 'card_number']) }}
							<span id="card-number_error" class="help-inline error"></span>
						</div>
					</div>
					<div class='col-md-6'>
						<div class='form-group input-form mw-100 my-2 expiration required'>
							<label class='control-label'>Expiration Month</label>
							{{ Form::text('card-expiry-month','' , ['placeholder' => 'MM','class'=>'form-control card-expiry-month', 'size'=>'2', 'id' => 'month']) }}
							<span id="card-expiry-month_error" class="help-inline error"></span>
						</div>
					</div>
					<div class='col-md-3'>
						<div class='form-group input-form mw-100 my-2 expiration required'>
							<label class='control-label'>Expiration Year</label>
							{{ Form::text('card-expiry-year','' , ['placeholder' => 'YYYY','class'=>'form-control card-expiry-year', 'size'=>'4', 'id' => 'year']) }}
							<span id="card-expiry-year_error" class="help-inline error"></span>
						</div>
					</div>
					<div class='col-md-3' id="cvv">
						<div class='form-group input-form mw-100 my-2'>
							<label class='control-label'>CVC</label>
							<div class="position-relative">
								{{ Form::text('cvc','' , ['placeholder' => 'ex. 311','class'=>'form-control card-cvc','autocomplete'=>'off', 'size'=>'4']) }}
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
									width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
										<rect x="0" y="0" width="24" height="24"></rect>
										<rect fill="#000000" opacity="0.3" x="2" y="5" width="20" height="14" rx="2">
										</rect>
										<rect fill="#000000" x="2" y="8" width="20" height="3"></rect>
										<rect fill="#000000" opacity="0.3" x="16" y="14" width="4" height="2" rx="1">
										</rect>
									</g>
								</svg>
							</div>
							<span id="cvc_error" class="help-inline error"></span>
						</div>
					</div>
				</div>
				{{ Form::hidden('stripe_token', '',['class'=>'form-control stripe_token']) }}
				{{ Form::hidden('plan_amount', $final_price, ['id' => 'plan_amount']) }}

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
									<input type="text" name="
							" value="" id="coupen_code" placeholder="Coupon Code">
									<span id="coupen_error" class="help-inline error"></span>
								</div>
								<button class="coupon-sub-btn" id="coupen_check" type="button">Apply Code</button>
								<button class="btn btn-danger ml-4" id="coupon_remove" type="button"
									style="display:none;">Remove</button>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="btn-block pt-3">
				{{ Form::button(trans("Submit"), ['class' => 'btn-theme text-white', 'type' => 'button', 'id' => 'user-register']) }}
				{{ Form::button(trans("Submit"), ['class' => 'btn-theme text-white', 'type' => 'button', 'id' => 'user-register-1']) }}
				<!--<button type="button"  class="btn-theme text-white">Submit</button>   -->
			</div>
			<!-- <div class="btn-block pt-3">
            <button class="btn-theme text-white plansubmit"  type="button">Submit</button> 
         </div> -->

			{{Form::close()}}
		</div>



	</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.5.2/bootbox.min.js"
	integrity="sha512-RdSPYh1WA6BF0RhpisYJVYkOyTzK4HwofJ3Q7ivt/jkpW6Vc8AurL1R+4AUcvn9IwEKAPm/fk7qFZW3OuiUDeg=="
	crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	@if(Auth::user())

<script type="text/javascript">


var $form         = $("#newplan-registration-form");

$(document).ready(function () {

	$('#user-register').click(function (e) {
		var $form = $("#newplan-registration-form"),
			inputSelector = ['input[type=email]', 'input[type=password]',
				'input[type=text]', 'input[type=file]',
				'textarea'
			].join(', '),
			$inputs = $form.find('.required').find(inputSelector),
			$errorMessage = $form.find('div.error'),
			valid = true;
		$errorMessage.addClass('hide');

		$('.has-error').removeClass('has-error');
		$inputs.each(function (i, el) {
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
				if (response.error.code == 'invalid_number') {
					$("#card-number_error").html(response.error.message);
					return false;

				} else if (response.error.code == 'incorrect_number') {
					$("#card-number_error").html(response.error.message);
					return false;

				} else if (response.error.code == 'invalid_cvc') {
					$("#cvc_error").html(response.error.message);
					$("#card-number_error").html('');

					return false;

				} else if (response.error.code == 'invalid_expiry_year') {
					$("#card-expiry-year_error").html(response.error.message);
					$("#card-number_error").html('');
					$("#card-expiry-month_error").html('');

					return false;

				} else if (response.error.code == 'invalid_expiry_month') {
					$("#card-expiry-month_error").html(response.error.message);
					$("#card-number_error").html('');
					$("#cvc_error").html('');
					return false;

				} else if (response.error.code == 'missing_payment_information') {
					show_message(response.error.message, "error");
				} else {
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

			newPlan();

		}
	});
});	


function newPlan() {

	var form = $("#newplan-registration-form").closest("form");
	var formData = new FormData(form[0]);
	var card_id = $(".select_plan:checked").val();
	var planid = {{$planDeatil->id}};
	formData.append('card_id', card_id);
	formData.append('planid', planid);
	$.ajax({
		url: "{{ route('user.mynewplan.submit')}}",
		method: 'post',
		//data: $('#newplan-registration-form').serialize(),
		data: formData,
		contentType: false,
		cache: false,
		processData: false,
		beforeSend: function () {
			$("#loader_img").show();
		},
		success: function (response) {
			$("#loader_img").hide();


			if (response.err) {
				show_message(response.err, "error");

			}
			if (response.success) {
				window.location.href = response.page_redirect;
			} else {

				$('span[id*="_error"]').each(function () {
					var id = $(this).attr('id');

					if (id in response.errors) {

						$("#" + id).html(response.errors[id]);
					} else {
						$("#" + id).html('');
					}
				});


			}


		}
	});
}
$(function () {
 $('#newplan-registration-form').keypress(function (e) { //use form id
		if (e.which == 13) {
			//-- to validate form 
			newPlan();
			return false;
		}
	});

 $('#user-register-1').click(function (e) { //use form id
		
	  newPlan();		
	});

});

$(document).ready(function () {
	$(".add_new_card_form").hide();
	$("#user-register").hide();

	$(".add_new_card_form").hide();
	$(".add_new_card").click(function () {
		$(".add_new_card_form").toggle();
		$("#submitplan").hide();
		$("#user-register").show();
		$("#user-register-1").hide();
		$('.select_plan').prop('checked', false);
		$('.add_new_card').prop('checked', true);
		

	});
	$(".plan-new").click(function () {
		$(".add_new_card_form").hide();
		$("#submitplan").show();
		$('.add_new_card').prop('checked', false);
	});

	
});

$('.plansubmit').click(function () {
	var card_id = $('.card_id').val();
	var planid = {{$planDeatil->id}};
	$.ajax({
		type: "POST",
		url: '{{route("user.plan.submit")}}',
		data: {
			"_token": "{{ csrf_token() }}",
			card_id: card_id,planid:planid
		},
		success: function (data) {
			//window.location.reload();
			console.log(data);
			//show_message(data.message, 'success');

		},
		error: function () {

		}
	});
});

$('#coupen_check').click(function() {
	   $("#coupen_error").html('');
	   var planid = {{ $planDeatil->id }}
	   
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


</script>
@endif
@stop