@extends('layout')

@section('content')
    <div class="event">
        <h2 class="event__header">{{ $concert->title }}</h2>
        <h4 class="event__sub-header">{{ $concert->subtitle }}</h4>
        <div class="event__row">
            <div class="event__icon"><i class="far fa-calendar-alt"></i></div>
            <div class="event__date">{{ $concert->formatted_date }}</div>
        </div>
        <div class="event__row">
            <div class="event__icon"><i class="far fa-clock"></i></div>
            <div class="event__start">Doors at {{ $concert->formatted_time }}</div>
        </div>
        <div class="event__row">
            <div class="event__icon"><i class="fas fa-dollar-sign"></i></div>
            <div class="event__price">{{ $concert->formatted_price }}</div>
        </div>
        <div class="event__row">
            <div class="event__icon"><i class="fas fa-map-marker-alt"></i></div>
            <div class="event__location">
                <p>{{ $concert->location }}</p>
                <p class="secondary-text">{{ $concert->street }}</p>
                <p class="secondary-text">{{ $concert->city }}, {{ $concert->state }} {{ $concert->zip }}</p>
            </div>
        </div>
        <div class="event__row">
            <div class="event__icon"><i class="fas fa-info-circle"></i></div>
            <div class="event__info">
                <p>Additional Information</p>
                <p class="secondary-text">{{ $concert->additional_info }}</p>
            </div>
        </div>
        <ticket-checkout :price="{{$concert->price}}" concert-title="{{$concert->title}}" :concert-id="{{$concert->id}}"></ticket-checkout>
    </div>
@endsection

@push('beforeScripts')
<script src="https://checkout.stripe.com/checkout.js"></script>
@endpush