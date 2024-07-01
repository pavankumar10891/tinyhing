
<?php $__env->startSection('content'); ?>
<div class="main-workspace">
            <div class="container">
                <div class="total-client">
                    <div class="client-block">
                        <div class="dashboard-heading-head">My Invoice</div>
                    </div>

                    <div class="theme-table theme-table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Start Date</th>
                                    <th class="text-center">End Date</th>
                                    <th class="text-center">Amount</th>
                                    <th class="text-center">Nanny</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                            	<?php if(!empty($Invoices)): ?>
                            	<?php $__currentLoopData = $Invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php 
                                $checkReview    = CustomHelper::checkClientReview($value->nanny_id);
                                $checkTip       = CustomHelper::checkClientTip($value->nanny_id);
                                 ?>
                                <tr>
                                    <td data-label="Name">
                                        <?php echo e(date('m/d/Y', strtotime($value->start_date))); ?>

                                    </td>
                                    <td data-label="Date" class="text-center">
                                      <?php echo e(date('m/d/Y',strtotime($value->end_date))); ?>

                                    </td>
                                    <td data-label="Time" class="text-center">
                                       $ <?php echo e($value->total_amount); ?>

                                    </td>
                                    <td data-label="Time" class="text-center">
                                        <?php echo e($value->name); ?>

                                    </td>
                                    <td data-label="Action" class="text-right">
                                         <?php if($checkReview == 0): ?>
                                        <a href="javascript:void(0);" class="btn btn-theme giveTip"  data-id="<?php echo e($value->nanny_id); ?>" data-name="<?php echo e($value->name); ?>">
                                            Send tip
                                        </a>
                                         <?php endif; ?>
                                        <?php if($checkReview == 0): ?>
                                        <a href="javascript:void(0);" class="btn btn-theme giveRating" data-id="<?php echo e($value->nanny_id); ?>" data-name="<?php echo e($value->name); ?>">
                                            Give a Review
                                        </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                <tr>
                                	<td colspan="5">No Invoice Found</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>


                </div>
            </div>

        </div>

        <!-- Review Model -->
        <div class="modal fade give-feedback show pr-0" id="exampleModal9" tabindex="-1"
            aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="feedbackForm" data-id="">
                        <div class="modal-header">
                            <h3>Give Feedback</h3>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                            <div class="text-block text-center">
                                <h2 class="clientName">Author Name</h2>
                                <span class="postedDate"> </span>
                            </div>



                            <div class="star-rating">
                                <fieldset>
                                    <input type="radio" id="star5" name="rating" value="5" /><label for="star5"
                                    title="Outstanding">5 stars</label>
                                    <input type="radio" id="star4" name="rating" value="4" /><label for="star4"
                                    title="Very Good">4 stars</label>
                                    <input type="radio" id="star3" name="rating" value="3" /><label for="star3"
                                    title="Good">3 stars</label>
                                    <input type="radio" id="star2" name="rating" value="2" /><label for="star2"
                                    title="Poor">2 stars</label>
                                    <input type="radio" id="star1" name="rating" value="1" /><label for="star1"
                                    title="Very Poor">1 star</label>
                                </fieldset>
                            </div>


                            <textarea class="w-100" rows="5" name="review" placeholder="Review"></textarea>
                            
                            <div class="btn-block text-center pt-3">

                                <input type="submit" href="javascript:void(0);" class="btn-theme" value="Submit Review"/>



                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade give-feedback show pr-0" id="exampleModa20" tabindex="-1"
            aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="feedbackFormTinyHug" data-id="">
                        <div class="modal-header">
                            <h3>Give Feedback</h3>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                            <div class="text-block text-center">
                                <h2 class="clientName">Provide rating for Tiny Hugs</h2>
                                <span class="postedDate"> </span>
                            </div>



                            <div class="star-rating">
                                <fieldset>
                                    <input class="rating" type="radio" id="star51" name="site_rating" value="5" /><label for="star51"
                                    title="Outstanding">5 stars</label>
                                    <input class="rating" type="radio" id="star41" name="site_rating" value="4" /><label for="star41"
                                    title="Very Good">4 stars</label>
                                    <input class="rating" type="radio" id="star33" name="site_rating" value="3" /><label for="star31"
                                    title="Good">3 stars</label>
                                    <input class="rating" type="radio" id="star21" name="site_rating" value="2" /><label for="star22"
                                    title="Poor">2 stars</label>
                                    <input class="rating" type="radio" id="star11" name="site_rating" value="1" /><label for="star11"
                                    title="Very Poor">1 star</label>
                                </fieldset>
                            </div>


                            <textarea class="w-100 " rows="5" name="review" id="review" placeholder="Review"></textarea>
                            
                            <div class="btn-block text-center pt-3">

                                <input type="button" class="btn-theme" id="siterating" value="Submit Review"/>



                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade give-feedback show pr-0" id="tipModel" tabindex="-1"
            aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <div class="container plan-details">
                        <div class="plans">
                            <div class=" pb-2">
                                <div class="plan-detail-input">
                                    <?php echo e(Form::open(array('id' => 'tip-form', 'class' => 'form validation','data-cc-on-file'=>"false",'data-stripe-publishable-key'=> env('STRIPE_PUBLISHER')))); ?>


                                    <h5>Enter Card Detail</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group input-form mw-100 my-2">
                                                <label>Name</label>
                                                <?php echo e(Form::text('name','' , ['placeholder' => trans("Name"), 'class'=>'name form-control'])); ?>

                                                <span id="name_error" class="help-inline error"></span>
                                            </div>
                                        </div>
                                        <div class='col-md-6'>
                                            <div class='form-group input-form mw-100 my-2'>
                                                <label class='control-label'>Card Number</label>
                                                <?php echo e(Form::text('card-number','' , ['class'=>'form-control card-number','autocomplete'=>'off','size'=>'20'])); ?>

                                                <span id="card-number_error" class="help-inline error"></span>

                                            </div>
                                        </div>

                                        <div class='col-md-4'>
                                            <div class='form-group input-form mw-100 my-2'>
                                                <label class='control-label'>CVC</label> 


                                                <?php echo e(Form::text('cvc','' , ['placeholder' => 'ex. 311','class'=>'form-control card-cvc','autocomplete'=>'off', 'size'=>'4'])); ?>

                                                <span id="cvc_error" class="help-inline error"></span>

                                            </div>
                                        </div>
                                        <div class='col-md-4'>

                                            <div class='form-group input-form mw-100 my-2 expiration required'>
                                                <label class='control-label'>Expiration Month</label> 


                                                <?php echo e(Form::text('card-expiry-month','' , ['placeholder' => 'MM','class'=>'form-control card-expiry-month', 'size'=>'2'])); ?>

                                                <span id="card-expiry-month_error" class="help-inline error"></span>

                                            </div>
                                        </div>
                                        <div class='col-md-4'>

                                            <div class='form-group input-form mw-100 my-2 expiration required'>
                                                <label class='control-label'>Expiration Year</label> 

                                                <?php echo e(Form::text('card-expiry-year','' , ['placeholder' => 'YYYY','class'=>'form-control card-expiry-year', 'size'=>'4'])); ?>

                                                <span id="card-expiry-year_error" class="help-inline error"></span>
                                                <?php echo e(Form::hidden('stripe_token', '',['class'=>'form-control stripe_token'])); ?>

                                                <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>" />
                                                <input type="hidden" id="nanny_id" name="nanny_id" value="" />
                                                <input type="hidden" name="user_id" value="<?php echo e(Auth::user()->id); ?>" />
                                               
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="floating-label">

                                                <label>Amount</label>
                                                <?php echo e(Form::text('amount', '', ['class' => 'form-control  amount'])); ?>

                                                <span id="amount_error" class="help-inline error"></span>   
                                                <br>
                                                <strong>Note: Minimum amount is $50.</strong>
                                                <br><br>
                                                <?php echo e(Form::button('Save', ['type' => 'button', 'class' => 'btn btn-primary', 'id' => 'tip-submit'])); ?>


                                            </div>
                                        </div>
                                    </div>
                                    <?php echo e(Form::close()); ?> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script>
    $(document).on('click','.giveRating',function(){
        $id=$(this).attr('data-id');
        $name=$(this).attr('data-name');
        $date=$(this).attr('data-date');

        $("#feedbackForm").find('.clientName').html($name);
        $("#feedbackForm").attr('data-id',$id);
        //$html='Rating Provided by '+$date; 
        //$("#feedbackForm").find('.postedDate').html($html);
        $("#feedbackForm")[0].reset();
        $("#exampleModal9").modal('show');

    });

    $("#feedbackForm").on('submit',function(e){
        e.preventDefault();
        var formData= new FormData($("#feedbackForm")[0]);
        formData.append('nanny_id',$(this).attr('data-id'));
        $("#loader_img").show();
        $.ajax({
                headers: {
                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
             },
             url: '<?php echo e(route("user.clientGiveFeedback")); ?>',
             data: formData,
             type: "POST",
             contentType: false,
             cache: false,
             processData:false,
             success: function(res) {
                 if(res.success==1){
                    $("#loader_img").hide();
                    $("#exampleModal9").modal('hide');
                    
                    if(res.rating > 2){
                       $("#exampleModa20").modal('show'); 
                    }else{
                        window.location.reload();
                       show_message(res.message,'success'); 
                    }
                    

                }else if(res.success==0){
                    $("#loader_img").hide();
                    if(res.errors.rating){
                        show_message(res.errors.rating,'error');
                    }
                    if(res.errors.review){
                        show_message(res.errors.review,'error');
                    }
                }else{
                    $("#loader_img").hide();
                    $("#exampleModal9").modal('hide');
                    show_message(res.message,'error');
                }
            }
        });
    });

    $("#siterating").on('click',function(e){
        e.preventDefault();        
        //var radioValue = $("input[name='site_rating']:checked").val();
       //values=  $("#feedbackFormTinyHug").serialize()
        //alert(values);

        //$("#loader_img").show();
        $.ajax({
                headers: {
                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
             },
             url: '<?php echo e(route("user.clientGiveFeedbacktinyhug.site")); ?>',
             data: $("#feedbackFormTinyHug").serialize(),
             type: "POST",
             success: function(res) {
                 if(res.success==1){
                    $("#loader_img").hide();
                    $("#exampleModa20").modal('hide');
                    show_message(res.message,'success');
                    window.location.reload();
                    
                }else if(res.success==0){
                    $("#loader_img").hide();
                    if(res.errors.site_rating){
                        show_message(res.errors.site_rating,'error');
                    }
                    if(res.errors.review){
                        show_message(res.errors.review,'error');
                    }
                }else{
                    $("#loader_img").hide();
                    $("#exampleModa20").modal('hide');
                    show_message(res.message,'error');
                }
            }
        });
    });
   $(document).on('click','.giveTip',function(){
        id=$(this).attr('data-id');
        $('#nanny_id').val(id);
        $name=$(this).attr('data-name');
         $("#feedbackForm").find('.clientName').html($name);
        $("#feedbackForm").attr('data-id',id)
         $("#tipModel").modal('show');

   });
  
</script>


        <script type="text/javascript">


            var $form         = $("#tip-form");
            console.log($form);
            $('#tip-submit').click(function(e) { 
                var $form         = $("#tip-form"),
                inputSelector = ['input[type=email]', 'input[type=password]',
                'input[type=text]', 'input[type=file]',
                'textarea'].join(', '),
                $inputs       = $form.find('.required').find(inputSelector),
                $errorMessage = $form.find('div.error'),
                valid         = true;
                $errorMessage.addClass('hide');

                $('.has-error').removeClass('has-error');
                $inputs.each(function(i, el) {
                    var $input = $(el);
                    if ($input.val() === '') {
                        $input.parent().addClass('has-error');
                        $errorMessage.removeClass('hide');
                        e.preventDefault();
                    }
                });

                if (!$form.data('cc-on-file')) {
                    e.preventDefault();
                    Stripe.setPublishableKey($form.data('stripe-publishable-key'));
                    Stripe.createToken({
                        number: $('.card-number').val(),
                        cvc: $('.card-cvc').val(),
                        exp_month: $('.card-expiry-month').val(),
                        exp_year: $('.card-expiry-year').val()
                    }, stripeResponseHandler);
                }


                function stripeResponseHandler(status, response) {
                    if (response.error) {
                        if(response.error.code == 'invalid_number') {
                            $("#card-number_error").html(response.error.message);
                            return false;

                        } 
                        else if(response.error.code == 'incorrect_number') {
                            $("#card-number_error").html(response.error.message);
                            return false;
                            
                        }
                        else if(response.error.code == 'invalid_cvc') {
                            $("#cvc_error").html(response.error.message);
                            $("#card-number_error").html('');

                            return false;
                            
                        }
                        else if(response.error.code == 'invalid_expiry_year') {
                            $("#card-expiry-year_error").html(response.error.message);
                            $("#card-number_error").html('');
                            $("#card-expiry-month_error").html('');
                            
                            return false;

                        }
                        else if(response.error.code == 'invalid_expiry_month') {
                            $("#card-expiry-month_error").html(response.error.message);
                            $("#card-number_error").html('');
                            $("#cvc_error").html('');
                            return false;

                        }
                        else if(response.error.code == 'missing_payment_information'){
                            show_message(response.error.message, "error"); 
                        }
                        else{
                            show_message(response.error.message, "error"); 
                            return false;
                        }
                    } else {
                        var token = response['id'];
                        console.log(token);
                        $('.stripe_token').val(token);

                    }

                    tip();

                }


            });

            function tip(){
                var amount  = $('.amount').val();
                var name    = $('.name').val();
                var number  = $('.card-number').val();
                var cvc     = $('.card-cvc').val();
                var month   = $('.card-expiry-month').val();
                var year    = $('.card-expiry-year').val();


                var form = $("#tip-form").closest("form");
                console.log(form);
                var formData = new FormData(form[0]);
                $("#loader_img").show();

                $.ajax({
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '<?php echo e(route("user.tip-save")); ?>',
                    data: formData,
                    contentType: false,       
                    cache: false,             
                    processData:false,
                    success: function(data) {
                        $("#loader_img").hide();
                        if(data.success==1){
                            window.location.reload();

                        }else{
                            console.log(data);

                            $("#loader_img").hide();
                            show_message(data.message,'error');
                                // show_message(data.errors,'error');


                                $('span[id*="_error"]').each(function() {
                                    var id = $(this).attr('id');

                                    if(id in data.errors) {
                                        $("#"+id).html(data.errors[id]);
                                    } else {
                                        $("#"+id).html('');
                                    }
                                });

                            }

                // show_message(data.message,'success');

            },
            error: function() {
            }
        });
                

            };


        </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('front.dashboard.layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\xampp\htdocs\tinyhugs\resources\views/front/dashboard/invoice/invoice.blade.php ENDPATH**/ ?>