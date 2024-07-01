@extends('front.layouts.default')
@section('content')

<!-- <section class="about-us padding-section">
        <div class="container">
            <div class="heading py-lg-5 pb-3  text-center">
                <h4>Pricing</h4>
            </div>
        </div>
    </section>  -->
    @if(count($pakages) > 0)    
    <div class="pricing-table backg-img padding-section">
        <div class="container">
            <div class="heading py-lg-5 pb-3  text-center">

                <h4>Choose Your Plan</h4>
            </div>
            <div class="row py-5">
              @foreach($pakages as $key=>$value)
              @php
              $chanPan =  CustomHelper::checkUsertCurretntPlan($value->id);
              @endphp
              @if(Auth::user() && $chanPan == 1)
              <div class="col-md-4 mb-4">
                <div class="card mb-5 mb-lg-0 rounded-lg shadow">
                    <div class="card-header">
                        <h5 class="card-title text-uppercase text-center">{{$value->name}}</h5>
                        <h6 class="h3 text-white text-center">${{number_format($value->price, 2)}}
                            @if($value->no_of_month == 1)
                                <span class="h6 text-white-50">/Per Month</span>
                            @else 
                                <span class="h6 text-white-50">/{{ $value->no_of_month }} Months</span>
                            @endif
                        </h6>
                    </div>
                    <div class="card-body bg-light rounded-bottom">
                        {!! $value->description !!}
                    </div>


                    <div class="btn-block tab-checkboox">
                        <input type="radio" id="{{  $value->id }}" onclick="selectPlanType({{  $value->id }})" name="plan_type" value="{{  $value->id }}" hidden> 
                        <label for="{{  $value->id }}"> Select </label>
</div>

</div>
</div>
@endif
@if(!Auth::user())
<div class="col-md-4 mb-4">
    <div class="card mb-5 mb-lg-0 rounded-lg shadow">
        <div class="card-header">
            <h5 class="card-title text-uppercase text-center">{{$value->name}}</h5>
            <h6 class="h3 text-white text-center">${{number_format($value->price, 2)}}
                <span class="h6 text-white-50">
                    /Per Month</span></h6>
                </div>
                <div class="card-body bg-light rounded-bottom">
                    {!! $value->description !!}
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
                           
                        </div>
                        <div class="btn-block tab-checkboox">
                                                  
                            <input type="radio" id="{{  $value->id }}" onclick="selectPlanType({{ $value->id }})" name="plan_type" value="{{  $value->id }}"  hidden> 
                            <label for="{{  $value->id }}"> Select </label>
                            
                            <!-- <input type="radio" name="plan_type" id="plan_type" value="{{  $value->id }}" class="form-control"  >  Buy Now    -->
                            <!-- <a href="javascript:void(0)" id="{{  $value->id }}" class="btn-theme mw-100 plan_id">Buy now</a>  -->
                            </div>
                    </div>
                </div>
                @endif
                @endforeach
                <div class="col-md-12">
                    <div class="coupon-code w-100">


                        
                       
                    </div>
                    
                </div>
                <br>
                <br>
                <br>
                <br>

            </div>
        </div>
    </div>
        @endif   

        <script type="text/javascript">
         $(document).ready(function() {


            $('.plan_id').click(function() {
                var planid = $("input[name='plan_type']:checked").val();
                var coupen_code_id = $("#coupen_code_id").val();

                if(planid !='' &&  planid !=undefined){
                    $.ajax({
                        url: "{{ route('user.plan.submit')}}",
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
                            url: "{{ route('user.checkCoupenCode')}}",
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

        function selectPlanType(planid){
            if(planid !='' &&  planid !=undefined){
                    $.ajax({
                        url: "{{ route('user.plan.submit')}}",
                        method: 'post',
                        data: { planid: planid},
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
        }


    </script>
    @endsection