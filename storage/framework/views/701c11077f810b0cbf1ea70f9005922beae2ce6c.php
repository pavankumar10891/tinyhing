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
                                    <?php echo e(date(config::get("Reading.date_format"),strtotime($result->interview_date))); ?>

                                    </td>
                                    <td data-label="Time" class="text-center">
                                        <span class="badge-theme">   <?php echo e($result->time_slot); ?></span>
                                    </td>
                                    <td data-label="Action" class="text-right">
                                        <a href="javascript:void(0);" class="btn btn-theme">
                                            Join Now
                                        </a>
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

<?php $__env->stopSection(); ?>
<?php echo $__env->make('front.dashboard.layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\tinyhugs\resources\views/front/dashboard/nanny_schedule_interview.blade.php ENDPATH**/ ?>