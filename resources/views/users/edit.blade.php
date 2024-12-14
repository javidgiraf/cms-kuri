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
  <div class="pagetitle">
    <h1>Customer</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{route('users.index')}}">Customers</a></li>
        <li class="breadcrumb-item active">Update Customer</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
  <section class="section">
    <div class="row">
      <div class="col-lg-12">

        <div class="card">
          <div class="card-title d-flex justify-content-between m-3 mt-0">
            <h5><strong>Update</strong> Customer</h5>
            <a href="{{route('users.index')}}" class="btn btn-primary"><i class="zmdi zmdi-arrow-left" style="padding-right: 6px;"></i><span class="text-white">Back</span></a>
          </div>
          <div class="card-body">

            <form method="post" enctype="multipart/form-data" action="{{ route('users.update', encrypt($user->id)) }}">
              @csrf
              @method('patch')

              <h5 class="card-title"> Profile Details</h5>
              <div class="row mb-3">
                <input type="hidden" name="id" value="{{ $user->id }}">
                <label for="inputText" class="col-sm-3 col-form-label">Name <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                  <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" placeholder="Enter Name" autocomplete="one-time-code">
                  @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="row mb-3">
                <label for="inputText" class="col-sm-3 col-form-label">Email</label>
                <div class="col-sm-9">
                  <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" placeholder="Enter Email" autocomplete="one-time-code">
                  @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                  @enderror
                </div>
              </div>

              <div class="row mb-3">
                <label for="inputText" class="col-sm-3 col-form-label">Mobile <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                  <input type="text" id="mobile" name="mobile" class="form-control @error('mobile') is-invalid @enderror" value="{{ ($user->customer) ? old('mobile', $user->customer->mobile) : old('mobile') }}" placeholder="Enter Mobile Number" autocomplete="one-time-code">
                  @error('mobile')
                    <span class="invalid-feedback">{{ $message }}</span>
                  @enderror
                </div>
              </div>

              
              <div class="row mb-3">
                <label for="inputText" class="col-sm-3 col-form-label">Referrel Code <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                  <input type="text" 
                      id="referrel_code" name="referrel_code" 
                      class="form-control @error('referrel_code') is-invalid @enderror" 
                      value="{{ ($user->customer) ? old('referrel_code', $user->customer->referrel_code) : old('referrel_code') }}" 
                      placeholder="Enter Referrel Code" autocomplete="one-time-code" readonly>
                  @error('referrel_code')
                    <span class="invalid-feedback">{{ $message }}</span>
                  @enderror
                </div>
              </div>
              <div class="row mb-3">
                <label for="inputText" class="col-sm-3 col-form-label">Aadhaar No <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                  <input type="text" id="aadhar_number" name="aadhar_number" class="form-control @error('aadhar_number') is-invalid @enderror" value="{{ ($user->customer) ? old('aadhar_number', $user->customer->aadhar_number) : old('aadhar_number') }}" placeholder="Enter Aadhaar No" autocomplete="one-time-code">
                  @error('aadhar_number')
                    <span class="invalid-feedback">{{ $message }}</span>
                  @enderror
                </div>
              </div>

              <div class="row mb-3">
                <label for="inputText" class="col-sm-3 col-form-label">Pancard No</label>
                <div class="col-sm-9">
                  <input type="text" id="pancard_no" name="pancard_no" class="form-control @error('pancard_no') is-invalid @enderror" value="{{ ($user->customer) ? old('pancard_no', $user->customer->pancard_no) : old('pancard_no') }}" placeholder="Enter Pancard No" autocomplete="one-time-code">
                  @error('pancard_no')
                    <span class="invalid-feedback">{{ $message }}</span>
                  @enderror
                </div>
              </div>

              <h5 class="card-title"> Address</h5>

              <div class="row mb-3">
                <label for="inputText" class="col-sm-3 col-form-label">Address <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                  <textarea name="address" id="address" cols="30" rows="10" class="form-control @error('address') is-invalid @enderror" placeholder="Enter Address">
                    {{ isset($user->address) ? old('address', $user->address->address) : old('address') }}
                  </textarea>
                  @error('address')
                    <span class="invalid-feedback">{{ $message }}</span>
                  @enderror
                </div>
              </div>

              <div class="row mb-3">
                <label for="inputText" class="col-sm-3 col-form-label">State <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                  <input type="hidden" name="country_id" id="country_id" value="{{ \App\Models\Country::COUNTRY_ID }}">
                  <select name="state_id" id="state_id" class="form-control @error('state_id') is-invalid @enderror">

                  </select>
                  @error('state_id')
                    <span class="invalid-feedback">{{ $message }}</span>
                  @enderror
                </div>
              </div>
              <div class="row mb-3">
                <label for="inputText" class="col-sm-3 col-form-label">District <span class="text-danger">*</span></label>
                <div class="col-sm-9">

                  <select name="district_id" id="district_id" class="form-control @error('district_id') is-invalid @enderror">

                  </select>
                  @error('district_id')
                    <span class="invalid-feedback">{{ $message }}</span>
                  @enderror
                </div>
              </div>
              <div class="row mb-3">
                <label for="inputText" class="col-sm-3 col-form-label">Pin code</label>
                <div class="col-sm-9">

                  <input type="text" id="pincode" name="pincode" class="form-control @error('pincode') is-invalid @enderror" value="{{ isset($user->address) ? old('pincode', $user->address->pincode) : old('pincode') }}" placeholder="Enter Pin Code" autocomplete="one-time-code">
                  @error('pincode')
                    <span class="invalid-feedback">{{ $message }}</span>
                  @enderror
                </div>
              </div>


              <h5 class="card-title"> Nominee</h5>
              <div class="row mb-3">
                <label for="inputText" class="col-sm-3 col-form-label">Nominee Name <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                  <input type="text" id="nominee_name" name="nominee_name" class="form-control @error('nominee_name') is-invalid @enderror" value="{{ isset($user->nominee) ? old('nominee_name', $user->nominee->name) : old('nominee_name') }}" placeholder="Enter Nominee Name" autocomplete="one-time-code">
                  @error('nominee_name')
                    <span class="invalid-feedback">{{ $message }}</span>
                  @enderror
                </div>
              </div>

              <div class="row mb-3">
                <label for="inputText" class="col-sm-3 col-form-label">Nominee Relationship <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                  <input type="text" id="nominee_relationship" name="nominee_relationship" class="form-control @error('nominee_relationship') is-invalid @enderror" value="{{ isset($user->nominee) ? old('nominee_relationship', $user->nominee->relationship) : old('nominee_relationship') }}" placeholder="Enter Nominee Relationship" autocomplete="one-time-code">
                  @error('nominee_relationship')
                    <span class="invalid-feedback">{{ $message }}</span>
                  @enderror
                </div>
              </div>
              <div class="row mb-3">
                <label for="inputText" class="col-sm-3 col-form-label">Nominee Phone <span class="text-danger">*</span></label>
                <div class="col-sm-9">

                  <input type="text" id="nominee_phone" name="nominee_phone" class="form-control @error('nominee_phone') is-invalid @enderror" value="{{ isset($user->nominee) ? old('nominee_phone', $user->nominee->phone) : old('nominee_phone') }}" placeholder="Enter Nominee Phone" autocomplete="one-time-code">
                  @error('nominee_phone')
                    <span class="invalid-feedback">{{ $message }}</span>
                  @enderror
                </div>
              </div>

              <div class="row mb-3">
                <label for="inputText" class="col-sm-3 col-form-label">Status <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                  <div id="enabled">
                    <p>Inactive</p>
                    <label class="switch">
                      <input type="checkbox" id="togBtn" name="status" value="{{ ($user->customer) ? ($user->customer->status == true ? true : false) : false }}" {{ ($user->customer) ? ($user->customer->status == true ? 'checked' : '') : '' }}>
                      <span class=" slider"></span>
                    </label>
                    <p>Active</p>
                  </div>
                </div>
              </div>


              <div class="row mb-3">
                <div class="col-sm-10">
                  <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                </div>
              </div>


            </form>

          </div>
        </div>
      </div>
    </div>
  </section>
</main>

@endsection
@push('scripts')

<script>
  function getDistrictsonload(state_id, district_id) {
    $.ajax({
      url: "{{route('states.get-districts')}}",
      type: "GET",
      data: {
        state_id: state_id,
        district_id: district_id,
      },
      success: function(response2) {
        $('#district_id').html(response2.data);

      },
    });
  }

  $(document).ready(function() {
    var country_id = $('#country_id').val();
    var state_id = "{{ isset($user->address->state_id) ? $user->address->state_id : '' }}";
    var district_id = "{{ isset($user->address->district_id) ? $user->address->district_id : '' }}";


    $.ajax({
      url: "{{ route('countries.get-states') }}",
      type: "GET",
      data: {
        country_id: country_id,
        state_id: state_id,
        district_id: district_id
      },
      success: function(response) {

        $('#state_id').html(response.data);
        getDistrictsonload(state_id, district_id);
      }
    });

    $('#state_id').change(function() {
      var state_id = $(this).val();
      var district_id = "";
      getDistrictsonload(state_id, district_id);

    });
    var switchStatus = false;
    $("#togBtn").on('change', function() {
      if ($(this).is(':checked')) {
        switchStatus = $(this).is(':checked');
        $("#togBtn").val('1');
      } else {
        switchStatus = $(this).is(':checked');
        $("#togBtn").val('0');
      }
    });
  });
</script>
@endpush