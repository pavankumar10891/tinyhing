@extends('front.layouts.default')
@section('content')
<div class="padding-section">
   <div class="container">
       <div class="faq py-lg-5 py-4">
        <div class="heading pb-lg-4 pb-4 text-center">

            <h3>Frequently Asked Questions</h3>
        <p>Need any help jusct send a message via our email address</p>
        </div>
        <div id="accordion" class="accordion">
        @if(!empty($faqs ))
        @php($count = 1)
          @foreach($faqs as $key=>$_faqs)
           
            <div class="card">
                <div class="card-header @if($key != 0)collapsed @endif " data-toggle="collapse" href="#collapse_{{ $count }}">
                    <a class="card-title"> {!! $_faqs->question !!} </a>
                </div>
                <div id="collapse_{{ $count }}" class="card-body collapse @if($key == 0)show @endif" data-parent="#accordion">
                    {!! $_faqs->answer !!}
                </div>
               
              
            </div>
            @php( $count = $count+1 )
           @endforeach 
       @endif
      
        </div>
   </div>
</div>
</div>


@endsection