@extends('front.layouts.default')
@section('content')   
<link rel="stylesheet" href="{{WEBSITE_CSS_URL}}jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<nav aria-label="breadcrumb" class="padding-section">
        <div class="breadcrumb-block">
            <div class="container">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{url('/our-nannies')}}">Our Nannies</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Profile</li>
                </ol>
            </div>
        </div>
    </nav>
    <div class="profile-detail backg-img padding-bottom">
        <div class="container">
            <div class="profile-block" >

                <div class="row align-items-center">
                    @if(!empty($nannyProfile->photo_id) && file_exists(USER_IMAGE_ROOT_PATH.$nannyProfile->photo_id))
                    <div class="col-md-3">
                        <?php $image =  USER_IMAGE_URL.$nannyProfile->photo_id; ?>
                        <div class="img-wall" style="background-image: url({{$image}})">
                            <img src="{{$image}}" class="img-fluid">
                        </div>
                    </div>
                    @else
                    <div class="col-md-3">
                        <?php $image =  WEBSITE_IMG_URL.'no-female.jpg'; ?>
                        <div class="img-wall" style="background-image: url({{$image}})">
                            <img src="{{$image}}" class="img-fluid">
                        </div>
                    </div>
                    @endif
                    <div class="col pl-3">
                        <div class="profile-info">
                            <div class="d-md-flex align-items-center">
                                <div class="heading d-flex pb-2 pb-md-0 ">
                                    <h4>{{!empty($nannyProfile->name) ? $nannyProfile->name:''}}</h4>
                                    <span>{{ !empty($nannyProfile->cpr_certificate) ? 'CPR Certificate'  : '' }}</span>
                                </div>

                                 <?php  if(!empty(Auth::user())){   ?>
                                    <div class="flex-grow-1 text-md-right">
                                    <a href="javascript:void(0)" class="btn-theme text-white  schedule_interview">Schedule</a>
                                    </div>

                                    <?php   }else{  ?>

                                    <div class="flex-grow-1 text-md-right">
                                    <a href="javascript:void(0)" class="btn-theme text-white nanny_interview">Schedule</a>
                                    </div>

                                    <?php   }   ?>
                                <!--<div class="flex-grow-1 text-md-right">

                                    <a class="btn-theme text-white">Schedule</a>

                                </div>   -->
                            </div>
                            <ul>
                                <li>
                                    <svg style="width: 22px" aria-hidden="true" focusable="false" data-prefix="fas"
                                        data-icon="clock" role="img" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 512 512" class="svg-inline--fa fa-clock fa-w-16 fa-2x">
                                        <path fill="currentColor"
                                            d="M256,8C119,8,8,119,8,256S119,504,256,504,504,393,504,256,393,8,256,8Zm92.49,313h0l-20,25a16,16,0,0,1-22.49,2.5h0l-67-49.72a40,40,0,0,1-15-31.23V112a16,16,0,0,1,16-16h32a16,16,0,0,1,16,16V256l58,42.5A16,16,0,0,1,348.49,321Z"
                                            class=""></path>
                                    </svg> <label>Available</label>
                                </li>
                                <?php if($nannyProfile->city !='' ||  $nannyProfile->state!='' ){   ?>
                                <li>
                                    <svg aria-hidden="true" focusable="false" data-prefix="fas"
                                        data-icon="map-marker-alt" role="img" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 384 512" class="svg-inline--fa fa-map-marker-alt fa-w-12 fa-2x">
                                        <path fill="currentColor"
                                            d="M172.268 501.67C26.97 291.031 0 269.413 0 192 0 85.961 85.961 0 192 0s192 85.961 192 192c0 77.413-26.97 99.031-172.268 309.67-9.535 13.774-29.93 13.773-39.464 0zM192 272c44.183 0 80-35.817 80-80s-35.817-80-80-80-80 35.817-80 80 35.817 80 80 80z"
                                            class=""></path>
                                    </svg>
                                    <label>{{ !empty($nannyProfile->city) ?$nannyProfile->city : '' }} <?php if($nannyProfile->city!='' AND $nannyProfile->state!='' ){ echo '/'; }  ?> {{ !empty($nannyProfile->state) ?$nannyProfile->state : '' }}</label>
                                </li>
                                <?php   }  ?>
                                <?php if($nannyProfile->experience !=''){   ?>
                                <li>
                                 <label> Experience:</label>
                                <strong> {{  $nannyProfile->experience.' Years'  }}</strong>
                               </li>
                               <?php   }  ?>
                            </ul>
                            <div class="stars">
                                <strong> 4.5</strong>
                                <span class="satrimg">
                                <img src="{{WEBSITE_IMG_URL}}star.png">
                                <img src="{{WEBSITE_IMG_URL}}star.png">
                                <img src="{{WEBSITE_IMG_URL}}star.png">
                            <img src="{{WEBSITE_IMG_URL}}star.png">
                            <img src="{{WEBSITE_IMG_URL}}star.png">
                                </span>
                                <span class="text"> (200 Reviews)</span>
                            </div>
                            <p>
                                {{ !empty($nannyProfile->description) ? $nannyProfile->description  : '' }} </p>
                        </div>
                    </div>
                </div>
                <div class="rating-sec">
                    <div class="row">
                        <div class="col-lg-3 mb-md-3">
                            <div class="bg-white p-4">
                               @if(!empty($nannyProfile->cpr_certificate) ||  !empty($nannyProfile->other_certificates)  )
                              <a href="javascript:void(0)" data-toggle="modal" data-target="#exampleModal" > 
                                <div class="doc-block text-center mb-3">
                                    <img src="{{WEBSITE_IMG_URL}}certificate.png">
                                    <h4>View All Certificate</h4>
                               </div>
                               </a>
                                @endif 
                                @if(!empty($nannyProfile->resume))
                                @php($resume = CERTIFICATES_AND_FILES_URL.$nannyProfile->resume  )
                                <a href="{{ $resume}}" target="_blank"><div class="doc-block text-center">
                                    <img src="{{WEBSITE_IMG_URL}}resume.png">
                                    <h4>View Resume</h4>
                                </div></a>
                                 @endif

                                <?php  
                               
                                if(!empty(Auth::user())){   ?>
                                    <div class="btn-block py-3">
                                    <a href="javascript:void(0)" class="btn-theme text-white mw-100 schedule_interview">Schedule Interview</a>
                                    </div>

                                    <?php   }else{  ?>

                                    <div class="btn-block py-3">
                                    <a href="javascript:void(0)" class="btn-theme text-white mw-100 nanny_interview">Schedule Interview</a>
                                    </div>

                                    <?php   }   ?>
                               
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <div class="rating-block">
                                    <div class="rating-block-heading">Ratings</div>
                                    <div class="row">
                                        <div class="col-md-auto col-xl-4">

                                            <div class="stars text-center">
                                                <strong> 4.5</strong>
                                                <div class="satrimg">
                                                    <img src="{{WEBSITE_IMG_URL}}star.png">
                                                    <img src="{{WEBSITE_IMG_URL}}star.png">
                                                    <img src="{{WEBSITE_IMG_URL}}star.png">
                                                <img src="{{WEBSITE_IMG_URL}}star.png">
                                                <img src="{{WEBSITE_IMG_URL}}star.png">
                                                  </div>
                                               
                                                <span class="text">Based on 200 Reviews</span>
                                            </div>
                                        </div>
                                        <div class="col">


                                        <div class="progress-block">
                                           <div class="progress-heading"> excellent</div>
                                            <div class="progress">
                                            
                                                <div class="progress-bar bg-success" role="progressbar"
                                                 style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                              </div>
                                        </div>

                                        <div class="progress-block">
                                        
                                        <div class="progress-heading"> Good</div>
                                            <div class="progress">
                                           
                                                <div class="progress-bar bg-green" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                             
                                        </div>      </div>
                                        
                                        
                                        <div class="progress-block">
                                            <div class="progress-heading"> average</div>
                                        
                                        <div class="progress">
                                        
                                                <div class="progress-bar bg-warning" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                        
                                        </div>    </div>


                                        <div class="progress-block">
                                            <div class="progress-heading">Below   average</div>
                                 
                                            <div class="progress">
                                      
                                                <div class="progress-bar bg-orange" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                              </div>    </div>

                                              <div class="progress-block">
                                                <div class="progress-heading">   Poor</div>

                                              <div class="progress">
                                               <div class="progress-bar bg-red" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                                 </div>
                                              </div>   
                                            </div>
                                        </div>
                           

                      <div class="review-block">
                        <div class="rating-block-heading pt-5">Ratings</div>
                                        <div class="card-horizontal no-gutters">
                                            <div class="col-auto">
                                                <div class="img-wall1">
                                                    <img src="{{WEBSITE_IMG_URL}}profile-2.png" class="img-fluid">
                                                </div>
                                                <h4 class="card-title">Jessica Smith</h4>
                                                <span>
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                </span>
                                            </div>
                                            <div class="col pl-lg-3">
                                            
                
                                                    <div class="update-post text-md-right">
                                                    
                                                     25/10/2010 | 5:30pm
                                                       
                                                    </div>
                                              
                                                <p class="card-text">
                                                    Reference site about Lorem Ipsum, giving information on its origins, as well as a random Lipsum generator.Reference site about Lorem Ipsum, giving information on its origins, as well as a random Lipsum generator.</p>
                                            </div>
                                        </div>
                                        <div class="card-horizontal no-gutters">
                                            <div class="col-auto">
                                                <div class="img-wall1">
                                                    <img src="{{WEBSITE_IMG_URL}}profile-2.png" class="img-fluid">
                                                </div>
                                                <h4 class="card-title">Jessica Smith</h4>
                                                <span>
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                </span>
                                            </div>
                                            <div class="col pl-lg-3">
                                            
                
                                                    <div class="update-post text-md-right">
                                                    
                                                     25/10/2010 | 5:30pm
                                                       
                                                    </div>
                                              
                                                <p class="card-text">
                                                    Reference site about Lorem Ipsum, giving information on its origins, as well as a random Lipsum generator.Reference site about Lorem Ipsum, giving information on its origins, as well as a random Lipsum generator.</p>
                                            </div>
                                        </div>
                                     </div>                                    
                               </div>
                          </div>
                    </div>
                </div>
                <div class="listing-img profile-img">
                    <img src="{{WEBSITE_IMG_URL}}line.png" class="line-img ab-img">
                    <img src="{{WEBSITE_IMG_URL}}triangle.png" class="triangle-img ab-img">
                      <img src="{{WEBSITE_IMG_URL}}line1.png" class="line1-img ab-img">
                    <img src="{{WEBSITE_IMG_URL}}close.png" class="close-img ab-img">
                    </div>

               </div>
             </div>
         </div>
<!-------------------------CERTIFICATE LIST MODAL START-------------------------------->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">All Certificate</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
           @if(!empty($nannyProfile->cpr_certificate))
            @php($crp = CERTIFICATES_AND_FILES_URL.$nannyProfile->cpr_certificate  )
            <h5>CRP Certificte</h5>
            <a href="{{ $crp}}" target="_blank"><div class="doc-block text-center">
                <img src="{{WEBSITE_IMG_URL}}resume.png">
                <h4>View Certificte</h4>
            </div></a>
            @endif

            @if(!empty($nannyProfile->other_certificates))
            @foreach($nannyProfile->other_certificates as $otherCertificate)
            @php($othercrp = OTHER_CERTIFICATES_DOCUMENT_URL.$otherCertificate->other_certificates  )
            <h5>Other Certifictes</h5>
            <a href="{{ $othercrp}}" target="_blank"><div class="doc-block text-center">
                <img src="{{WEBSITE_IMG_URL}}resume.png">
                <h4>View Certificte</h4>
            </div></a>
            @php($othercrp =  '')
            @endforeach
            @endif
       

        </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
    </div>
  </div>
</div>
<!-------------------------CERTIFICATE LIST MODAL END-------------------------------->
<!--------------------------------SIGN UP FORM MOADAL START------------------------------------------------------->
<div class="modal fade registration-block" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            {{ Form::open(array('id' => 'user-registration-form', 'class' => 'form')) }}
            <div class="modal-header">
                 <h5 class="modal-title" id="myModalLabel">Enter Your Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
           

                <div class="form-group">
                <label>Name</label>
                    <div class="theme_input">
                       {{ Form::text('first_name', (! empty(Request::old('first_name'))) ? Request::old('first_name') : '', ['placeholder' => trans("Name"), 'class'=>'form-control']) }}
                    </div>
                    <span id="first_name_error" class="help-block"></span>
                </div>

                <div class="form-group">
                <label>Email</label>
                    <div class="theme_input">
                       {{ Form::text('email', (! empty(Request::old('email'))) ? Request::old('email') : '', ['placeholder' => trans("Email"), 'class'=>'form-control']) }}
                    </div>
                    <span id="email_error" class="help-block"></span>
                </div>

                <div class="form-group">
                <label>Phone (optional)</label>
                    <div class="theme_input">
                       {{ Form::text('phone_number', (! empty(Request::old('phone_number'))) ? Request::old('phone_number') : '', ['placeholder' => trans("Phone Number"), 'class'=>'form-control']) }}
                    </div>
                    <span id="phone_number_error" class="help-block"></span>
                </div>
            </div>
            <div class="modal-footer">
                <div class="btn-block text-right">
               {{ Form::button(trans("Submit"), ['class' => 'btn-theme text-white', 'type' => 'button', 'id' => 'user-register']) }}
            </div>
            </div>
            {{Form::close()}}
        </div>
    </div>
</div>

<!--------------------------------END SIGN UP FORM MOADAL------------------------------------------------------->

<!-------------------------------INTERVIEW MOADAL OPEN------------------------------------------------------->

<div class="modal fade registration-block" id="schedul_interview" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          
            <div class="modal-header">
                 <h5 class="modal-title" id="myModalLabel">Schedule Interview</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                <input type="hidden" id="in_first_name" name="in_first_name" value="" >
                <input type="hidden" id="in_email" name="in_email" value="" >
                <input type="hidden" id="in_phone_number" name="in_phone_number" value="" >
                <label>Select a Date</label>
                    <div class="theme_input">
                    <input type="text" class="form-control" id="date" name="trip-start" value="" placeholder="YYYY-MM-DD" readonly>
                  <!--  <span class="help-inline error date_error"></span>-->
                    <span id="date_error" class="help-inline error"></span>
                    </div>
                </div>

                <div class="time_slots_list">
                
                </div>
              
              <div>

              </div>
          

            </div>
            <div class="modal-footer">
                <div class="btn-block text-right">
                <a href="javascript:void(0);" class="btn-theme submit add_holiday">Submit</a>
            </div>
            </div>
         
        </div>
    </div>
</div>

<!-------------------------------INTERVIEW MOADAL END------------------------------------------------------->


<script type="text/javascript">

function signUp(){
    var form = $("#user-registration-form").closest("form");
    var formData = new FormData(form[0]);
    $.ajax({
        url: "{{ route('user.userCheckInfo')}}",
        method: 'post',
        //data: $('#user-registration-form').serialize(),
        data: formData,
        contentType: false,       
        cache: false,             
        processData:false, 
        beforeSend: function() {
            $("#loader_img").show();
        },
        success: function(response){
            $("#loader_img").hide();
        
            if(response.success) {
                $("#in_first_name").val(response.first_name);
                $("#in_email").val(response.email);
                $("#in_phone_number").val(response.phone_number);
                $('#myModal').modal('hide');
                $('#schedul_interview').modal('show');
               // window.location.href=response.page_redirect;
            } else {
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

  $(function() {
        $('#user-registration-form').keypress(function(e) { //use form id
            if (e.which == 13) {
               //-- to validate form 
             signUp();
                return false;
            }
        });
    });
</script>
<script>
        $(document).ready(function () {

        $(".nanny_interview").click(function(){
              
                 $('#myModal').modal('show');
            });


        $(".schedule_interview").click(function(){

                $("#selected_nany_id").val('');
                $(".time_slots_list").html('');
                var nanny_id  =  <?php echo $nannyProfile->id; ?>; 
                $("#selected_nany_id").val(nanny_id);  
                $('#schedul_interview').modal('show');
        
         });    

        $('#user-register').click(function() {
            signUp(); 
            });       
          });





        function gettimeSlots(date){
        //    var id  =  document.getElementsByClassName('nanny')[0].value;
           var nannyId  =  <?php echo $nannyProfile->id; ?>; 
           if(nannyId!=''){
           $.ajax({
                url    : '{{ route("user.time.slots") }}',
                method : "POST",
                data:{date:date , nanny_id:nannyId},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }, 
                    beforeSend: function() {
                    $("#loader_img").show();
                },
                success : function (res)
                {
                    $("#loader_img").hide();
                   
                    if(res.success==true){
                        $(".add_holiday").css("display", "block");
                        $(".time_slots_list").html(res.data);

                    } else{
                        
                        $('.add_holiday').css('display','none');
                        $(".time_slots_list").html("<span class='help-inline error'>"+res.mesg+"</span>");
                        
                    }
             
                }
            });

           }else{

               alert("PLease select nanny for schedule interview");
               return false;
           }
         
            
        }
    
        $(function() {
            $("#date").datepicker({
               changeYear: true,
                changeMonth:true,
                dateFormat: 'yy-mm-dd',
                minDate:new Date(),
                onSelect:function(selectedDate)
                 {
                     if(selectedDate != ''){
                        
                        gettimeSlots(selectedDate);
                     }
                      
                 }
                //minDate:  Date.now()
            });
        });     

        $(".add_holiday").click(function(){

                var date = $("#date").val();
                var nanny_id = <?php echo $nannyProfile->id; ?>; 
                var time_slot_id = $("input[name='time_slot']:checked").val();
                var in_first_name = $("#in_first_name").val();
                var in_email = $("#in_email").val();
                var in_phone_number = $("#in_phone_number").val();

                $.ajax({
                url    : '{{ route("user.schedule.interview") }}',
                method : "POST",
                data:{date:date , nanny_id:nanny_id ,time_slot_id:time_slot_id,first_name:in_first_name,email:in_email,phone_number:in_phone_number },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }, 
                    beforeSend: function() {
                    $("#loader_img").show();
                },
                success : function (res){
                    $("#loader_img").hide();
                    if(res.success==true){
                        $('#schedul_interview').modal('hide');
                        location.reload();
                      } else{
                        //alert(res.errors.date_error);
                        $('span[id*="_error"]').each(function() {
                            var id = $(this).attr('id');

                            if(id in res.errors) {

                                $("#"+id).html(res.errors[id]);
                            } else {
                                $("#"+id).html('');
                            }
                        });
                    }
             
                }
            });
    });
</script>


@endsection
@section('scripts')
@endsection
    

