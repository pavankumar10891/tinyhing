@extends('front.dashboard.layouts.default')
@section('content')
<div class="main-workspace mb-4">
    <div class="booking-sec backg-img">
        <div class="container">
          {{ Form::open(['method' => 'get','role' => 'form','class' => 'kt-form kt-form--fit mb-0','id'=>'searchForm','autocomplete'=>"off",'onSubmit'=>'return false']) }}
            {{ Form::hidden('display') }}
            <div class="sidebar-block topsiderbar ">
                <div class="filter-option">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" />
                </div>
                <div class="filter-option">
                    <label>Status</label>
                    {{ Form::select('status',array(''=>trans('All'),0=>trans('Pending'),1=>trans('Accepted'),2=>trans('Stopped'),3=>trans('Rejected')),((isset($searchVariable['status'])) ? $searchVariable['status'] : ''), ['class' => 'form-control']) }}
                </div>
                <div class="filter-option">
                    <label>Date from</label>
                    <input class="form-control" name="date_from" type="date">
                </div>

                <div class="filter-option">
                    <label>Date to</label>
                    <input class="form-control" name="date_to" type="date">
                </div>
                <button class=" btn btn-theme mt-4 mr-2" onclick="loadBookingData()">Search</button>
                <button class=" btn btn-theme mt-4 resetBtn">Clear</button>
            </div>
            {{ Form::close() }}  
            <div class="booking-block">
                <div class="row bookingData">
                    @if($results->isNotEmpty())
                    @foreach($results as $result)
                        <div class="col-md-12">
                            <div class="bg-white block-inner">
                                <div class="col">
                                    <div class="text-block ">
                                        <div class="d-flex align-items-center">
                                            <span>
                                                @if(!empty($result->photo_id) && file_exists(USER_IMAGE_ROOT_PATH.$result->photo_id))
                                                <img src="{{ WEBSITE_URL.'image.php?width=148&height=148&cropratio=3:3&image='.USER_IMAGE_URL.$result->photo_id }}" height="148" width="148">
                                                @else
                                                <img src="{{ WEBSITE_URL.'image.php?width=148&height=148&cropratio=3:3&image='.WEBSITE_IMG_URL.'no-female.jpg' }}" alt="">
                                                @endif
                                            </span>

                                            <div class="pl-4">
                                                <h2> {{$result->name}}</h2>
                                                <div class="date">
                                                    {{date('m/d/Y', strtotime($result->booking_date))}}
                                                </div>
                                                <div class="sdate"> Start Date:
                                                    <span class="badge ">
                                                        {{date('m/d/Y', strtotime($result->start_date))}}
                                                    </span>
                                                </div>
                                                <div class="sdate"> End Date:
                                                    <span class="badge ">
                                                        {{date('m/d/Y', strtotime($result->end_date))}}
                                                    </span>
                                                </div>
                                                <div class="status"> status:
                                                    @if($result->status == 0)
                                                    <span class="badge badge-warning">
                                                        Approval Pending 
                                                    </span>
                                                    @elseif($result->status == 1)
                                                    <span class="badge badge-success">
                                                       Booking  Accepted
                                                   </span>
                                                   @elseif($result->status == 2)
                                                   <span class="badge badge-info">
                                                       Booking  Stopped
                                                   </span>
                                                   @elseif($result->status == 3)
                                                   <span class="badge badge-danger">
                                                    Booking Rejected
                                                    </span>
                                                    @endif
                                                    @if(!empty($result->reject_reason))
                                                    <p>
                                                    <strong>Reason:</strong> {!! nl2br($result->reject_reason) !!}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-auto">
                                    <div class="btn-block pt-3 pt-md-0">
                                        <a class=" btn btn-theme btn-view nanny_details" class="btn-theme mw-100 "
                                        id="{{ $result->id }}">View</a>
                                        @if($result->status == 1)    
                                        <a class=" btn btn-theme btn-stop stop-nanny" id="{{ Crypt::encrypt($result->id) }}">Stop </a>
                                        @endif
                                        <?php // $bookingId = Crypt::encrypt($result->id); ?>
                                        @if($result->status == 2 && $result->updated_by ==  Auth::user()->id)
                                        <a class="btn btn-theme btn-start" onclick="return bookingRestart({{ $result->id }})" onclick="">Restart</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @else
                    <div class="col-md-12">
                        <div class="bg-white block-inner ">
                            <span class="text-center">No Booking Found</span>
                        </div>    
                    </div>
                    @endif
                </div>   
            </div>
            <div class="text-center mt-4">
                @if($results->isNotEmpty())
                <button class="btn btn-theme loadMoreBtn" type="button" onclick="showMore($(this))">
                    Load More
                </button>
                @endif
            </div>
        </div>

    </div>
</div>

<div class="modal fade set-availblity-bx" id="exampleModalLong12" tabindex="-1" role="dialog"
aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered booking-form-model" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Booking Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="book_avaiblity">

                </div>
            </div>
        </div>
    </div>
</div>

    
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.5.2/bootbox.min.js" integrity="sha512-RdSPYh1WA6BF0RhpisYJVYkOyTzK4HwofJ3Q7ivt/jkpW6Vc8AurL1R+4AUcvn9IwEKAPm/fk7qFZW3OuiUDeg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript">

    $(".resetBtn").click(function() {

        location.reload();

        //$("#searchForm")[0].reset();
        // loadBookingData();
    });
    // $("input[name=date_from]").change(function() {
    //     loadBookingData();
    // });
    // $("input[name=date_to]").change(function() {
    //     loadBookingData();
    // });

    function loadBookingData($elem) {
         //$elem.hide();
        $(".loadMoreSpinner").show();
        var formData = new FormData($("#searchForm")[0]);
        formData.append('offset', 0);
        $("#loader_img").show();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '{{route("user.clientBookingList")}}',
            data: formData,
            type: "POST",
            contentType: false,
            cache: false,
            processData: false,
            success: function(res) {
                console.log(res);
                if (res != '') {
                    $("#loader_img").hide();
                    $('.bookingData').html('');
                    $('.bookingData').html(res);
                    $('.loadMoreBtn').show();

                } else {
                    $("#loader_img").hide();
                    $html =
                    ' <div class="col-md-12"><div class="bg-white block-inner "><span class="text-center">No Bookings Found</span></div></div>';
                    $('.bookingData').html($html);
                    $('.loadMoreBtn').hide();
                }
            }
        });
    }
    var offset = 1;

    function showMore($elem) {
        var formData = new FormData($("#searchForm")[0]);
        formData.append('offset', offset);
        $("#loader_img").show();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '{{route("user.clientBookingList")}}',
            data: formData,
            type: "POST",
            contentType: false,
            cache: false,
            processData: false,
            success: function(res) {
                if (res != '') {
                    $elem.show();
                    $("#loader_img").hide();
                    $('.bookingData').append(res);
                    offset++;
                } else {
                    $elem.hide();
                    $("#loader_img").hide();
                }
            }
        });

    }
    $(document).ready(function() {
        $("body").on('click', '.nanny_details', function() {
            var bookingid = $(this).attr('id');

            $("#loader_img").show();
            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                url: '{{ route("user.getBookingSlots") }}',
                type: 'POST',
                data: {
                    'id': bookingid
                },
                success: function(data) {
                    if (data != '') {
                        $("#loader_img").hide();
                        $('.book_avaiblity').html(data.data);
                        $(".nanny-booking").hide();
                        $('#exampleModalLong12').modal('show');
                    }

                }
            });
        });
        $("body").on('click', '.stop-nanny', function() {
            var bookingid = $(this).attr('id');
            var dialog = bootbox.prompt({
                title: "Stop Booking?",
                message: "Are you sure want to Stop Booking?, Please enter the stop reason before stoping the request.",
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
                            'url': '{{ route("user.stop.booking") }}',
                            'data': {"_token": "{{ csrf_token() }}", 'bookingid': bookingid, 'stop_reason': result},
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
        });

    });

    function bookingRestart(id){
           bootbox.confirm({
            title: "Restart Booking",
            message: "Are you sure want to restart booking?",
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
                   window.location.href = "{{ URL('/restart-client-booking') }}/"+id;
               }
               
           }
       });
       }
       
</script>
@stop