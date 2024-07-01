<link href="{{WEBSITE_CSS_URL}}admin/button.css" rel="stylesheet">
<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
	<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title"><?php echo trans("Email Detail"); ?></h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<i aria-hidden="true" class="ki ki-close"></i>
			</button>
		</div>
		<div class="modal-body">
			<?php
				if (!empty($result)) {
				?>
					<div class="row">
						<div class="col-lg-6">
							<div class="form-group row my-2">
								<label class="col-12 font-size-h6 text-dark-75 font-weight-bolder"><?php echo trans("Email To"); ?></label>
								<div class="col-12">
									<span class="font-size-sm text-muted font-weight-bold mt-1r"><?php echo $result->email_to;  ?></span>
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group row my-2">
								<label class="col-12 font-size-h6 text-dark-75 font-weight-bolder"><?php echo trans("Email From"); ?></label>
								<div class="col-12">
									<span class="font-size-sm text-muted font-weight-bold mt-1r"><?php echo $result->email_from; ?></span>
								</div>
							</div>
						</div>
					</div>
					<hr>
					<div class="form-group row my-2">
						<label class="col-12 font-size-h6 text-dark-75 font-weight-bolder"><?php echo trans("Subject"); ?></label>
						<div class="col-12">
							<span class="font-size-sm text-muted font-weight-bold mt-1r"><?php echo  $result->subject; ?></span>
						</div>
					</div>
					<hr>
					<div class="form-group row my-2">
						<label class="col-12 font-size-h6 text-dark-75 font-weight-bolder">Messages</label>
						<div class="col-12">
							<span class="font-size-sm text-muted font-weight-bold mt-1r"><?php echo  $result->message; ?></span>
						</div>
					</div>
				<?php
			} ?>
			<div class="clearfix">&nbsp;</div>
		</div>
		<!-- <div class="modal-footer">
			<button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
		</div> -->
	</div>
</div>