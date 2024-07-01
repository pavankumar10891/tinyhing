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
                                <tr>
                                    <td>Monday</td>
                                    <td><input type="text" name="monday_from_time" class="timeformat1" value="{{ !empty($availabilities->monday_from_time) ? $availabilities->monday_from_time:'' }}"></td>
                                    <td><input type="text" name="monday_to_time" class="timeformat2" value="{{ !empty($availabilities->monday_to_time) ? $availabilities->monday_to_time:'' }}">
                                     
                                    </td>
                                </tr>
                                @endif
                                @if(!empty($availabilities->tuesday_form_time))
                                <tr>
                                    <td>Tuesday</td>
                                    <td><input type="text" name="tuesday_form_time" class="timeformat1" value="{{ !empty($availabilities->tuesday_form_time) ? $availabilities->tuesday_form_time:'' }}"></td>
                                    <td><input type="text" name="tuesday_to_time" class="timeformat2" value="{{ !empty($availabilities->tuesday_to_time) ? $availabilities->tuesday_to_time:'' }}">
                                            
                                    </td>
                                </tr>
                                @endif
                                 @if(!empty($availabilities->wednesday_form_time))
                                <tr>
                                    <td>Wednesday</td>
                                    <td><input type="text" name="wednesday_form_time" class="timeformat1" value="{{ !empty($availabilities->wednesday_form_time) ? $availabilities->wednesday_form_time:'' }}"></td>
                                    <td><input type="text" name="wednesday_to_time" class="timeformat2" value="{{ !empty($availabilities->wednesday_to_time) ? $availabilities->wednesday_to_time:'' }}">
                                            
                                    </td>
                                </tr>
                                 @endif
                                 @if(!empty($availabilities->thursday_form_time))
                                <tr>
                                    <td>Thursday</td>
                                    <td><input type="text" name="thursday_form_time" class="timeformat1" value="{{ !empty($availabilities->thursday_form_time) ? $availabilities->thursday_form_time:'' }}"></td>
                                    <td><input type="text" name="thursday_to_time" class="timeformat2" value="{{ !empty($availabilities->thursday_to_time) ? $availabilities->thursday_to_time:'' }}">  
                                    </td>
                                </tr>
                                @endif
                                @if(!empty($availabilities->friday_form_time))
                                <tr>
                                    <td>Friday</td>
                                   <td><input type="text" name="friday_form_time" class="timeformat1" value="{{ !empty($availabilities->friday_form_time) ? $availabilities->friday_form_time:'' }}"></td>
                                    <td><input type="text" name="friday_to_time" class="timeformat2" value="{{ !empty($availabilities->friday_to_time) ? $availabilities->friday_to_time:'' }}">
                                         
                                    </td>
                                </tr>
                                @endif
                                @if(!empty($availabilities->saturday_form_time))
                                <tr>
                                    <td>Saturday</td>
                                    <td><input type="text" name="saturday_form_time" class="timeformat1" value="{{ !empty($availabilities->saturday_form_time) ? $availabilities->saturday_form_time:'' }}"></td>
                                    <td><input type="text" name="saturday_to_time" class="timeformat2" value="{{ !empty($availabilities->saturday_to_time) ? $availabilities->saturday_to_time:'' }}">   
                                    </td>
                                </tr>
                                @endif
                                 @if(!empty($availabilities->sunday_form_time))
                                <tr>
                                    <td>Sunday</td>
                                    <td><input type="text" name="sunday_form_time" class="timeformat1" value="{{ !empty($availabilities->sunday_form_time) ? $availabilities->sunday_form_time:'' }}"></td>
                                    <td><input type="text" name="sunday_to_time" class="timeformat2" value="{{ !empty($availabilities->sunday_to_time) ? $availabilities->sunday_to_time:'' }}"> 
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