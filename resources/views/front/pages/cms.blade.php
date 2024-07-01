@extends('front.layouts.default')
@section('content')

<div class="padding-section terms-conditions">
  <div class="container">
  @if(!empty($result))
    <div class="heading py-lg-5 pb-3  text-center">

        <h4>{{  !empty($result->title)? $result->title  : ''}}</h4>
    </div>
      <div class="row">
     
          <div class="col-md-12">
                <div class="terms-service-block">
                    
                {!!  !empty($result->body)? $result->body  : '' !!}
              
                </div>
             </div> 
         </div>
         @endif
    </div>
</div>
   
@endsection