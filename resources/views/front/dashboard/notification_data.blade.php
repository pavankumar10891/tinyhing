@if($results->isNotEmpty())
    @foreach($results as $result)
    <li>
        <a href="javascript:void(0);" class="unread">
            <div class="notiwall">
                <svg aria-hidden="true" focusable="false" data-prefix="fas"
                    data-icon="calendar-alt" role="img" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 448 512" class="svg-inline--fa fa-calendar-alt fa-w-14 fa-2x">
                    <path fill="currentColor"
                        d="M0 464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V192H0v272zm320-196c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12h-40c-6.6 0-12-5.4-12-12v-40zm0 128c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12h-40c-6.6 0-12-5.4-12-12v-40zM192 268c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12h-40c-6.6 0-12-5.4-12-12v-40zm0 128c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12h-40c-6.6 0-12-5.4-12-12v-40zM64 268c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12H76c-6.6 0-12-5.4-12-12v-40zm0 128c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12H76c-6.6 0-12-5.4-12-12v-40zM400 64h-48V16c0-8.8-7.2-16-16-16h-32c-8.8 0-16 7.2-16 16v48H160V16c0-8.8-7.2-16-16-16h-32c-8.8 0-16 7.2-16 16v48H48C21.5 64 0 85.5 0 112v48h448v-48c0-26.5-21.5-48-48-48z"
                        class=""></path>
                </svg>
            </div>

            <p class="notidesc">{{$result->message}}
            <?php
                $currentDate=date('m-d-Y');
                $dataDate=date('m-d-Y',strtotime($result->created_at));
                $currentHour=date('H'); 
                $dataHour=date('H',strtotime($result->created_at)); 
            ?>
            @if($currentDate == $dataDate)
                <span>{{$currentHour - $dataHour}} Hrs ago</span>
            @else
            <span>{{date(Config::get('Reading.date_time_format'),strtotime($result->created_at))}}</span>
            @endif
            </p>

        </a>
    </li>
    @endforeach
@endif