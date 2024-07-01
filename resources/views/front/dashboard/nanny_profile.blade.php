@extends('front.dashboard.layouts.default')
@section('content')
<div class="main-workspace">
    <div class="container">

        <div class="dashboard-profile">
            <div class="dashboard-block">
                <div class="row pb-md-4 pb-3 flex-row">
                    <div class="col">
                        <div class="heading">
                            <h4>Referral Code</h4>
                        </div>
                    </div>
                </div>
                <div class="form-bx upload-resume form-group col-md-6">
                    <div class="d-md-inline-flex">
                        <input class="form-control referral_code" type="text" placeholder="Referral Code"
                            readonly
                            value="<?php  echo  !empty($user_data->referral_code) ? $user_data->referral_code  : ''  ?>"
                            id="referral_code">
                        @if($user_data->referral_code)
                        <button type="button" class="btn-theme text-white" id="copyy">Copy</button>
                        @endif
                    </div>
                </div>
            </div>
            <div class="dashboard-block">
                <div class="row pb-4 flex-row">
                    <div class="col">
                        <div class="heading">

                            <h4>Edit Information</h4>
                        </div>
                    </div>
                </div>

                <div class="signup-cont">
                {{ Form::open(array('id' => 'nanny-edit-form', 'class' => 'form', 'files' => true,'enctype'=> 'multipart/form-data')) }}
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="img-wall1">
                                @if(!empty($user_data->photo_id) && file_exists(USER_IMAGE_ROOT_PATH.$user_data->photo_id))
                                    @php($profile_img =  USER_IMAGE_URL.Auth::user()->photo_id) 
                                @else
                                    @php($profile_img =  WEBSITE_IMG_URL.'no-female.jpg' ) 
                                @endif

                                @if(!empty($user_data->cpr_certificate))
                                @php($cpr_certificate =  CERTIFICATES_AND_FILES_URL.Auth::user()->cpr_certificate) 
                                @else
                                @php($cpr_certificate =  '' ) 
                                @endif

                                @if(!empty($user_data->resume))
                                @php($resume =  CERTIFICATES_AND_FILES_URL.Auth::user()->resume) 
                                @else
                                @php($resume =  '' ) 
                                @endif

                                <img id="previewImg" src="{{ $profile_img  }}" class="img-fluid">
                                <a id="removeimg" href="javascript:void(0)"><i class="fa fa-times"
                                        aria-hidden="true"></i></a>
                            </div>
                        </div>
                        <div class="col pl-3">
                            <div class="signup-detail">
                                <h5>Upload you Photo ID</h5>
                                <div class="upload-img">
                                    <label for="profile_pic">
                                        <a class="btn-theme text-white" for="profile_pic1">
                                            <svg aria-hidden="true" focusable="false" data-prefix="fas"
                                                data-icon="cloud-upload-alt" role="img"
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"
                                                class="svg-inline--fa fa-cloud-upload-alt fa-w-20 fa-2x">
                                                <path fill="currentColor"
                                                    d="M537.6 226.6c4.1-10.7 6.4-22.4 6.4-34.6 0-53-43-96-96-96-19.7 0-38.1 6-53.3 16.2C367 64.2 315.3 32 256 32c-88.4 0-160 71.6-160 160 0 2.7.1 5.4.2 8.1C40.2 219.8 0 273.2 0 336c0 79.5 64.5 144 144 144h368c70.7 0 128-57.3 128-128 0-61.9-44-113.6-102.4-125.4zM393.4 288H328v112c0 8.8-7.2 16-16 16h-48c-8.8 0-16-7.2-16-16V288h-65.4c-14.3 0-21.4-17.2-11.3-27.3l105.4-105.4c6.2-6.2 16.4-6.2 22.6 0l105.4 105.4c10.1 10.1 2.9 27.3-11.3 27.3z"
                                                    class=""></path>
                                            </svg>Upload</a>
                                    </label>

                                    <input formcontrolname="image" name="photo_id" type="file" id="profile_pic"
                                        ng-reflect-name="image" class="form-control" hidden="">
                                    <span id="photo_id_error" class="help-inline error"></span>
                                </div>
                            </div>
                        </div>


                    </div>

                    <div class="edit-profile-heading py-0 my-4"> User Information</div>
                    <div class="row">


                        <div class="col-md-4">
                            <div class="form-group input-form mw-100">

                                <input class="form-control" name="name" type="text"
                                    value="<?php  echo  !empty($user_data->name) ? $user_data->name  : ''  ?>"
                                    placeholder="Name">
                                <span id="name_error" class="help-inline error"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group input-form mw-100">

                                <input class="form-control " name="email" type="email"
                                    value="<?php  echo  !empty($user_data->email) ? $user_data->email  : ''  ?>"
                                    placeholder="Email">

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group input-form mw-100">
                                <input class="form-control" type="text" name="phone_number"
                                    value="<?php  echo  !empty($user_data->phone_number) ? $user_data->phone_number  : ''  ?>"
                                    placeholder="Phone Number">
                                <span id="phone_number_error" class="help-inline error"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group input-form mw-100">

                                <input class="form-control cpr_certicate1" type="text"
                                    placeholder="CPR certificate (if applicable)" readonly>
                                <label for="CPR">
                                    <svg aria-hidden="true" focusable="false" data-prefix="fas"
                                        data-icon="cloud-upload-alt" role="img" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 640 512" class="svg-inline--fa fa-cloud-upload-alt fa-w-20 fa-2x">
                                        <path fill="currentColor"
                                            d="M537.6 226.6c4.1-10.7 6.4-22.4 6.4-34.6 0-53-43-96-96-96-19.7 0-38.1 6-53.3 16.2C367 64.2 315.3 32 256 32c-88.4 0-160 71.6-160 160 0 2.7.1 5.4.2 8.1C40.2 219.8 0 273.2 0 336c0 79.5 64.5 144 144 144h368c70.7 0 128-57.3 128-128 0-61.9-44-113.6-102.4-125.4zM393.4 288H328v112c0 8.8-7.2 16-16 16h-48c-8.8 0-16-7.2-16-16V288h-65.4c-14.3 0-21.4-17.2-11.3-27.3l105.4-105.4c6.2-6.2 16.4-6.2 22.6 0l105.4 105.4c10.1 10.1 2.9 27.3-11.3 27.3z"
                                            class=""></path>
                                    </svg>
                                </label>
                                <input formcontrolname="image" type="file" id="CPR" name="cpr_certificate"
                                    ng-reflect-name="image" class="form-control cpr_certicate" hidden="">
                                <span id="cpr_certificate_error" class="help-inline error"></span>
                                    @if($user_data->cpr_certificate != "")
                                        <a href="{{$cpr_certificate}}" download target="_blank"><i class="fa fa-download" aria-hidden="true"></i></a>
                                    @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group input-form mw-100">

                                <input class="form-control other_certificate1" type="text"
                                    placeholder="Other certificates (if applicable)" readonly>


                                <label for="Other_crp">
                                    <svg aria-hidden="true" focusable="false" data-prefix="fas"
                                        data-icon="cloud-upload-alt" role="img" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 640 512" class="svg-inline--fa fa-cloud-upload-alt fa-w-20 fa-2x">
                                        <path fill="currentColor"
                                            d="M537.6 226.6c4.1-10.7 6.4-22.4 6.4-34.6 0-53-43-96-96-96-19.7 0-38.1 6-53.3 16.2C367 64.2 315.3 32 256 32c-88.4 0-160 71.6-160 160 0 2.7.1 5.4.2 8.1C40.2 219.8 0 273.2 0 336c0 79.5 64.5 144 144 144h368c70.7 0 128-57.3 128-128 0-61.9-44-113.6-102.4-125.4zM393.4 288H328v112c0 8.8-7.2 16-16 16h-48c-8.8 0-16-7.2-16-16V288h-65.4c-14.3 0-21.4-17.2-11.3-27.3l105.4-105.4c6.2-6.2 16.4-6.2 22.6 0l105.4 105.4c10.1 10.1 2.9 27.3-11.3 27.3z"
                                            class=""></path>
                                    </svg>
                                </label>
                                @if($user_data->other_certificates != "")
                                    <a href="{{$cpr_certificate}}" download target="_blank"><i class="fa fa-download" aria-hidden="true"></i></a>
                                @endif   
                                <input formcontrolname="image" type="file" name="other_certificates" id="Other_crp"
                                    ng-reflect-name="image" class="form-control other_certificate" hidden="">
                                <span id="other_certificates_error" class="help-inline error"></span>
                                    

                            </div>
                        </div>

                    </div>
                    
                    <div class="upload-resume">
                        <div class="d-block d-xl-flex no-gutters">
                            <div class="form-group col-md-6">
                                <label>Upload you Resume</label>

                                <div class="d-md-inline-flex">
                                    <input class="form-control resume-input resume1" type="text" placeholder="Update Resume" readonly>
                                    <label for="profile_pic3">
                                        <a class="btn-theme text-white">Upload</a>
                                    </label>
                                    <input formcontrolname="image" type="file" id="profile_pic3" name="resume" ng-reflect-name="image" class="form-control resume" hidden="">
                                    
                                </div>
                                @if($user_data->resume != "")
                                    <a href="{{$resume}}" download target="_blank"><i class="fa fa-download" aria-hidden="true"></i></a>
                                    @endif
                            </div>


                            <div class="form-group col-md-6 ml-xl-3">

                                <label>Select Zip Code/ Postal Code</label>
                                <div class="d-md-inline-flex">
                                    <input class="form-control"
                                        value="<?php  echo  !empty($user_data->postcode) ? $user_data->postcode  : ''  ?>"
                                        name="postcode" type="text" placeholder="Select Zip Code/ Postal Code">
                                    <span id="postcode_error" class="help-inline error"></span>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-center">
                    <div class="form-group identification col-md-6">
                        <label>Identification Type</label><br>
                    <div class="custom-radio mr-md-2">    <input class="identification_type" type="radio" name="identification_type" value="1" id="identification_type_passport" {{ ($user_data->identification_type == 1) ? 'checked':'' }}> <label for="identification_type_passport">Passport  </label></div>
                    <div class="custom-radio">     <input class="identification_type" type="radio" name="identification_type" value="2" id="identification_type_license" {{ ($user_data->identification_type == 2) ? 'checked':'' }}> <label for="identification_type_license">Driving License </label></div>





                          <!-- <label for="profile_pic2"> <a class="btn-theme text-white">Upload</a> </label> -->
                          <span id="identification_type_error" class="help-inline error"></span>
                    </div>
                    <div class="form-group d-inline-flex col-md-6">
                          <input class="form-control identification_file1" type="text" placeholder="Upload your Passport" readonly="">
                          <label for="identification_file">
                          <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="cloud-upload-alt" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" class="svg-inline--fa fa-cloud-upload-alt fa-w-20 fa-2x">
                            <path fill="currentColor" d="M537.6 226.6c4.1-10.7 6.4-22.4 6.4-34.6 0-53-43-96-96-96-19.7 0-38.1 6-53.3 16.2C367 64.2 315.3 32 256 32c-88.4 0-160 71.6-160 160 0 2.7.1 5.4.2 8.1C40.2 219.8 0 273.2 0 336c0 79.5 64.5 144 144 144h368c70.7 0 128-57.3 128-128 0-61.9-44-113.6-102.4-125.4zM393.4 288H328v112c0 8.8-7.2 16-16 16h-48c-8.8 0-16-7.2-16-16V288h-65.4c-14.3 0-21.4-17.2-11.3-27.3l105.4-105.4c6.2-6.2 16.4-6.2 22.6 0l105.4 105.4c10.1 10.1 2.9 27.3-11.3 27.3z" class=""></path>
                          </svg>
                        </label>
                        <input formcontrolname="image" type="file" id="identification_file" name="identification_file" ng-reflect-name="image" class="form-control identification_file" hidden="">
                        <span id="identification_file_error" class="help-inline error"></span>
                        @if($user_data->identification_file != "")
                                        <a href="{{ CERTIFICATES_AND_FILES_URL.$user_data->identification_file }}" class="download-img" download="{{ $user_data->identification_file }}" title="Click To Download">
                                            <img src="{{ WEBSITE_IMG_URL.'download.png' }}" width="30px" height="">
                                        </a>
                                    @endif
                    </div>
                  </div>
                    
                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group input-form mw-100">
                                <input class="form-control"
                                    value="<?php  echo  !empty($user_data->age) ? $user_data->age  : ''  ?>" type="text"
                                    name="age" placeholder="Age">
                                <span id="age_error" class="help-inline error"></span>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group input-form mw-100">
                                <input class="form-control" name="city" type="text"
                                    value="<?php  echo  !empty($user_data->city) ? $user_data->city  : ''  ?>"
                                    placeholder="City">
                                <span id="city_error" class="help-inline error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group input-form mw-100">
                                <input class="form-control" name="state" type="text"
                                    value="<?php  echo  !empty($user_data->state) ? $user_data->state  : ''  ?>"
                                    placeholder="Province">
                                <span id="state_error" class="help-inline error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group input-form mw-100">
                                <input class="form-control" type="text" name="experience"
                                    value="<?php  echo  !empty($user_data->experience) ? $user_data->experience  : ''  ?>"
                                    placeholder="Experience">
                                <span id="experience_error" class="help-inline error"></span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group input-form mw-100">
                                <input class="form-control" type="text" name="nanny_price"
                                    value="<?php  echo  !empty($user_data->nanny_price) ? $user_data->nanny_price  : ''  ?>"
                                    placeholder="Fee Charge">
                                <span id="nanny_price_error" class="help-inline error"></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group input-form mw-100">
                                <textarea class="w-100" name="description" rows="3"
                                    placeholder="Description"><?php  echo  !empty($user_data->description) ? $user_data->description  : ''  ?></textarea>
                                <span id="description_error" class="help-inline error"></span>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-theme text-white" id="save-user-data">Save Changes</button>
                    {{Form::close()}}
                </div>
            </div>
            <div class="dashboard-block">
                <div class="row pb-md-4 pb-3 flex-row">
                    <div class="col">
                        <div class="heading">
                            <h4>Reset Password</h4>
                        </div>
                    </div>
                    <!-- <div class="col-md-auto">
                   <div class="create-account">   Already have an account,<a href="">Log In   </a></div>
               </div> -->
                </div>
                <div class="form-bx">


                    {{ Form::open(array('id' => 'change-password', 'class' => 'form')) }}
                    <div class="pass-block">
                        <div class="form-group input-form mw-100">
                            {{Form::password('old_password', ['class' => 'form-control ', 'placeholder' => 'Old
                            Password'])}}
                            <span id="old_password_error" class="help-inline error"></span>
                        </div>
                        <div class="form-group input-form mw-100">
                            {{Form::password('new_password', ['class' => 'form-control', 'placeholder' => 'New
                            Password'])}}
                            <span id="new_password_error" class="help-inline error"></span>
                        </div>

                        <div class="form-group input-form mb-1 mw-100">
                            {{Form::password('new_password_confirmation', ['class' => 'form-control', 'placeholder' =>
                            'Confirm Password'])}}
                            <span id="new_password_confirmation_error" class="help-inline error"></span>
                        </div>

                    </div>
                    <div class="btn-block">
                        <button type="button" id="password-reset" class="btn-theme">
                            Submit
                        </button>
                    </div>

                    {{Form::close()}}
                </div>

            </div>

        </div>
    </div>

</div>

<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<script type="text/javascript">

    $('#copyy').click(function () {
        var text = document.getElementById("referral_code");

        text.select();

        document.execCommand("copy");

    });

    function signUp() {
        var form = $("#nanny-edit-form").closest("form");
        var formData = new FormData(form[0]);
        $.ajax({
            url: "{{ route('nanny.profile.update')}}",
            method: 'post',
            //data: $('#user-registration-form').serialize(),
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                $("#loader_img").show();
            },
            success: function (response) {
                $("#loader_img").hide();

                if (response.success) {
                    window.location.href = response.page_redirect;
                } else {

                    $('span[id*="_error"]').each(function () {
                        var id = $(this).attr('id');

                        if (id in response.errors) {

                            $("#" + id).html(response.errors[id]);
                        } else {
                            $("#" + id).html('');
                        }
                    });
                }
            }
        });
    }

    $(function () {
        $('#nanny-edit-form').keypress(function (e) { //use form id
            if (e.which == 13) {
                //-- to validate form 
                signUp();
                return false;
            }
        });
    });

</script>

<script type="text/javascript">
    $(document).ready(function () {
        $("#removeimg").hide();
        $('#save-user-data').click(function () {
            signUp();
        });



        $('#profile_pic').change(function () {
            $('#photo_id_error').html('');
            var fsize = this.files[0].size,
                ftype = this.files[0].type,
                fname = this.files[0].name,
                fextension = fname.substring(fname.lastIndexOf('.') + 1);
            validExtensions = ["jpg", "jpeg", "gif", "png"];
            if ($.inArray(fextension, validExtensions) == -1) {
                $('#photo_id_error').html('The photo id must be in: jpeg, jpg, png, gif, bmp formats');
                this.value = "";
                return false;
            } else {
                if (fsize > 3145728) {
                    $('#photo_id_error').html('File size too large! Please upload less than 3MB');
                    this.value = "";
                    return false;
                }
                const file = this.files[0];

                if (file) {
                    console.log(file);
                    let reader = new FileReader();
                    reader.onload = function (event) {
                        console.log(event.target.result);
                        $("#removeimg").show();
                        $('#previewImg').attr('src', event.target.result);
                    }
                    reader.readAsDataURL(file);
                }
            }

        });

        $('#removeimg').click(function () {
            $('#previewImg').attr('src', '<?php echo WEBSITE_IMG_URL; ?>signup-profile.PNG');
            $("#removeimg").hide();
            //$('#previewImg').remove();
        });
        $('.cpr_certicate').on('change', function () {
            $('#cpr_certificate_error').html('');
            var fsize = this.files[0].size,
                ftype = this.files[0].type,
                fname = this.files[0].name,
                fextension = fname.substring(fname.lastIndexOf('.') + 1);

            validExtensions = ["jpg", "pdf", "jpeg", "gif", "png"];
            $('.cpr_certicate1').val(fname);
            if ($.inArray(fextension, validExtensions) == -1) {
                $('#cpr_certificate_error').html('The cpr certificate must be in: pdf, docx, doc formats');
                this.value = "";
                return false;
            } else {
                return true;
            }
        });

        // $('.other_certificate').change(function(){
        $('.other_certificate').on('change', function () {
            $('#other_certificates_error').html('');
            var fsize = this.files[0].size,
                ftype = this.files[0].type,
                fname = this.files[0].name,
                fextension = fname.substring(fname.lastIndexOf('.') + 1);

            validExtensions = ["jpg", "pdf", "jpeg", "gif", "png"];
            $('.other_certificate1').val(fname);
            if ($.inArray(fextension, validExtensions) == -1) {
                $('#other_certificates_error').html('The other certificates must be in: pdf, docx, doc formats');
                this.value = "";
                return false;
            } else {
                return true;
            }
        });

        //  $('.resume').change(function(){
        $('.resume').on('change', function () {
            $('#resume_error').html('');
            var fsize = this.files[0].size,
                ftype = this.files[0].type,
                fname = this.files[0].name,
                fextension = fname.substring(fname.lastIndexOf('.') + 1);

            validExtensions = ["jpg", "pdf", "jpeg", "gif", "png", "doc", "docx"];
            $('.resume1').val(fname);
            if ($.inArray(fextension, validExtensions) == -1) {
                $('#resume_error').html('The resume must be in: jpeg, jpg, png, gif, bmp, pdf, docx, doc, xls, excel formats');
                this.value = "";
                return false;
            } else {
                return true;
            }
        });


        $('#password-reset').click(function () {
            $.ajax({
                url: "{{ url('password-update/')}}",
                method: 'post',
                data: $('#change-password').serialize(),
                beforeSend: function () {
                    $("#loader_img").show();
                },
                success: function (response) {
                    $("#loader_img").hide();

                    if (response.success) {
                        //console.log(response.);
                        //window.location.href="{{url('/')}}";
                        window.location.href = response.page_redirect;
                        //location.reload();
                    } else {

                        $('span[id*="_error"]').each(function () {
                            var id = $(this).attr('id');

                            if (id in response.errors) {
                                $("#" + id).html(response.errors[id]);
                            } else {
                                $("#" + id).html('');
                            }
                        });
                    }
                }
            });
        });
    });


</script>

@endsection