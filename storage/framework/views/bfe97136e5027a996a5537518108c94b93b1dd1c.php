
<?php $__env->startSection('content'); ?>
<div class="main-workspace">
            <div class="container">
                <div class="total-client  mb-5">
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
                                 <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                 <tr>
                                 <td data-label="Name">
                                    <?php echo e($result->user_name); ?>

                                    </td>
                                    <td data-label="Date" class="text-center">
                                    <?php echo e(date('m/d/Y',strtotime($result->interview_date))); ?>

                                    </td>
                                    <td data-label="Time" class="text-center">
                                        <?php
                                            $timeSlotData = explode('-', $result->meeting_day_time); 
                                            $fromTIme = !empty($timeSlotData[0]) ? date('h:i a', strtotime($timeSlotData[0])):'';
                                            $toTIme = !empty($timeSlotData[1]) ? date('h:i a',strtotime($timeSlotData[1])):'';
                                            $date1 =  !empty($timeSlotData[1]) ? date('Y-m-d h:i', strtotime($result->interview_date.' '.$timeSlotData[1])) :'';
                                            $date2 =  date('Y-m-d h:i');  
                                         ?>
                                        <span class="badge-theme"> <?php echo e($fromTIme.'-'.$toTIme); ?></span>
                                    </td>
                                    <td data-label="Action" class="text-right">

                                        <?php if($result->is_interview == 1): ?>
                                        <?php if(strtotime($date1) > strtotime($date2)): ?>
                                        <a href="<?php echo e(route('meeting.join', Crypt::encrypt($result->id))); ?>"  class="btn btn-theme">
                                            Join Now
                                        </a>
                                        <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if($result->is_interview==0): ?>
                                        <a class=" btn btn-theme btn-view" onclick="statusApproved(<?php echo e($result->id); ?>)">Approve</a>
                                        <a class=" btn btn-theme btn-stop" onclick="statusRejected(<?php echo e($result->id); ?>)">Reject </a>
                                        <?php endif; ?>
                                    </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                    <tr><td colspan="6" style="text-align:center;"><?php echo e(trans("Record not found.")); ?></td></tr>
                                    <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php echo $__env->make('pagination.default', ['results' => $results], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    

                </div>
            </div>

        </div>
        <script>
        function page_limit() {
    $("form").submit();
}
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.5.2/bootbox.min.js" integrity="sha512-RdSPYh1WA6BF0RhpisYJVYkOyTzK4HwofJ3Q7ivt/jkpW6Vc8AurL1R+4AUcvn9IwEKAPm/fk7qFZW3OuiUDeg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<style>
    .theme-table .table thead th {
        border: 0;
        background-color: #82a79c;
        color:#fff;
    }
    .btn-theme{
        background-color: #82a79c;
    }
    .theme-table {
        border: 1px solid #e0e9e3;
        border-radius: 10px;
        overflow: hidden;
    }
    </style>
    <script type="text/javascript">
        
    function statusApproved(id){
        bootbox.confirm({
            title: "Approve Interview Sheduled?",
            message: "Are you sure want to Approve Interview Sheduled?",
            buttons: {
                cancel: {
                    label: '<i class="fa fa-times"></i> Cancel'
                },
                confirm: {
                    label: '<i class="fa fa-check"></i> Confirm'
                }
            },
            callback: function (result) {
                if(result){
                   window.location.href = "<?php echo e(URL('/approve-interview')); ?>/"+id;
               }
               
           }
       });
   }

   function statusRejected(id){
        var dialog = bootbox.prompt({
            title: "Reject Interview?",
            message: "Are you sure want to Reject Interview?",
            inputType:'textarea',
            buttons: {
                cancel: {
                    label: '<i class="fa fa-times"></i> Cancel'
                },
                confirm: {
                    label: '<i class="fa fa-check"></i> Confirm'
                }
            },
            callback: function (result) {
                if(result != null){
                    $.ajax({
                        'type': 'post',
                        'url': '<?php echo e(URL("/reject-interview")); ?>',
                        'data': {"_token": "<?php echo e(csrf_token()); ?>", 'id': id, 'reject_reason': result},
                        'success': function(response) {
                            if(response.success) {
                                location.reload();
                            } else {
                                $('.reject-reason-error').remove();
                                dialog.find('.bootbox-input-textarea').css('border', '2px solid red');
                                $(".bootbox-form").append("<span class='reject-reason-error' style='color: red; font-weight: bold; font-size: 18px;'>"+ response.message+"</span>");
                            }
                        }
                    });
                } else {
                    return true;
                }
                return false;
                
           }
       });
   }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('front.dashboard.layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\xampp\htdocs\tinyhugs\resources\views/front/dashboard/nanny_schedule_interview.blade.php ENDPATH**/ ?>