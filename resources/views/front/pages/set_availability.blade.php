@extends('front.dashboard.layouts.default')

@section('content')
@section('title', 'Set Availability') 
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="{{ WEBSITE_CSS_URL }}front/jquery.timepicker.min.css">
<script type="text/javascript" src="{{WEBSITE_JS_URL}}front1/jquery.timepicker.min.js"></script>
<div class="main-workspace">
<div class="login set-availblity py-5">
        <div class="container">
            <div class="">
                <div class="login-form signup-form  py-5 mb-5 ">
                    <div class="availblity-table">
                        <div class="row pb-4 flex-row">
                            <div class="col">
                                <div class="heading">
                                    <!-- <h5>Looking for care?</h5> -->
                                    <h4>Availability</h4>
                                </div>
                            </div>
                            <!-- <div class="col-md-auto">
                                    <div class="create-account">Already have an account, <a href="{{ route('user.login') }}">
                                Log In </a> 
                        </div> -->
                        </div>
                        <div class="table-responsive">
                            <?php 
                            $weekdays = array(
                                0=>array(
                                    'time'=>'12:00-02:00',
                                    'label'=>'12AM to 2AM',
                                    'days'=>array(
                                        'monday',
                                        'tuesday',
                                        'wednesday',
                                        'thursday',
                                        'friday',
                                        'saturday',
                                        'sunday'
                                    )
                                ),
                                1=>array(
                                    'time'=>'02:00-04:00',
                                    'label'=>'2AM to 4AM',
                                    'days'=>array(
                                        'monday',
                                        'tuesday',
                                        'wednesday',
                                        'thursday',
                                        'friday',
                                        'saturday',
                                        'sunday'
                                    )
                                ),
                                2=>array(
                                    'time'=>'04:00-06:00',
                                    'label'=>'4AM to 6AM',
                                    'days'=>array(
                                        'monday',
                                        'tuesday',
                                        'wednesday',
                                        'thursday',
                                        'friday',
                                        'saturday',
                                        'sunday'
                                    )
                                ),
                                3=>array(
                                    'time'=>'06:00-08:00',
                                    'label'=>'6AM to 8AM',
                                    'days'=>array(
                                        'monday',
                                        'tuesday',
                                        'wednesday',
                                        'thursday',
                                        'friday',
                                        'saturday',
                                        'sunday'
                                    )
                                ),
                                4=>array(
                                    'time'=>'08:00-10:00',
                                    'label'=>'10AM to 10AM',
                                    'days'=>array(
                                        'monday',
                                        'tuesday',
                                        'wednesday',
                                        'thursday',
                                        'friday',
                                        'saturday',
                                        'sunday'
                                    )
                                ),
                                5=>array(
                                    'time'=>'10:00-12:00',
                                    'label'=>'10AM to 12PM',
                                    'days'=>array(
                                        'monday',
                                        'tuesday',
                                        'wednesday',
                                        'thursday',
                                        'friday',
                                        'saturday',
                                        'sunday'
                                    )
                                ),
                                6=>array(
                                    'time'=>'12:00-14:00',
                                    'label'=>'12PM to 2PM',
                                    'days'=>array(
                                        'monday',
                                        'tuesday',
                                        'wednesday',
                                        'thursday',
                                        'friday',
                                        'saturday',
                                        'sunday'
                                    )
                                ),
                                7=>array(
                                    'time'=>'14:00-16:00',
                                    'label'=>'2PM to 4PM',
                                    'days'=>array(
                                        'monday',
                                        'tuesday',
                                        'wednesday',
                                        'thursday',
                                        'friday',
                                        'saturday',
                                        'sunday'
                                    )
                                ),

                                8=>array(
                                    'time'=>'16:00-18:00',
                                    'label'=>'4PM to 6PM',
                                    'days'=>array(
                                        'monday',
                                        'tuesday',
                                        'wednesday',
                                        'thursday',
                                        'friday',
                                        'saturday',
                                        'sunday'
                                    )
                                ),

                                9=>array(
                                    'time'=>'18:00-20:00',
                                    'label'=>'6PM to 8PM',
                                    'days'=>array(
                                        'monday',
                                        'tuesday',
                                        'wednesday',
                                        'thursday',
                                        'friday',
                                        'saturday',
                                        'sunday'
                                    )
                                ),

                                10=>array(
                                    'time'=>'20:00-22:00',
                                    'label'=>'8PM to 10PM',
                                    'days'=>array(
                                        'monday',
                                        'tuesday',
                                        'wednesday',
                                        'thursday',
                                        'friday',
                                        'saturday',
                                        'sunday'
                                    )
                                ),

                                11=>array(
                                    'time'=>'22:00-24:00',
                                    'label'=>'10PM to 12AM',
                                    'days'=>array(
                                        'monday',
                                        'tuesday',
                                        'wednesday',
                                        'thursday',
                                        'friday',
                                        'saturday',
                                        'sunday'
                                    )
                                ),
                            )
                            ?>
                          {{ Form::open(array('id' => 'user-registration-form', 'route'=>'user.set.availability','class' => 'form')) }}  
                           <table class="table table-striped">
                                <tr>
                                    <td>Days</td>
                                    <td class="text-center">Start Time</td>
                                    <td class="text-center">End Time</td>
                                    <td class="text-center">Action</td>
                                </tr>
                                <tr>
                                    <td>Monday</td>
                                    <td class="text-center"><input type="text" name="monday_from_time" class="timeformat1" value="{{ !empty($availabilities->monday_from_time) ? $availabilities->monday_from_time:'' }}"></td>
                                    <td class="text-center"><input type="text" name="monday_to_time" class="timeformat2" value="{{ !empty($availabilities->monday_to_time) ? $availabilities->monday_to_time:'' }}">
                                    </td>
                                    <td class="text-center">
                                    @if(!empty($availabilities->monday_from_time) || !empty($availabilities->monday_to_time))  
                                    <button class="btn btn-danger" type="button" onclick="clear_avaiblity('monday')" >Clear</button> 
                                      @endif 
                                    </td>
                                </tr>
                                <tr>
                                    <td>Tuesday</td>
                                    <td class="text-center"><input type="text" name="tuesday_form_time" class="timeformat1" value="{{ !empty($availabilities->tuesday_form_time) ? $availabilities->tuesday_form_time:'' }}"></td>
                                    <td class="text-center"><input type="text" name="tuesday_to_time" class="timeformat2" value="{{ !empty($availabilities->tuesday_to_time) ? $availabilities->tuesday_to_time:'' }}">
                                    </td>
                                    <td class="text-center">
                                    @if(!empty($availabilities->tuesday_form_time) || !empty($availabilities->tuesday_to_time))  
                                    <button class="btn btn-danger" type="button" onclick="clear_avaiblity('tuesday')">Clear</button> 
                                      @endif   
                                    </td>
                                </tr>
                                <tr>
                                    <td>Wednesday</td>
                                    <td class="text-center"><input type="text" name="wednesday_form_time" class="timeformat1" value="{{ !empty($availabilities->wednesday_form_time) ? $availabilities->wednesday_form_time:'' }}"></td>
                                    <td class="text-center"><input type="text" name="wednesday_to_time" class="timeformat2" value="{{ !empty($availabilities->wednesday_to_time) ? $availabilities->wednesday_to_time:'' }}"> 
                                    </td>
                                    <td class="text-center">
                                    @if(!empty($availabilities->wednesday_form_time) || !empty($availabilities->wednesday_to_time))  
                                    <button class="btn btn-danger" type="button" onclick="clear_avaiblity('wednesday')">Clear</button> 
     
                                      @endif  
                                    </td>
                                </tr>
                                <tr>
                                    <td>Thursday</td>
                                    <td class="text-center"><input type="text" name="thursday_form_time" class="timeformat1" value="{{ !empty($availabilities->thursday_form_time) ? $availabilities->thursday_form_time:'' }}"></td>
                                    <td class="text-center"><input type="text" name="thursday_to_time" class="timeformat2" value="{{ !empty($availabilities->thursday_to_time) ? $availabilities->thursday_to_time:'' }}">
                                    </td>
                                    <td class="text-center">
                                    @if(!empty($availabilities->thursday_form_time) || !empty($availabilities->thursday_to_time))  
                                     <button class="btn btn-danger" type="button" onclick="clear_avaiblity('thursday')">Clear</button> 
     
                                      @endif  
                                    </td>
                                </tr>
                                <tr>
                                    <td>Friday</td>
                                   <td class="text-center"><input type="text" name="friday_form_time" class="timeformat1" value="{{ !empty($availabilities->friday_form_time) ? $availabilities->friday_form_time:'' }}"></td>
                                    <td class="text-center"><input type="text" name="friday_to_time" class="timeformat2" value="{{ !empty($availabilities->friday_to_time) ? $availabilities->friday_to_time:'' }}">
                                    </td>
                                    <td class="text-center">
                                    @if(!empty($availabilities->friday_form_time) || !empty($availabilities->friday_to_time))  
                                    <button class="btn btn-danger" type="button" onclick="clear_avaiblity('friday')">Clear</button> 
     
                                      @endif 
                                    </td>
                                </tr>
                                <tr>
                                    <td>Saturday</td>
                                    <td class="text-center"><input type="text" name="saturday_form_time" class="timeformat1" value="{{ !empty($availabilities->saturday_form_time) ? $availabilities->saturday_form_time:'' }}"></td>
                                    <td class="text-center"><input type="text" name="saturday_to_time" class="timeformat2" value="{{ !empty($availabilities->saturday_to_time) ? $availabilities->saturday_to_time:'' }}">
                                    </td>
                                    <td class="text-center">
                                    @if(!empty($availabilities->saturday_form_time) || !empty($availabilities->saturday_to_time))  
                                    <button class="btn btn-danger" type="button" onclick="clear_avaiblity('saturday')">Clear</button> 
     
                                      @endif   
                                    </td>
                                </tr>
                                <tr>
                                    <td>Sunday</td>
                                    <td class="text-center"><input type="text" name="sunday_form_time" class="timeformat1" value="{{ !empty($availabilities->sunday_form_time) ? $availabilities->sunday_form_time:'' }}"></td>
                                    <td class="text-center"><input type="text" name="sunday_to_time" class="timeformat2" value="{{ !empty($availabilities->sunday_to_time) ? $availabilities->sunday_to_time:'' }}">
                                    </td>
                                    <td class="text-center">
                                    @if(!empty($availabilities->sunday_form_time) || !empty($availabilities->sunday_to_time)) 
                                    <button class="btn btn-danger" type="button" onclick="clear_avaiblity('sunday')">Clear</button> 
                                    @endif 
                                    </td>
                                </tr>
                           </table>
                            <div class="btn-block text-right pt-3">
                                <!-- <a href="#" class="btn-theme ">Submit</a>  -->
                                <input type="submit" class="btn-theme" value="Submit">
                            </div>
                            {{Form::close()}}  
                        </div>
                    </div>
            
                    </div>
                <div class="login-form signup-form  pt-3 ">
                              <div class="availblity-table">
                        <div class="row py-4 flex-row align-items-baseline">
                            <div class="col-md-auto">
                                <div class="heading">
                                    <!-- <h5>Looking for care?</h5> -->
                                    <h4>Holidays</h4>
                                </div>
                            </div>
                            <div class="col">
                                <div class="d-md-flex  add-holiday align-items-baseline">
                                    <strong>  Add Holiday </strong> 

                                    <div class="date-bx px-md-3 py-3 py-md-0">

                                        <input type="text" id="date" name="trip-start" value="" placeholder="YYYY-MM-DD" readonly>
                                        <span class="help-inline error date_error"></span>
                                    </div>

                                    <a href="javascript:void(0);" class="btn-theme submit add_holiday">Submit</a>

                                    </div>
                                     
                                    
                                </div>
                            </div>
                            <script>
                            $(function() {
                                $("#date").datepicker({
                                    changeYear: true,
                                    changeMonth:true,
                                    dateFormat: 'yy-mm-dd',
                                    minDate: 0,
                                });
                                $(".add_holiday").click(function(){
                                    $(".date_error").removeClass('error');
                                    var date = $("#date").val();
                                    //alert(date);
                                    if(date == ""){
                                        $(".date_error").addClass('error');
                                        $(".date_error").html('Please select date.');
                                    }else{
                                       window.location.href = "{{ URL('add-holiday') }}/"+date;
                                    }
                                })
                            });             
                        </script>
                

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th class="text-left">Date</th>
                                    <th>Action</th>
                                 
                                </tr>
                                </thead>
                                <tbody>
                                @if(count($holidays))
                                 @foreach($holidays as $key=>$value)
                                <tr>
                                    <td class="text-uppercase">{{ date('d M Y', strtotime($value->holiday_date)) }}</td>
                                    <td>
                                        <div class="delete-bx text-center">
                                        <a  href="javascript:void" onclick="deleteHolidays({{$value->id}})" title="delete">
                                        <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="trash-alt" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-inline--fa fa-trash-alt fa-w-14 fa-2x"><path fill="currentColor" d="M268 416h24a12 12 0 0 0 12-12V188a12 12 0 0 0-12-12h-24a12 12 0 0 0-12 12v216a12 12 0 0 0 12 12zM432 80h-82.41l-34-56.7A48 48 0 0 0 274.41 0H173.59a48 48 0 0 0-41.16 23.3L98.41 80H16A16 16 0 0 0 0 96v16a16 16 0 0 0 16 16h16v336a48 48 0 0 0 48 48h288a48 48 0 0 0 48-48V128h16a16 16 0 0 0 16-16V96a16 16 0 0 0-16-16zM171.84 50.91A6 6 0 0 1 177 48h94a6 6 0 0 1 5.15 2.91L293.61 80H154.39zM368 464H80V128h288zm-212-48h24a12 12 0 0 0 12-12V188a12 12 0 0 0-12-12h-24a12 12 0 0 0-12 12v216a12 12 0 0 0 12 12z" class=""></path></svg>
                                         </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr><td colspan="2">No Holiday Found</td></tr>
                                @endif
                               
                                </tbody>
                            </table>
                        </div>
                    </div>     
               
                </div>
            </div>
        </div>

        </div>
              
</div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.5.2/bootbox.min.js" integrity="sha512-RdSPYh1WA6BF0RhpisYJVYkOyTzK4HwofJ3Q7ivt/jkpW6Vc8AurL1R+4AUcvn9IwEKAPm/fk7qFZW3OuiUDeg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript">
    function deleteHolidays(id){
         bootbox.confirm({
            title: "Delete Holiday?",
            message: "Are you sure want to delete?",
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
                     window.location.href = "{{ URL('/delete-holiday') }}/"+id;
                }
               
            }
        });
    }
    $(function() {
        $('.timeformat1').timepicker({ 
            'timeFormat': 'H:i',
            'step': 15
        }).end().on('keypress paste', function (e) {
            e.preventDefault();
            return false;
        });;
        $('.timeformat2').timepicker({ 
            'timeFormat': 'H:i',
            'step': 15
        }).end().on('keypress paste', function (e) {
            e.preventDefault();
            return false;
        });;
    });
    $(document).ready(function() {
        $(".clear_avaiblity").on('click', function() {
          //var avablity_id = attr('id');
          alert();
         });
    }); 

    /*function clear_avaiblity(day){
        var id = {{ !empty($availabilities->id) ? $availabilities->id:0 }}
        if(day != ''){
            bootbox.confirm({
            title: "Clear Availabilities",
            message: "Are you sure want to Clear Availabilities?",
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
                    if(result == true){
                        $.ajax({
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            url: '{{ route("clear.setavablity") }}',
                            type: 'POST',
                            data: {id:id,day:day},
                            success: function(data) {
                                console.log(data);
                              //window.location.reload();
                            }
                        });    
                    }
                    
                } else {
                    return true;
                }
                return false;
                
           }
       });
            
        }
    } */  

    function clear_avaiblity(day){
        var id = '{{ !empty($availabilities->id) ? $availabilities->id:0 }}';
         bootbox.confirm({
            title: "Clear Availability?",
            message: "Are you sure want to clear availability?",
            buttons: {
                cancel: {
                    label: '<i class="fa fa-times"></i> Cancel'
                },
                confirm: {
                    label: '<i class="fa fa-check"></i> Confirm'
                }
            },
            callback: function (result) {
                if(result == true){
                    $.ajax({
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        url: '{{ route("clear.setavablity") }}',
                        type: 'POST',
                        data: {id:id,day:day},
                        success: function(data) {
                          window.location.reload();
                        }
                    });    
                }
               
            }
        });
    }

</script>
@endsection

