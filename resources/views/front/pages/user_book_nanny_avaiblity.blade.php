<div class="table-responsive">
     <?php 
            $weekdays = array(
                1=>array(
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
                2=>array(
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
                3=>array(
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
                4=>array(
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
                5=>array(
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
                6=>array(
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
                7=>array(
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
                8=>array(
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

                9=>array(
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

                10=>array(
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

                11=>array(
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

                12=>array(
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
            ?>               <input type="hidden" name="nannay_id" value="{{ $nannyId }}">
                             <input type="hidden" name="avablity_id" value="{{ $availabilities->id }}">
                            <table class="table table-striped">
                                <tr>
                                    <td>Days</td>
                                    <td>Start Time</td>
                                    <td>End Time</td>
                                </tr>
                                @if(!empty($availabilities->monday_from_time))
                                <?php
                                 $mondaySlotData =  CustomHelper::getSlotsByTime($availabilities->monday_from_time, $availabilities->monday_to_time);
                                 ?>
                                <tr>
                                    <td>Monday</td>

                                    <td>
                                        <select name="monday_from_time" class="form-control">
                                            @if(!empty($mondaySlotData))
                                            <option value="">Select Time</option>
                                             @php
                                             $i=1;
                                             $count = count($mondaySlotData);
                                             @endphp
                                             @foreach($mondaySlotData as $key=>$value)                                           
                                                 <option value="{{$value['start']}}">{{$value['start']}}</option>
                                             @php
                                             $i++;
                                             @endphp
                                            @endforeach
                                            @else
                                            <option value="">Not Found</option>
                                            @endif
                                        </select>
                                    </td>
                                    <td>
                                        <select name="monday_to_time" class="form-control">
                                            @if(!empty($mondaySlotData))
                                            <option value="">Select Time</option>
                                            @php
                                             $i=1;
                                             $count = count($mondaySlotData);
                                             @endphp
                                            @foreach($mondaySlotData as $key=>$value)
                                                <option value="{{$value['end']}}">{{$value['end']}}</option>
                                             @php
                                             $i++;
                                             @endphp
                                            @endforeach
                                            @else
                                            <option value="">Not Found</option>
                                            @endif
                                        </select>
                                    </td>
                                </tr>
                                @endif
                                @if(!empty($availabilities->tuesday_form_time))
                                <?php
                                 $tuesdaySlotData =  CustomHelper::getSlotsByTime($availabilities->tuesday_form_time, $availabilities->tuesday_to_time);
                                 ?>
                                <tr>
                                    <td>Tuesday</td>
                                    <td>
                                        <select name="tuesday_from_time" class="form-control">
                                            @if(!empty($tuesdaySlotData))
                                            <option value="">Select Time</option>
                                             @php
                                             $i=1;
                                             $count = count($tuesdaySlotData);
                                             @endphp
                                             @foreach($tuesdaySlotData as $key=>$value)
                                                <option value="{{$value['start']}}">{{$value['start']}}</option>
                                             @php
                                             $i++;
                                             @endphp
                                            @endforeach
                                            @else
                                            <option value="">Not Found</option>
                                            @endif
                                        </select>
                                    </td>
                                    <td>
                                        <select name="tuesday_to_time" class="form-control">
                                            @if(!empty($tuesdaySlotData))
                                            <option value="">Select Time</option>
                                            @php
                                             $i=1;
                                             $count = count($tuesdaySlotData);
                                             @endphp
                                            @foreach($tuesdaySlotData as $key=>$value)
                                                <option value="{{$value['end']}}">{{$value['end']}}</option>
                                             @php
                                             $i++;
                                             @endphp
                                            @endforeach
                                            @else
                                            <option value="">Not Found</option>
                                            @endif
                                        </select>
                                    </td>
                                </tr>
                                @endif
                                 @if(!empty($availabilities->wednesday_form_time))
                                 <?php
                                 $wednesdaySlotData =  CustomHelper::getSlotsByTime($availabilities->wednesday_form_time, $availabilities->wednesday_to_time);
                                 ?>
                                <tr>
                                    <td>Wednesday</td>
                                   <td>
                                        <select name=">wednesday_from_time" class="form-control">
                                            @if(!empty($wednesdaySlotData))
                                            <option value="">Select Time</option>
                                             @php
                                             $i=1;
                                             $count = count($wednesdaySlotData);
                                             @endphp
                                             @foreach($wednesdaySlotData as $key=>$value)
                                                <option value="{{$value['start']}}">{{$value['start']}}</option>
                                             @php
                                             $i++;
                                             @endphp
                                            @endforeach
                                            @else
                                            <option value="">Not Found</option>
                                            @endif
                                        </select>
                                    </td>
                                    <td>
                                        <select name="wednesday_to_time" class="form-control">
                                            @if(!empty($wednesdaySlotData))
                                            <option value="">Select Time</option>
                                            @php
                                             $i=1;
                                             $count = count($wednesdaySlotData);
                                             @endphp
                                            @foreach($wednesdaySlotData as $key=>$value)
                                                <option value="{{$value['end']}}">{{$value['end']}}</option>
                                             @php
                                             $i++;
                                             @endphp
                                            @endforeach
                                            @else
                                            <option value="">Not Found</option>
                                            @endif
                                        </select>
                                    </td>
                                </tr>
                                 @endif
                                 @if(!empty($availabilities->thursday_form_time))
                                 <?php
                                 $thursdaydaySlotData =  CustomHelper::getSlotsByTime($availabilities->thursday_form_time, $availabilities->thursday_to_time);
                                 ?>
                                <tr>
                                    <td>Thursday</td>
                                    <td>
                                        <select name="thursday_from_time" class="form-control">
                                            @if(!empty($thursdaydaySlotData))
                                            <option value="">Select Time</option>
                                             @php
                                             $i=1;
                                             $count = count($thursdaydaySlotData);
                                             @endphp
                                             @foreach($thursdaydaySlotData as $key=>$value)
                                                <option value="{{$value['start']}}">{{$value['start']}}</option>
                                             @php
                                             $i++;
                                             @endphp
                                            @endforeach
                                            @else
                                            <option value="">Not Found</option>
                                            @endif
                                        </select>
                                    </td>
                                    <td>
                                        <select name="thursday_to_time" class="form-control">
                                            @if(!empty($thursdaydaySlotData))
                                            <option value="">Select Time</option>
                                            @php
                                             $i=1;
                                             $count = count($thursdaydaySlotData);
                                             @endphp
                                            @foreach($thursdaydaySlotData as $key=>$value)
                                                <option value="{{$value['end']}}">{{$value['end']}}</option>
                                             @php
                                             $i++;
                                             @endphp
                                            @endforeach
                                            @else
                                            <option value="">Not Found</option>
                                            @endif
                                        </select>
                                    </td>
                                </tr>
                                @endif
                                @if(!empty($availabilities->friday_form_time))
                                <?php
                                 $fridaySlotData =  CustomHelper::getSlotsByTime($availabilities->friday_form_time, $availabilities->friday_to_time);
                                 ?>
                                <tr>
                                    <td>Friday</td>
                                   <td>
                                        <select name="friday_from_time" class="form-control">
                                            @if(!empty($fridaySlotData))
                                            <option value="">Select Time</option>
                                             @php
                                             $i=1;
                                             $count = count($fridaySlotData);
                                             @endphp
                                             @foreach($fridaySlotData as $key=>$value)
                                                <option value="{{$value['start']}}">{{$value['start']}}</option>
                                             @php
                                             $i++;
                                             @endphp
                                            @endforeach
                                            @else
                                            <option value="">Not Found</option>
                                            @endif
                                        </select>
                                    </td>
                                    <td>
                                        <select name="friday_to_time" class="form-control">
                                            @if(!empty($fridaySlotData))
                                            <option value="">Select Time</option>
                                            @php
                                             $i=1;
                                             $count = count($fridaySlotData);
                                             @endphp
                                            @foreach($fridaySlotData as $key=>$value)
                                                <option value="{{$value['end']}}">{{$value['end']}}</option>
                                             @php
                                             $i++;
                                             @endphp
                                            @endforeach
                                            @else
                                            <option value="">Not Found</option>
                                            @endif
                                        </select>
                                    </td>
                                </tr>
                                @endif
                                @if(!empty($availabilities->saturday_form_time))
                                <?php
                                 $saturdaySlotData =  CustomHelper::getSlotsByTime($availabilities->saturday_form_time, $availabilities->saturday_to_time);
                                 ?>
                                <tr>
                                    <td>Saturday</td>
                                    <td>
                                        <select name="saturday_from_time" class="form-control">
                                            @if(!empty($saturdaySlotData))
                                            <option value="">Select Time</option>
                                             @php
                                             $i=1;
                                             $count = count($saturdaySlotData);
                                             @endphp
                                             @foreach($saturdaySlotData as $key=>$value)
                                                <option value="{{$value['start']}}">{{$value['start']}}</option>
                                             @php
                                             $i++;
                                             @endphp
                                            @endforeach
                                            @else
                                            <option value="">Not Found</option>
                                            @endif
                                        </select>
                                    </td>
                                    <td>
                                        <select name="saturday_to_time" class="form-control">
                                            @if(!empty($saturdaySlotData))
                                            <option value="">Select Time</option>
                                            @php
                                             $i=1;
                                             $count = count($saturdaySlotData);
                                             @endphp
                                            @foreach($saturdaySlotData as $key=>$value)
                                                <option value="{{$value['end']}}">{{$value['end']}}</option>
                                             @php
                                             $i++;
                                             @endphp
                                            @endforeach
                                            @else
                                            <option value="">Not Found</option>
                                            @endif
                                        </select>
                                    </td>   
                                </tr>
                                @endif
                                 @if(!empty($availabilities->sunday_form_time))
                                 <?php
                                 $sundaySlotData =  CustomHelper::getSlotsByTime($availabilities->sunday_form_time, $availabilities->sunday_to_time);
                                 ?>
                                <tr>
                                    <td>Sunday</td>
                                    <td>
                                        <select name="sunday_from_time" class="form-control">
                                            @if(!empty($sundaySlotData))
                                            <option value="">Select Time</option>
                                             @php
                                             $i=1;
                                             $count = count($sundaySlotData);
                                             @endphp
                                             @foreach($sundaySlotData as $key=>$value)
                                                <option value="{{$value['start']}}">{{$value['start']}}</option>
                                             @php
                                             $i++;
                                             @endphp
                                            @endforeach
                                            @else
                                            <option value="">Not Found</option>
                                            @endif
                                        </select>
                                    </td>
                                    <td>
                                        <select name="sunday_to_time" class="form-control">
                                            @if(!empty($sundaySlotData))
                                            <option value="">Select Time</option>
                                            @php
                                             $i=1;
                                             $count = count($sundaySlotData);
                                             @endphp
                                            @foreach($sundaySlotData as $key=>$value)
                                                <option value="{{$value['end']}}">{{$value['end']}}</option>
                                             @php
                                             $i++;
                                             @endphp
                                            @endforeach
                                            @else
                                            <option value="">Not Found</option>
                                            @endif
                                        </select>
                                    </td>
                                </tr>
                                @endif
                           </table>
  </div>
  @if(!empty($availabilities))
  <div class="btn-block text-right">
      <input type="button" class="btn-theme nanny-booking" value="Submit" id="" onclick="nannybookiing()">
  </div>
  @endif
<link rel="stylesheet" href="{{ WEBSITE_CSS_URL }}front/jquery.timepicker.min.css">
<script type="text/javascript" src="{{WEBSITE_JS_URL}}front1/jquery.timepicker.min.js"></script>
  <script type="text/javascript">
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
  </script>
  <!-- {{Form::close()}} -->