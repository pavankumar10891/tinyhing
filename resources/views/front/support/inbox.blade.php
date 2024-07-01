@extends('front.dashboard.layouts.default')
@section('content')
<!-- <script src="{{WEBSITE_JS_URL}}socket.io-1.4.5.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/4.0.0/socket.io.js" integrity="sha512-74AKPNm8Tfd5E9c4otg7XNkIVfIe5ynON7wehpX/9Tv5VYcZvXZBAlcgOAjLHg6HeWyLujisAnle6+iKnyWd9Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="{{WEBSITE_JS_URL}}moment.js"></script>
<div class="main-workspace">
    <div class="container">
        <div class="dashboard-heading-head">Inbox</div>
        <div class="chatWrap">

            <div class="chatright">
                <div class="chatright_inner">
                    <div class="chatright_head">
                        <?php $logo = CustomHelper::getlogo();
                        $logoimage = '';
                        if($logo){
                            $logoimage = $logo->image;
                        } 
                        ?>
                        <div class="chatUser" style="background-image: url('<?php echo $logoimage; ?>');">
                        </div>
                        <h4 class="mb-0">Tiny Hugs Support</h4>

                        <a href="javascript:void(0);" class="chatMobtoggle d-md-none">
                            <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px"
                                y="0px" viewBox="0 0 384.97 384.97" style="enable-background:new 0 0 384.97 384.97;" xml:space="preserve">
                                <g>
                                    <g id="Menu_1_">
                                        <path d="M12.03,120.303h360.909c6.641,0,12.03-5.39,12.03-12.03c0-6.641-5.39-12.03-12.03-12.03H12.03
                                        c-6.641,0-12.03,5.39-12.03,12.03C0,114.913,5.39,120.303,12.03,120.303z" />
                                        <path d="M372.939,180.455H12.03c-6.641,0-12.03,5.39-12.03,12.03s5.39,12.03,12.03,12.03h360.909c6.641,0,12.03-5.39,12.03-12.03
                                        S379.58,180.455,372.939,180.455z" />
                                        <path d="M372.939,264.667H132.333c-6.641,0-12.03,5.39-12.03,12.03c0,6.641,5.39,12.03,12.03,12.03h240.606
                                        c6.641,0,12.03-5.39,12.03-12.03C384.97,270.056,379.58,264.667,372.939,264.667z" />
                                    </g>
                                </g>
                            </svg>
                        </a>
                    </div>

                    <div class="chatright_body" id="message_section">
                        @if(!empty($result))
                            @foreach($result as $record)
                                @if($record->user_type=='support')
                                    <div class="chatright_msg myContact">
                                @else
                                    <div class="chatright_msg ">
                                @endif
                                    <div class="chatmsg_row">
                                        <div class="msgBubble">{!!  $record->message !!}</div>
                                        <span>{{date(Config::get('Reading.date_time_format'),strtotime($record->created_at))}}</span>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="chatmsg_row no_chat_found">
                                <div class="msgBubble">No conversation</div>
                            </div>
                        @endif
                    </div>

                    <div class="chatright_foot">
                        <div class="chatTypebox">
                            <textarea name="" id="message_input"></textarea>
                            <button type="button" class="sendmsg send_message">
                                <i class="far fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
.chatright {
    -webkit-box-flex: 0;
    -ms-flex: 0 1 calc(100% - 350px);
    flex: 0 1 calc(100%);
    max-width: calc(100%);
    width: 100%;
    height: 500px;
    max-height: 500px;
}
</style>
<script>
var client = io.connect('<?php echo NODE_JS_SERVER;?>', {'transports': ['websocket']});
client.on('connect', function(data){
    console.log(data)
    client.emit('roomLogin', {room: "support_<?php echo Auth::user()->id; ?>",user_id: "<?php echo Auth::user()->id; ?>"});	
    
});
$(document).ready(function(){
        $("#message_section").animate({
            scrollTop: $("#message_section")[0].scrollHeight
        }, 'slow');
        function nl2br(str, is_xhtml) {
            var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
            return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
        }
        $(".send_message").click(function(){
            var message = $.trim($('#message_input').val());
            var final_msg = nl2br(message);
            if (message == '') {
                $('#message_input').css({
                    'border': 'red solid 1px'
                });
            } else {
                $('#message_input').css({
                    'border': ''
                });
                var msg = 'Just Now'
                var append_record = ' <div class="chatright_msg">\
                                <div class="chatmsg_row">\
                                    <div class="msgBubble">' + final_msg + '</div>\
                                    <span>' + msg + '</span>\
                                </div>\
                            </div>';
                $('#message_section').append(append_record);
                $("#message_section").animate({
                    scrollTop: $("#message_section")[0].scrollHeight
                }, 'slow');
                $('#message_input').val('');
                $('.no_chat_found').hide();
                client.emit('saveSupportChatHistory',{
                    message:message,
                    sender_id:<?php echo Auth::user()->id; ?>,
                    receiver_id:1,
                    user_id:<?php echo Auth::user()->id; ?>,
                    support_id:1,
                    user_type:'user',
                });
            }
        });
        client.on('receive_support_message',function(data){
            var append_record = ' <div class="chatright_msg myContact">\
                                <div class="chatmsg_row">\
                                    <div class="msgBubble">' + nl2br(data.message) + '</div>\
                                    <span>Just Now</span>\
                                </div>\
                            </div>';
           $('#message_section').append(append_record);
           $("#message_section").animate({
                scrollTop: $("#message_section")[0].scrollHeight
            }, 'slow');
        });	
    })
</script>
@stop