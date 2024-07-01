@if(!empty($result))
    @foreach($result as $record)
        @if($record->sender_id == Auth::user()->id)
        <div class="chatright_msg">
            <div class="chatmsg_row">
                <div class="msgBubble">{!! $record->message !!}</div>
                <span>{{date(Config::get('Reading.date_time_format'),strtotime($record->created_at))}}</span>
            </div>
        </div>
        @else 
        <div class="chatright_msg myContact">
            <div class="chatmsg_row">
                <div class="msgBubble">{!! $record->message !!}</div>
                <span>{{date(Config::get('Reading.date_time_format'),strtotime($record->created_at))}}</span>
            </div>
        </div>
        @endif
    @endforeach
@endif