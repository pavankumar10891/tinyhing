@extends('front.layouts.default')
@section('content')

<div class="padding-section terms-conditions">
  <div class="container">
  @if(!empty($terms))
    <div class="heading py-lg-5 pb-3  text-center">

        <h4>{{  !empty($terms->title)? $terms->title  : ''}}</h4>
    </div>
      <div class="row">
     
          <div class="col-md-12">
                <div class="terms-service-block">
                    
                {!!  !empty($terms->body)? $terms->body  : '' !!}
              
                </div>
             </div> 
         </div>
         @endif
    </div>
</div>
   
@endsection