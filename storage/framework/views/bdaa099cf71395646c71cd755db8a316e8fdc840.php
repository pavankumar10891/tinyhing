
<?php $__env->startSection('content'); ?>
<div class="main-workspace">


    <div class="container">

        <div class="dashboard-heading-head">Inbox</div>
        <div class="chatWrap">
            <div class="chatleft">
                <div class="chatleft_scroll">
                    <ul class="list-unstyled mb-0">
                      <?php if(!empty($userData)): ?>

                      <input type="hidden" name="nanny" value="<?php echo e($userData[0]->nanny_id); ?>" id="nanny_id">
                        <?php $__currentLoopData = $userData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li>

                            <a href="javascript:void(0);" onclick="getNannyData(<?php echo e($value->nanny_id); ?>)" class="chat_users" data-id="<?php echo e($value->nanny_id); ?>">
                                <?php if( !empty($value->photo_id) && file_exists(USER_IMAGE_ROOT_PATH.$value->photo_id)): ?>
                                <div class="chatUser" style="background-image: url(<?php echo e(USER_IMAGE_URL.$value->photo_id); ?>);">
                                <?php else: ?>
                                <div class="chatUser" style="background-image: url(<?php echo e(WEBSITE_IMG_URL.'no-female.jpg'); ?>);">
                                <?php endif; ?>    

                                </div>
                                <div class="chatUser_msg">
                                    <div class="form-row align-items-center">
                                        <div class="col-6">
                                            <h4 class="mb-0"><?php echo e($value->nanny); ?></h4>
                                        </div>
                                        <div class="col-6 text-right">
                                            <h6 class="mb-0">12 Hrs ago</h6>
                                        </div>
                                    </div>
                                    <div class="form-row align-items-center">
                                        <div class="col-10">
                                            <p class="mb-0">How are you!</p>
                                        </div>
                                        <div class="col-2 text-right">
                                            <span class="usermsgCount">4</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                       <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>


            <div class="chatright">
                <div class="chatright_inner">
                    <div class="chatright_head">
                        <p class="nanny-data">  
                            
                        </p>

                        <a href="javascript:void(0);" class="chatMobtoggle d-md-none">
                            <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 384.97 384.97"
                                style="enable-background:new 0 0 384.97 384.97;" xml:space="preserve">
                                <g>
                                    <g id="Menu_1_">
                                        <path
                                            d="M12.03,120.303h360.909c6.641,0,12.03-5.39,12.03-12.03c0-6.641-5.39-12.03-12.03-12.03H12.03
                                                c-6.641,0-12.03,5.39-12.03,12.03C0,114.913,5.39,120.303,12.03,120.303z" />
                                        <path d="M372.939,180.455H12.03c-6.641,0-12.03,5.39-12.03,12.03s5.39,12.03,12.03,12.03h360.909c6.641,0,12.03-5.39,12.03-12.03
                                                S379.58,180.455,372.939,180.455z" />
                                        <path
                                            d="M372.939,264.667H132.333c-6.641,0-12.03,5.39-12.03,12.03c0,6.641,5.39,12.03,12.03,12.03h240.606
                                                c6.641,0,12.03-5.39,12.03-12.03C384.97,270.056,379.58,264.667,372.939,264.667z" />
                                    </g>
                                </g>
                            </svg>
                        </a>
                    </div>

                    <div class="chatright_body chat_data">

                        
                        
                        
                    </div>

                    <div class="chatright_foot">

                        <div class="chatTypebox">
                            <input type="hidden" name="nannies_id" value="" id="nannies_id">
                            <textarea name="" id="message"></textarea>
                            <button type="button" class="sendmsg">
                                <i class="far fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function getNannyData(id){
        $(".chat_users").removeClass('active');
        $(this).addClass('active');
        document.getElementById("nannies_id").value = id;
       $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '<?php echo e(route("chat.nanny-data")); ?>',
            data: {
                'id': id
            },
            type: "POST",
            beforeSend: function() {
                $("#loader_img").show();
            },
            
            success: function(res) {
                var histories = res.data;
                var html = '';
                histories.forEach(function(value,key){
                    console.log(value.id);
                    if(value.sender_id == id){
                        html +="<div class='chatright_msg'>\
                            <div class='chatmsg_row'>\
                                <div class='msgBubble'>"+value.message+"</div>\
                            <span>"+value.created_at+"</span>\
                        </div>\
                      </div>";
                    }else{
                       
                        html +="<div class='chatright_msg myContact'>\
                            <div class='chatmsg_row'>\
                                <div class='msgBubble'>"+value.message+"</div>\
                            <span>"+value.created_at+"</span>\
                        </div>\
                      </div>";
                    }
                    

                })
                $("#loader_img").hide();
                $('.chatright_head').html('<div class="chatUser" style="background-image: url('+res.setUserData.image+');"></div><h4 class="mb-0">'+ res.setUserData.name +'</h4>');
                $('')
                if(res.status == 'success'){
                    $('.chat_data').html(html);
                }else{
                   $('.chat_data').html('<label>No Chat Available</lable>');
                 }
            }
        });
    }

    function saveChantNanny(){
        var setnannyid = $('input[name=nannies_id]').val();
        var message = $('#message').val();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '<?php echo e(route("chat.save.nanny-data")); ?>',
            data: {
                'id': setnannyid,'message': message, 
            },
            type: "POST",
            beforeSend: function() {
                $("#loader_img").show();
            },
            success: function(res) {
                $("#loader_img").hide();
                console.log(res);
            }
        });
    }
    $(document).ready(function() {
       var id = $('#nanny_id').val();
       //alert(id);
       getNannyData(id);
       $('.sendmsg').click(function(){

         saveChantNanny();
       });

    });

</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('front.dashboard.layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\xampp\htdocs\tinyhugs\resources\views/front/dashboard/client_inbox.blade.php ENDPATH**/ ?>