@extends('admin.layouts.default')
@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/4.0.0/socket.io.js" integrity="sha512-74AKPNm8Tfd5E9c4otg7XNkIVfIe5ynON7wehpX/9Tv5VYcZvXZBAlcgOAjLHg6HeWyLujisAnle6+iKnyWd9Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="{{WEBSITE_JS_URL}}moment.js"></script>
<div class="d-flex flex-column-fluid">
    <!--begin::Container-->
    <div class=" container ">
        <!--begin::Chat-->
        <div class="d-flex flex-row">
            <!--begin::Aside-->
            <div class="flex-row-auto offcanvas-mobile w-350px w-xl-400px" id="kt_chat_aside">
                <!--begin::Card-->
                <div class="card card-custom">
                    <!--begin::Body-->
                    <div class="card-body">
                        <!--begin:Search-->
                        <!-- <div class="input-group input-group-solid">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <span class="svg-icon svg-icon-lg"><svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                            viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect x="0" y="0" width="24" height="24" />
                                                <path
                                                    d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z"
                                                    fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                                <path
                                                    d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z"
                                                    fill="#000000" fill-rule="nonzero" />
                                            </g>
                                        </svg>
                                    </span> </span>
                            </div>
                            <input type="text" class="form-control py-4 h-auto" placeholder="Email" />
                        </div> -->
                        <!--end:Search-->

                        <!--begin:Users-->
                        <div class="mt-7 scroll scroll-pull users_list">
                            @if(!empty($result))
                                @foreach($result as $user)
                                <!--begin:User-->
                                @if(!empty($user->photo_id) && file_exists(USER_IMAGE_ROOT_PATH.$user->photo_id))
                                    <?php $image = USER_IMAGE_URL.$user->photo_id ?>
                                @else
                                <?php $image = WEBSITE_IMG_URL.'no-image.png' ?>
                                @endif
                                    <div class="d-flex align-items-center justify-content-between mb-5 get_user" data-image="{{ $image }}" data-name="{{ $user->name }}"  data-id="{{ $user->id }}" style="padding: 10px;">
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-circle symbol-50 mr-3">
                                                @if(!empty($user->photo_id) && file_exists(USER_IMAGE_ROOT_PATH.$user->photo_id))
                                                    <img alt="Pic" src="{{ USER_IMAGE_URL.$user->photo_id }}" />
                                                @else
                                                    <img alt="Pic" src="{{ WEBSITE_IMG_URL.'no-image.png' }}" />
                                                @endif
                                            </div>
                                            <div class="d-flex flex-column">
                                                <a href="#" class="text-dark-75 text-hover-primary font-weight-bold font-size-lg">{{ $user->name }}</a>
                                                <!-- <span class="text-muted font-weight-bold font-size-sm">Head of
                                                    Development</span> -->
                                            </div>
                                        </div>
                                        <!-- <div class="d-flex flex-column align-items-end">
                                            <span class="text-muted font-weight-bold font-size-sm">35 mins</span>
                                        </div> -->
                                    </div>
                                <!--end:User-->
                                @endforeach
                            @endif
                            
                        </div>
                        <!--end:Users-->
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Card-->
            </div>
            <!--end::Aside-->

            <!--begin::Content-->
            <div class="flex-row-fluid ml-lg-8" id="kt_chat_content">
                <!--begin::Card-->
                <div class="card card-custom">
                    <!--begin::Header-->
                    <div class="card-header align-items-center px-4 py-3">
                        <div class="text-center text-center">
                            <div class="symbol-group symbol-hover justify-content-center">
                                <div class="symbol symbol-35 symbol-circle selected_userimage" data-toggle="tooltip" title="Ana Fox">
                                    <img alt="Pic" src="" />
                                </div>
                                <span class="selected_user"></span>
                            </div>
                        </div>
                    </div>
                    <!--end::Header-->

                    <!--begin::Body-->
                    <div class="card-body ">
                        <!--begin::Scroll-->
                        <div class="scroll scroll-pull" data-mobile-height="350">
                            <!--begin::Messages-->
                            <div class="messages" id="message_section">
                              

                            </div>
                            <!--end::Messages-->
                        </div>
                        <!--end::Scroll-->
                    </div>
                    <!--end::Body-->

                    <!--begin::Footer-->
                    <div class="card-footer align-items-center">
                        <!--begin::Compose-->
                        <input type="hidden" value="0" id="select_user_id"> 
                        <textarea class="form-control border-0 p-0" rows="2"  id="message_input" placeholder="Type a message"></textarea>
                        <div class="d-flex align-items-center justify-content-between mt-5">
                            <div>
                                <button type="button"
                                    class="send_message btn btn-primary btn-md text-uppercase font-weight-bold chat-send py-2 px-6">Send</button>
                            </div>
                        </div>
                        <!--begin::Compose-->
                    </div>
                    <!--end::Footer-->
                </div>
                <!--end::Card-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Chat-->
    </div>
    <!--end::Container-->
</div>
<script src="{{ WEBSITE_JS_URL }}chat.js"></script>
<script>
function get_messageThread(id){
    $.ajax({
        url: '{{ URL::to("adminpnlx/get-chat-history") }}',
        type: "POST",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            user_id: id
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
    var id = $(this).data('id');
    $(".selected_userimage").find('img').attr('src',image);
    $(".selected_user").html(name);
    $("#select_user_id").val(id);
    $("#kt_chat_content").show();
    get_messageThread(id);
    $(this).addClass('active');
});
var client = io.connect('<?php echo NODE_JS_SERVER;?>', {'transports': ['websocket']});
client.on('connect', function(data){
    console.log(data)
    client.emit('roomLogin', {room: "support_1",user_id:1});	
    
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
            if (message == '') {
                $('#message_input').parent().css({
                    'border': 'red solid 1px'
                });
            } else {
                $('#message_input').parent().css({
                    'border': ''
                });
                var msg = 'Just Now'
                var image = '{{ WEBSITE_IMG_URL."logo-light.png" }}'
                var append_record = '<div class="d-flex flex-column mb-5 align-items-end">\
                                    <div class="d-flex align-items-center">\
                                        <div>\
                                            <span class="text-muted font-size-sm">'+msg+'</span>\
                                            <a href="#" class="text-dark-75 text-hover-primary font-weight-bold font-size-h6">You</a>\
                                        </div>\
                                        <div class="symbol symbol-circle symbol-35 ml-3">\
                                            <img alt="Pic" src="'+image+'" />\
                                        </div>\
                                    </div>\
                                    <div\
                                        class="mt-2 rounded p-5 bg-light-primary text-dark-50 font-weight-bold font-size-lg text-right max-w-400px">'+final_msg+'\
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
                    sender_id:1,
                    receiver_id:$("#select_user_id").val(),
                    user_id:$("#select_user_id").val(),
                    support_id:<?php echo Auth::guard('admin')->user()->id; ?>,
                    user_type:'support',
                });
            }
        });
        client.on('receive_support_message',function(data){
            var image = $(".selected_userimage").find('img').attr('src');
            var append_record = '<div class="d-flex flex-column mb-5 align-items-start">\
                                    <div class="d-flex align-items-center">\
                                        <div>\
                                            <span class="text-muted font-size-sm">Just Now</span>\
                                            <a href="#" class="text-dark-75 text-hover-primary font-weight-bold font-size-h6">'+data.sender_name+'</a>\
                                        </div>\
                                        <div class="symbol symbol-circle symbol-35 ml-3">\
                                            <img alt="Pic" src="'+image+'" />\
                                        </div>\
                                    </div>\
                                    <div\
                                        class="mt-2 rounded p-5 bg-light-primary text-dark-50 font-weight-bold font-size-lg text-right max-w-400px">'+nl2br(data.message)+'\
                                    </div>\
                                </div>';;
           $('#message_section').append(append_record);
            $("#message_section").animate({
                scrollTop: $("#message_section")[0].scrollHeight
            }, 'slow');
        });
    })
</script>
<style>
.active{
    background: #f3f3f3;
}
    
</style>
@stop