@extends('layouts.page')
@section('content')

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Pay Desposit Subscription From Admin Side</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{route('deposits.index')}}">Deposits</a></li>
                <li class="breadcrumb-item active">Pay Desposit Subscription From Admin Side</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <section class="section">
        <div class="row">
            <div class="col-lg-12">




                <div class="card">

                    <div class="card-body">
                        <h5 class="card-title"><strong>Pay Deposit</strong> For Subscription</h5>

                        {{-- <div style='text-align: end' ;><a href="{{route('deposits.index')}}" class="btn btn-primary"><i class="zmdi zmdi-arrow-left" style="padding-right: 6px;"></i><span>Back</span></a>
                    </div><br>--}}
                    @include('layouts.partials.messages')
                    <!-- General Form Elements -->
                    <form method="post" enctype="multipart/form-data" action="">
                        @csrf
                        @method('patch')
                        <div class="row mb-3">
                            <label for="inputText" class="col-sm-2 col-form-label">Users</label>
                            <div class="col-sm-10">
                                <select name="users" class="form-control" id="users">
                                    <option value="" selected disabled>Select the User</option>
                                    @foreach($users as $user)
                                    <option value="{{encrypt($user->id)}}">{{$user->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3" id="scheme_list">
                            <label for="inputText" class="col-sm-2 col-form-label">Users Subscriptions</label>
                            <div class="col-sm-10">
                                <select name="user_subscription_id" class="form-control" id="user_subscription_id">

                                </select>

                            </div>
                        </div>

                        <div class="row mb-3" id="unpaid_depost_list">
                        </div>




                    </form><!-- End General Form Elements -->

                </div>
            </div>
        </div>
        </div>
    </section>

</main>
@endsection
@push('scripts')
<script>
    $('#users').on('change', function() {
        var user_id = $(this).val();
        $.ajax({
            url: '{{ route("users.get-user-subscriptions-list") }}', // URL to your Laravel route
            type: 'GET',
            data: {
                user_id: user_id,
            }, // Pass the serialized data
            dataType: 'json',
            success: function(response) {

                $('#user_subscription_id').html(response.data);
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    });
    $('#user_subscription_id').on('change', function() {
        var user_subscription_id = $(this).val();
        var user_id = $('#users').val();
        $.ajax({
            url: '{{ route("users.unpaid-list") }}', // URL to your Laravel route
            type: 'GET',
            data: {
                user_subscription_id: user_subscription_id,
                user_id: user_id,


            }, // Pass the serialized data
            dataType: 'json',
            success: function(response) {

                $('#unpaid_depost_list').html(response.data);
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    });
    $(document).on('click', '.btn-pay-deposit', function() {
        //console.log(checkedPermissions);

        var user_subscription_id = $("#user_subscription_id").val();

        var jsonData = JSON.stringify(checkedPermissions);

        const formData = new FormData($("#frm_transation_details")[0]);


        if ($("#payment_method").val() == "") {
            $("#payment_method").addClass('is-invalid');
            $(".payment_method").addClass('invalid-feedback').text("Please enter Payment Method!");
            return false;
        } else {
            if ($("#payment_method").val() != "cash") {

                if ($("#transaction_no").val() == "") {
                    $("#transaction_no").addClass('is-invalid');
                    $(".transaction_no").addClass('invalid-feedback').text("Please enter Transaction No!");
                    return false;
                }
            }
        }



        // if ($("#payment_method").val() == "") {

        //     $("#payment_method").val('0')
        // } else {

        //     $("#payment_method").val($("#payment_method").val())
        // }
        // if (!$('#receipt_upload').val()) {
        //     $("#frmtrasaction").addClass('alert alert-danger').text("Please upload Receipt!");
        //     return false;
        // }
        formData.append('subscription_id', user_subscription_id);
        formData.append('totalAmount', totalAmount);
        formData.append('checkdata', jsonData);
        // formData.append('_token', '{{ csrf_token() }}');

        $.ajax({
            url: '{{ route("users.pay-deposit") }}', // URL to your Laravel route
            type: 'POST',
            data: formData, // Pass the serialized data
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,

            beforeSend: function(xhr) {
                $('#loading').show();
                $('.error_transaction_msg').removeClass('alert alert-danger').html('');
                $('#frmtrasaction').removeClass('alert alert-success').html('');
            },
            success: function(response) {
                $('#loading').hide();
                $('#exampleModal').modal('hide');
                toastr.success('Deposit Added Successfully');
                for (var i = 0; i < checkedPermissions.length; i++) {
                    var permission = checkedPermissions[i];
                    var id = permission.date;
                    $('#tableRow_' + id).remove();
                }
                $("#frm_transation_details")[0].reset();
                if (response.data2 != "") {
                    $('#enter-trasaction-details').hide();
                    $('#fetch-trasaction-details').show();
                    $('#fetch-trasaction-details').html(response.data2);
                } else {
                    $('#enter-trasaction-details').show();
                    $('#fetch-trasaction-details').hide();
                }
                $('.btn-add-deposit-model').prop('disabled', true);
            },
            error: function(data) {

                $.each($('.permission'), function() {
                    $(this).prop('checked', false);
                    $('.btn-add-deposit-model').prop('disabled', true);
                });

                if (data.responseJSON && data.responseJSON.errors && data.responseJSON.errors.transaction_no) {
                    $("#transaction_no").addClass('is-invalid');
                    $(".transaction_no").addClass('invalid-feedback').text(data.responseJSON.errors.transaction_no[0]);
                } else {
                    toastr.error('An error occurred: ' + (data.responseJSON.message || 'Please try again.'));
                }

            }
        });
    });
</script>

@endpush