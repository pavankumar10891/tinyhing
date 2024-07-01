<?php $__env->startSection('content'); ?>


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
		<?php if(Auth::user() && Auth::user()->stripe_user_id == ''): ?>
	
		<div class="btn-block">
	
			<?php $clientId = env('STRIPE_CLIENT_ID'); ?>
			<a href="https://connect.stripe.com/oauth/authorize?response_type=code&client_id=<?php echo e($clientId); ?>&scope=read_write&redirect_uri=<?php echo e(route('user.registerStripe')); ?>" title="Connect with Stripe"
			 class="btn-connect">Connect with Stripe</a>

		</div>
		<?php else: ?>
		<div class="btn-block ">
			<button type="button"  id="connect"  class="btn-disconnect">
				<a href="<?php echo e(route('user.disConnectStripe')); ?>" title="Disconnect" id="Disconnect" 	 >Disconnect</a>

			</button>
		</div>
		<?php endif; ?>

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

<?php $__env->stopSection(); ?>

<?php echo $__env->make('front.dashboard.layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\xampp\htdocs\tinyhugs\resources\views/front/users/nanny-payment-setting.blade.php ENDPATH**/ ?>