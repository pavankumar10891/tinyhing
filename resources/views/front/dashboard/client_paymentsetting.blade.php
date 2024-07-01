@extends('front.dashboard.layouts.default')
@section('content')
<div class="main-workspace">


    <div class="rating-block backg-img">
        <div class="container">
            <div class="heading pt-lg-5 pb-3  pt-3   ">
                <h4>Payment Setting</h4>
            </div>
            ededewdew
            <div class="row">
                <div class="col-md-6">
                    {{ Form::open(array('id' => 'addCardForm', 'class' => 'form')) }}
                    <div class="pass-block">
                        <div class="form-group input-form mw-100">
                            <label for="">Card Number</label>
                            {{Form::text('card_number', '', ['class' => 'form-control ', 'placeholder' => 'Card Number'])}}
                            <span id="card_number_error" class="help-inline error"></span>
                        </div>
                        <div class="form-group input-form mw-100">
                            <label for="">Expiration Date</label>
                            <div class="row">
                                <div class="col-md-6">
                                    {{Form::text('month', '', ['class' => 'form-control ', 'placeholder' => 'MM'])}}
                                    <span id="month_error" class="help-inline error"></span>
                                </div>
                                <div class="col-md-6">
                                    {{Form::text('year', '', ['class' => 'form-control ', 'placeholder' => 'YYYY'])}}
                                    <span id="year_error" class="help-inline error"></span>
                                </div>

                            </div>

                        </div>
                        <div class="form-group input-form mw-100">
                            <label for="">Name on a Card</label>
                            {{Form::text('card_name', '', ['class' => 'form-control ', 'placeholder' => 'Name on a Card'])}}
                            <span id="card_name_error" class="help-inline error"></span>
                        </div>
                        <div class="form-group input-form mw-100">
                            <label for="">Security Number</label>
                            {{Form::text('security_number', '', ['class' => 'form-control ', 'placeholder' => 'Security Number'])}}
                            <span id="security_number_error" class="help-inline error"></span>
                        </div>

                    </div>
                    <div class="btn-block">
                        <button type="button" id="add-card" class="btn-theme">
                            Add Card
                        </button>
                    </div>

                    {{Form::close()}}

                </div>

            </div>










        </div>
    </div>

</div>



@stop