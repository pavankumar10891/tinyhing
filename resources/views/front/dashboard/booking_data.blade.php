@if($results->isNotEmpty())
@foreach($results as $result)
<div class="col-md-12">
	<div class="bg-white block-inner ">
		<div class="col">

			<div class="text-block ">
				<div class="d-flex align-items-center">
					<span>
						@if(!empty($result->photo_id) && file_exists(USER_IMAGE_ROOT_PATH.$result->photo_id))
						<img src="{{ WEBSITE_URL.'image.php?width=148&height=148&cropratio=3:3&image='.USER_IMAGE_URL.$result->photo_id }}" height="148" width="148">
						@else
						<img src="{{ WEBSITE_URL.'image.php?width=148&height=148&cropratio=3:3&image='.WEBSITE_IMG_URL.'no-image.png' }}" alt="">
						@endif

					</span>
					<div class="pl-4">
						<h2> {{$result->name}}</h2>
						<div class="date">
							{{date(Config::get('Reading.date_format'),strtotime($result->booking_date))}}
						</div>
						<div class="sdate"> Start Date:
							<span class="badge ">
								{{date('m/d/Y', strtotime($result->start_date))}}
							</span>

						</div>
						<div class="sdate"> End Date:
							<span class="badge ">
								{{date('m/d/Y', strtotime($result->end_date))}}
							</span>

						</div>

						<div class="status"> status:
							@if($result->status == 0)
							<span class="badge badge-warning">
								Approval Pending 
							</span>
							@elseif($result->status == 1)
							<span class="badge badge-success">
								Booking  Accepted
							</span>
							@elseif($result->status == 2)
							<span class="badge badge-info">
								Booking  Stopped
							</span>
							@elseif($result->status == 3)
							<span class="badge badge-danger">
								Booking Rejected
							</span>
							@endif
						</div>
					</div>
				</div>

			</div>

		</div>
		<div class="col-sm-auto">
			<div class="btn-block pt-3 pt-md-0">

				@if($type=='nanny')
				@if($result->status==0)
				<a class=" btn btn-theme btn-view" onclick="statusApproved({{$result->id}})">Approve</a>
				<a class=" btn btn-theme btn-stop" onclick="statusRejected({{$result->id}})">Reject </a>
				@endif
				<a class=" btn btn-theme btn-view " class="btn-theme mw-100" data-toggle="modal"
				data-target="#exampleModalLong12">View</a>
				@if($result->status==1)    
				<a class=" btn btn-theme btn-stop">Stop </a>
				@endif
				@if($result->status==2) 
				<a class=" btn btn-theme btn-start">Restart</a>
				@endif

				@elseif($type=='client')
				<a class=" btn btn-theme btn-view nanny_details" class="btn-theme mw-100 "
				id="{{ $result->nanny_id }}">View</a>
				@if($result->status == 1)    
				<a class=" btn btn-theme btn-stop stop-nanny">Stop </a>
				@endif
				@if($result->status == 2)
				<a class=" btn btn-theme btn-start">Restart</a>
				@endif
				@endif    
			</div>


		</div>
	</div>
</div>
@endforeach
@endif
