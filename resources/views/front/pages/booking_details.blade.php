<div class="theme-table theme-table-responsive">
	<table class="table mb-0">
		<thead>
			<tr>
				<th>Date</th>
				<th>Type</th>
				<th>Day</th>
			</tr>
		</thead>
		<tbody>
			@if(!empty($bookingDetails))
			@foreach($bookingDetails as $detail)
			<tr>
				<td data-label="Date">
					{{ date(Config::get("Reading.date_format"),strtotime($detail->booking_date)) }}
				</td>
				<td data-label="Time">
					{{ !empty($detail->from_time) ? date('h:i a', strtotime($detail->from_time)):'' }} - {{ !empty($detail->to_time) ? date('h:i a', strtotime($detail->to_time)) :'' }}
				</td>
				<td data-label="Day">
					{{ $detail->day }}
				</td>
			</tr>
			@endforeach
			@else 
			<tr>
				<td colspan="3" class="text-center">No Record Found.</td>
			</tr>
			@endif
		</tbody>
	</table>
</div>