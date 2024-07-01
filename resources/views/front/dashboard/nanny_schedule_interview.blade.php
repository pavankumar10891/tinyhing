@extends('front.dashboard.layouts.default')
@section('content')
<div class="main-workspace">
            <div class="container">
                <div class="total-client  mb-5">
                    <div class="client-block">
                        <div class="dashboard-heading-head">Interviews</div>
                    </div>

                    <div class="theme-table theme-table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Time</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                            @if(!$results->isEmpty())
                                 @foreach($results as $result)
                                 <tr>
                                 <td data-label="Name">
                                    {{ $result->user_name }}
                                    </td>
                                    <td data-label="Date" class="text-center">
                                    {{ date('m/d/Y',strtotime($result->interview_date)) }}
                                    </td>
                                    <td data-label="Time" class="text-center">
                                        <?php
                                            $timeSlotData = explode('-', $result->meeting_day_time); 
                                            $fromTIme = !empty($timeSlotData[0]) ? date('h:i a', strtotime($timeSlotData[0])):'';
                                            $toTIme = !empty($timeSlotData[1]) ? date('h:i a',strtotime($timeSlotData[1])):'';
                                            $date1 =  !empty($timeSlotData[1]) ? date('Y-m-d h:i', strtotime($result->interview_date.' '.$timeSlotData[1])) :'';
                                            $date2 =  date('Y-m-d h:i');  
                                         ?>
                                        <span class="badge-theme"> {{ $fromTIme.'-'.$toTIme }}</span>
                                    </td>
                                    <td data-label="Action" class="text-right">

                                        @if($result->is_interview == 1)
                                        @if(strtotime($date1) > strtotime($date2))
                                        <a href="{{ route('meeting.join', Crypt::encrypt($result->id)) }}"  class="btn btn-theme">
                                            Join Now
                                        </a>
                                        @endif
                                        @endif
                                        @if($result->is_interview==0)
                                        <a class=" btn btn-theme btn-view" href="javascript:void(0);" onclick="statusApproved({{$result->id}})">Approve</a>
                                        <a class=" btn btn-theme btn-stop" href="javascript:void(0);" onclick="statusRejected({{$result->id}})">Reject </a>
                                        @endif
                                    </td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr><td colspan="6" style="text-align:center;">{{ trans("Record not found.") }}</td></tr>
                                    @endif
                            </tbody>
                        </table>
                    </div>
                    @include('pagination.default', ['results' => $results])
                    

                </div>
            </div>

        </div>
        <script>
        function page_limit() {
    $("form").submit();
}
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.5.2/bootbox.min.js" integrity="sha512-RdSPYh1WA6BF0RhpisYJVYkOyTzK4HwofJ3Q7ivt/jkpW6Vc8AurL1R+4AUcvn9IwEKAPm/fk7qFZW3OuiUDeg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<style>
    .theme-table .table thead th {
        border: 0;
        background-color: #82a79c;
        color:#fff;
    }
    .btn-theme{
        background-color: #82a79c;
    }
    .theme-table {
        border: 1px solid #e0e9e3;
        border-radius: 10px;
        overflow: hidden;
    }
    </style>
    <script type="text/javascript">
        
    function statusApproved(id){
        bootbox.confirm({
            title: "Approve Interview Sheduled?",
            message: "Are you sure want to Approve Sheduled Interview?",
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
                   window.location.href = "{{ URL('/approve-interview') }}/"+id;
               }
               
           }
       });
   }

   function statusRejected(id){
        var dialog = bootbox.prompt({
            title: "Reject Interview?",
            message: "Are you sure want to Reject Interview?",
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
                        'url': '{{ URL("/reject-interview") }}',
                        'data': {"_token": "{{ csrf_token() }}", 'id': id, 'reject_reason': result},
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
    </script>
@endsection