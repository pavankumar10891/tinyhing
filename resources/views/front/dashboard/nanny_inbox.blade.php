@extends('front.dashboard.layouts.default')
@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/4.0.0/socket.io.js" integrity="sha512-74AKPNm8Tfd5E9c4otg7XNkIVfIe5ynON7wehpX/9Tv5VYcZvXZBAlcgOAjLHg6HeWyLujisAnle6+iKnyWd9Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="{{WEBSITE_JS_URL}}moment.js"></script>
<div class="main-workspace">
<div class="container">
   <div class="dashboard-heading-head">Inbox</div>
        @if(!$userData->isEmpty())
        <div class="chatWrap">
           
            <div class="chatleft">
                <div class="chatleft_scroll">
                    <ul class="list-unstyled mb-0 users_list">
                   
                        @foreach($userData as $key=>$value)
                        <li>
                                @if( !empty($value->photo_id) && file_exists(USER_IMAGE_ROOT_PATH.$value->photo_id))
                                   <?php $image = USER_IMAGE_URL.$value->photo_id;?>
                                @else
                                    <?php $image = WEBSITE_IMG_URL.'no-female.jpg';?>
                                @endif
                            <a href="javascript:void(0);"class="chat_users get_user" data-id="{{$value->user_id}}" data-name="{{ $value->customer }}"  data-image="{{ $image }}">
                                @if( !empty($value->photo_id) && file_exists(USER_IMAGE_ROOT_PATH.$value->photo_id))
                                    <div class="chatUser" style="background-image: url({{USER_IMAGE_URL.$value->photo_id}});">
                                @else
                                    <div class="chatUser" style="background-image: url({{WEBSITE_IMG_URL.'no-female.jpg'}});">
                                @endif
                                </div>
                                <div class="chatUser_msg">
                                    <div class="form-row align-items-center">
                                        <div class="col-6">
                                            <h4 class="mb-0">{{$value->customer}}</h4>
                                        </div>
                                        <!-- <div class="col-6 text-right">
                                            <h6 class="mb-0">12 Hrs ago</h6>
                                        </div> -->
                                    </div>
                                    <!-- <div class="form-row align-items-center">
                                        <div class="col-10">
                                            <p class="mb-0">How are you!</p>
                                        </div>
                                        <div class="col-2 text-right">
                                            <span class="usermsgCount">4</span>
                                        </div>
                                    </div> -->
                                </div>
                            </a>
                        </li>
                       @endforeach
                        
                    </ul>
                </div>
            </div>
            <div class="chatright">
                <div class="chatright_inner" id="kt_chat_content">
                    <div class="chatright_head">
                        <div class="chatUser selected_userimage" style="background-image: url('img/profile-img1.jpg');"></div>
                        <h4 class="mb-0 selected_user"></h4>
                        <a href="javascript:void(0);" class="chatMobtoggle d-md-none">
                            <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 384.97 384.97" style="enable-background:new 0 0 384.97 384.97;" xml:space="preserve">
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
                        
                    </div>
                    <div class="chatright_foot">
                        <div class="chatTypebox">
                            <input type="hidden" name="nanny" value="{{$userData[0]->nanny_id}}" id="select_user_id">
                            <textarea name="" id="message_input"></textarea>
                            <button type="button" class="sendmsg send_message"><i class="far fa-paper-plane"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
            <p class="text-center">No Conversation</p>
        @endif
    </div>
</div>
<script>
function get_messageThread(id){
    $.ajax({
        url: '{{ URL::to("get-chat-history") }}',
        type: "POST",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            sender_id: <?php echo Auth::user()->id; ?>,
            receiver_id:id
        },
        success: function(response) {
            $("#message_section").html(response)
        },
        error: function(err) {
            //$("#loader_img").hide();
        }
    });
}
$(".get_user").click(function(){
    $(".get_user").removeClass('active');
    var image = $(this).data('image');
    var name = $(this).data('name');
    var id   = $(this).data('id');
    $(".selected_userimage").attr('data-image',image);
    $(".selected_userimage").css("background-image", "url(" + image + ")");
    $(".selected_user").html(name);
    $("#select_user_id").val(id);
    $("#kt_chat_content").show();
    get_messageThread(id);
    $(this).addClass('active');
});
var client = io.connect('<?php echo NODE_JS_SERVER;?>', {'transports': ['websocket']});
client.on('connect', function(data){
    client.emit('roomLogin', {room: "user_"+<?php echo Auth::user()->id; ?>,user_id:<?php echo Auth::user()->id; ?>});
});
$(document).ready(function(){
    $(".users_list").find('.get_user:first').trigger('click')
    function nl2br(str, is_xhtml) {
        var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
        return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
    }
    $(".send_message").click(function(){
        var message = $.trim($('#message_input').val());
        var final_msg = nl2br(message);
        if (message == ''){
            $('#message_input').parent().css({
                'border': 'red solid 1px'
            });
        }else{
            $('#message_input').parent().css({
                'border': ''
            });
            var msg = 'Just Now'
            var image = '{{ WEBSITE_IMG_URL."logo-light.png" }}'
            var append_record = '<div class="chatright_msg">\
                                    <div class="chatmsg_row">\
                                        <div class="msgBubble">'+final_msg+'</div>\
                                        <span>'+msg+'</span>\
                                    </div>\
                                </div>';
            $('#message_section').append(append_record);
            $("#message_section").animate({
                scrollTop: $("#message_section")[0].scrollHeight
            }, 'slow');
            $('#message_input').val('');
            $('.no_chat_found').hide();
            client.emit('saveChatHistory',{
                message:message,
                sender_id:<?php echo Auth::user()->id; ?>,
                receiver_id:$("#select_user_id").val(),
                user_id:$("#select_user_id").val()
            });
        }
    });
    client.on('receive_message',function(data){
        console.log(data)
        if(data.receiver_id == <?php echo Auth::user()->id; ?>){
            var append_record = '<div class="chatright_msg myContact">\
                                    <div class="chatmsg_row">\
                                        <div class="msgBubble">'+nl2br(data.message)+'</div>\
                                        <span>Just Now</span>\
                                    </div>\
                                </div>';
            $('#message_section').append(append_record);
            $("#message_section").animate({
                scrollTop: $("#message_section")[0].scrollHeight
            }, 'slow');
        }
    });
});
</script>
<style>
.active{
    background: #f3f3f3;
}
</style>
@stop