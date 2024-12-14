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
        <h1>{{ __('Admins') }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Admins') }}</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">


            <div class="col-lg-12">

                <div class="card">
                    <div class="card-title d-flex justify-content-between m-3 mt-0">
                        <h5><b>{{ __('Manage Admins') }}</b></h5>
                        <a href="{{route('admins.create')}}" class="btn btn-primary"><i class="bi bi-align-middle"></i> <span class="text-white">Create Admin</span></a>
                    </div>
                    <div class="card-body table-responsive">

                        @include('layouts.partials.messages')
                        <!-- Table with stripped rows -->
                        <table class="table table-striped">

                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Status</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($admins) > 0)
                                @foreach($admins as $admin)
                                <tr>
                                    <th scope="row">{{ $admins->firstItem() + $loop->index }}</th>
                                    <td>{{ $admin->name }}</td>
                                    <td>{{ $admin->email }}</td>
                
                                    <td><span
                                            {{ $admin->is_admin == true ?  
                                                    'class=active' : 'class=inactive' }}>
                                            {{ ($admin->is_admin == true) ?  
                                                        'Active' : 'Not Active' }}
                                        </span></td>
                                    <td>
                                        <a href="{{ route('admins.edit', encrypt($admin->id)) }}" style="margin-right: 10px;"><i class="bi bi-pencil-square"></i></a>
                                        @role('superadmin')
                                            <a href="javascript:void(0);" onclick="event.preventDefault(); deleteAdmin('{{ $admin->id }}');"><i class="bi bi-x-circle"></i></a>
                                        @endrole
                                    </td>
                                    <form method="post" action="{{ route('admins.destroy', encrypt($admin->id)) }}" style="display:none" id="delete-form-{{ $admin->id }}">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="5">No Records available in table</td>
                                </tr>
                                @endif
                            </tbody>

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
    function deleteAdmin(id) {

        swal({
                title: "Are you sure ?",
                text: "Do you want to delete this admin ?",
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

</script>
@endpush