
<?php $__env->startSection('content'); ?>

<div class="main-workspace">
    <div class="container">
        <div class="total-client">
            <div class="client-block">
                <div class="dashboard-heading-head">Interviews</div>
            </div>

            <div class="theme-table theme-table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th class="text-center">Date</th>
                            <th class="text-center">Time</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!$results->isEmpty()): ?>
                         
                        <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                         
                            <td data-label="Name">
                                <?php echo e($value->user_name); ?>

                                 <?php if($value->is_interview == 1): ?>
                                 <p>Status: <span class="badge badge-success">Approved</span></p>
                                 <?php elseif($value->is_interview == 2): ?>
                                  <p>Status: <span class="badge badge-danger">Rejected</span></p>
                                  <p class="lable warning">Reason: <?php echo e(!empty($value->reject_reason) ? $value->reject_reason:''); ?></p>
                                 <?php else: ?>
                                 <p>Status: <span class="badge badge-warning">Approval Pending</span></p>
                                <?php endif; ?>
                            </td>
                            <td data-label="Date" class="text-center">

                                <?php echo e(date('m/d/Y',strtotime($value->interview_date))); ?>

                            </td>
                            <td data-label="Time" class="text-center">
                                <?php
                                    $timeSlotData = explode('-', $value->meeting_day_time); 
                                    $fromTIme = !empty($timeSlotData[0]) ? date('h:i a', strtotime($timeSlotData[0])):'';
                                    $toTIme = !empty($timeSlotData[1]) ? date('h:i a',strtotime($timeSlotData[1])):''; 
                                    $todayDate = strtotime (date('Y-m-d H:i')); 
                                    $date1 =  !empty($timeSlotData[1]) ? date('Y-m-d h:i', strtotime($value->interview_date.' '.$timeSlotData[1])) :'';
                                    $date2 =  date('Y-m-d h:i');
                                 ?>
                                <span class="badge-theme"> <?php echo e($fromTIme.'-'.$toTIme); ?></span>
                            </td>
                            <td data-label="Action" class="text-right">
                                <?php if($value->is_interview == 1): ?>

                                <?php if(strtotime($value->interview_date.$timeSlotData[1]) > $todayDate): ?>
                                <?php if(strtotime($date1) > strtotime($date2)): ?>
                                <a href="<?php echo e(route('meeting.join', Crypt::encrypt($value->id))); ?>"  class="btn btn-theme join-now-booking" id="<?php echo e($value->id); ?>">
                                    Join Now
                                </a>
                                <?php endif; ?>
                                <?php endif; ?>

                                <?php if($value->is_booking != 1): ?>
                                <a href="javascript:void(0);" class="btn btn-theme book_nanny" id="<?php echo e($value->nanny_id); ?>" data-id="<?php echo e($value->id); ?>">
                                    Book Now
                                </a>
                                <?php endif; ?>
                                <?php endif; ?>
                            </td>   
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">No Interview Scheduled.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>


        </div>
    </div>

</div>


 <!-- book Nanny -->
 <div class="modal fade set-availblity-bx" id="exampleModalLong" tabindex="-1" role="dialog"
 aria-labelledby="exampleModalLongTitle" aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered booking-form-model" role="document">
 	<div class="modal-content">
 		<div class="modal-header">
 			<h5 class="modal-title" id="exampleModalLongTitle">Booking Form</h5>
 			<button type="button" class="close" data-dismiss="modal"
 			aria-label="Close">
 			<span aria-hidden="true">&times;</span>
 		</button>
 	</div>
 	<?php echo e(Form::open(array('id' => 'user-nannaybooking-form', 'class' => 'form'))); ?>


 	<div class="row">
 		<div class="col-xl-5" style="margin-left: 20px;">
 			<div class="form-group">
 				<?php echo HTML::decode( Form::label('start_date', trans("Start Date").'<span
 				class="text-danger"> * </span>')); ?>

 				<?php echo e(Form::text('start_date','', ['class' => 'form-control form-control-solid form-control-lg datetimepicker-input '.($errors->has('start_date') ? 'is-invalid':''),'placeholder'=>'Start Date','id'=>'datepickerfrom'])); ?>


 			</div>
 		</div>
        <input type="hidden" name="interview_id" id="interview_id">
 		<div class="col-xl-5" >
 			<div class="form-group">
 				<?php echo HTML::decode( Form::label('end_date', trans("End Date").'<span
 				class="text-danger"> * </span>')); ?>

 				<?php echo e(Form::text('end_date','',['class' => 'form-control form-control-solid form-control-lg datetimepicker-input '.($errors->has('end_date') ? 'is-invalid':''),'placeholder'=>'End Date','id'=>'datepickerto'])); ?>

 			</div>
 		</div>
 	</div>
 	<div class="modal-body">
 		<div class="book_avaiblity">


 		</div>

 	</div>
 	<?php echo e(Form::close()); ?>

 </div>
</div>
</div>
<link rel="stylesheet" href="<?php echo e(WEBSITE_CSS_URL); ?>jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
    $('#datepickerfrom').datepicker({
        minDate: 0
	});
	$('#datepickerto').datepicker({
        minDate: 0
	});

    $("body").on('click', '.book_nanny',function(){
    		var nannyId = $(this).attr('id');
            var interviewId = $(this).data('id');
             $('#interview_id').val(interviewId);

    		$("#loader_img").show();        
    		$.ajax({
    			headers     :   { "X-CSRF-TOKEN": "<?php echo e(csrf_token()); ?>" },
    			url         :   '<?php echo e(route("user.getNannyAvaiblity")); ?>',
    			type        :   'POST', 
    			data        :   {'nannyId':nannyId},              
    			success   :   function(data) {
    				if(data!=''){
    					$("#loader_img").hide(); 
    					$('.book_avaiblity').html(data.data); 
    					$('#exampleModalLong').modal('show');
    				}  

    			} 
    		});
    	});

        function nannybookiing(){
    	var formData 	= $('#user-nannaybooking-form')[0];
    	var startDate 	= $('#datepickerfrom').val();
    	var endDate 	= $('#datepickerto').val();

    	if(startDate == ''){
    		show_message("Start Date is required", "error");
    		return false;
    	}

    	if(endDate == ''){
    		show_message("End Date is required", "error");
    		return false;
    	}

    	if (Date.parse(startDate) > Date.parse(endDate)){
    		show_message("Start Date cannot be greater than end date", "error");
    		return false;
    	}
        /*if($(".avail_slots_box").find(".avail_slots:checked").length == 0){
            show_message("Please select time slots.", "error");
            return false;
        }*/
    	$.ajax({
    		headers     :   { "X-CSRF-TOKEN": "<?php echo e(csrf_token()); ?>" },
    		url: "<?php echo e(route('user.nannybooking')); ?>",
    		method: 'post',
                        //data: $('#user-registration-form').serialize(),
                        data: new FormData(formData),
                        contentType: false,       
                        cache: false,             
                        processData:false, 
                        beforeSend: function() {
                        	$("#loader_img").show();
                        },
                        success: function(response){
                        	$("#loader_img").hide();
                        // console.log(response);
                        if(response.success) {
                             window.location.reload();
                        	/*$("#in_first_name").val(response.first_name);
                        	$("#in_email").val(response.email);
                        	$("#in_phone_number").val(response.phone_number);
                        	show_message(response.message,'success');
                           
                        	$('#myModal').modal('hide');
                        	$('#exampleModalLong').modal('hide');
                        	$('#schedul_interview').modal('show')*/;
                        } else {

                        	 show_message(response.message, "error");
                            return false;
                        }
                    }
                });

    }

    $(document).ready(function () {
    	$('.nanny-booking').click(function() {
    		nannybookiing();
    	});
    });
        (function(){
            var doc = document;

            jQuery('.join-interview').click(function(e){
                e.preventDefault();
                window.popup = window.open(jQuery(this).attr('href'), 'importwindow', 'width=500, height=200, top=100, left=200, toolbar=1');

                window.popup.onload = function() {
                    window.popup.onbeforeunload = function(){
                        doc.location.reload(true); //will refresh page after popup close
                    }
                }
            });
        })();
    </script>
    <style>
    .theme-table .table thead th {
        border: 0;
        background-color: #208fbf;
        color:#fff;
    }
    .btn-theme{
        background-color: #208fbf;
    }
    .theme-table {
        border: 1px solid #e0e9e3;
        border-radius: 10px;
        overflow: hidden;
    }
    </style>

<!-- end book Nanny -->


<?php $__env->stopSection(); ?>
<?php echo $__env->make('front.dashboard.layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\xampp\htdocs\tinyhugs\resources\views/front/dashboard/client_schedule_interview.blade.php ENDPATH**/ ?>