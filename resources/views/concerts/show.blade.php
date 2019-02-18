<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>TicketStorm</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700,800" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:900" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
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
    </div>
</body>
</html>