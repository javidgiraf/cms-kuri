<?php

use App\Services\UserService;
?>
@extends('layouts.page')
@push('styles')
<style>
    /* The switch - the box around the slider */
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    p {
        display: inline-block;
        vertical-align: middle;
        padding-top: 5px;
        padding-right: 5px;
    }

    /* Hide default HTML checkbox */
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    /* The slider */
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
        border-radius: 34px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked+.slider {
        background-color: #2196F3;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked+.slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }
</style>
@endpush
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

        .modal {

            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border-radius: 10px;

        }
    </style>
    <div class="pagetitle">
        <h1>{{ __('Customers') }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('home')}}">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Customers') }}</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">


            <div class="col-lg-12">

                <div class="card">
                    <div class="card-title d-flex justify-content-between m-3 mt-0">
                        <h5><b>{{ __('Manage Customers') }}</b></h5>
                        <a href="{{route('users.create')}}" class="btn btn-primary"><i class="bi bi-align-middle"></i> <span class="text-white">Add Customer</span></a>
                    </div>
                    <div class="card-body table-responsive">

                        @include('layouts.partials.messages')
                        <!-- Table with stripped rows -->
                        <table class="table table-striped">

                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Scheme</th>
                                    <th scope="col">Phone</th>
                                    <th scope="col">Profile Completion</th>
                                    <th scope="col">Created At</th>
                                    <th scope="col">Status</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($users) > 0)
                                @foreach($users as $user)
                                <tr>
                                    <th scope="row">{{ $users->firstItem() + $loop->index }}</th>
                                    <td>
                                        {{ $user->name }}
                                        <br>
                                        <p><span {{ isset($user->customer) ? ($user->customer->is_verified == true ? 'class=active' : 'class=inactive') : 'class=inactive' }}> {{ isset($user->customer) ? ($user->customer->is_verified == true ? 'Verified' : 'Not Verified') : 'Not Verified' }} </span></p>
                                    </td>
                                    <td>

                                        {{ ($user->UserSubscriptions->first()) ?

                                                $user->UserSubscriptions->first()->scheme->title : '' }}
                                    </td>
                                    <td>{{ isset($user->customer) ? $user->customer->mobile : '' }}</td>
                                    <td>
                                        <div class="progress mt-1 w-100">
                                            <div
                                                class="progress-bar"
                                                role="progressbar"
                                                style="width: <?= \App\Services\UserService::calculateCompletionPercentage($user) ?>%;"
                                                aria-valuenow="<?= \App\Services\UserService::calculateCompletionPercentage($user) ?>"
                                                aria-valuemin="0"
                                                aria-valuemax="100">
                                                {{ \App\Services\UserService::calculateCompletionPercentage($user) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ date('d/m/Y', strtotime($user->created_at)) }}</td>
                                    <td id="status{{ $user->id }}">
                                        <span
                                            {{ isset($user->customer) ? 
                                                ($user->customer->status == true ? 
                                                    'class=active' : 'class=inactive') 
                                                    : 'class=inactive' }}>
                                            {{ isset($user->customer) ? 
                                                    ($user->customer->status == true ? 
                                                        'Active' : 'Not Active') : 'Not Active' }}
                                        </span>
                                    </td>

                                    <td>
                                        <a href="{{ route('users.show', encrypt($user->id)) }}" style="margin-right: 10px;"><i class="bi bi-eye"></i></a>
                                        <a href="{{ route('users.edit', encrypt($user->id)) }}" style="margin-right: 10px;"><i class="bi bi-pencil-square"></i></a>
                                        <a href="javascript:void(0);" onclick="event.preventDefault(); deleteUser('{{ $user->id }}');"><i class="bi bi-x-circle"></i></a>
                                    </td>
                                    <form method="post" action="{{route('users.destroy', encrypt($user->id))}}" style="display:none" id="delete-form-{{$user->id}}">
                                        @csrf
                                        @method('DELETE')
                                    </form>

                                </tr>
                                @endforeach
                                @else
                                    <tr>
                                        <td colspan="8">No records available in table</td>
                                    </tr>
                                @endif

                            </tbody>

                            <tfoot>
                                <tr>
                                    <td colspan="8">{{ $users->links() }}</td>
                                </tr>
                            </tfoot>

                        </table>
                        <!-- End Table with stripped rows -->

                    </div>
                </div>







            </div>
        </div>
    </section>

</main>
@endsection
@push('scripts')
<script>
    function deleteUser(id) {

        swal({
                title: "Are you sure ?",
                text: "Do you want to delete this user ?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    var $modal = $('.modal');

    //when hidden
    $modal.on('hidden.bs.modal', function(e) {
        $('.success').removeClass('alert alert-success').html('');
    });

    var switchStatus = false;
    $(".togBtn").on('change', function() {
        var id = $(this).attr('data-id');

        if ($(this).is(':checked')) {
            switchStatus = $(this).is(':checked');
            $("#togBtn" + id).val('1');
        } else {
            switchStatus = $(this).is(':checked');
            $("#togBtn" + id).val('0');
        }
    });
    $('.btn-change-status').on('click', function() {
        var id = $(this).attr('id');
        var switchToggle = $('#togBtn' + id).val();
        $.ajax({
            //  url: "{{route('users.update-plan-status')}}", // URL to your Laravel route
            url: "{{route('users.change-status')}}", // URL to your Laravel route
            type: 'POST',
            data: {
                user_id: id,
                status: switchToggle,

            }, // Pass the serialized data
            dataType: 'json',
            beforeSend: function(xhr) {
                $('#loading' + id).show();
                $('.success').removeClass('alert alert-success').html('');

            },
            success: function(response) {
                $('#loading' + id).hide();
                if (switchToggle == '1') {
                    ($('#status' + id).html('<span class="active">Active</span><span style="padding-left: 9px;"><a data-bs-toggle="modal" class="model" data-bs-target="#ExtralargeModal' + id + '" style="color:blue"><i class ="bi bi-pencil-square"></i></a></span>'));
                } else {
                    ($('#status' + id).html('<span class="inactive">Not Active</span><span style="padding-left: 9px;"><a data-bs-toggle="modal" class="model" data-bs-target="#ExtralargeModal' + id + '" style="color:blue"><i class ="bi bi-pencil-square"></i></a></span>'));

                }
                // $('.modal').modal('hide');
                $('.success').addClass('alert alert-success').html('Status Updated Successfully');

            }
        });




    });
</script>
@endpush