@extends('front.dashboard.layouts.default')
@section('content')
<div class="main-workspace">
	<div class="rating-block backg-img">
		<div class="container">
			<div class="heading pt-lg-5 pb-3  pt-3   ">
				<h4>My Plan</h4>
			</div>
			<div class="pricing-table mb-5">
				<div class="active-plans">
					<div class="row">
						@if(!empty($package))
						<div class="col-md-6">
							<div class="plans-right-border">

								<div class="cards-heading">
									<h5 class="card-title text-uppercase text-center">{{$package->name}}</h5>
									<h6 class="h1  text-center">${{number_format($package->price, 2)}}
										@if($package->no_of_month == 1)
										<span class="h6 ">/Per Month</span>
										@else
										<span class="h6 ">/{{ $package->no_of_month }} Months</span>
										@endif
									</h6>
								</div>
								<div class="py-2 py-md-3">
									<div class="active-plan"><label>Starting Date:</label> <strong>{{date('m/d/Y',
											strtotime($package->plan_start_date))}}</strong></div>
									<div class="active-plan "><label>Ending Date:</label> <strong>{{date('m/d/Y',
											strtotime($package->plan_end_date))}}</strong></div>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="plan-right-block">
								<ul class="list-unstyled mb-4">
									{!! $package->description !!}
								</ul>
							</div>
						</div>
						<div class="btn-block text-right px-5 pb-4 pb-md-0">
							@if($package->status==1)
							<button id="cancel_plan" value="{{  $package->id }}" class="btn btn-red loadMoreBtn">
								Cancel Plan</button>
							@endif
							<!-- <a href="javascript:void(0)" id="{{  $package->id }}" class="btn-theme mw-100 plan_id">Buy now</a>  -->
						</div>

					</div>
				</div>
			</div>
			@else
			<div class="col-md-12 mb-4">
				<p class="text-center">No Active Plan</p>
			</div>
			@endif
		</div>
		<div class="pricing-table backg-img padding-section">
			<div class="container">
				<div class="heading py-lg-5 pb-3  text-center">
					<h4>Choose your Plan</h4>
				</div>
				<div class="row pb-5">
					@if(!empty($pakages))
						@foreach($pakages as $key=>$value)
							@php
							$chanPan = CustomHelper::checkUsertCurretntPlan($value->id);
							@endphp
							@if(Auth::user() && $chanPan == 1)
							<div class="col-md-4 mb-4">
								<div class="card mb-5 mb-lg-0 rounded-lg shadow">
									<div class="card-header">
										<h5 class="card-title text-uppercase text-center">{{$value->name}}</h5>
										<h6 class="h3 text-white text-center">${{number_format($value->price, 2)}}
											<span class="h6 text-white-50">
												/{{$value->no_of_month}} {{ ($value->no_of_month > 1) ? 'Months'
												:'Month'}}</span>
										</h6>
									</div>
									<div class="card-body bg-light rounded-bottom">
										{!! $value->description !!}
									</div>
									<div class="btn-block tab-checkboox">
										<a href="{{ URL('my-newplan/'.base64_encode($value->id)) }}" class="btn-theme mw-100 plan_id">Buy now</a>
										<!-- <input type="radio" name="plan_type" id="plan_type" value="{{  $value->id }}" class="form-control"  >  Buy Now    -->
										<!-- <a href="javascript:void(0)" id="{{  $value->id }}" class="btn-theme mw-100 plan_id">Buy now</a>  -->
									</div>
								</div>
								
							</div>
						@endif
					@endforeach
				@endif
			</div>
		</div>

	</div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

@if(Auth::user())
<script>


	$(document).on('click', '#cancel_plan', function () {
		$id = $(this).attr('value');

		Swal.fire({
			title: "Are you sure?",
			text: "Want to Cancel Plan",
			icon: "warning",
			showCancelButton: true,
			confirmButtonText: "Yes",
			cancelButtonText: "No",
			reverseButtons: true
		}).then(function (result) {
			if (result.value) {
				$("#loader_img").show();
				$elem = $(this);
				$.ajax({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					url: '{{route("user.changeUserPlanStatus")}}',
					data: {
						'id': $id,

					},
					type: "POST",
					success: function (res) {
						if (res.success == 1) {
							$("#loader_img").hide();

							location.reload();
						} else {
							$("#loader_img").hide();
						}
					}
				});
			}
		});

	});

	function selectPlanType(planid) {
		if (planid != '' && planid != undefined) {
			var url = '<?php echo url(' / my - newplan'); ?>' + '?plan=' + planid;
			window.location.href = url;
		} else {
			var mesg = 'Please select a plan to proceed';
			show_message(mesg, 'error');
			return false;
		}
	}


</script>
@endif
@stop