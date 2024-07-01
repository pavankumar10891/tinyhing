@extends('front.dashboard.layouts.default')
@section('content')


<div  class="main-workspace mb-5">
<div class=" backg-img padding-section">
	<div class="container">
	<div class="heading py-lg-5 pb-3  text-center">

		<h4>Payment Setting</h4>
	</div>

	<div class="col-md-12 mb-12">
		<div class="payment-label">
	<label>Stripe</label>
	</div<
		@if(Auth::user() && Auth::user()->stripe_user_id == '')
	
		<div class="btn-block">
	
			<?php $clientId = env('STRIPE_CLIENT_ID'); ?>
			<a href="https://connect.stripe.com/oauth/authorize?response_type=code&client_id={{$clientId}}&scope=read_write&redirect_uri={{route('user.registerStripe')}}" title="Connect with Stripe"
			 class="btn-connect">Connect with Stripe</a>

		</div>
		@else
		<div class="btn-block ">
			<button type="button"  id="connect"  class="btn-disconnect">
				<a href="{{route('user.disConnectStripe')}}" title="Disconnect" id="Disconnect">Disconnect</a>
			</button>
		</div>
		@endif

	</div>
</div>
	</div>

</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

<script type="text/javascript">
	
	$("#Disconnect").click(function (e) {
		e.stopImmediatePropagation();
		url = $(this).attr('href');
		Swal.fire({
			title: "Are you sure?",
			text: "Want to disconnect",
			icon: "warning",
			showCancelButton: true,
			confirmButtonText: "Yes",
			cancelButtonText: "No",
			reverseButtons: true
		}).then(function (result) {
			if (result.value) {
				window.location.replace(url);
			}
		});
		e.preventDefault();
	});

</script>

@endsection
