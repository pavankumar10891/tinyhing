@extends('front.layouts.default')
@section('content')
<?php //echo "<pre>";print_r($lists);die; ?>
<link rel="stylesheet" href="{{WEBSITE_CSS_URL}}jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<section class="our-nanny  backg-img padding-section">
	<div class=" container">
		<div class="listing-page">
			<section class="search-block">

				<div class="heading py-lg-5 py-4 text-center">

					<h3>Search Nannies Near You</h3>
				</div>
				<div class="location-sec mt-0">
					<div class="">
						<div class="d-flex">
							<div class=" fields-bx">
								<div class="location-input-wrap">
									<div class="location-input">
										<svg aria-hidden="true" focusable="false" data-prefix="fal" data-icon="search"
										role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
										class="svg-inline--fa fa-search fa-w-16 fa-2x">
										<path fill="currentColor"
										d="M508.5 481.6l-129-129c-2.3-2.3-5.3-3.5-8.5-3.5h-10.3C395 312 416 262.5 416 208 416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c54.5 0 104-21 141.1-55.2V371c0 3.2 1.3 6.2 3.5 8.5l129 129c4.7 4.7 12.3 4.7 17 0l9.9-9.9c4.7-4.7 4.7-12.3 0-17zM208 384c-97.3 0-176-78.7-176-176S110.7 32 208 32s176 78.7 176 176-78.7 176-176 176z"
										class=""></path>
									</svg>
									<input type="text" name="zipcode" id="zipcode_val" value="<?php echo  (isset($zipcode))? $zipcode : '' ?>"  id="" placeholder="Enter Your Zip/Postal Code">
								</div>
								<div class="location-button">
									<button type="button" id="zipcode_search" class="location-sub-btn location-search "><i class="fas fa-search"></i>Search</button>
									<button class="location-sub-btn ml-md-3" onclick=" return getCurrentLocation()" ><i class="fas fa-paper-plane"></i>Detect your
									location</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

		</section>

		<section class="list-block">
			<div class="sortby">
				<form action="" id="sor_by_form" method="get" >
					<input type="hidden" name="sort_by"  id="sortBy"   value="">
					<div class="sortby-block">
						<label>
							Sort By:
						</label>
						<ul>   
							<?php  $request  ?>
							<li class="<?php echo  (isset($_REQUEST['sort_by']) && $_REQUEST['sort_by'] == 1 )  ? 'active' : '' ?>"><a href="javascript:void(0)" id="1" class="sortBy"> Near Me</a></li>
							<li class="<?php echo  (isset($_REQUEST['sort_by']) && $_REQUEST['sort_by'] == 2 )  ? 'active' : '' ?>"><a href="javascript:void(0)" id="2" class="sortBy"> Newly Added</a></li>
							<li class="<?php echo  (isset($_REQUEST['sort_by']) && $_REQUEST['sort_by'] == 3 )  ? 'active' : '' ?>"><a href="javascript:void(0)" id="3" class="sortBy"> Most Rated</a></li>
						</ul>
					</div>
				</form>
			</div>
			<div class="listing padding-bottom">
				<div class="heading pb-md-4 pb-3  ">

					<h3>Listing Our Nannies</h3>
				</div>
				<div class="row listingnanny">

					@foreach($lists as $listsk=>$listsv)
	                    <div class="col-sm-6 col-lg-4 mb-md-5 mb-4">
							<a href="{{ route('user.nanny.profile', base64_encode($listsv->id)) }}">
								<div class="bg-white mr-md-auto">
									<?php
									if(!empty($listsv->photo_id) && file_exists(USER_IMAGE_ROOT_PATH.$listsv->photo_id)){
										$image = USER_IMAGE_URL.$listsv->photo_id;   
									}else{
										$image =  WEBSITE_IMG_URL.'no-female.jpg';
									} 
									?>
									<div class="img-wall" style="background-image: url({{$image}})">
										<img src="{{WEBSITE_URL.'image.php?width=253px&height=226px&cropratio=3:2&image='.$image}}" class="w-100" alt="">
									</div>
									<div class="text-block">
										<div class="d-flex align-items-center">
											<h3>{{!empty($listsv->name) ? $listsv->name:''}}</h3>
											<div class="rating-block">
												<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="star"
													role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"
													class="svg-inline--fa fa-star fa-w-18 fa-2x">
													<path fill="currentColor"
													d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z"
													class=""></path>
												</svg>
												4.3
											</div>
										</div>
										<ul class="">
											<li> <label>age:</label><strong>{{  !empty($listsv->age) ? $listsv->age  : '' }}</strong> </li>
											<li> <label> Exp:</label><strong>{{ !empty($listsv->experience) ? $listsv->experience.' Years'  : '' }} </strong></li>
										</ul>
										<div class="">
											<p>{{ !empty($listsv->description) ? $listsv->description  : '' }}</p>
										</div>
									</div>
									<input type="hidden" name="nanny" class="nanny" value="{{$listsv->id}}">
									<div class="btn-block mt-1 text-center">
										@if(!empty(Auth::user()))
											<a href="javascript:void(0);" class="btn-theme mw-100 schedule_interview" id="{{$listsv->id}}" >
												Schedule Interview
											</a>
										 @else
											<a href="javascript:void(0);" class="btn-theme mw-100 nanny_interview" id="{{$listsv->id}}" >
												Schedule Interview
											</a>
										@endif
									</div>
								</div>
							</a>
						</div>
                    @endforeach
	                    <input type="hidden" value="<?php  echo $offset; ?>"   id="offset">
						@if(count($lists) > 8)
		                 <div id="remove-row"  class="btn-block text-center pb-5 load-more loadMoreBtnDV" >
		                 	<a href="javascript:void(0);" onclick="getMoreJobs();" id="loadMoreBtn" class="btn-theme">Load More</a>
		                 </div>
	                 	@endif
                </div>
			</div>
			

             </div>


         </section>
     </div>

 </div>
 <div class="listing-img">
 	<img src="{{WEBSITE_IMG_URL}}line.png" class="line-img ab-img">
 	<img src="{{WEBSITE_IMG_URL}}triangle.png" class="triangle-img ab-img">
 	<img src="{{WEBSITE_IMG_URL}}line1.png" class="line1-img ab-img">
 	<img src="{{WEBSITE_IMG_URL}}close.png" class="close-img ab-img">
 </div>
</div>

</section>

<input type="hidden" name="" value="" id="selected_nany_id">

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

                <!-- <div class="form-group">
                    <div class="theme_input">
                       {{ Form::text('last_name', (! empty(Request::old('last_name'))) ? Request::old('last_name') : '', ['placeholder' => trans("Last Name"), 'class'=>'form-control']) }}
                    </div>
                    <span id="last_name_error" class="help-block"></span>
                </div> -->

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

               <!-- <div class="form-group">
                    <div class="theme_input">
                       {{ Form::text('postcode', (! empty(Request::old('postcode'))) ? Request::old('postcode') : '', ['placeholder' => trans("Post Code"), 'class'=>'form-control']) }}
                    </div>
                    <span id="postcode_error" class="help-block"></span>
                </div>  -->

                <!-- <div class="form-group">
                      <div class="theme_input">
                        {{Form::password('password', ['class' => 'form-control', 'placeholder' => 'New Password'])}}
                      </div>
                       <span id="password_error" class="help-block"></span>
                </div>

                 <div class="form-group">
                      <div class="theme_input">
                        {{Form::password('confirm_password', ['class' => 'form-control', 'placeholder' => 'Confirm Password'])}}
                      </div>
                     <span id="confirm_password_error" class="help-block"></span>   
                 </div> -->

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

 				<input type="hidden" id="in_first_name" name="in_first_name" value="" >
 				<input type="hidden" id="in_email" name="in_email" value="" >
 				<input type="hidden" id="in_phone_number" name="in_phone_number" value="" >
 				<div class="form-group">
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
 	{{ Form::open(array('id' => 'user-nannaybooking-form', 'class' => 'form')) }}

 	<div class="row">
 		<div class="col-xl-5" style="margin-left: 20px;">
 			<div class="form-group">
 				{!! HTML::decode( Form::label('start_date', trans("Start Date").'<span
 				class="text-danger"> * </span>')) !!}
 				{{ Form::text('start_date','', ['class' => 'form-control form-control-solid form-control-lg datetimepicker-input '.($errors->has('start_date') ? 'is-invalid':''),'placeholder'=>'Start Date','id'=>'datepickerfrom']) }}

 			</div>
 		</div>
 		<div class="col-xl-5" >
 			<div class="form-group">
 				{!! HTML::decode( Form::label('end_date', trans("End Date").'<span
 				class="text-danger"> * </span>')) !!}
 				{{ Form::text('end_date','',['class' => 'form-control form-control-solid form-control-lg datetimepicker-input '.($errors->has('end_date') ? 'is-invalid':''),'placeholder'=>'End Date','id'=>'datepickerto']) }}
 			</div>
 		</div>
 	</div>
 	<div class="modal-body">
 		<div class="book_avaiblity">


 		</div>

 	</div>
 	{{Form::close()}}
 </div>
</div>
</div>
<!-- end book Nanny -->


@endsection

@section('scripts')

<script type="text/javascript">
	$('#datepickerfrom').datepicker({
	});
	$('#datepickerto').datepicker({
	});


	$('#profile_pic').change(function(){
		$('#photo_id_error').html('');
		var fsize = this.files[0].size,
		ftype = this.files[0].type,
		fname = this.files[0].name,
		fextension = fname.substring(fname.lastIndexOf('.')+1);
		validExtensions = ["jpg","jpeg","gif","png"];
		if ($.inArray(fextension, validExtensions) == -1){
			$('#photo_id_error').html('The photo id must be in: jpeg, jpg, png, gif, bmp formats');
			this.value = "";
			return false;
		}else{
			if(fsize > 3145728){
				$('#photo_id_error').html('File size too large! Please upload less than 3MB');
				this.value = "";
				return false;
			}
			const file = this.files[0];

               /*  if (file)
                {
                  console.log(file);
                  let reader = new FileReader();
                  reader.onload = function(event){
                    console.log(event.target.result);
                    $("#removeimg").show();
                    $('#previewImg').attr('src', event.target.result);
                  }
                  reader.readAsDataURL(file);
              } */
          }

      });


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

	var offset          =   1;
	function getMoreJobs(){
		$("#loader_img").show();        
		$.ajax({
			headers     :   { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
			url         :   '{{ url("nanny-loadmore/") }}',
			type        :   'POST', 
			data        :   {'offset':offset},              
			success   :   function(data) {  
				offset++;                    
				$(data).insertBefore('.loadMoreBtnDV');                    
				$("#loader_img").hide();

				if($.trim(data) == "No Record Found"){
					$('#loadMoreBtn').hide();
				}
			} 
		});
	}


	$(document).ready(function () {
		$('#user-register').click(function() {
			signUp(); 
		});



		$(document).on('click','#btn-more',function(){
			var offset = $('#offset').val();
			var zipcode = $('#zipcode_val').val();
			$("#btn-more").html("Loading....");
			loadmoreNannay(offset, zipcode)

		});  




		$(".partner-owl").owlCarousel({
			nav: false,
			dots: false,
			loop: false,
			autoplay: true,
			autoplayTimeout: 2000,
			autoplayHoverPause: true,
			stagePadding: 0,
			margin: 0,
			items: 6,
			navText: ["<i class='fa fa-chevron-left'></i>", "<i class='fa fa-chevron-right'></i>"],
			responsive: {
				0: {
					items: 2
				},
				575: {
					items: 3
				},
				767: {
					items: 4
				},
				991: {
					items: 6
				}
			}
		});


		$(".nanny_interview").click(function(){
			$('#myModal').modal('show');
			var nanny_id  =  $(this).attr("id") ; 
			$("#selected_nany_id").val(nanny_id);  
		});
		$("body").on('click', '.schedule_interview',function(){
			$("#selected_nany_id").val('');
			$(".time_slots_list").html('');
			var nanny_id  =  $(this).attr("id") ; 
			$("#selected_nany_id").val(nanny_id);  
			$('#schedul_interview').modal('show');
		});  

		$(".sortBy").click(function(){
			var sortValue =  $(this).attr("id") ; 

			$("#sortBy").val(sortValue);
			$("#sor_by_form").submit();

		});   

            /*  $(".date").change(function(){

                $("#selected_nany_id").val('');
            
            }); */

        });


	function getCurrentLocation()
	{
		$.ajax({
			url    : '{{ route("user.current.location") }}',
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

	function gettimeSlots(date){
        //    var id  =  document.getElementsByClassName('nanny')[0].value;
        var nannyId  =  $("#selected_nany_id").val();   
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
        				$(".time_slots_list").html("<p>"+res.mesg+"</p>");

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
    	var nanny_id = $("#selected_nany_id").val();
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
    		success : function (res)
    		{
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


    $(document).ready(function(){
    	$("#styled").on('change',function () {
    		$('input:checkbox').not(this).prop('checked', this.checked);
    	});
    	$("#dashboard").click(function () {
    		$('.dashboard').not(this).prop('checked', this.checked);
    	});
    	$("#groups").click(function () {
    		$('.groups').not(this).prop('checked', this.checked);
    	});
    	$("#user-admins").click(function () {
    		$('.user-admins').not(this).prop('checked', this.checked);
    	});

    	$("#customer-admin").click(function () {
    		$('.customer-admin').not(this).prop('checked', this.checked);
    	});
    	$("#vendor-admin").click(function () {
    		$('.vendor-admin').not(this).prop('checked', this.checked);
    	});
    	$("#email-new-customers-to vendors").click(function () {
    		$('.email-new-customers-to vendors').not(this).prop('checked', this.checked);
    	});
    	$("#customer-purchasing-inquiry").click(function () {
    		$('.customer-purchasing-inquiry').not(this).prop('checked', this.checked);
    	});
    	$("#Customer-vendor-list").click(function () {
    		$('.Customer-vendor-list').not(this).prop('checked', this.checked);
    	});
    	$("#vendor-transaction-journal").click(function () {
    		$('.vendor-transaction-journal').not(this).prop('checked', this.checked);
    	});

    	$("#import-export").click(function () {
    		$('.import-export').not(this).prop('checked', this.checked);
    	});
    	$("#payments").click(function () {
    		$('.payments').not(this).prop('checked', this.checked);
    	});
    	$("#reports").click(function () {
    		$('.reports').not(this).prop('checked', this.checked);
    	});
    	$("#system-management").click(function () {
    		$('.system-management').not(this).prop('checked', this.checked);
    	});
    	$("#settings").click(function () {
    		$('.settings').not(this).prop('checked', this.checked);
    	});

    });

    $(document).ready(function () {
    	$("body").on('click', '.book_nanny',function(){
    		var nannyId = $(this).attr('id');
    		$("#loader_img").show();        
    		$.ajax({
    			headers     :   { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
    			url         :   '{{ route("user.getNannyAvaiblity") }}',
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

    	$.ajax({
    		headers     :   { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
    		url: "{{ route('user.nannybooking')}}",
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
                        	$("#in_first_name").val(response.first_name);
                        	$("#in_email").val(response.email);
                        	$("#in_phone_number").val(response.phone_number);
                        	show_message(response.message,'success');
                        	$('#myModal').modal('hide');
                        	$('#exampleModalLong').modal('hide');
                        	$('#schedul_interview').modal('show');
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

    $(document).ready(function () {
    	$('.nanny-booking').click(function() {
    		alert();
    	});
    });


</script>
@endsection