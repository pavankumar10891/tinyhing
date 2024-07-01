@extends('front.dashboard.layouts.default')
@section('content')


<div class="main-workspace mb-4">
	<div class=" backg-img padding-section h-100">
		<div class="container">
			<div class="heading py-lg-5 pb-3  text-center">

				<h4>Payment Settings</h4>
			</div>

			<div class="">


				<div class="theme-table theme-table-responsive">
					<table class="table mb-0">
						<thead>
							<tr>

								<th class="text-center">Brand</th>
								<th class="text-center">Country</th>
								<th class="text-center">Expiry Month</th>
								<th class="text-center">Expiry Year </th>
								<th class="text-center">Type</th>
								<th class="text-center">Card Number</th>
								<th class="text-right">Action</th>
							</tr>
						</thead>
						@if(!empty($customer))
						@foreach($customer as $data)
						<tbody>
							<tr>

								<td data-label="Brand" class="text-center">{{ !empty($data->brand) ? $data->brand:'' }}
								</td>
								<td data-label="Country" class="text-center"> {{ !empty($data->country) ?
									$data->country:'' }}</td>
								<td data-label="Expiry Month" class="text-center">{{ !empty($data->exp_month) ?
									$data->exp_month:'' }}</td>
								<td data-label="Expiry Year" class="text-center">{{ !empty($data->exp_year) ?
									$data->exp_year:'' }}</td>
								<td data-label="Type" class="text-center">{{ !empty($data->funding) ? $data->funding:''
									}}</td>
								<td data-label="Card Number" class="text-center">{{ !empty($data->last4) ?
									'************'.$data->last4:'' }}</td>
								<td data-label="Action" class="text-right">
									<input type="hidden" name="card_id" value="{{$data->id }}" class="card_id">
									<input class=' btn btn-theme' type='button' value='Update' class='updateCard' />
								</td>

							</tr>

						</tbody>
						@endforeach
						@else
						<tbody>
							<tr>
								<td colspan="7" class="text-center">No Active Card.</td>	
							</tr>
						</tbody>
						@endif
					</table>
				</div>
				<!-- <table style="width:100%">
			<thead>
				<th>Brand</th>
				<th>Country</th>
				<th>Expiry Month</th>
				<th>Expiry Year</th>
				<th>Type</th>
				<th>Card Number</th>
			</thead>
			@if(!empty($customer))
			@foreach($customer as $data)
			<tbody>
				<td>{{ !empty($data->brand) ? $data->brand:'' }}</td>
				<td> {{ !empty($data->country) ? $data->country:'' }}</td>
				<td>{{ !empty($data->exp_month) ? $data->exp_month:'' }}</td>
				<td>{{ !empty($data->exp_year) ? $data->exp_year:'' }}</td>
				<td>{{ !empty($data->funding) ? $data->funding:'' }}</td>
				<td>{{ !empty($data->last4) ? '************'.$data->last4:'' }}</td>
				<td>
					<input type="hidden" name="card_id" value="{{$data->id }}" class="card_id">
					<input class='btn btn-primary' type='button' value='Update' id='updateCard'/>
				</td>

			</tbody>
			@endforeach
			@endif
		</table> -->

			</div>

			<div class="py-3 payment-setting-checkbx">
				<!-- 
		Weekly Recurring Schedule -->
				<!-- <input class="" id="styled-checkbox-1" type="checkbox" value="value1" hidden> -->

				{{ Form::checkbox('weekly_recurring',1,(Auth::user()->weekly_recurring == 1 ? true : false),
				array('id'=>'styled-checkbox-1','class'=>'styled-checkbox')) }}

				Weekly Recurring Schedule
				<label for="styled-checkbox-1"></label>

				<!-- 	
		{{ Form::checkbox('weekly_recurring',1,(Auth::user()->weekly_recurring == 1 ? true : false), array('id'=>'weekly_recurring')) }} -->


			</div>
		</div>
	</div>
</div>
<script type="text/javascript">

	$('#styled-checkbox-1').click(function () {
		var statusValue = $('#styled-checkbox-1').is(":checked");

		$.ajax({
			type: "POST",
			url: '{{URL("weekly-recurring-status")}}',
			data: {
				"_token": "{{ csrf_token() }}",
				"status": statusValue
			},
			success: function (data) {
				window.location.reload();
				// show_message(data.message,'success');

			},
			error: function () {
			}
		});


	});

	$('.updateCard').click(function () {
		var card_id = $('.card_id').val();

		$.ajax({
			type: "POST",
			url: '{{URL("update-card")}}',
			data: {
				"_token": "{{ csrf_token() }}",
				card_id: card_id,
			},
			success: function (data) {
				window.location.reload();
				show_message(data.message, 'success');

			},
			error: function () {
			}
		});


	});

</script>



@endsection