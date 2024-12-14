<div class="modal-content">
  <div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Deposit Details</h5>

    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
  </div>
  <div class="modal-body">
    <div class="row">
      <div class="col-xxl-6 col-md-6">
        <div class="card info-card sales-card">

          <div class="card-body" style="padding: 10px;">

            <div class="ps-3">
              <dl class="row">
                <dt class="col-sm-5">Deposit Id</dt>
                <dt class="col-sm-1 head">:</dt>
                <dd class="col-sm-5"> {{ $success_deposit_by_order['order_id'] }}</dd>

                <dt class="col-sm-5">Date</dt>
                <dt class="col-sm-1 head">:</dt>
                <dd class="col-sm-5">{{ date('d-m-Y', strtotime($success_deposit_by_order['paid_at'])) }}</dd>

      
                <dt class="col-sm-5">User Type</dt>
                <dt class="col-sm-1 head">:</dt>
                <dd class="col-sm-5">{{ $success_deposit_by_order['user_type'] == 'admin' ? 'Admin' : 'Customer' }}</dd>

                <!-- Add more dt/dd pairs as needed -->
              </dl>

            </div>
          </div>

        </div>
      </div><!-- End Sales Card -->
      <div class="col-xxl-6 col-md-6">
        <div class="card info-card sales-card">

          <div class="card-body" style="padding: 10px;">

            <div class="ps-3">
              <dl class="row">
              
                <dt class="col-sm-5">Total Amount</dt>
                <dt class="col-sm-1 head">:</dt>
                <dd class="col-sm-5">{{ \App\Models\Setting::CURRENCY }} {{ number_format($success_deposit_by_order['total_scheme_amount'], 2) }}</dd>
                
                <dt class="col-sm-5">Final Amount</dt>
                <dt class="col-sm-1 head">:</dt>
                <dd class="col-sm-5">{{ \App\Models\Setting::CURRENCY }} {{ number_format($success_deposit_by_order['final_amount'], 2) }}</dd>

                <dt class="col-sm-5">Payment Type</dt>
                <dt class="col-sm-1 head">:</dt>
                <dd class="col-sm-5">{{ $success_deposit_by_order['payment_type'] }}</dd>

                <!-- Add more dt/dd pairs as needed -->
              </dl>

            </div>
          </div>

        </div>
      </div><!-- End Sales Card -->
    </div>
  </div>
  <div class="m-4">
  <table class="table" width="100%">
    <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col">Date</th>
        <th scope="col">Amount</th>
        <th scope="col">Due Status</th>
        <th scope="col">Status</th>

      </tr>
    </thead>
    <tbody>
      @foreach($success_deposit_by_order['deposit_periods'] as $deposit_period)
      <tr>
        <th scope="row">{{ $loop->iteration }}</th>
        <td>{{ date('d-m-Y', strtotime($deposit_period['due_date'])) }}</td>
        <td>{{ \App\Models\Setting::CURRENCY }} {{ number_format($deposit_period['scheme_amount'], 2) }}</td>
        <td>{{ $deposit_period['status'] == 1 ? ($deposit_period['is_due'] == '1' ? 'Payment is Delayed' : 'Payment on time') : '' }}</td>
        <td>{{ $deposit_period['status']== '1' ? 'Payment Completed' : 'Incomplete Payments' }}</td>

      </tr>
      @endforeach
    </tbody>

  </table>
  </div>
  @if($success_deposit_by_order['payment_type'] != 'cash')
  <div class="card-body" id="enter-trasaction-details">
    <h5 class="card-title" style="text-align: left;">Enter Transaction Details</h5>
    <div id="frmtrasaction"></div>
    <div class="error_transaction_msg"></div>
    <!-- Horizontal Form -->
    <form id="frm_transation_details">
      <div class="row mb-3">
        <label for="inputEmail3" class="col-sm-2 col-form-label">Transaction No <span class="text-danger">*</span></label>
        <div class="col-sm-10">
          <input type="hidden" class="form-control" id="deposit_id" name="deposit_id" value="{{encrypt($success_deposit_by_order['id'])}}">
          <input type="text" class="form-control" id="transaction_no" name="transaction_no">
          <span class="transactionNoError"></span>
        </div>
      </div>
      <div class="row mb-3">
        <label for="inputEmail3" class="col-sm-2 col-form-label">Receipt Upload <span class="text-danger">*</span></label>
        <div class="col-sm-10">
          <input type="file" class="form-control" name="receipt_upload" id="receipt_upload" accept=".svg,.png,.jpeg,.jpg,.webp,.pdf">
          <span class="receiptUploadError"></span>
        </div>
      </div>
      <div class="row mb-3">
        <label for="inputPassword3" class="col-sm-2 col-form-label">Remark</label>
        <div class="col-sm-10">
          <textarea class="form-control" id="remark" name="remark"></textarea>
        </div>
      </div>
      <div class="col-md-12" id="transaction-loading" style="text-align: center;display:none">
        <img src="{{asset('assets/img/loading.gif')}}" style="width: 25%;">
      </div>

      <div class="text-center">
        <button type="button" class="btn btn-primary btn-transaction-save">Submit</button>

      </div>
    </form><!-- End Horizontal Form -->

  </div>
  <div class="card-body" id="fetch-trasaction-details" style="display:none">

  </div>
  @endif

  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

  </div>
</div>