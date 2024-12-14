<section class="section">
    <div class="row">


        <div class="col-lg-12">

            <div class="card">
                <div class="card-body">

                    <h5 class="card-title">Manage Customer Deposit Subscriptions</h5>
                    @include('layouts.partials.messages')
                    <!-- Table with stripped rows -->

                    <style>
                        .md {
                            margin-bottom: 25px;
                        }

                        .fixed-left {
                            position: sticky;
                            left: 0;
                            background-color: #ffffff;
                            z-index: 1;
                        }

                        @media (max-width: 768px) {
                            .fixed-left {
                                position: static;
                                z-index: auto;
                            }
                        }
                    </style>
                    <div class="row md">

                        <div class="col-md-3">
                            <div wire:ignore>
                                <select wire:model="user_id" wire:ignore class="form-control" id="user_id">
                                    <option value="" selected disabled>Select User</option>
                                    @foreach($users as $user)
                                    <option value="{{$user->id}}">{{$user->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div wire:ignore>
                                <select wire:model="scheme_id" class="form-control" id="scheme_id">
                                    <option value="">Select scheme</option>
                                    @foreach($schemes as $scheme)
                                    <option value="{{$scheme->id}}">{{$scheme->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div wire:ignore>
                                <select wire:model="status" class="form-control" id="status">
                                    <option value="">Select Status</option>
                                    <option value="1">Success</option>
                                    <option value="2">Failed</option>
                                    <option value="0">Processing</option>

                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div wire:ignore>
                                <select wire:model="order_id" class="form-control" id="order_id">
                                    <option value="">Select Order Id</option>
                                    @foreach($orders as $order)
                                    <option value="{{$order->order_id}}">{{$order->order_id}}</option>
                                    @endforeach

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row md">

                        <div class="col-md-3">
                            <input type="date" wire:model="from_date" class="date form-control" id="from_date" wire:change="date_id_filer">
                        </div>
                        <div class=" col-md-3">
                            <input type="date" wire:model="to_date" class="date form-control" id="to_date" wire:change="date_id_filer">
                        </div>

                    </div>

                    <div class="row md">


                        <div class="col-md-3">
                            <button wire:click="resetOnClick" class="btn btn-primary" id="reset">Reset</button>
                        </div>

                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped" style="width: 130%;">  
                            {{-- <div style='text-align: end' ;><a href="{{route('districts.create')}}" class="btn btn-primary"><i class="bi bi-align-middle"></i><span>Add District</span></a>
                    </div> --}}

                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col" width="10%">User</th>
                            <th scope="col">Deposit ID</th>
                            <th scope="col" width="10%">Paid At</th>
                            <th scope="col" width="12%">Scheme</th>
                            <th scope="col">Total Amount</th>
                            <th scope="col">Final Amount</th>
                            <th scope="col">User Type</th>
                            <th scope="col">Payment Type</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($deposits) > 0)
                        @foreach($deposits as $deposit)
                        <tr>
                            <th scope="row">{{ $deposits->firstitem() + $loop->index }}</th>
                            <td>{{ $deposit->subscription?->user?->name }}</td>
                            <td>
                                
                                <a data-bs-toggle="modal" class="model" data-bs-target="#ExtralargeModal" style="color:blue; cursor: pointer; text-decoration: underline;" order_id="{{ encrypt($deposit->order_id) }}" status="{{ $deposit->status }}">
                                    {{ $deposit->order_id }}
                                </a>
                            </td>
                            <td>{{ date('d-m-Y', strtotime($deposit->paid_at)) }}</td>
                            <td>{{ $deposit->subscription?->scheme?->title }}</td>

                            <td>
                                {{ \App\Models\Setting::CURRENCY }} {{ number_format($deposit->total_scheme_amount, 2) }}
                            </td>

                            <td>
                                {{ \App\Models\Setting::CURRENCY }} {{ number_format($deposit->final_amount, 2) }}
                            </td>
                            <td>{{ $deposit->user_type == 'admin' ? 'Admin' : 'Customer' }}</td>
                            <td>{{ $deposit->payment_type }}</td>

                            <td id="deposit_order_id_{{$deposit->id}}">@if($deposit->status == '1') <span class="badge rounded-pill active p-2">Success</span> @elseif($deposit->status=='2') <span class="badge rounded-pill inactive p-2">Failed</span> @else <span class="badge rounded-pill bg-primary p-2">Processed</span> @endif</td>




                            <td class="fixed-left">

                                {{-- <a href="{{route('users.edit-scheme-details',[encrypt($userSubscription->id),encrypt($userSubscription->user?->id),encrypt($userSubscription->scheme?->id)])}}" style="margin-right: 10px;"><i class="bi bi-pencil-square"></i></a> --}}
                                {{-- <a href="javascript:void(0);" onclick="event.preventDefault();
                                                document.getElementById('delete-form-{{ $district->id }}').submit();"><i class="bi bi-x-circle"></i></a> --}}
                            </td>

                            {{-- <form method="post" action="{{route('districts.destroy', encrypt($district->id))}}" style="display:none" id="delete-form-{{$district->id}}">
                            @csrf
                            @method('DELETE')
                            </form> --}}

                        </tr>
                        @endforeach
                        @else
                            <tr>
                                <td colspan="10">No Records available in table</td>
                            </tr>
                        @endif
                        <div class="modal fade" id="ExtralargeModal" tabindex="-1">
                            <div class="modal-dialog modal-xl fetch-deposit-list">
                            </div>
                        </div>
                    </tbody>
                    </table>
                    {{ $deposits->links() }}
                </div>
                <!-- End Table with stripped rows -->

            </div>
        </div>







    </div>
    </div>
</section>
@push('scripts')

<script>
    $('#order_id').on('change', function(e) {
        var order_id = $('#order_id').val();
        @this.set('order_id', order_id);
    });
    $('#user_id').on('change', function(e) {
        var user_id = $('#user_id').val();
        @this.set('user_id', user_id);
    });
    $('#scheme_id').on('change', function(e) {
        var scheme_id = $('#scheme_id').val();
        @this.set('scheme_id', scheme_id);
    });
    $('#status').on('change', function(e) {
        var status = $('#status').val();
        @this.set('status', status);
    });
</script>


<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function fetch_transaction_details() {
        var deposit_id = $('#deposit_id').val();
        $.ajax({
            type: 'POST',
            url: "{{route('users.fetch-transaction-details')}}",
            data: {
                'deposit_id': deposit_id
            },
            dataType: 'JSON',
            success: function(res) {

                if (res.data2 != "") {
                    $('#enter-trasaction-details').hide();
                    $('#fetch-trasaction-details').show();
                    $('#fetch-trasaction-details').html(res.data2);
                } else {
                    $('#enter-trasaction-details').show();
                    $('#fetch-trasaction-details').hide();
                }
            }
        });

    }
    $(document).on('click', '.model', function() {

        var id = $(this).attr('order_id');
        var status = $(this).attr('status');
        if (status == '1') {
            $.ajax({
                url: "{{route('users.fetch-success-deposit-by-order')}}",
                type: "GET",
                data: {
                    order_id: id,
                },
                success: function(response) {

                    $('.fetch-deposit-list').html(response.data);
                    fetch_transaction_details();

                }
            });
        } else {
            $.ajax({
                url: "{{route('users.fetch-failed-deposit-by-order')}}",
                type: "GET",
                data: {
                    order_id: id,
                },
                success: function(response) {

                    $('.fetch-deposit-list').html(response.data);


                }
            });
        }

    });


    $(document).on('click', '.btn-transaction-save', function(e) {
        e.preventDefault();

        $(".is-invalid").removeClass('is-invalid');
        $(".invalid-feedback").removeClass('invalid-feedback').text('');
        
        const formData = new FormData($("#frm_transation_details")[0]);
        if ($("#transaction_no").val() == "") {
            $("#transaction_no").addClass('is-invalid');
            $(".transactionNoError").addClass('invalid-feedback').text("Please enter Transaction No!");
            return false;
        }
        if (!$('#receipt_upload').val()) {
            $("#receipt_upload").addClass('is-invalid');
            $(".receiptUploadError").addClass('invalid-feedback').text("Please upload Receipt!");
            return false;
        }

        $.ajax({
            type: 'POST',
            url: "{{ route('users.save-transaction-details') }}",
            data: formData,
            dataType: 'JSON',
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function(xhr) {
                $(".is-invalid").removeClass('is-invalid');
                $(".invalid-feedback").removeClass('invalid-feedback').text('');
            },
            success: function(res) {
                $('#transaction-loading').hide();
                $("#frmtrasaction").addClass('alert alert-success').text('Transaction details saved successfully');
                $("#frm_transation_details")[0].reset();
                if (res.data2 != "") {
                    $('#enter-trasaction-details').hide();
                    $('#fetch-trasaction-details').show();
                    $('#fetch-trasaction-details').html(res.data2);
                } else {
                    $('#enter-trasaction-details').show();
                    $('#fetch-trasaction-details').hide();
                }
            },
            error: function(data) {
                if (data.responseJSON.errors.transaction_no) {
                   $("#transaction_no").addClass('is-invalid');
                   $(".transactionNoError").addClass('invalid-feedback').text(data.responseJSON.errors.transaction_no[0]);
                }

                if(data.responseJSON.errors.receipt_upload) {
                    $("#receipt_upload").addClass('is-invalid');
                    $(".receiptUploadError").addClass('invalid-feedback').text(data.responseJSON.errors.receipt_upload[0]);
                }
            }
        });
    });
    $(document).on('click', '.btn-failed-process-transaction-save', function(e) {

        e.preventDefault();

        var id = $("#deposit_id").val();

        //var tdText = $('#deposit_order_id_' + id).text(); // Get the text content of the td element

        const formData = new FormData($("#frm_process_failed_transation_details")[0]);
        if ($("#failed_process_status").val() == null) {
            $("#frmfailedtrasaction").addClass('alert alert-danger').text("Please Select Status");
            return false;
        }
        $.ajax({
            type: 'POST',
            url: "{{route('users.save-failed-process-status')}}",
            data: formData,
            dataType: 'JSON',
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function(xhr) {
                // Show the loader
                $('#process-transaction-loading').show();
                $("#frmfailedtrasaction").removeClass('alert alert-danger');
            },
            success: function(res) {

                if (res.data == '1') {
                    $('#process-transaction-loading').hide();
                    $("#frmfailedtrasaction").addClass('alert alert-success').text('Status Updated Successfully');
                    $('#extralargeFailedModal').modal('hide');
                    $('#failed-process-status-update').hide();
                    if ($("#failed_process_status").val() == '1') {
                        $('#deposit_order_id_' + id).text('Success');
                    } else {
                        $('#tb_' + id + ' td#failed_status').text('Failed');
                        $('#deposit_order_id_' + id).text('Failed');

                    }
                }

            },
            error: function(data) {
                let err_str = '';
                $('#process-transaction-loading').hide();
                if (data.responseJSON.errors) {
                    loading = false;
                    $('#loader').html('');
                    err_str =
                        '<dl class="row"><dt class="col-sm-3"></dt><dt class="col-sm-9"><p><b>Whoops!</b> There were some problems with your input.</p></dt>';
                    $.each(data.responseJSON.errors, function(key, val) {
                        err_str += '<dt class="col-sm-3">' + key.replace("_",
                                " ") + ' </dt><dd class="col-sm-9">' + val +
                            '</dd>';
                    });
                    err_str += '</dl>';
                    $('.error_transaction_msg').addClass('alert alert-danger').html(err_str);
                    return;
                }
            }
        });
    });
</script>
@endpush