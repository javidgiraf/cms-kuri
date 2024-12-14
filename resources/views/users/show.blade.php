@extends('layouts.page')
@section('content')

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Customer Information</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{route('users.index')}}">Customers</a></li>
                <li class="breadcrumb-item active">Show Customer Information</li>
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
                <div class="row">

                    <!-- Sales Card -->
                    <div class="col-xxl-6 col-md-6">
                        <div class="card info-card sales-card">

                            <div class="card-body">
                                <h5 class="card-title">Customer Details</h5>

                                <div class="d-flex align-items-center">
                                    
                                    <div class="ps-3">
                                        <dl class="row">
                                            <dt class="col-sm-5">Customer </dt>
                                            <dt class="col-sm-1 head">:</dt>
                                            <dd class="col-sm-5">{{ $current_plan_history['user']['name'] }}</dd>
                                        
                                            <dt class="col-sm-5">Email</dt>
                                            <dt class="col-sm-1 head">:</dt>
                                            <dd class="col-sm-5">{{ $current_plan_history['user']['email'] }}</dd>
                                            
                                            <dt class="col-sm-5">Mobile</dt>
                                            <dt class="col-sm-1 head">:</dt>
                                            <dd class="col-sm-5">{{ $current_plan_history['user']['customer']['mobile'] }}</dd>

                                            @if($current_plan_history['user']['customer']['referrel_code'])
                                            <dt class="col-sm-5">Referrel Code</dt>
                                            <dt class="col-sm-1 head">:</dt>
                                            <dd class="col-sm-5">{{ $current_plan_history['user']['customer']['referrel_code'] }}</dd>
                                            @endif

                                            
                                            <dt class="col-sm-5">Aadhar No</dt>
                                            <dt class="col-sm-1 head">:</dt>
                                            <dd class="col-sm-5">{{ ($current_plan_history['user']['customer']['aadhar_number']) ? 
                                                $current_plan_history['user']['customer']['aadhar_number'] : __('xxxx-xxxx-xxxx') }}</dd>
                                            

                                            <dt class="col-sm-5">Pancard No</dt>
                                            <dt class="col-sm-1 head">:</dt>
                                            <dd class="col-sm-5">{{ ($current_plan_history['user']['customer']['pancard_no']) ? 
                                                $current_plan_history['user']['customer']['pancard_no'] : __('xxxxx-xxxxx') }}</dd>
                                            
                                        </dl>
                                    </div>
                                </div>

                                @if($current_plan_history['user']['address'])    
                                <h5 class="card-title">Customer Address</h5>

                                <div class="d-flex align-items-center">
                                    
                                    <div class="ps-3">
                                        <dl class="row">    
                                
                                            <dt class="col-sm-5">Address</dt>
                                            <dt class="col-sm-1 head">:</dt>
                                            <dd class="col-sm-5">{{ $current_plan_history['user']['address']['address'] }}</dd>    

                                            <dt class="col-sm-5">District</dt>
                                            <dt class="col-sm-1 head">:</dt>
                                            <dd class="col-sm-5">{{ $current_plan_history['user']['address']['district']['name'] }}</dd> 
                                            
                                            <dt class="col-sm-5">Pincode</dt>
                                            <dt class="col-sm-1 head">:</dt>
                                            <dd class="col-sm-5">{{ $current_plan_history['user']['address']['pincode'] }}</dd> 

                                            <dt class="col-sm-5">State</dt>
                                            <dt class="col-sm-1 head">:</dt>
                                            <dd class="col-sm-5">{{ $current_plan_history['user']['address']['state']['name'] }}</dd> 

                                            <dt class="col-sm-5">Country</dt>
                                            <dt class="col-sm-1 head">:</dt>
                                            <dd class="col-sm-5">{{ $current_plan_history['user']['address']['country']['name'] }}</dd> 
                                            
                                        </dl>
                                    </div>
                                </div>
                                @endif

                                @if($current_plan_history['user']['nominee'])
                                <h5 class="card-title">Nominee Details</h5>

                                <div class="d-flex align-items-center">
                                    
                                    <div class="ps-3">
                                        <dl class="row">
                                            <dt class="col-sm-5">Nominee</dt>
                                            <dt class="col-sm-1 head">:</dt>
                                            <dd class="col-sm-5">{{ $current_plan_history['user']['nominee']['name'] }}</dd> 

                                            <dt class="col-sm-5">Relationship</dt>
                                            <dt class="col-sm-1 head">:</dt>
                                            <dd class="col-sm-5">{{ $current_plan_history['user']['nominee']['relationship'] }}</dd> 

                                            <dt class="col-sm-5">Phone No</dt>
                                            <dt class="col-sm-1 head">:</dt>
                                            <dd class="col-sm-5">{{ $current_plan_history['user']['nominee']['phone'] }}</dd> 
                                        
                                        </dl>

                                    </div>
                                    
                                </div>
                                @endif
                            </div>

                        </div>
                    </div><!-- End Sales Card -->

                    <!-- Revenue Card -->

                    <!-- Customers Card -->
                    <div class="col-xxl-6 col-xl-6">

                        <div class="card info-card customers-card">


                            <div class="card-body">
                                <h5 class="card-title">Plan Details</h5>

                                <div class="d-flex align-items-center">
                                    @if(isset($current_plan_history['scheme']))
                                    <div class="ps-3">
                                        <dl class="row">
                                            <dt class="col-sm-5">Plan Name </dt>
                                            <dt class="col-sm-1 head">:</dt>
                                            <dd class="col-sm-5">{{ $current_plan_history['scheme']['title'] }}</dd>
                                            @if($current_plan_history['subscribe_amount'])
                                            <dt class="col-sm-5">Plan Amount</dt>
                                            <dt class="col-sm-1 head">:</dt>
                                            <dd class="col-sm-5">{{ $currency }} {{ number_format($current_plan_history['subscribe_amount'],2) }}</dd>
                                            @endif
                                            <dt class="col-sm-5">Plan Duration</dt>
                                            <dt class="col-sm-1 head">:</dt>
                                            <dd class="col-sm-5">{{ $current_plan_history['scheme']['total_period'] }} months</dd>

                                            <!-- <dt class="col-sm-5">Schedule Amount</dt>
                      <dt class="col-sm-1 head">:</dt>
                      <dd class="col-sm-5">₹ {{number_format($current_plan_history['scheme']['schedule_amount'],2)}}</dd> -->


                                            <!-- Add more dt/dd pairs as needed -->
                                        </dl>

                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">Subscription Details</h5>

                                <div class="d-flex align-items-center">

                                    <div class="ps-3">
                                        <dl class="row">
                                    

                                            <dt class="col-sm-5">Plan Period</dt>
                                            <dt class="col-sm-1 head">:</dt>
                                            <dd class="col-sm-6">{{ date('d/m/Y', strtotime($current_plan_history['scheme_start_date'])) }} - {{ date('d/m/Y', strtotime($current_plan_history['scheme_end_date'])) }}</dd>
                                            <dt class="col-sm-5">Maturity Status</dt>
                                            <dt class="col-sm-1 head">:</dt>
                                            <dd class="col-sm-5" id='mat_status'>{{ ($current_plan_history['user_subscription']['is_closed'] == true) ? 'Closed' : 'Open' }}</dd>
                                            <dt class="col-sm-5">Subscription Status</dt>
                                            <dt class="col-sm-1 head">:</dt>

                                            @if($current_plan_history['user_subscription']['status'] == $userSubscription::STATUS_ACTIVE)
                                            <dd class="col-sm-3 p-2 badge bg-success" id='sub_status'>Active</dd>
                                            @elseif($current_plan_history['user_subscription']['status'] == $userSubscription::STATUS_DISCONTINUE)
                                            <dd class="col-sm-3 p-2 badge bg-danger" id='sub_status'>Discontinued</dd>
                                            @elseif($current_plan_history['user_subscription']['status'] == $userSubscription::STATUS_ONHOLD)
                                            <dd class="col-sm-3 p-2 badge bg-warning" id='sub_status'>On Hold</dd>
                                            @else
                                            <dd class="col-sm-3 p-2 badge bg-secondary" id='sub_status'>In active</dd>
                                            @endif


                                            <!--<dt class="col-sm-6 head">Schedule Amount :</dt>
                      <dd class="col-sm-6">₹ {{number_format($current_plan_history['scheme']['schedule_amount'],2)}}</dd> -->


                                            <!-- Add more dt/dd pairs as needed -->
                                        </dl>

                                    </div>
                                </div>

                            </div>
                        </div>

                    </div><!-- End Customers Card -->
                </div>

            </div>
    </section>
</main>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $(".subscription_status").select2({
            placeholder: "Select Status",
            allowClear: true
        });
    });
</script>
@endpush