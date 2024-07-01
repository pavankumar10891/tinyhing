<div class="client-block">
    <div class="dashboard-heading-head">My Earnings</div>
</div>

<div class="row">
    <div class="col-md-8 order-2 order-md-1">
        <div class="earning_filter">
            <div class="theme-input position-relative">
                <i class="fal fa-calendar-alt inputIcon"></i>
                <input type="text" name="created_at" class="form-control" placeholder="Search by date" id="created_at" value="{{$dateValue}}"/> 
            </div>
        </div>
        <div class="theme-table theme-table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th class="text-right">Earning</th>
                    </tr>
                </thead>

                <tbody>

                    @if(!empty($earnings))
                    @foreach($earnings as $earning)
                    <tr>
                        <td data-label="Date">
                            {{ !empty($earning->created_at) ? date('d/m/Y',strtotime($earning->created_at)) : '' }}
                        </td>
                        <td data-label="Type">
                            @if($earning->type == 1)
                            <span class="badge-theme"> Booking</span>
                            @elseif($earning->type == 2)
                            <span class="badge-theme"> Tip</span>
                            @else
                            @endif
                        </td>
                        <td data-label="Earning" class="text-right font-600">
                            ${{ !empty($earning->amount > 0) ? number_format($earning->amount,2):0 }}
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="3" class="text-center">No Record Found.</td>
                    </tr>
                    @endif

                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-4 order-1 order-md-2">
        <div class="totalearn_box">
            <h4>Total Earnings</h4>

            <h5>${{ !empty($totalEarnings) ? $totalEarnings : 0 }}</h5>

            <a href="javascript:void(0);" class="btn btn-theme">
                Withdraw Now
            </a>
        </div>
    </div>
</div>


<script>

    $('#created_at').datepicker({

        onSelect: function (selectedDate) {
            $("#loader_img").show();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '{{route("user.nannyEarningListSearch")}}',
                data: {date:selectedDate},
                type: "get",
                success: function(res) {
                    console.log(res);
                    if (res != '') {
                     $("#loader_img").hide();

                     $('.mainEarningShow').html('');
                     $('.mainEarningShow').html(res);


                 } else {
                    $("#loader_img").hide();

                    $html =
                    ' <div class="col-md-12"><div class="bg-white block-inner "><span class="text-center">No Earnings Found</span></div></div>';
                    $('.mainEarningShow').html($html);

                }
            }
        });
        }
    });

</script>
