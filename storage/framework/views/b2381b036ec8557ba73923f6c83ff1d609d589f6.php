
<?php $__env->startSection('content'); ?>
<div class="main-workspace mb-5">
    <div class="booking-sec backg-img">
        <div class="container">
            <?php echo e(Form::open(['method' => 'get','role' => 'form','class' => 'kt-form kt-form--fit mb-0','id'=>'searchForm','autocomplete'=>"off",'onSubmit'=>'return false'])); ?>

            <?php echo e(Form::hidden('display')); ?>

            <div class="sidebar-block topsiderbar ">
                <div class="filter-option">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" />
                </div>
                <div class="filter-option">
                    <label>Status</label>
                    <?php echo e(Form::select('status',array(''=>trans('All'),0=>trans('Pending'),1=>trans('Accepted'),2=>trans('Stopped'),3=>trans('Rejected')),((isset($searchVariable['status'])) ? $searchVariable['status'] : ''), ['class' => 'form-control'])); ?>


                </div>
                <div class="filter-option">
                    <label>Date from</label>

                    <input class="form-control" name="date_from" type="date">

                </div>

                <div class="filter-option">
                    <label>Date to</label>
                    <input class="form-control" name="date_to" type="date">
                </div>
                <button class=" btn btn-theme mt-4 mr-2" onclick="loadBookingData()">Search</a>
                    <button class=" btn btn-theme mt-4 resetBtn">Clear</a>

                    </div>
                    <?php echo e(Form::close()); ?>


                    <div class="booking-block">
                        <div class="row bookingData">
                            <?php if($results->isNotEmpty()): ?>
                            <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-12">
                                <div class="bg-white block-inner ">
                                    <div class="col">
                                        <div class="text-block ">
                                            <div class="d-flex align-items-center">
                                                <span>
                                                   <?php if(!empty($result->photo_id) && file_exists(USER_IMAGE_ROOT_PATH.$result->photo_id)): ?>
                                                   <img src="<?php echo e(WEBSITE_URL.'image.php?width=148&height=148&cropratio=3:3&image='.USER_IMAGE_URL.$result->photo_id); ?>" height="148" width="148">
                                                   <?php else: ?>
                                                   <img src="<?php echo e(WEBSITE_URL.'image.php?width=148&height=148&cropratio=3:3&image='.WEBSITE_IMG_URL.'no-image.png'); ?>" alt="">
                                                   <?php endif; ?>
                                               </span>
                                               <div class="pl-4">
                                                <h2> <?php echo e($result->name); ?></h2>
                                                <div class="date">
                                                    <?php echo e(date(Config::get('Reading.date_format'),strtotime($result->booking_date))); ?>

                                                </div>


                                                <div class="status"> status:
                                                    <?php if($result->status == 0): ?>
                                                    <span class="badge badge-warning">
                                                        Approval Pending 
                                                    </span>
                                                    <?php elseif($result->status == 1): ?>
                                                    <span class="badge badge-success">
                                                     Booking  Accepted
                                                 </span>
                                                 <?php elseif($result->status == 2): ?>
                                                 <span class="badge badge-info">
                                                     Booking  Stopped
                                                 </span>
                                                 <?php elseif($result->status == 3): ?>
                                                 <span class="badge badge-danger">
                                                    Booking Rejected
                                                </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>
                            <div class="col-sm-auto">
                                <div class="btn-block pt-3 pt-md-0">
                                    <?php if($result->status==0): ?>
                                    <a class=" btn btn-theme btn-view" onclick="statusApproved(<?php echo e($result->id); ?>)">Approve</a>
                                    <a class=" btn btn-theme btn-stop" onclick="statusRejected(<?php echo e($result->id); ?>)">Reject </a>
                                    <?php endif; ?>
                                    <a class=" btn btn-theme btn-view nanny_details" class="btn-theme mw-100" id="<?php echo e($result->id); ?>">View</a>
                                    

                                </div>


                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                    <div class="col-md-12">
                        <div class="bg-white block-inner ">
                            <span class="text-center">No Booking Found</span>
                        </div>
                    </div>
                    <?php endif; ?>
                    

                    <!-- <div class="col-md-12">
                        <div class="bg-white block-inner ">
                            <div class="col">

                                <div class="text-block ">
                                    <div class="d-flex align-items-center">
                                        <span><img src="img/review-img.png"></span>
                                        <div class="pl-4">
                                            <h2> client name</h2>
                                            <div class="date">
                                                06/04/2021
                                            </div>


                                            <div class="status"> status:
                                                <span class="badge badge-success">Success</span>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>
                            <div class="col-sm-auto">
                                <div class="btn-block pt-3 pt-md-0">
                                    <a class=" btn btn-theme btn-view " class="btn-theme mw-100" data-toggle="modal"
                                        data-target="#exampleModalLong12">View</a>
                                    <a class=" btn btn-theme btn-stop">Stop </a>
                                    <a class=" btn btn-theme btn-start">Restart</a>

                                </div>

                            </div>
                        </div>
                    </div> -->
                    
                    

                    
                </div>

                <div class="text-center mt-4">
                    <?php if($results->isNotEmpty()): ?>
                    <button class="btn btn-theme loadMoreBtn" type="button" onclick="showMore($(this))">
                        Load More
                    </button>
                    <?php endif; ?>
                    
                </div>

                
                


            </div>
        </div>

    </div>
</div>
<div class="modal fade set-availblity-bx" id="exampleModalLong12" tabindex="-1" role="dialog"
aria-labelledby="exampleModalLongTitle" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered booking-form-model" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Booking Details</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="book_avaiblity">

            </div>

                <!-- <div class="btn-block text-right">
                    <input type="submit" class="btn-theme" value="Submit">
                </div> -->
            </div>

        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.5.2/bootbox.min.js" integrity="sha512-RdSPYh1WA6BF0RhpisYJVYkOyTzK4HwofJ3Q7ivt/jkpW6Vc8AurL1R+4AUcvn9IwEKAPm/fk7qFZW3OuiUDeg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    
    $(".resetBtn").click(function(){
        location.reload();
        
        // $("#searchForm")[0].reset();
        // loadBookingData();
    });
    // $("input[name=date_from]").change(function(){
    //     loadBookingData();
    // });
    // $("input[name=date_to]").change(function(){
    //     loadBookingData();
    // });
    function loadBookingData(){
     
        var formData = new FormData($("#searchForm")[0]);
        formData.append('offset',0);
        $("#loader_img").show();
        $.ajax({
            headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
           },
           url: '<?php echo e(route("user.nannyBookingList")); ?>',
           data: formData,
           type: "POST",
           contentType: false,
           cache: false,
           processData:false,
           success: function(res) {
            
               if(res.data!=''){
                $("#loader_img").hide();
                $('.bookingData').html('');
                $('.bookingData').html(res.data);
                $('.loadMoreBtn').show();
                
            }else{
                $("#loader_img").hide();
                $html=' <div class="col-md-12"><div class="bg-white block-inner "><span class="text-center">No Bookings Found</span></div></div>';
                $('.bookingData').html($html);
                $('.loadMoreBtn').hide();
            }
        }
    });
    }
    var offset = 1;
    function showMore($elem){
        var formData = new FormData($("#searchForm")[0]);
        formData.append('offset',offset);
        $("#loader_img").show();
        $.ajax({
            headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
           },
           url: '<?php echo e(route("user.nannyBookingList")); ?>',
           data: formData,
           type: "POST",
           contentType: false,
           cache: false,
           processData:false,
           success: function(res) {
               if(res.data!=''){
                $elem.show();
                $("#loader_img").hide();
                $('.bookingData').append(res.data);
                offset++;
            }else{
                $elem.hide(); 
                $("#loader_img").hide();
            }
        }
    });

    }


    function statusApproved(id){
       bootbox.confirm({
        title: "Approve Booking?",
        message: "Are you sure want to Approve Booking?",
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
               window.location.href = "<?php echo e(URL('/approve-booking')); ?>/"+id;
           }
           
       }
   });
   }

   function statusRejected(id){
      var dialog = bootbox.prompt({
        title: "Reject Booking?",
        message: "Are you sure want to Reject Booking?",
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
                    'url': '<?php echo e(URL("/approve-rejected")); ?>',
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

   $(document).ready(function() {
    $("body").on('click', '.nanny_details', function() {
        var id = $(this).attr('id');

        $("#loader_img").show();
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": "<?php echo e(csrf_token()); ?>"
            },
            url: '<?php echo e(route("user.getBookingSlots")); ?>',
            type: 'POST',
            data: {
                'id': id
            },
            success: function(data) {
                if (data != '') {
                    $("#loader_img").hide();
                    $('.book_avaiblity').html(data.data);
                    $(".nanny-booking").hide();
                    $('#exampleModalLong12').modal('show');
                }

            }
        });
    });
    $("body").on('click', '.stop-nanny', function() {
        alert();
    });

});
   
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('front.dashboard.layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\xampp\htdocs\tinyhugs\resources\views/front/dashboard/nanny_mybooking.blade.php ENDPATH**/ ?>