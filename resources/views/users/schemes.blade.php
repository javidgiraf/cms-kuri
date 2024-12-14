@extends('layouts.page')
@section('content')
<main id="main" class="main">
    <style>
        span.active {
            color: white;
            background-color: green;
            padding: 5px;
            font-size: 12px;
        }

        span.inactive {
            color: white;
            background-color: red;
            padding: 5px;
            font-size: 12px;
        }
    </style>
    <div class="pagetitle">
        <h1>Customer Subscriptions</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
                <li class="breadcrumb-item active">Customer Subscriptions</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    @livewire('user-subscription-filter')

</main>
@endsection
