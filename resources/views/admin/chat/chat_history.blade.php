@if(!empty($result))
    @foreach($result as $record)
        @if($record->user_type == 'support')
            <!--begin::Message Out-->
            <div class="d-flex flex-column mb-5 align-items-end">
                <div class="d-flex align-items-center">
                    <div>
                        <span class="text-muted font-size-sm">{{date(Config::get('Reading.date_time_format'),strtotime($record->created_at))}}</span>
                        <a href="#" class="text-dark-75 text-hover-primary font-weight-bold font-size-h6">{{ $record->sender_name }}</a>
                    </div>
                    <?php $logo = CustomHelper::getlogo();
                    $logoimage = '';
                    if($logo){
                        $logoimage = $logo->image;
                    }
                    ?>
                    <div class="symbol symbol-circle symbol-35 ml-3">
                        <img alt="Pic" src="{{ $logoimage }}" />
                    </div>
                </div>
                <div
                    class="mt-2 rounded p-5 bg-light-primary text-dark-50 font-weight-bold font-size-lg text-right max-w-400px">
                    {!!  $record->message !!}
                </div>
            </div>
            <!--end::Message Out-->
        @else
            <!--begin::Message In-->
            <div class="d-flex flex-column mb-5 align-items-start">
                <div class="d-flex align-items-center">
                    @if(!empty($record->receiver_photo_id) && file_exists(USER_IMAGE_ROOT_PATH.$record->receiver_photo_id))
                        <?php $image = USER_IMAGE_URL.$record->receiver_photo_id ?>
                    @else
                    <?php $image = WEBSITE_IMG_URL.'no-image.png' ?>
                    @endif
                    <div class="symbol symbol-circle symbol-35 mr-3">
                        <img alt="Pic" src="{{ $image }}" />
                    </div>
                    <div>
                        <a href="#"
                            class="text-dark-75 text-hover-primary font-weight-bold font-size-h6">{{ $record->receiver_name }}</a>
                        <span class="text-muted font-size-sm">{{date(Config::get('Reading.date_time_format'),strtotime($record->created_at))}}</span>
                    </div>
                </div>
                <div
                    class="mt-2 rounded p-5 bg-light-success text-dark-50 font-weight-bold font-size-lg text-left max-w-400px">
                    {!!  $record->message !!}
                </div>
            </div>
            <!--end::Message In-->
        @endif
    @endforeach
@endif