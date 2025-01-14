<?php

namespace App\Http\Controllers\api\v1;

use App\Helpers\MachineHelper;
use App\Helpers\UniqueHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Address;
use App\Models\Country;
use App\Models\State;
use App\Models\Customer;
use App\Models\User;
use App\Models\Nominee;
use App\Models\District;
use App\Models\UserSubscription;
use App\Models\Deposit;
use App\Models\DepositPeriod;
use App\Models\GoldRate;
use App\Models\GoldDeposit;
use Hash;
use Carbon\Carbon;
use App\Helpers;
use App\Models\RazorpayTransaction;
use Illuminate\Support\Arr;
use Razorpay\Api\Api;
use App\Models\TransactionDetail;
use App;
use App\Models\SchemeSetting;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;



class ProfileController extends Controller
{
  //



  public function profile()
  {
    $id = auth()->user()->id;
    $user = User::with('customer')->with('nominee')->whereId($id)->first();
    $address = Address::with('country')->with('state')->with('district')->where('user_id', $id)->first();
    return response()->json([
      'user' => $user,
      'address' => $address,
      'status' => '1'
    ]);
  }

  public function profileUpdate(Request $request)
  {

    $id = auth()->user()->id;
    $request->validate([
      'name' => 'required',
      'email' => 'required|email|unique:users,email,' . $id . ',id',
      'referrel_code' => 'required|unique:customers,referrel_code,' . $id . ',user_id',
      'mobile' => 'required|unique:customers,mobile,' . $id . ',user_id',
      'aadhar_number' => 'required|numeric|unique:customers,aadhar_number,' . $id . ',user_id',
      'address' => 'required',
      'country_id' => 'required',
      'state_id' => 'required',
      'district_id' => 'required',
      'pincode' => 'required|numeric',
      'nominee_name' => 'required',
      'nominee_relationship' => 'required',
      'nominee_phone' => 'required',
     
    ]);
    $password =  (isset($request->password) && $request->password) ? $request->password : '123456';
    $password = Hash::make($password);
    User::where('id', $id)->update([
      'name' => $request->name,
      'email' => $request->email,
      'password' => $password,

    ]);
    Customer::where('user_id', $id)->update([
      'referrel_code' => $request->referrel_code,
      'mobile' => $request->mobile,
      'password' => $password,
      'aadhar_number' => $request->aadhar_number,
    ]);

    Address::updateOrCreate([
      'user_id'   => $id,
    ], [
      'address'     => $request->address,
      'country_id' => $request->country_id,
      'state_id'    => $request->state_id,
      'district_id'   => $request->district_id,
      'pincode'       => $request->pincode,
    ]);
    Nominee::updateOrCreate([
      'user_id'   => $id,
    ], [
      'name'     => $request->nominee_name,
      'relationship' => $request->nominee_relationship,
      'phone'    => $request->nominee_phone,
    ]);


    return response()->json([
      'message' => 'Profile Updated successfully',
      'status' => '1'
    ]);
  }
  public function get_all_countries()
  {
    $countries = Country::all();
    return response()->json([
      'countries' => $countries,
      'status' => '1'
    ]);

  }
  public function get_states_by_country($id)
  {
    $states = State::where('country_id', $id)->get();
    return response()->json([
      'states' => $states,
      'status' => '1'
    ]);
  }
  public function get_districts_by_state($id)
  {
    $districts = District::where('state_id', $id)->get();
    return response()->json([
      'districts' => $districts,
      'status' => '1'
    ]);
  }


  public function userSubscriptions()
  {
    $id = auth()->user()->id;
    $user_subscriptions = UserSubscription::with(['deposits', 'scheme.schemeType','schemeSetting'])
    ->where('user_id', $id)
    ->orderBy('id', 'desc')
    ->get()
    ->map(function ($subscription) {
      $scheme = $subscription->scheme;
      $schemeType = $scheme->schemeType;
      $schemeSetting = $subscription->schemeSetting;

      $startDate = $subscription->start_date;

      $schemeTitle = trans("messages.scheme_title_". $scheme->scheme_type_id);
     

      if($schemeType->flexibility_duration==""){

        $schemeStatus = false;
      }else{

        $startDate = Carbon::parse($subscription->start_date);

        $sevenMonthsLater = $startDate->copy()->addMonths($schemeType->flexibility_duration);

        $currentMonth = now()->format('Y-m');
        $sevenMonthsMonth = $sevenMonthsLater->format('Y-m');

        if ($currentMonth > $sevenMonthsMonth) {

          $schemeStatus = false;
        }else{

          $schemeStatus = true;
        }
      }

      $currentMonth = now()->format('Y-m');

      // Check if a deposit exists for the current month
      $depositExistsThisMonth = $subscription->deposits->contains(function ($deposit) use ($currentMonth) {
        return Carbon::parse($deposit->paid_at)->format('Y-m') === $currentMonth && $deposit->status == 1;
      });


      $dueDate = $schemeSetting ? now()->startOfMonth()->addDays($schemeSetting->due_duration - 1) : null;


      $isDue = false;
      if ($dueDate) {
        $isDue = !$depositExistsThisMonth && now()->greaterThan($dueDate);
      }
      $holdStatus = false;
      if ($dueDate) {
        $holdStatus = now()->isAfter($dueDate) && !now()->isSameDay($dueDate);
      }


      $currentMonth = Carbon::now()->format('Y-m');
      $depositExistsThisMonth = $subscription->deposits->contains(function ($deposit) use ($currentMonth) {
        return Carbon::parse($deposit->paid_at)->format('Y-m') === $currentMonth && $deposit->status == 1;
      });
      $dueDate = $schemeSetting ? Carbon::now()->startOfMonth()->addDays($schemeSetting->due_duration - 1)->format('Y-m-d') : null;

      return [
        'id' => $subscription->id,
        'user_id' => $subscription->user_id,
        'scheme_id' => $subscription->scheme_id,
        'subscribe_amount' => $subscription->subscribe_amount,
        'is_closed' => $subscription->is_closed,
        'status' => $subscription->status,
        'is_flexible' => $schemeStatus,
        'title' => $schemeTitle,
        'date' => $dueDate,
        'is_due' => $isDue,
        'hold_status' => $holdStatus,
        'have_paid' => $depositExistsThisMonth,
        'payment_terms' => trans("messages.payment_terms"),
        
      ];
    });

    return response()->json([
      'user_subscriptions' => $user_subscriptions,
      'status' => '1'
    ], 200, [], JSON_UNESCAPED_UNICODE);
  }


  function generateDates($start_date_str, $end_date_str)
  {
    $start_date = Carbon::parse($start_date_str);
    $end_date = Carbon::parse($end_date_str);

    $current_date = $start_date;
    $dates_list = [];

    while ($current_date <= $end_date) {
      $dates_list[] = $current_date->format('d-m-Y');
      if ($current_date->format('m-Y') == now()->format('m-Y')) {
        break;
      }

      $current_date->addMonth();
    }

    return $dates_list;
  }



  public function plan_duration($scheme_id)
  {
    $id = auth()->user()->id;
    $user_subscription = UserSubscription::with('scheme')->where('user_id', $id)->where('scheme_id', $scheme_id)->first();

    $user_subscription_deposits =  Deposit::where('subscription_id', $user_subscription->id)->get();

    $start_date_str = $user_subscription->start_date;
    $end_date_str = $user_subscription->end_date;

    $result_dates = $this->generateDates($start_date_str, $end_date_str);
    $rs_dates = [];
    foreach ($result_dates as &$d) {
      $rs_dates[] = [
        'date' => $d,
        'amount' => $user_subscription->scheme->schedule_amount,
        'is_due' => 0,
        'status' => '0',

      ];
    }
    if ($user_subscription_deposits != "") {
      $deposit_periods = [];
      foreach ($user_subscription_deposits as $dp) {
        $deposit_periods[] = $dp->deposit_periods
        ->where('status', 1)
        ->toarray();
      }

      $deposit_dues = [];
      foreach ($user_subscription_deposits as $dp) {
        $deposit_dues[] = $dp->deposit_periods
        ->where('is_due', 1)
        ->toarray();
      }

      $flattenedArray = array_merge_recursive(...$deposit_periods);
      $flattenedduesArray = array_merge_recursive(...$deposit_dues);

      $items = [];
      foreach ($flattenedArray as &$item) {
        $items[] = [

          'due_date' =>
          Carbon::parse($item['due_date'])->format('d-m-Y'),
          'is_due' => $item['is_due'],
          'status' => $item['status'],
        ];
      }

      $dues = [];
      foreach ($flattenedduesArray as &$due) {
        $dues[] = [

          'due_date' =>
          Carbon::parse($due['due_date'])->format('d-m-Y'),
          'is_due' => $due['is_due'],
          'status' => $due['status'],
        ];
      }

      foreach ($rs_dates as &$item1) {
        $item1['status'] = 0;
        $item1['is_due'] = 0;

        foreach ($items as $item2) {
          if ($item1['date'] === $item2['due_date']) {
            if ($item2['status'] === 1) {
              $item1['status'] = 1;
            }
            break;
          }
        }
        foreach ($dues as $item2) {

          if ($item1['date'] === $item2['due_date']) {
            if ($item2['is_due'] === 1) {
              $item1['is_due'] = 1;
            }
            break;
          }
        }
      }
    }

    return response()->json([
      'result_dates' => $rs_dates,
      'status' => '1'
    ]);
  }

  public function current_plan_history($scheme_id)
  {

    $id = auth()->user()->id;

    $user_subscription = UserSubscription::with(['deposits','scheme','schemeSetting','scheme.schemeType'])->where('user_id', $id)->where('scheme_id', $scheme_id)->first();

    $user_subscription_deposits = Deposit::where('subscription_id', $user_subscription->id)->where('status','1')->get();

    if ($user_subscription_deposits != "") {
      $deposit_periods = [];
      $sum = 0;

      foreach ($user_subscription_deposits as $dp) {
        $deposit_periods[] = $dp->paid_at;

        if ($dp->status == 1) {
          $sum += $dp->final_amount;
        }
      }
      $schemeSetting = $user_subscription->schemeSetting;
      $balance_amount = $user_subscription->scheme->total_amount - $sum;
      $payment_history = [];
      $schemeType = $user_subscription->scheme->schemeType;
      $isGold = $schemeType && strtolower($schemeType->shortcode ) === 'gold' ? true : false;

      foreach ($user_subscription_deposits as $dp) {
        $paymentData = [
          'scheme_amount' => $dp->final_amount,
          'paid_at' => Carbon::parse($dp->paid_at)->format('d-m-Y'),
          'status' => $dp->status,
        ];


        if ($isGold) {
          $goldDeposit = GoldDeposit::where('deposit_id', $dp->id)->first();
          $paymentData['gold_weight'] = $goldDeposit ? number_format($goldDeposit->gold_weight, 2) : null;
        }

        $payment_history[] = $paymentData;
      }

      $currentMonth = Carbon::now()->format('Y-m');

      $depositExistsThisMonth = $user_subscription->deposits->contains(function ($deposit) use ($currentMonth) {
        return Carbon::parse($deposit->paid_at)->format('Y-m') === $currentMonth && $deposit->status == 1;
      });


      if (isset($user_subscription->subscribe_amount) && $user_subscription->subscribe_amount !== null) {
        $schemeDuration =$user_subscription->scheme->total_period;
        $totalAmount = $user_subscription->subscribe_amount * $schemeDuration;
        $balance_amount = $totalAmount - $sum;
      }


      if($schemeType->flexibility_duration==""){

        $isFlexible = false;
      }else{

        $startDate = Carbon::parse($user_subscription->start_date);
        $flexibleEndDate = $startDate->copy()->addMonths($schemeType->flexibility_duration);

        $currentYearMonth = Carbon::now()->format('Y-m');
        $flexibleEndYearMonth = $flexibleEndDate->format('Y-m');

        if ($currentYearMonth > $flexibleEndYearMonth) {
          $isFlexible = false;
        } else {
          $isFlexible = true;
        }
      }


      $is_flexible = $schemeType && strtolower($schemeType->shortcode ) === 'flexible' ? true : false;
      $duration = $schemeSetting->due_duration;


      $schemeTitle = trans("messages.scheme_title_". $user_subscription->scheme->scheme_type_id);

      $schemeDescription = trans("messages.scheme_description_". $user_subscription->scheme->scheme_type_id);
      $termsDescription = trans("messages.terms_and_condition_". $user_subscription->scheme->scheme_type_id);


    }
    return response()->json([
      'scheme_name' => $schemeTitle,
      'is_gold' => $isGold,
      'payment_terms' => trans("messages.payment_terms"),
      'scheme_description' =>$schemeDescription,
      'terms_description' =>$termsDescription,
      'starting_date' => Carbon::parse($user_subscription->start_date)->format('d-m-Y'),
      'ending_date' => Carbon::parse($user_subscription->end_date)->format('d-m-Y'),
      'total_amount_paid' => $sum,
      'balance_amount' => $balance_amount ?? null,
      'have_paid' =>  $depositExistsThisMonth,
      'status' => '1',
      'payment_date' =>Carbon::now()->startOfMonth()->format('Y-m-d'),
      'is_flexible' => $isFlexible,
      'deposit_periods' => $payment_history,
    ], 200, [], JSON_UNESCAPED_UNICODE);
  }


  public function deposit(Request $request, $sub_id)
  {

    $request->validate([
      'total_scheme_amount' => 'required',
    ]);

    $id = auth()->user()->id;

    $userSubscription = UserSubscription::where('user_id', $id)
    ->where('id', $sub_id)
    ->with(['scheme.schemeType'])
    ->first();
    
    $deposit_ids = $userSubscription->deposits->pluck('id') ?? null;
    $startDate = Carbon::parse($user_subscription->start_date);
    $dueDate = $user_subscription->deposits
            ->flatMap(function ($deposit) {
                return $deposit->deposit_periods;
            })
            
            ->pluck('due_date')
            ->map(function ($dueDate) {
                return $dueDate ? Carbon::parse($dueDate) : null; // Ensure Carbon parsing
            })
            ->filter() // Remove null values
            ->sort()
            ->last();
            
            $totalFlexibleSchemeAmount = DepositPeriod::whereIn('deposit_id', $deposit_ids)
                    ->where('due_date', '>=', $startDate->format('Y-m-d'))
                    ->where('due_date', '<=', $dueDate->format('Y-m-d'))
                    ->sum('scheme_amount');


    $existingDeposit = Deposit::where('subscription_id', $sub_id)
    ->whereYear('paid_at', Carbon::now()->year)
    ->whereMonth('paid_at', Carbon::now()->month)
    ->first();
    
    $order_id = UniqueHelper::UniqueID();
    $service_charge = '0.00';
    $gst_charge = '0.00';
    $total_scheme_amount = $request->total_scheme_amount;
    $final_amount = $total_scheme_amount +  $service_charge + $gst_charge;
    $api = new Api(env('API_KEY'), env('API_SECRET'));
    $latestGoldRate = GoldRate::where('status', 1)->latest('date_on')->first();

    $totalSchemeAmount = $request->total_scheme_amount;
    $goldWeight = $total_scheme_amount / $latestGoldRate->per_gram;

    $deposit = Deposit::create([
      'subscription_id' => $sub_id,
      'order_id' => $order_id,
      'total_scheme_amount' => $request->total_scheme_amount,
      'final_amount' =>  $final_amount,
      'paid_at' => Carbon::now(),
    ]);

    if($userSubscription->scheme->schemeType->shortcode=="Gold"){

      $goldDeposit = GoldDeposit::create([
        'deposit_id' => $deposit->id,
        'gold_weight' => $goldWeight,
        'gold_unit' => 'gram',
        'status' => '1',
      ]);
    }

    $today = Carbon::now()->format('Y-m-d');
    $dueDuration = SchemeSetting::where('scheme_id',$userSubscription->scheme_id)->first()->due_duration;
    $dueDate = Carbon::now()->startOfMonth()->addDays($dueDuration)->format('Y-m-d');

    DepositPeriod::create([
      'deposit_id' => $deposit->id,
      'due_date' =>  $dueDate ,
      'scheme_amount' => $request->total_scheme_amount,
      'is_due' => ($today > $dueDate) ? '1' : '0',
    ]);

    $amount = $final_amount * 100;
    $order = $api->order->create(array('receipt' => $order_id, 'amount' => $amount, 'currency' => 'INR', 'notes' => array()));
    RazorpayTransaction::create([
      'deposit_id' => $deposit->id,
      'razorpay_order_id' => $order->id,
    ]);

    $currentMonth = Carbon::now()->month;
    $startMonth = Carbon::parse($userSubscription->start_date)->month;
    $startYear = Carbon::parse($userSubscription->start_date)->year;
    $monthDifference = $currentMonth - $startMonth;


    if (abs($monthDifference) >= 6) {


      $totalSchemeAmount = $request->total_scheme_amount;
      $amountPerMonth = $totalSchemeAmount / 6;


      $userSubscription->subscribe_amount = $amountPerMonth;
      $userSubscription->save();
    }

    $responseData = [
      'api_key' => env('API_KEY'),
      'api_secret' => env('API_SECRET'),
      'order_id' => $order->id,
      'amount' => $final_amount,
      'order' => $order,
      'status' => '1'
    ];
    if ($userSubscription->scheme->schemeType->shortcode == "Gold") {
      $responseData['gold_weight'] = number_format($goldWeight, 2);
      $responseData['gold_unit'] = 'gram';
    }
    return response()->json($responseData);

  }

  public function verifySignature(Request $request)
  {
    $input = $request->all();
    $api = new Api(env('API_KEY'), env('API_SECRET'));
    $api->utility->verifyPaymentSignature($input);

    $razor_pay_transaction = RazorpayTransaction::where('razorpay_order_id', $input['razorpay_order_id'])->first();
    $razor_pay_transaction->update([
      'razorpay_payment_id' => $input['razorpay_payment_id'],
      'razorpay_signature' => $input['razorpay_signature'],
      'status' => 1
    ]);

    $deposit = Deposit::where('id', $razor_pay_transaction->deposit_id)->with('deposit_periods')->first();
    foreach ($deposit->deposit_periods as $deposit_period) {
      $dp = DepositPeriod::where('id', $deposit_period->id)->first();
      $dp->update([
        'status' => 1,
      ]);
    }

    $deposit->update([
      'status' => '1',
    ]);

    return response()->json([
      'status' => '1',
      'message' => 'Payment Success',
      'id'=>$deposit->id,
    ]);

  }
  public function wallet()
  {
    $id = auth()->user()->id;
    $user_subscriptions = UserSubscription::with('deposits')->where('user_id', $id)->get();

    $sum = 0;
    foreach ($user_subscriptions as $user_subscription) {
      $sum += $user_subscription->deposits()
      ->where('status', 1)
      ->sum('final_amount');
    }
    return response()->json([
      'wallet' => $sum,
      'status' => '1'
    ]);
  }

  public function my_payments()
  {
    $id = auth()->user()->id;
    $user_subscription = UserSubscription::where('user_id', $id)->first();

    $deposits = [];
    $current_scheme_latest_month = [];
    $user_subscription_deposits =  Deposit::where('subscription_id', $user_subscription->id)->get();

    $start_date_str = $user_subscription->start_date;
    $end_date_str = $user_subscription->end_date;
    $result_dates = $this->generateDates($start_date_str, $end_date_str);

    $rs_dates = [];
    foreach ($result_dates as &$d) {
      $rs_dates[] = [
        'date' => $d,
        'amount' => $user_subscription->scheme->schedule_amount,
        'is_due' => 0,
        'status' => '0',

      ];
    }
    if ($user_subscription_deposits != "") {
      $deposit_periods = [];
      foreach ($user_subscription_deposits as $dp) {
        $deposit_periods[] = $dp->deposit_periods
        ->where('status', 1)
        ->toarray();
      }

      $flattenedArray = array_merge_recursive(...$deposit_periods);
      $deposit_dues = [];
      foreach ($user_subscription_deposits as $dp) {
        $deposit_dues[] = $dp->deposit_periods
        ->where('is_due', 1)
        ->toarray();
      }
      $flattenedArray = array_merge_recursive(...$deposit_periods);
      $simpleflattenedArray = $flattenedArray;

      $flattenedduesArray = array_merge_recursive(...$deposit_dues);

      $items = [];
      foreach ($flattenedArray as &$item) {
        $items[] = [

          'due_date' =>
          Carbon::parse($item['due_date'])->format('d-m-Y'),
          'is_due' => $item['is_due'],
          'status' => $item['status'],
        ];
      }

      $dues = [];
      foreach ($flattenedduesArray as &$due) {
        $dues[] = [

          'due_date' =>
          Carbon::parse($due['due_date'])->format('d-m-Y'),
          'is_due' => $due['is_due'],
          'status' => $due['status'],
        ];
      }
      foreach ($rs_dates as &$item1) {
        $item1['status'] = 0;
        $item1['is_due'] = 0;

        foreach ($items as $item2) {
          if ($item1['date'] === $item2['due_date']) {
            if ($item2['status'] === 1) {
              $item1['status'] = 1;
            }
            break;
          }
        }
        foreach ($dues as $item2) {
          if ($item1['date'] === $item2['due_date']) {
            if ($item2['is_due'] === 1) {
              $item1['is_due'] = 1;
            }
            break;
          }
        }
      }
      foreach ($rs_dates as $key => $item) {
        if ($item["status"] == 1) {

          unset($rs_dates[$key]);
        }
      }
      $last_array = end($rs_dates);
      $current_scheme_latest_month[] = [
        'scheme_name' => $user_subscription->scheme->title,
        'date' =>  $last_array['date'],
        'amount' => $last_array['amount'],
      ];
    } else {
      $last_array = end($result_dates);
      $current_scheme_latest_month[] = [
        'scheme_name' => $user_subscription->scheme->title,
        'date' =>  $last_array['date'],
        'amount' => $last_array['amount'],
      ];
    }
    return response()->json([
      'my_payments' => $current_scheme_latest_month,
      'status' => '1'
    ]);
  }
  public function scheme_history()
  {
    $id = auth()->user()->id;

    $user_subscription = UserSubscription::with('scheme','scheme.schemeType')->where('user_id', $id)->first();

    // $user_subscription_deposits =  Deposit::where('subscription_id', $user_subscription->id)->get();
    $user_subscription_deposits = Deposit::with(['transactions' => function($query) {
      $query->select('deposit_id', 'razorpay_payment_id');
    }])->where('subscription_id', $user_subscription->id)->get();

    $sum = 0;
    $dates_array = [];
    foreach ($user_subscription_deposits as  $user_subscription_deposit) {

      if ($user_subscription_deposit->status == '1') {
        $sum += $user_subscription_deposit->final_amount;
        $transaction = $user_subscription_deposit->transactions->first();

        $razorpay_payment_id = $transaction ? $transaction->razorpay_payment_id : null;



        $schemeTitle = trans("messages.scheme_title_".$user_subscription->scheme->schemeType->id);
        $dates_array[] = [
          'deposit_id' => $user_subscription_deposit->id,
          'scehme' =>    $schemeTitle,
          'date' => $user_subscription_deposit->paid_at,
          'amount' => $user_subscription_deposit->final_amount,
          'razorpay_payment_id' => $razorpay_payment_id,

        ];
      }
    }
    return response()->json([
      'wallet' => $sum,
      'scheme_history' => $dates_array,
      'status' => '1'
    ], 200, [], JSON_UNESCAPED_UNICODE);

  }
  public function paymentDetails($dep_id)
  {
    $deposit = Deposit::with([
      'userSubscription.user.customer',
      'userSubscription.scheme',
      'transactions',
      ])->findOrFail($dep_id);

      $depositDetails = [
        'id' => $deposit->id,
        'name' => $deposit->userSubscription->user->name ?? null,
        'email' => $deposit->userSubscription->user->email ?? null,
        'scheme' => $deposit->userSubscription->scheme->title ?? null,
        'mobile' => $deposit->userSubscription->user->customer->mobile ?? null,
        'referralCode' => $deposit->userSubscription->user->customer->referrel_code ?? null,
        'transactionId' => $deposit->transactions->first()->transaction_no ?? null,
        'paidAmount' => $deposit->final_amount,
        'paymentMethod' => $deposit->payment_type,
        'paymentDate' => $deposit->paid_at,
      ];
      return response()->json([
        'data' => $depositDetails,
        'status' => '1'
      ]);

    }
  public function token(Request $request)
{
   
    $validated = $request->validate([
        'token_id' => 'required', 
        'device_type' => 'required|string', 
    ]);

    
    $customer = Customer::where('user_id', auth()->id())->first();

    if ($customer) {
       
        $customer->update([
            'token_id' => $validated['token_id'],
            'device_type' => $validated['device_type'],
        ]);

        $title = "Device Token Saved";
        $body = "Your device token has been successfully saved.";

      
      

        return response()->json([
            'status' => '1',
        ]);
    }

  
    return response()->json([
        'message' => 'Customer not found.',
    ], 404);
   }

  
public function sendNotification()
{

    $users = UserSubscription::with(['deposits', 'user', 'schemeSetting', 'user.customer'])
        ->get();

    
    $messaging = (new Factory)
        ->withServiceAccount(storage_path('app/google/madhurima-gold-a20be1d55954.json'))
        ->createMessaging();

   
    $currentMonth = now()->format('Y-m');

   
    $due_duration = $users->first()->schemeSetting->due_duration;

    
    $due_date = now()->startOfMonth()->addDays($due_duration - 1);

    
    $notification_date = $due_date->copy()->subDays(3); 

   
    if (now()->between($notification_date->startOfDay(), $due_date->endOfDay())) {

     
        $tokensToNotify = [];

        foreach ($users as $user_subscription) {
         
            $depositExistsThisMonth = $user_subscription->deposits->contains(function ($deposit) use ($currentMonth) {
                return Carbon::parse($deposit->paid_at)->format('Y-m') === $currentMonth && $deposit->status == 0;
            });

           
            if (!$depositExistsThisMonth) {
              
                $token_id = $user_subscription->user->customer->token_id ?? null;

              
                if ($token_id && !in_array($token_id, $tokensToNotify)) {
                    $tokensToNotify[] = $token_id; // Collect token IDs
                } elseif (!$token_id) {
                 
                    \Log::warning("Token ID is missing for user: {$user_subscription->user_id}");
                }
            }
        }

      
        if (!empty($tokensToNotify)) {
         
            $title = "Payment Reminder";
            $body = "Your payment is due on {$due_date->format('Y-m-d')}. Please make the payment before the due date.";

          
            $notification = Notification::create($title, $body);

           
            $message = CloudMessage::new()
                ->withNotification($notification)
                ->withData([
                   
                ]);

         
            try {
              
                $response = $messaging->sendMulticast($message, $tokensToNotify);

             
                \Log::info("One notification sent to multiple users.", ['response' => $response]);
            } catch (\Throwable $e) {
             
                \Log::error("Failed to send notification to users", ['error' => $e->getMessage()]);
            }
        }
    }

    return response()->json(['message' => 'Notification sent successfully!']);
}

  }
