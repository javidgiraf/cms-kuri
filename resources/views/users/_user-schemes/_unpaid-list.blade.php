   <div class="card">

     <div class="card-body">
       <h5 class="card-title">Unpaid List</h5>
       <style>
         .change {
           padding: 10px;
           border: 1px solid #4154f1;
           font-weight: 700;
           text-align: center;
           color: white;
           background: #4154f1;
         }

         .total-amount {
           font-weight: 700;
           text-align: end;
         }
       </style>
       <p class="change">
         Please check the list of unpaid date to pay from admin. </p>
         <div class="success"></div>

       <input type="button" class="btn btn-success btn-add-deposit-model" id="submit" value="Submit" style="background:#4154f1;">
       <table class="table" id="upaid-list">
         <thead>
           <tr>
             <th scope="col"><input type="checkbox" name="all_permission" class="all_permission"></th>
             <th scope="col">Date</th>
             <th scope="col">Amount</th>



           </tr>
         </thead>
         <tbody>

         </tbody>

       </table>
       <input type="button" class="btn btn-success btn-add-deposit-model" id="submit" value="Submit" style="background:#4154f1;">


     </div>
   </div>
   <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
     <div class="modal-dialog">
       <div class="modal-content">
         <div class="modal-header">
           <h5 class="modal-title" id="exampleModalLabel">Pay the deposit</h5>
           <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
           <table id="permissionsTable" class="table">
             <!-- Table headers (if any) -->
             <thead>
               <tr>
                 <th>Date</th>
                 <th>Amount</th>
               </tr>
             </thead>
             <!-- Table body -->
             <tbody>
               <!-- Table rows will be dynamically added here -->
             </tbody>

           </table>
           <div class="row total-amount">
             <p>Total Amount : ₹ <span id="total-amount-value"></span></p>
           </div>

         </div>
         <div class="modal-footer">
           <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
           <button type="button" class="btn btn-primary btn-pay-deposit">Save changes</button>
           <div class="col-md-12" id="loading" style="text-align: center;display:none">
           <img src="{{asset('assets/img/loading.gif')}}" style="width: 35%;">
           </div>
         </div>
       </div>
     </div>
   </div>
   @push('scripts')
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

   <script type="text/javascript">
     function number_format(number, decimals, dec_point, thousands_sep) {
       number = parseFloat(number);
       if (!isFinite(number) || !Number.isInteger(decimals) || decimals < 0) {
         throw new TypeError('number_format: invalid parameters');
       }

       decimals = decimals || 0;
       dec_point = dec_point || '.';
       thousands_sep = thousands_sep || ',';

       var parts = number.toFixed(decimals).toString().split('.');
       parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_sep);
       return parts.join(dec_point);
     }
     $.ajaxSetup({
       headers: {
         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
       }
     });
     $(document).ready(function() {
       $('.btn-add-deposit-model').prop('disabled', true);
       $('[name="all_permission"]').on('click', function() {
         $('.btn-add-deposit-model').prop('disabled', false);
         if ($(this).is(':checked')) {

           $.each($('.permission'), function() {
             $(this).prop('checked', true);

           });
         } else {
           $.each($('.permission'), function() {
             $(this).prop('checked', false);
             $('.btn-add-deposit-model').prop('disabled', true);
           });
         }

       });
     });
     var table = $('#permissionsTable');

     function clearTable() {
       table.find("tr:gt(0)").remove(); // Remove all rows except the header
       $('#total-amount-value').val('0');
     }
     var checkedPermissions = [];
     var totalAmount = 0;
     $('.btn-add-deposit-model').on('click', function() {

       clearTable(); // Clear the table before appending new rows
       // Reset total amount before recalculating
       totalAmount = 0;
       checkedPermissions = [];
       $.each($('.permission'), function() {

         if ($(this).is(':checked')) {
           var id = $(this).attr('id');
           var date = id;
           var amount = $('#amount' + id).html();
           var amt = parseFloat(amount);
           totalAmount += amt;
           // Create a table row with the checked values
           // Create an object to store checked permission details
           var permissionObject = {
             date: date,
             amount: amount
           };
           // Push the permission object to the array
           checkedPermissions.push(permissionObject);
           var row = $('<tr>').append(
             //$('<td>').text(id),
             $('<td>').text(date),
             $('<td>').text(number_format(amount, 2, '.', ','))
           );

           // Append the row to the table
           table.append(row);

           $('#exampleModal').modal('show');

         }
       });
       $('#total-amount-value').text(number_format(totalAmount, 2, '.', ','));

     });
     $('.btn-pay-deposit').on('click', function() {
       //console.log(checkedPermissions);
       var jsonData = JSON.stringify(checkedPermissions);

       $.ajax({
         url: '{{ route("users.pay-deposit") }}', // URL to your Laravel route
         type: 'POST',
         data: {
           checkdata: jsonData,
           totalAmount: totalAmount,
           subscription_id: "{{$user_subscription_id}}",

         }, // Pass the serialized data
         dataType: 'json',

          beforeSend: function (xhr) {
                    $('#loading').show();
                    $('.success').removeClass('alert alert-success').html('');
                },
         success: function(response) {
            $('#loading').hide();
           $('#exampleModal').modal('hide');
           $('.success').addClass('alert alert-success').html('Deposit Added Successfully');
           for (var i = 0; i < checkedPermissions.length; i++) {
             var permission = checkedPermissions[i];
             var id = permission.date;
             $('#tableRow_' + id).remove();

           }
           $('.btn-add-deposit-model').prop('disabled', true);
         },
         error: function(xhr, status, error) {
            //  toastr.error(error);
           if (xhr.responseJSON && xhr.responseJSON.message) {
                toastr.error(xhr.responseJSON.message); // Show error message from the backend
            } else {
                toastr.error('An unexpected error occurred.');
            }
            console.error('Error:', error);
         }
       });

     });

     function checkIfAnyChecked() {
       return $('.permission:checked').length > 0;
     }
     $(document).delegate(".permission", "click", function() {
       if ($(this).is(':checked')) {
         $('.btn').prop('disabled', false);
       } else {
         $('.btn').prop('disabled', !checkIfAnyChecked());
         //$('.btn').prop('disabled', true);
       }
     });
   </script>
   @endpush
