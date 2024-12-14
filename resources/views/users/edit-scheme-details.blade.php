@extends('layouts.page')
@section('content')

<main id="main" class="main">
  <div class="pagetitle">
    <h1>Subscription History</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{route('users.get-user-subscriptions')}}">Subscriptions</a></li>
        <li class="breadcrumb-item active">Subscription History</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
  <section class="section">
    <div class="row">
      <div class="col-lg-12">
        @if ($errors->any())
        <div class="alert alert-danger">
          <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif
        <style>
          .head {
            text-align: end;
          }

          .card-title {
            text-align: center;
          }

          .card-tt {
            text-align: center;
          }
        </style>
      
        <div class="card">
          <div class="card-body">
            <h5 class="card-title"></h5>

            <!-- Default Tabs -->
            
            <div class="tab-content pt-2" id="myTabjustifiedContent">
              <div class="tab-pane fade show active" id="subscription-history" role="tabpanel" aria-labelledby="subscription-list-tab">
                @include('users._user-schemes._subscription-history')
              </div>
            
            </div><!-- End Default Tabs -->

          </div>
        </div>
      </div>
    </div>
  </section>
</main>

@endsection
@push('scripts')

<script type="text/javascript">
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  function subscriptionHistory() {
    $('#subscription-history tbody').empty();
    $.ajax({
      url: '{{ route("users.current-plan-history") }}', // URL to your Laravel route
      type: 'GET',
      data: {
        user_subscription_id: "{{encrypt($user_subscription_id)}}",
        user_id: "{{encrypt($user_id)}}",
        scheme_id: "{{encrypt($scheme_id)}}",

      }, // Pass the serialized data
      dataType: 'json',
      success: function(response) {

        $('#subscription-history tbody').append(response.data);
      },
      error: function(xhr, status, error) {
        console.error('Error:', error);
      }
    });

  }

  subscriptionHistory();

  function upaidList() {
    $('#upaid-list tbody').empty();
    $.ajax({
      url: '{{ route("users.unpaid-list") }}', // URL to your Laravel route
      type: 'GET',
      data: {
        user_subscription_id: "{{encrypt($user_subscription_id)}}",
        user_id: "{{encrypt($user_id)}}",
        scheme_id: "{{encrypt($scheme_id)}}",

      }, // Pass the serialized data
      dataType: 'json',
      success: function(response) {

        $('#upaid-list tbody').append(response.data);
      },
      error: function(xhr, status, error) {
        console.error('Error:', error);
      }
    });
  }

  function changeStatus() {
    $('#discontinue-details').hide();
    $('#maturity-status-success-box').hide();
    $('#scheme-status-success-box').hide();
    $('.error_transaction_msg').removeClass('alert alert-danger').html('');
    $("#frmtrasaction").removeClass('alert alert-danger').html('');
    $.ajax({
      url: "{{route('users.get-plan-status')}}", // URL to your Laravel route
      type: 'GET',
      data: {
        subscription_id: "{{encrypt($user_subscription_id)}}",
      }, // Pass the serialized data
      dataType: 'json',

      success: function(response) {
        if (response.maturity_status == '1') {
          $('#mat_status').html("");
          $('#maturity-status-box').hide();
          $('#maturity-status-success-box').show();
          $('#mat_status').html("Closed");
        }
        if (response.scheme_status == '2') {
          $('#sub_status').html("");
          $('#scheme-status-box').hide();
          $('#scheme-status-success-box').show();
          $('#discontinue-details-details').html(response.discontinued_details);
          $('#sub_status').html("Discontinued");

        } else {

          if (response.scheme_status == '1') {
            $('#sub_status').html("");
            $('#sub_status').html("Active");
          }
          if (response.scheme_status == '0') {
            $('#sub_status').html("");
            $('#sub_status').html("In Active");
          }
          if (response.scheme_status == '3') {
            $('#sub_status').html("");
            $('#sub_status').html("On Hold");
          }
          $("#scheme_status").val(response.scheme_status).change();
        }
        if (response.scheme_status == '2' && response.maturity_status == '1') {
          $('#btn-update').prop('disabled', true);
        } else {
          $('#btn-update').prop('disabled', false);
        }
      }
    });

  }



  $('document').ready(function() {

    var ref_this = $('.nav-link.active');
    functionName = ref_this.attr('data-id');
    window[functionName]();

    $(".nav-link").click(function() {
      $('#discontinue-details').hide();
      $('.success').removeClass('alert alert-success').html('');
      $('#scheme_staus').hide();
      $(".nav-link.active").removeClass("active");
      $(this).addClass("active");
      var ref_this = $('.nav-link.active');
      functionName = ref_this.attr('data-id');
      window[functionName]();
    });
    // alert("hello");
  });
</script>
@endpush
