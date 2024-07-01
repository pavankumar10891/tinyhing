<?php $newsletterHeading = CustomHelper::get_newsletter_heading();  ?>

<section class="counter-sec newletter_wrap">
    <div class="container">
        <div class=" newletter_inner">
            <div class="pr-3">
                <h2> <?php echo !empty($newsletterHeading->description) ? strip_tags($newsletterHeading->description):''; ?></h2>
                <h5><?php echo e(!empty($newsletterHeading->name) ? $newsletterHeading->name:''); ?></h5>
            </div>
            <div class="newletter-input-wrap">
                <div class="newletter-input">
                   <input name="_token" type="hidden" value="<?php echo e(csrf_token()); ?>"/>
                    <?php echo e(Form::text('email','', [ 'id' =>'newletter_email', 'class' => ''.($errors->has('email') ? 'is-invalid':'') , 'placeholder'=>'Enter Your Email Id'])); ?>  
                    <span id="email_error" class="help-inline error news_email_error"></span>
                </div>
                    
                <?php echo e(Form::button('Subscribe Now', ['type' => 'button', 'class' => 'newletter-sub-btn', 'id' => 'newsletter'])); ?>

               
            </div>
            
          <!--  <div class="newletter-input-wrap">
                <div class="newletter-input">
                    <input type="text" name="email" id="" placeholder="Enter Your Email Id"> 
                   
                </div>
               
               <button class="newletter-sub-btn ">Subscribe Now</button> 
              
            </div>   -->
        </div>
    </div>
</section>

<footer class="footer_wrapper">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="navbar-footer text-center pb-md-4">
                   <?php echo $__env->make('front.footer_links.footer_links', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
        <ul class="list-unstyled social_link_bar py-md-4 ">
            <li>
                <a href="<?php echo e(Config::get('Social.facebook_url')); ?>" target="_blank" class="facebook-bx">
                    <i class="fab fa-facebook-f"></i>
                </a>
            </li>
            <li>
                <a href="<?php echo e(Config::get('Social.youtube_url')); ?>" target="_blank" class="youtube-bx">
                    <i class="fab fa-youtube"></i>
                </a>
            </li>
            <li>
                <a href="<?php echo e(Config::get('Social.twitter_url')); ?>" target="_blank" class="twitter-bx">

                    <i class="fab fa-twitter"></i>
                </a>
            </li>

        </ul>
    </div>

    <div class="container">
        <div class="copyright">
            <span>
                <a href="<?php echo e(url('/')); ?>"> tinyhugs.net</a> © <?php echo date("Y");?>. All Rights Reserved </span>
        </div>
    </div>


    </div>
</footer>
<!-- end get quote model -->
<!-- Modal -->
<div class="modal fade price-moodel" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog  modal-dialog-centered" role="document">
    <div class="modal-content">
      <!-- <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div> -->
        <div class="modal-body">
        <div class="text-center py-4">
        <div class="price-block">    Based on the specifications, the weekly rate for your nanny would be <span class="h5 text-center estimateprice">$0.00</span>
            </br>
                <span class="h6">
                An additoinal
                montly subscription fee to ensure your 
                child receives the best care is also
                listed below, featuring various included services.</span> 
                <!-- <a  data-toggle="tooltip" data-placement="bottom" title="Tooltip on bottom">
                <svg aria-hidden="true" focusable="false" data-prefix="fal" data-icon="info-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-info-circle fa-w-16 fa-2x"><path fill="currentColor" d="M256 40c118.621 0 216 96.075 216 216 0 119.291-96.61 216-216 216-119.244 0-216-96.562-216-216 0-119.203 96.602-216 216-216m0-32C119.043 8 8 119.083 8 256c0 136.997 111.043 248 248 248s248-111.003 248-248C504 119.083 392.957 8 256 8zm-36 344h12V232h-12c-6.627 0-12-5.373-12-12v-8c0-6.627 5.373-12 12-12h48c6.627 0 12 5.373 12 12v140h12c6.627 0 12 5.373 12 12v8c0 6.627-5.373 12-12 12h-72c-6.627 0-12-5.373-12-12v-8c0-6.627 5.373-12 12-12zm36-240c-17.673 0-32 14.327-32 32s14.327 32 32 32 32-14.327 32-32-14.327-32-32-32z" class=""></path></svg>
                </a> -->
                </span>
        </div>
          
            <div class="radio-block mt-3  d-md-inline-flex">
                    <div class="custom-radio pr-3">
                            <input type="radio" id="actionInterview" name="action">
                            <label for="actionInterview">Schedule Interview</label>
                                            </div>
                                            <div class="custom-radio">
                            <input type="radio" id="actionPricing" name="action" value='pricing' checked>
                            <label for="actionPricing">Pricing</label>
                    </div>
    
                        <!-- <input type="radio" name='action' id="actionInterview" value='interview' checked/> Schedule Interview
                        <input type="radio" name='action' id="actionPricing" value='pricing'/> Pricing -->
            </div>

            <div class="btn-block mt-4">
                <a href="javascript::void(0)" class="btn-theme proceedNowBtn">Proceed Now</a>
            </div>
        </div>
        </div>
    
    </div>
  </div>
</div>

<div class="modal fade price-moodel" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog  modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="modal-body">
        <div class="text-center py-4">
            <div class="price-block">  Based on the specifications, the hourly rate for your babbysitter would be <span class="h5 text-center estimateprice">$0.00</span>
                </br>
                    <!-- <span class="h6">
                    An additoinal montly subscription fee to ensure your child receives the best care is also listed below, featuring various included services.</span> --> 
            </div>
            <!-- <div class="radio-block mt-3  d-md-inline-flex">
                    <div class="custom-radio pr-3">
                        <input type="radio" id="actionInterview" name="action">
                        <label for="actionInterview">Schedule Interview</label>
                                        </div>
                                        <div class="custom-radio">
                        <input type="radio" id="actionPricing" name="action" value='pricing' checked>
                        <label for="actionPricing">Pricing</label>
                    </div>
            </div> -->

            <div class="btn-block mt-4">
                <a href="<?php echo e(url('/our-nannies?type=babbysitter')); ?>" class="btn-theme proceedNowBtn">Select Babysitter</a>
            </div>
        </div>
        </div>
    </div>
  </div>
</div>

<form id="quoteform">
  <input type="hidden" name="children_value" id="children_value" value="">
  <input type="hidden" name="week_value" id="week_value" value=""> 
 </form>
<!-- end get quote model -->

<a href="javascript:void(0);" class="back_top">
    <i class="ion-ios-arrow-thin-up"></i>
</a>



<script type="text/javascript">
    $(document).ready(function() {
        $('#newsletter').click(function() {
            var newsletteremail = $('#newletter_email').val();
           
            $.ajax({
                url: "<?php echo e(url('newsletter/')); ?>",
                method: 'post',
                data: { email : newsletteremail } ,
                headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                beforeSend: function() {
                    $("#loader_img").show();
                },
                success: function(response){
                    $("#loader_img").hide();
                
                    if(response.success == 1) {
                      //console.log(response.);
                        window.location.href="<?php echo e(url('/')); ?>";
                        // window.location.href=response.page_redirect;
                       //location.reload();
                    }else if(response.success == "2") {
                        $(".news_email_error").html(response.errors)
                    }else {
                        $('span[id*="_error"]').each(function() {
                            var id = $(this).attr('id');
                            if(id in response.errors) {
                                $("#"+id).html(response.errors[id]);
                            } else {
                                $("#"+id).html('');
                            }
                        });
                    }
                }
            });
        });


       $('#zipcode_search').click(function() {
            var zipcode = $('#zipcode_val').val();
           
            window.location.href="<?php echo e(url('/our-nannies/?zipcode=')); ?>"+zipcode;
           // location.reload();

        });

    });
        
</script>

<script type="text/javascript">


  $(function() {
        $('#user-quote-form').keypress(function(e) { //use form id
            if (e.which == 13) {
              login();
            }
        });
    });
    function quote(id){
       // alert(id) 
   /* var child = $(".children_value").val();
    var week = $(".week_value").val();
    var duration = $(".duration_value").val();
    var type = $(".babysittertype").val();
   
    var date_time = $('.date_time_'+id).val();
    alert(date_time);*/
   var formData = $('.formData_'+id).serialize();
   //alert(formData);
      $.ajax({
            url: "<?php echo e(route('user.quote')); ?>",
            method: 'post',
            data:  formData,//{ children:child,weeks: week,duration:duration,type:type,date_time:date_time},
            headers: {
             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
            beforeSend: function() {
                $("#loader_img").show();
            },
            success: function(response){
                $("#loader_img").hide();
            
                if(response.success == true) {
                    $('.estimateprice').html(response.data.price);
                    if(response.data.type == 2){
                       $('#exampleModal2').modal('show'); 
                   }else{
                    $('#exampleModal').modal('show');
                   }
                    

                   // window.location.href=response.page_redirect;
                   //location.reload();
                }else if(response.success == 2){
                  show_message(response.message,'error');
                }else if(response.success == false){
                   $('.children_error').html(response.errors.children_error);
                   $('.weeks_error').html(response.errors.children_error);
                  //location.reload();
                }  else {

                    $('span[id*="_error"]').each(function() {
                        var id = $(this).attr('id');

                        if(id in response.errors) {
                            $("#"+id).html(response.errors[id]);
                        } else {
                            $("#"+id).html('');
                        }
                    });
                }
            }
        });
    }
    $(".proceedNowBtn").on('click',function(){
        if($("#actionInterview").prop('checked')==true){
            window.location.href="<?php echo e(route('user.nannylist')); ?>";
        }else{
            window.location.href="<?php echo e(route('user.pricing')); ?>";

        }
    });


    

    function getCurrentLocation()
        {
            
        $.ajax({
            url    : '<?php echo e(route("user.current.location")); ?>',
            method : "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, 
                beforeSend: function() {
                $("#loader_img").show();
            },
            success : function (res)
            {
                $("#loader_img").hide();
                if(res.data != '') 
                {
                    $('#zipcode_val').val(res.data);
                    }
                else
                {
                    alert(res.mesg);
                    return false;
                } 
            }
        });
    
        }
    $(document).ready(function() {
        /*$('#getquote').click(function() {
            quote();
        });*/
        $('.getquote').click(function() {
            quote();
        });

         $('.babbysitter_getquote').click(function() {
            quote($(this).attr('data-id'));
        });

        $('.children').change(function() {
            var chldval= $(this).val();
            $(".children_value").val(chldval);
        });

        $('.duration').change(function() {
            var chldval= $(this).val();
            $(".duration_value").val(chldval);
        });

        $('.weeks').change(function() {
            var weekdval= $(this).val();
           $(".week_value").val(weekdval);
       });

        $('.date_time').keypress(function() {
            var weekdval= $(this).val();
           $(".date_time").val(weekdval);
       }); 

       /* $(function() {
          $('.datetimepicker1').datetimepicker();
        });*/

      


       
    });
</script><?php /**PATH F:\xampp\htdocs\tinyhugs\resources\views/front/elements/footer.blade.php ENDPATH**/ ?>