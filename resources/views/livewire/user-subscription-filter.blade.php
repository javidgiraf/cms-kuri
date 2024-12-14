<section class="section">
    <div class="row">


        <div class="col-lg-12">

            <div class="card">
                <div class="card-body">

                    <h5 class="card-title">Manage Customer Subscriptions</h5>
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
                                <select wire:model="user_id" class="form-control" wire:change="user_table_filer" id="user_id">
                                    <option value="">Select Customer</option>
                                    @foreach($users as $user)
                                    <option <?= request('user_id') == $user->id ? 'selected' : '' ?> value="{{$user->id}}">{{$user->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div wire:ignore>
                                <select wire:model="scheme_id" class="form-control" wire:change="scheme_table_filer" id="scheme_id">
                                    <option value="">Select scheme</option>
                                    @foreach($schemes as $scheme)
                                    <option <?= request('scheme_id') == $scheme->id ? 'selected' : '' ?> value="{{$scheme->id}}">{{$scheme->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div wire:ignore>
                                <select wire:model="mature_status" class="form-control" wire:change="mature_status_table_filer" id="mature_status">
                                    <option value="">Select Maturity Status</option>
                                    <option value="1">Closed</option>
                                    <option value="0">Open</option>

                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div wire:ignore>
                                <select wire:model="scheme_status" class="form-control" wire:change="scheme_status_table_filer" id="scheme_status">
                                    <option value="">Select Status</option>
                                    <option value="1">Active</option>
                                    <option value="0">In active</option>
                                    <option value="2">Discontinue</option>
                                    <option value="3">Oh Hold</option>

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

                        <div class="col-md-3">
                            <button wire:click="resetOnClick" class="btn btn-primary" id="reset">Reset</button>

                            @can(['subscriptionsExport'])
                            <a class="btn btn-success btnExport" onclick="updateExportUrl();">Export To Excel</a>
                            @endcan
                        </div>
                    </div>


                    <div style="overflow-x:auto;">
                        <table class="table table-striped" style="width: 110%;" id="subscriptionsTable">
                            {{-- <div style='text-align: end' ;><a href="{{route('districts.create')}}" class="btn btn-primary"><i class="bi bi-align-middle"></i><span>Add District</span></a>
                    </div> --}}

                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col" width="10%">User</th>
                            <th scope="col">Scheme</th>
                            <th scope="col">Amount</th>
                            <th scope="col">Collected</th>
                            <th scope="col">Date Period</th>
                            <th scope="col">Closing Status</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="fixed-left"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($userSubscriptions) > 0)
                        @foreach($userSubscriptions as $userSubscription)
                        <tr>
                            <th scope="row">{{ $userSubscriptions->firstitem() + $loop->index }}</th>
                            <td>{{$userSubscription->user?->name}}</td>
                            <td>{{$userSubscription->scheme?->title}}</td>

                            <td>{{ \App\Models\Setting::CURRENCY }} {{ number_format($userSubscription->subscribe_amount, 2) }}</td>
                            <td>
                                @php $totalAmount = 0; @endphp
                                @foreach($userSubscription->deposits as $deposit)
                                @if($deposit->status=='1')
                                @php $totalAmount += $deposit->final_amount @endphp
                                @endif
                                @endforeach
                                {{ \App\Models\Setting::CURRENCY }} {{ number_format($totalAmount, 2) }}
                            </td>
                            <td>
                                {{ date('d/m/Y', strtotime($userSubscription->start_date)) }}
                                -
                                {{ date('d/m/Y', strtotime($userSubscription->end_date)) }}
                            </td>

                            <?php

                            $status = NULL;
                            switch ($userSubscription->status) {
                                case $userSubscription::STATUS_ACTIVE:
                                    $status = '<span class="active activeStatus">Active</span>';
                                    break;
                                case $userSubscription::STATUS_DISCONTINUE:
                                    $status = '<span class="badge bg-secondary discontinueStatus p-2" style="border-radius: 0;">Discontinued</span>';
                                    break;
                                case $userSubscription::STATUS_ONHOLD:
                                    $status = '<span class="badge bg-warning onHoldStatus p-2" style="border-radius: 0;">On Hold</span>';
                                    break;
                                case $userSubscription::STATUS_INACTIVE:
                                    $status = '<span class="inactive inActiveStatus">In Active</span>';
                                    break;
                            }

                            ?>

                            <td>
                                <a data-subscription-id="{{ $userSubscription->id }}"
                                   data-maturity-status="{{ $userSubscription->is_closed }}"
                                   data-bs-toggle="modal" data-bs-target="#changeMaturityStatusModal"
                                   style="cursor: pointer;" class="changeMaturityStatusBtn">
                                    <span data-subscription-id="{{ $userSubscription->id }}" data-maturity-status="{{ $userSubscription->is_closed }}" class="<?= $userSubscription->is_closed == '0' ? 'badge bg-primary' : "badge bg-danger" ?>">
                                        {{ $userSubscription->is_closed == '0' ? "Open" : "Closed" }}
                                    </span>
                                </a>
                            </td>

                            <td><a 
                                    data-subscription-id="{{ $userSubscription->id }}" 
                                    data-status="{{ $userSubscription->status }}" 
                                    data-reason="{{ $userSubscription->reason }}" 
                                    data-final-amount="{{ ($userSubscription->discontinue) ? $userSubscription->discontinue->final_amount : 0 }}"
                                    data-settlement-amount="{{ ($userSubscription->discontinue) ? $userSubscription->discontinue->settlement_amount : 0 }}"
                                    data-bs-toggle="modal" data-bs-target="#changeStatusModal" style="cursor: pointer;" class="changeStatusBtn">{!! $status !!}</a></td>



                            <td class="fixed-left">
                                <a href="{{route('users.edit-scheme-details',[encrypt($userSubscription->id),encrypt($userSubscription->user?->id),encrypt($userSubscription->scheme?->id)])}}" style="margin-right: 10px;">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>



                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="9">No Records available in table</td>
                        </tr>
                        @endif
                        <div class="modal fade" id="ExtralargeModal" tabindex="-1">
                            <div class="modal-dialog modal-xl fetch-list">


                            </div>
                        </div>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="9">{{ $userSubscriptions->links() }}</td>
                        </tr>
                    </tfoot>
                    </table>

                </div>
                <!-- End Table with stripped rows -->

            </div>
        </div>







    </div>
    </div>

    <div class="modal fade" id="changeStatusModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Change Subscription Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Status <span class="text-danger">*</span></label>
                        <select class="form-control mt-1" id="subscription_status" name="subscription_status" style="width: 100%;">
                            <option value="">Select Status</option>
                            <option value="<?= \App\Models\UserSubscription::STATUS_ACTIVE ?>">Active</option>
                            <option value="<?= \App\Models\UserSubscription::STATUS_DISCONTINUE ?>">Discontinue</option>
                            <option value="<?= \App\Models\UserSubscription::STATUS_ONHOLD ?>">On Hold</option>
                            <option value="<?= \App\Models\UserSubscription::STATUS_INACTIVE ?>">In Active</option>
                        </select>
                        <span class="subscriptionStatusError"></span>
                    </div>
                    <div class="d-none" id="discontinue-details">
                        <h6 class="mt-3"><b>Discontinue Details</b></h6>
                    
                            <div class="form-group mt-2">
                                <label>Final Amount</label>
                                <input type="text" id="final_amount" name="final_amount" class="form-control mt-1" placeholder="Final Amount">
                            </div>
                            <div class="form-group mt-2">
                                <label>Settlement Amount</label>
                                <input type="text" id="settlement_amount" name="settlement_amount" class="form-control mt-1" placeholder="Settlement Amount">
                            </div>
                        
                        
                    </div>
                    <div class="form-group mt-2">
                        <label>Reason</label>
                        <textarea name="reason" id="reason" class="form-control mt-1"></textarea>
                    </div>
                    <input type="hidden" id="subscription_id" name="subscription_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary updateBtn">Update</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="changeMaturityStatusModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Change Maturity Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Maturity Status <span class="text-danger">*</span></label>
                        <select class="form-control mt-1" id="maturity_status" name="maturity_status" style="width: 100%;">
                            <option value="">Select Maturity Status</option>
                            <option value="1">Close the Subscription</option>
                        </select>
                        <span class="maturityStatusError"></span>
                    </div>

                    <div class="form-group mt-2">
                        <label>Reason</label>
                        <textarea name="maturity_reason" id="maturity_reason" class="form-control mt-1"></textarea>
                    </div>
                    <input type="hidden" id="maturity_subscription_id" name="maturity_subscription_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary updateMaturityBtn">Update</button>
                </div>
            </div>
        </div>
    </div>
</section>


@push('scripts')
<script>
    let baseUrl = "{{ route('users.get-user-subscriptions') }}";
    let exportUrl = "{{ route('subscriptionsExport') }}";

    function updateExportUrl() {
        // Collect filter values
        var user_id = $('#user_id').val();
        var scheme_id = $('#scheme_id').val();
        var mature_status = $('#mature_status').val();
        var scheme_status = $('#scheme_status').val();
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();

        // Construct query parameters
        var queryParams = [];
        if (user_id) queryParams.push('user_id=' + user_id);
        if (scheme_id) queryParams.push('scheme_id=' + scheme_id);
        if (mature_status) queryParams.push('mature_status=' + mature_status);
        if (scheme_status) queryParams.push('scheme_status=' + scheme_status);
        if (from_date) queryParams.push('from_date=' + from_date);
        if (to_date) queryParams.push('to_date=' + to_date);

        // Update export button URL
        let newExportUrl = exportUrl + (queryParams.length ? '?' + queryParams.join('&') : '');
        $('.btnExport').attr('href', newExportUrl);
    }

    function updateUrlAndLivewire(property, value) {
        @this.set(property, value);
        updateExportUrl();
    }

    $('#user_id').on('change', function() {
        updateUrlAndLivewire('user_id', $(this).val());
    });

    $('#scheme_id').on('change', function() {
        updateUrlAndLivewire('scheme_id', $(this).val());
    });

    $('#mature_status').on('change', function() {
        updateUrlAndLivewire('mature_status', $(this).val());
    });

    $('#scheme_status').on('change', function() {
        updateUrlAndLivewire('scheme_status', $(this).val());
    });

    $('#from_date, #to_date').on('change', function() {
        updateUrlAndLivewire('from_date', $('#from_date').val());
        updateUrlAndLivewire('to_date', $('#to_date').val());
    });

    $(document).on('click', '.model', function() {
        var id = $(this).attr('id');
        var user_id = $(this).attr('user_id');
        var scheme_id = $(this).attr('scheme_id');

        $.ajax({
            url: "{{route('users.current-plan-history')}}",
            type: "GET",
            data: {
                user_subscription_id: id,
                user_id: user_id,
                scheme_id: scheme_id
            },
            success: function(response) {

                $('.fetch-list').html(response.data);

            }
        });
    });

    $(document).on('change', '#subscription_status', function(){
        if($(this).val() == <?= \App\Models\UserSubscription::STATUS_DISCONTINUE ?>) {
            $("#discontinue-details").removeClass('d-none');
        }
        else {
            $("#discontinue-details").addClass('d-none');
        }
    });

    $(document).on('click', '.changeStatusBtn', function() {
        let subscription_id = $(this).data('subscription-id');
        let status = $(this).data('status');
        let reason = $(this).data('reason');
        let final_amount = $(this).data('final-amount');
        let settlement_amount = $(this).data('settlement-amount');

        $("#subscription_id").val(subscription_id);
        $("#subscription_status").val(status).trigger('change');
        $("#reason").val(reason);
        $("#final_amount").val(final_amount);
        $("#settlement_amount").val(settlement_amount);
    });

    $("#changeStatusModal").on('click', '.updateBtn', function(e) {

        $(".is-invalid").removeClass('is-invalid');
        $(".invalid-feedback").removeClass('invalid-feedback').text("");

        var subscription_id = $("#subscription_id").val();
        var status = $('#subscription_status').val();
        var final_amount = $("#final_amount").val();
        var settlement_amount = $("#settlement_amount").val();
        var reason = $('#reason').val();

        $.ajax({
            url: "{{ route('change-subscription-status') }}", // URL to your Laravel route
            type: 'POST',
            data: {
                subscription_id: subscription_id,
                scheme_status: status,
                final_amount: final_amount,
                settlement_amount: settlement_amount,
                reason: reason,
                _token: "{{ csrf_token() }}"
            },
            dataType: 'json',
            success: function(response) {
                $("#changeStatusModal").modal('hide');
                toastr.success(response.message);

                setTimeout(function() {
                    location.reload();
                }, 2000);
            },
            error: function(error) {
                if (error.responseJSON.errors.scheme_status) {
                    $("#subscription_status").addClass('is-invalid');
                    $('.subscriptionStatusError').addClass('invalid-feedback')
                        .text(error.responseJSON.errors.scheme_status[0]);
                }
            }
        });
    });

    $(document).on('click', '.changeMaturityStatusBtn', function() {
        let maturity_subscription_id = $(this).data('subscription-id');
        let maturity_status = $(this).data('maturity-status'); 
        $("#maturity_subscription_id").val(maturity_subscription_id);
        if(maturity_status == 1) {
            $("#maturity_status").val(maturity_status).trigger('change');
        }
        else {
            $("#maturity_status").val("").trigger('change');
        }
    });

    $("#changeMaturityStatusModal").on('click', '.updateMaturityBtn', function(e) {
        e.preventDefault();

        $("#maturity_status").removeClass('is-invalid');
        $(".maturityStatusError").removeClass('invalid-feedback').text("");

        var maturity_subscription_id = $("#maturity_subscription_id").val();
        var maturity_status = $("#maturity_status").val();
        var maturity_reason = $("#maturity_reason").val();

        console.log("Subscription ID:", maturity_subscription_id); // Debugging to ensure correct value
        console.log("Maturity Status:", maturity_status);
        console.log("Maturity Reason:", maturity_reason);

        $.ajax({
            url: "{{ route('change-maturity-status') }}",
            type: 'POST',
            data: {
                subscription_id: maturity_subscription_id,
                maturity_status: maturity_status,
                maturity_reason: maturity_reason,
                _token: "{{ csrf_token() }}"
            },
            dataType: 'json',
            success: function(response) {
                $("#changeMaturityStatusModal").modal('hide');
                toastr.success(response.message);

                setTimeout(function() {
                    location.reload();
                }, 2000);
            },
            error: function(error) {
                if (error.responseJSON.errors.maturity_status) {
                    $("#maturity_status").addClass('is-invalid');
                    $(".maturityStatusError").addClass('invalid-feedback').text(error.responseJSON.errors.maturity_status[0]);
                }
            }
        });
    });
</script>
@endpush