
<?php $__env->startSection('content'); ?>

<!-- <section class="about-us padding-section">
        <div class="container">
            <div class="heading py-lg-5 pb-3  text-center">
                <h4>Pricing</h4>
            </div>
        </div>
    </section>  -->
    <?php if(count($pakages) > 0): ?>    
    <div class="pricing-table backg-img padding-section">
        <div class="container">
            <div class="heading py-lg-5 pb-3  text-center">

                <h4>Choose your Plan</h4>
            </div>
            <div class="row py-5">
              <?php $__currentLoopData = $pakages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <?php
              $chanPan =  CustomHelper::checkUsertCurretntPlan($value->id);
              ?>
              <?php if(Auth::user() && $chanPan == 1): ?>
              <div class="col-md-4 mb-4">
                <div class="card mb-5 mb-lg-0 rounded-lg shadow">
                    <div class="card-header">
                        <h5 class="card-title text-uppercase text-center"><?php echo e($value->name); ?></h5>
                        <h6 class="h3 text-white text-center">$<?php echo e(number_format($value->price, 2)); ?>

                            <span class="h6 text-white-50">
                                /<?php echo e($value->no_of_month); ?> <?php echo e(($value->no_of_month > 1) ? 'Months' :'Month'); ?></span></h6>
                            </div>
                            <div class="card-body bg-light rounded-bottom">
                                <?php echo $value->description; ?>

                            <?php /*
                            <ul class="list-unstyled mb-4">
                                 @if(!empty($value->slug) && $value->slug == 'standard')
                                   @if(!empty($standard))
                                    @foreach($standard as $standard)
                                     @if($standard->optional == 1)
                                    <li class="mb-3"><span class="mr-3">
                                        <i class="fas fa-check text-primary"></i></span>{{$standard->code}}</li>
                                   @elseif($standard->optional == 0)             
                                        <li class="text-muted mb-3"><span class="mr-3"><i
                                                class="fas fa-times"></i></span>{{$standard->code}}</li>
                                     @endif           
                                     @endforeach           
                                 @endif            
                              @elseif(!empty($value->slug) && $value->slug == 'pro')
                                    
                                       @if(!empty($pro))
                                        @foreach($pro as $pro)
                                         @if($pro->optional == 1)
                                        <li class="mb-3"><span class="mr-3">
                                            <i class="fas fa-check text-primary"></i></span>{{$pro->code}}</li>
                                       @elseif($pro->optional == 0)             
                                            <li class="text-muted mb-3"><span class="mr-3"><i
                                                    class="fas fa-times"></i></span>{{$pro->code}}</li>
                                         @endif           
                                         @endforeach 
                                      @endif             
                               @elseif(!empty($value->slug) && $value->slug == 'advanced')

                                     @if(!empty($advanced))
                                        @foreach($advanced as $advanced)
                                         @if($advanced->optional == 1)
                                        <li class="mb-3"><span class="mr-3">
                                            <i class="fas fa-check text-primary"></i></span>{{$advanced->code}}</li>
                                       @elseif($advanced->optional == 0)             
                                            <li class="text-muted mb-3"><span class="mr-3"><i
                                                    class="fas fa-times"></i></span>{{$advanced->code}}</li>
                                         @endif           
                                         @endforeach 
                                      @endif 
                                                  
                              @endif              
                              </ul> */ ?>
                              <div class="btn-block tab-checkboox">
                                <input type="radio" id="<?php echo e($value->id); ?>" onclick="selectBudgetType('time')" name="plan_type" value="<?php echo e($value->id); ?>" checked="" hidden> 
                                <label for="<?php echo e($value->id); ?>"> Select </label>

<!-- 
 <input type="radio" name="plan_type" id="plan_type" value="<?php echo e($value->id); ?>" class="form-control"  >  Buy Now   -->
 <!-- <a href="javascript:void(0)" id="<?php echo e($value->id); ?>" class="btn-theme mw-100 plan_id">Buy now</a>  -->
</div>



</div>
</div>
</div>
<?php endif; ?>
<?php if(!Auth::user()): ?>
<div class="col-md-4 mb-4">
    <div class="card mb-5 mb-lg-0 rounded-lg shadow">
        <div class="card-header">
            <h5 class="card-title text-uppercase text-center"><?php echo e($value->name); ?></h5>
            <h6 class="h3 text-white text-center">$<?php echo e(number_format($value->price, 2)); ?>

                <span class="h6 text-white-50">
                    /<?php echo e($value->no_of_month); ?> <?php echo e(($value->no_of_month > 1) ? 'Months' :'Month'); ?></span></h6>
                </div>
                <div class="card-body bg-light rounded-bottom">
                    <?php echo $value->description; ?>

                            <?php /*
                            <ul class="list-unstyled mb-4">
                                 @if(!empty($value->slug) && $value->slug == 'standard')
                                   @if(!empty($standard))
                                    @foreach($standard as $standard)
                                     @if($standard->optional == 1)
                                    <li class="mb-3"><span class="mr-3">
                                        <i class="fas fa-check text-primary"></i></span>{{$standard->code}}</li>
                                   @elseif($standard->optional == 0)             
                                        <li class="text-muted mb-3"><span class="mr-3"><i
                                                class="fas fa-times"></i></span>{{$standard->code}}</li>
                                     @endif           
                                     @endforeach           
                                 @endif            
                              @elseif(!empty($value->slug) && $value->slug == 'pro')
                                    
                                       @if(!empty($pro))
                                        @foreach($pro as $pro)
                                         @if($pro->optional == 1)
                                        <li class="mb-3"><span class="mr-3">
                                            <i class="fas fa-check text-primary"></i></span>{{$pro->code}}</li>
                                       @elseif($pro->optional == 0)             
                                            <li class="text-muted mb-3"><span class="mr-3"><i
                                                    class="fas fa-times"></i></span>{{$pro->code}}</li>
                                         @endif           
                                         @endforeach 
                                      @endif             
                               @elseif(!empty($value->slug) && $value->slug == 'advanced')

                                     @if(!empty($advanced))
                                        @foreach($advanced as $advanced)
                                         @if($advanced->optional == 1)
                                        <li class="mb-3"><span class="mr-3">
                                            <i class="fas fa-check text-primary"></i></span>{{$advanced->code}}</li>
                                       @elseif($advanced->optional == 0)             
                                            <li class="text-muted mb-3"><span class="mr-3"><i
                                                    class="fas fa-times"></i></span>{{$advanced->code}}</li>
                                         @endif           
                                         @endforeach 
                                      @endif 
                                                  
                              @endif              
                              </ul> */ ?>
                              <div class="btn-block tab-checkboox">

                                <input type="radio" id="<?php echo e($value->id); ?>" onclick="selectBudgetType('time')" name="plan_type" value="<?php echo e($value->id); ?>" checked="" hidden> 
                                <label for="<?php echo e($value->id); ?>"> Select </label>
                                <!-- <input type="radio" name="plan_type" id="plan_type" value="<?php echo e($value->id); ?>" class="form-control"  >  Buy Now    -->
                                <!-- <a href="javascript:void(0)" id="<?php echo e($value->id); ?>" class="btn-theme mw-100 plan_id">Buy now</a>  -->
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-12">
                    <div class="coupon-code w-100">


                        <div class=" coupon-inner">
                            <div class="pr-3">
                                <h2> Add Coupon Code</h2>

                            </div>
                            <div class="coupon-input-wrap">
                                <div class="coupon-input">
                                    <input type="text" name="coupen_code" value="" id="coupen_code" placeholder="Coupon Code">
                                    <span id="coupen_error" class="help-inline error"></span>
                                </div>
                                <button class="coupon-sub-btn" id="coupen_check">Apply Code</button>
                                <button class="btn btn-danger ml-4" id="coupon_remove"  style="display:none;">Remove</button>
                            </div>



                        </div>
                        <div class="btn-block  text-center my-5">
                            <button class="coupon-sub-btn plan_id ">Proceed  Now</button>
                        </div>
                    </div>
                    <div class="btn-block  text-center my-5"><button class=""></button></div>
                </div>
                <input type="hidden" value=""  id="coupen_code_id">

            </div></div>
        </div>
        <?php endif; ?>   

        <script type="text/javascript">
         $(document).ready(function() {


            $('.plan_id').click(function() {
                var planid = $("input[name='plan_type']:checked").val();
                var coupen_code_id = $("#coupen_code_id").val();

                if(planid !='' &&  planid !=undefined){
                    $.ajax({
                        url: "<?php echo e(route('user.plan.submit')); ?>",
                        method: 'post',
                        data: { planid: planid , coupen_code: coupen_code_id},
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        beforeSend: function() {
                            $("#loader_img").show();
                        },
                        success: function(response){
                            $("#loader_img").hide();
                            if(response.success == true) {
                                window.location.href=response.page_redirect;
                            } 
                        }
                    });
                }else{
                 var mesg = 'Please select a plan to proceed';
                 show_message(mesg , 'error');
                 return false;
             }
         });

            $('#coupen_check').click(function() {
                $("#coupen_error").html('');
                var planid = $("input[name='plan_type']:checked").val();
                if(planid !='' &&  planid !=undefined){
                    var coupenCode = $("#coupen_code").val();
                    if(coupenCode==''){
                        $("#coupen_error").html('Please add coupen code');              
                    }else{

                        $.ajax({
                            url: "<?php echo e(route('user.checkCoupenCode')); ?>",
                            method: 'post',
                            data: { coupen_code: coupenCode},
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            beforeSend: function() {
                                $("#loader_img").show();
                            },
                            success: function(response){
                                $("#loader_img").hide();
                                if(response.success == true) {
                                 var codeid = btoa(response.data.id);
                                 $("#coupen_code_id").val(codeid); 

                                 show_message(response.mesg , 'success');
                                 $('#coupen_code').attr('readonly', true);
                                 $("#coupen_check").hide();
                                 $("#coupon_remove").show();


                             } else{
                               $("#coupen_error").html(response.mesg);
                           }
                       }
                   });
                    }
                }else{
                  var mesg = 'Please select a plan to proceed';
                  show_message(mesg , 'error');
                  return false;
              }

          });


            $('#coupon_remove').click(function() {
                $('#coupen_code_id').val('');
                $('#coupen_code').attr('readonly', false).val('');
                $("#coupen_check").show();
                $("#coupon_remove").hide();
                var mesg = 'Coupon code removed';
                show_message(mesg , 'success');

            });
        });



    </script>
    <?php $__env->stopSection(); ?>
<?php echo $__env->make('front.layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\xampp\htdocs\tinyhugs\resources\views/front/pages/pricing.blade.php ENDPATH**/ ?>