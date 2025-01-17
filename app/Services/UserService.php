<?php

namespace App\Services;

use App\Models\User;
use App\Models\Customer;
use App\Models\Address;
use App\Models\Nominee;
use App\Models\Scheme;
use App\Models\SchemeType;
use App\Models\UserSubscription;
use App\Models\Deposit;
use App\Models\DepositPeriod;
use App\Models\GoldRate;
use App\Models\GoldDeposit;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use File;

use App\Helpers\MachineHelper;
use App\Helpers\UniqueHelper;
use App\Models\BankTransfer;
use App\Models\Discontinue;
use App\Models\TransactionDetail;
use App\Models\TransactionHistory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserService
{

    public function getUsers(int $perPage = 10): Object
    {
        $users =
            User::whereHas('roles', function ($query) {
                $query->whereName('customer');
            })->whereIsAdmin(false)->with('roles', 'customer', 'UserSubscriptions')->latest()->paginate($perPage);
        return  $users;
    }


    public function getActiveUsers(): Object
    {
        $users =
            User::whereHas('roles', function ($query) {
                $query->whereName('customer');
            })->with('roles')->with('active_customer')->get();
        return  $users;
    }

    public function getUsersWithSchemes(): Object
    {
        $users =
            User::whereHas('roles', function ($query) {
                $query->whereName('customer');
            })->with('roles')->with('customer')->whereHas('UserSubscriptions', function ($query) {
                $query->where('is_closed', false);
            })->get();

        return  $users;
    }


    public function createUser(array $userData): User
    {
        $user = User::create([
            'name'     => $userData['name'],
            'email'    => $userData['email'],
            'password' => Hash::make($userData['password']),
        ]);
        $user->assignRole('customer');

        Customer::create([
            'user_id'   => $user->id,
            'mobile'     => $userData['mobile'],
            'referrel_code'     => $userData['referrel_code'],
            'password' => Hash::make($userData['password']),
            'status'     => $userData['status'],

        ]);
        return $user;
    }

    public function getUser($id): Object
    {
        return User::find($id);
    }


    public function updateUser(User $user, array $userData, string $imageUrl = null): void
    {

        $update = [
            'name'    => $userData['name'],
            'email'    => $userData['email'],
        ];
        $user->update($update);
        Customer::where('user_id', $user->id)->update([
            'referrel_code' => $userData['referrel_code'],
            'mobile' => $userData['mobile'],
            'aadhar_number' => $userData['aadhar_number'],
            'pancard_no' => $userData['pancard_no'],
            'status' => $userData['status'],
        ]);
        Address::updateOrCreate(
            [
                'user_id'   => $user->id,
            ],
            [
                'address'     => $userData['address'],
                'country_id' =>  $userData['country_id'],
                'state_id'    => $userData['state_id'],
                'district_id'   => $userData['district_id'],
                'pincode'       => $userData['pincode'],
            ]
        );
        Nominee::updateOrCreate(
            [
                'user_id'   => $user->id,
            ],
            [
                'name'     => $userData['nominee_name'],
                'relationship' => $userData['nominee_relationship'],
                'phone'    => $userData['nominee_phone'],
            ]
        );
    }

    public static function calculateCompletionPercentage($user)
    {
        $fields = [
            'name' => $user->name,
            'email' => $user->email,
            'mobile' => $user->customer->mobile ?? null,
            'aadhar_number' => $user->customer->aadhar_number ?? null,
            'pancard_no' => $user->customer->pancard_no ?? null,
            'address' => $user->address->address ?? null,
            'country_id' => $user->address->country_id ?? null,
            'state_id' => $user->address->state_id ?? null,
            'district_id' => $user->address->district_id ?? null,
            'pincode' => $user->address->pincode ?? null,
            'referrel_code' => $user->customer->referrel_code ?? null,
            'nominee_name' => $user->nominee->name ?? null,
            'nominee_relationship' => $user->nominee->relationship ?? null,
            'nominee_phone' => $user->nominee->phone ?? null,
            'password' => $user->password,
            'is_verified' => $user->customer->is_verified ?? null,
            'status' => $user->customer->status ?? null,
        ];

        $completedFields = 0;

        foreach ($fields as $field) {
            if (!empty($field)) {
                $completedFields++;
            }
        }

        $totalFields = count($fields);
        return round(($completedFields / $totalFields) * 100);
    }

    public function deleteUser(User $user): void
    {
        // delete country
        User::find($user->id)->delete();
        Customer::where('user_id', $user->id)->delete();
    }
    function generateDates($start_date_str, $end_date_str)
    {
        $start_date = Carbon::parse($start_date_str);
        $end_date = Carbon::parse($end_date_str);

        $current_date = $start_date;
        $dates_list = [];

        while ($current_date <= $end_date) {
            $dates_list[] = $current_date->format('d-m-Y');
            // Stop when the current month is reached
            if ($current_date->format('m-Y') == $end_date->format('m-Y')) {
                break;
            }

            $current_date->addMonth(); // Increment by one month
        }

        return $dates_list;
    }


    public function getCurrentPlanHistory($user_subscription_id, $user_id, $scheme_id)
    {
        $user_subscription = UserSubscription::with('scheme.schemeType')->where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
        $user_subscription_deposits = Deposit::where('subscription_id', $user_subscription_id)->get();

        $start_date_str = $user_subscription->start_date
            ? date('Y-m-d', strtotime($user_subscription->start_date))
            : null;

        $end_date_str = $user_subscription->end_date
            ? date('Y-m-d', strtotime($user_subscription->end_date))
            : null;

        $result_dates = $this->generateDates($start_date_str, $end_date_str);

        $startDate = Carbon::parse($user_subscription->start_date)->format('Y-m-d');
        $currentDate = now();
        $flexibility_duration = $user_subscription->scheme->schemeType->flexibility_duration ?? 0;
        $endSixMonthPeriod = Carbon::parse($user_subscription->start_date)->addMonths($flexibility_duration)->format('Y-m-d');
        $totalFlexibleSchemeAmount = DepositPeriod::whereIn('deposit_id', $user_subscription->deposits->pluck('id'))
            ->where('due_date', '>=', $startDate)
            ->where('due_date', '<', $endSixMonthPeriod)
            ->where('status', true)
            ->sum('scheme_amount');

        // Flatten $due_dates if it's a nested collection or array
        $due_dates = collect($user_subscription_deposits)
            ->map(function ($deposit) {
                return $deposit->deposit_periods->pluck('due_date');
            })
            ->flatten()
            ->toArray();

        // Extract the months from $due_dates
        $due_months = array_map(function ($date) {
            return Carbon::parse($date)->format('Y-m'); // Format as "YYYY-MM"
        }, $due_dates);

        // Filter $result_dates to exclude dates with the same month as $due_dates
        $filtered_dates = array_filter($result_dates, function ($date) use ($due_months) {
            $result_month = Carbon::parse($date)->format('Y-m'); // Format as "YYYY-MM"
            return !in_array($result_month, $due_months);
        });

        // Convert filtered dates to a plain array and re-index
        $filtered_dates = array_values($filtered_dates);


        $rs_dates = [];
        foreach ($filtered_dates as &$d) {
            $rs_dates[] = [
                'date' => $d,
                'amount' => $user_subscription->subscribe_amount,
                'is_due' => '0',
                'status' => '0',
                'schemeType' => $user_subscription->scheme->scheme_type_id
            ];
        }
        if ($user_subscription_deposits != "") {
            $deposit_periods = [];
            $sum = 0;
            foreach ($user_subscription_deposits as $dp) {
                $deposit_periods[] = $dp->deposit_periods
                    ->toarray();
                if ($dp->status == 1) {
                    $sum += $dp->final_amount;
                }
            }
            $balance_amount = $user_subscription->scheme->total_amount - $sum;

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
                // Keep only 'due_date' and 'status'
                $items[] = [

                    'due_date' =>
                    Carbon::parse($item['due_date'])->format('d-m-Y'),

                    'is_due' => $item['is_due'],
                    'status' => $item['status'],
                ];
            }
            /// get only unique values for MOST FREQUENT DATES
            $filteredArray = [];
            $grouped = [];
            foreach ($items as $entry) {
                $date = $entry['due_date'];
                if (!isset($grouped[$date])) {
                    $grouped[$date] = ['count' => 0, 'entry' => null];
                }
                $grouped[$date]['count']++;
                $grouped[$date]['entry'] = $entry;
            }

            // Find the most frequent entry for each date
            foreach ($grouped as $date => $info) {
                if ($info['count'] > 0) {
                    $filteredArray[] = $info['entry'];
                }
            }

            $dues = [];
            foreach ($flattenedduesArray as &$due) {
                // Keep only 'due_date' and 'status'

                $dues[] = [

                    'due_date' =>
                    Carbon::parse($due['due_date'])->format('d-m-Y'),
                    'is_due' => $due['is_due'],
                    'status' => $due['status'],
                ];
            }

            foreach ($rs_dates as &$item1) {
                // Set initial status to 0
                $item1['status'] = 0;
                $item1['is_due'] = 0;

                foreach ($filteredArray as $item2) {
                    // Compare based on some criteria, for example, 'id'

                    //  echo $item2['status'];
                    //   echo count($item2['due_date']) . "</br>";

                    if ($item1['date'] === $item2['due_date']) {
                        // If data exists, set status to 1
                        //    $count = count($item2['due_date']);
                        if ($item2['status'] === 1) {
                            $item1['status'] = 1;
                        }
                        break; // No need to check further
                    }
                }
                foreach ($dues as $item2) {
                    // Compare based on some criteria, for example, 'id'
                    if ($item1['date'] === $item2['due_date']) {
                        // If data exists, set status to 1
                        if ($item2['is_due'] === 1) {
                            $item1['is_due'] = 1;
                        }

                        break; // No need to check further
                    }
                }
            }
        }


        return  $responseData = [
            'user_subscription' => $user_subscription,
            'user' => $user_subscription->user,
            'scheme' => $user_subscription->scheme,
            'scheme_start_date' => Carbon::parse($user_subscription->start_date)->format('d-m-Y'),
            'scheme_end_date' => Carbon::parse($user_subscription->end_date)->format('d-m-Y'),
            'total_amount_paid' => $sum,
            'balance_amount' => $balance_amount,
            'result_dates' => $rs_dates,
            'status' => '1',
            'subscribe_amount' => $user_subscription->subscribe_amount,
            'totalPaidAmount' => $totalFlexibleSchemeAmount ?? 0
        ];
    }


    public function payDeposit(array $userData): Deposit
    {
        DB::beginTransaction();

        try {
            
            $order_id = UniqueHelper::UniqueID();
            $service_charge = 0.00;
            $gst_charge = 0.00;
            $total_scheme_amount = $userData['totalAmount'];
            $final_amount = $total_scheme_amount + $service_charge + $gst_charge;
            $user_id = auth()->user()->id;

            
            $user_subscription = UserSubscription::with([
                'deposits.deposit_periods',
                'scheme.schemeType',
                'schemeSetting',
            ])->findOrFail($userData['subscription_id']);

            $scheme = $user_subscription->scheme;
            $schemeType = $scheme->schemeType;
            $schemeSetting = $scheme->schemeSetting;

            $startDate = Carbon::parse($user_subscription->start_date);
            $currentDate = now();
            $flexibility_duration = $schemeType->flexibility_duration ?? 0;
            $endSixMonthPeriod = (clone $startDate)->addMonths($flexibility_duration);

            $duplicateMonths = DepositPeriod::whereHas('deposit', function ($query) use ($user_subscription) {
                $query->where('subscription_id', $user_subscription->id);
            })
                ->where('due_date', '>', $endSixMonthPeriod)
                ->get()
                ->groupBy(function ($item) {
                    return Carbon::parse($item->due_date)->format('Y-m');
                })
                ->filter(function ($group) {
                    return $group->count() > 1;
                });

            if ($duplicateMonths->isNotEmpty()) {
                throw new \Exception('Only one payment is allowed per month after the 6-month period.');
            }

            // Validate total flexible scheme amount
            $totalFlexibleSchemeAmount = DepositPeriod::whereIn('deposit_id', $user_subscription->deposits->pluck('id'))
                ->where('due_date', '>=', $startDate->format('Y-m-d'))
                ->where('due_date', '<', $endSixMonthPeriod->format('Y-m-d'))
                ->where('status', true)
                ->sum('scheme_amount');

            // Create deposit record
            $deposit = Deposit::create([
                'subscription_id' => $userData['subscription_id'],
                'order_id' => $order_id,
                'user_type' => 'admin',
                'total_scheme_amount' => $total_scheme_amount,
                'service_charge' => $service_charge,
                'gst_charge' => $gst_charge,
                'final_amount' => $final_amount,
                'payment_type' => $userData['payment_method'],
                'paid_at' => now(),
                'status' => '1',
            ]);

            $data = collect(json_decode($userData['checkdata'], true));
            $insertData = [];
            $goldDepositData = [];

            foreach ($data as $item) {
                $dueDate = Carbon::parse($item['date']);
                if (now()->isBefore($dueDate)) {
                    throw new \Exception('The payment date cannot be after the current date.');
                }

                if ($schemeType->id !== SchemeType::FIXED_PLAN && $currentDate->diffInMonths($startDate) > $flexibility_duration) {
                    $allowedAmount = $flexibility_duration ? ($totalFlexibleSchemeAmount / $flexibility_duration) : 0;
                    if ($item['amount'] > round($allowedAmount)) {
                        throw new \Exception('The deposit amount exceeds the allowable amount for the period.');
                    }
                }

                $insertData[] = [
                    'deposit_id' => $deposit->id,
                    'due_date' => $dueDate->format('Y-m-d'),
                    'scheme_amount' => $item['amount'] ?? 0,
                    'is_due' => now()->greaterThan($dueDate) ? '1' : '0',
                    'status' => '1',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if ($schemeType->shortcode === "Gold") {
                    $latestGoldRate = GoldRate::where('status', 1)->latest('date_on')->first();
                    if (!$latestGoldRate) {
                        throw new \Exception('Gold rate information is unavailable.');
                    }

                    $goldWeight = $item['amount'] / $latestGoldRate->per_gram;
                    $goldDepositData[] = [
                        'deposit_id' => $deposit->id,
                        'gold_weight' => $goldWeight,
                        'gold_unit' => 'gram',
                        'status' => '1',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            DepositPeriod::insert($insertData);

            if (!empty($goldDepositData)) {
                GoldDeposit::insert($goldDepositData);
            }

            DB::commit();

            return $deposit;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }




    public function getUserSubscription($user_subscription_id): Object
    {
        $userSubscription = UserSubscription::find($user_subscription_id);

        return  $userSubscription;
    }

    public function getSuccessDepositList($user_subscription_id, $user_id, $scheme_id): Object
    {

        $successDeposits = Deposit::where('subscription_id', $user_subscription_id)->where('status', '1')->with('deposit_periods')->orderBy('id', 'desc')->get();
        return  $successDeposits;
    }

    public function getFailedDepositList($user_subscription_id, $user_id, $scheme_id): Object
    {

        $failedDeposits = Deposit::where('subscription_id', $user_subscription_id)->where('status', '2')->orWhere('status', '0')->with('deposit_periods')->orderBy('id', 'desc')->get();
        return  $failedDeposits;
    }



    public function updatePlan(array $data, $userSubscription): void
    {

        if ($data['maturity_status'] != '') {

            $userSubscription->update([
                'is_closed' => $data['maturity_status'],
            ]);
        }
        if ($data['scheme_status'] != '') {
            $userSubscription->update([
                'status' => $data['scheme_status'],
            ]);
            if ($data['scheme_status'] == '2') {
                Discontinue::create([
                    'subscription_id' => $data['subscription_id'],
                    'final_amount' => $data['final_amount'],
                    'settlement_amount' => $data['settlement_amount'],
                    'paid_on' => Carbon::now()->format('Y-m-d'),
                    'reason' => $data['reason'],
                ]);
            }
        }
    }
    public function getDiscontinuedDetails($user_subscription_id)
    {

        $dis = Discontinue::where('subscription_id', $user_subscription_id)->first();
        return $dis;
    }
    public function getSuccessDepositByOrder($order_id): Object
    {
        $successDepositByOrder = Deposit::where('order_id', $order_id)->with('deposit_periods')->first();
        return $successDepositByOrder;
    }

    public function getTransactionByOrder($deposit_id): Object
    {
        $getTransactionByOrder = TransactionHistory::where('deposit_id', $deposit_id)->with('deposit')->first();
        return $getTransactionByOrder;
    }


    public function getFailedDepositByOrder($order_id): Object
    {
        $failedDepositByOrder = Deposit::where('order_id', $order_id)->with('deposit_periods')->first();
        return $failedDepositByOrder;
    }

    public function uploadImage(Request $request): ?string
    {
        $receipt_upload = "";

        if ($request->hasfile('receipt_upload')) {
            $file = $request->receipt_upload;
            $assetName = UniqueHelper::UniqueID() . '-' . time();
            $filename =  $assetName . '.' . $file->getClientOriginalExtension();
            $receipt_upload = 'recipts/' . $filename;
            $file->storeAs('public/', $receipt_upload);
        }

        return $receipt_upload;
    }

    public function saveFailedProcessStatus(array $userData): ?string
    {

        $update = [
            'status'    => $userData['failed_process_status'],
        ];
        Deposit::where('id', $userData['deposit_id'])->update($update);
        $deposit_period = DepositPeriod::where('deposit_id', $userData['deposit_id'])->get();
        foreach ($deposit_period as $item) {
            $item->update([
                'status' => $userData['failed_process_status'],
            ]);
        }
        return '1';
    }
    public function saveTransactionHistory(array $userData, string $receipt_upload): TransactionHistory
    {

        $service_charge = '0.00';
        $gst_charge = '0.00';
        $total_scheme_amount = (array_key_exists('totalAmount', $userData)) ? $userData['totalAmount'] : 0;
        $final_amount = $total_scheme_amount +  $service_charge + $gst_charge;

        return TransactionHistory::create([
            'deposit_id'    => $userData['deposit_id'],
            'transaction_no'    => $userData['transaction_no'],
            'payment_method' => (array_key_exists('payment_method', $userData)) ?
                $userData['payment_method'] : '',
            'payment_response' => (array_key_exists('payment_response', $userData)) ?
                $userData['payment_response'] : '',
            'paid_amount' => $final_amount,
            'upload_file'    => $receipt_upload,
            'remarks'    => $userData['remark'],
            'status' => true
        ]);
    }

    public function saveBankTransfers(array $userData, string $receipt_upload)
    {
        return BankTransfer::create([
            'deposit_id'    => $userData['deposit_id'],
            'transaction_no'    => $userData['transaction_no'],
            'receipt_upload'    => $receipt_upload,
            'remarks'    => $userData['remark'],
            'status' => true
        ]);
    }

    public function getTransactionDetails($deposit_id)
    {
        $transactionDetails = TransactionHistory::where('deposit_id', $deposit_id)->first();

        return $transactionDetails;
    }

    public function changeStatus(array $userData)
    {

        $update = [
            'status'    => $userData['status'],
        ];
        $user = Customer::where('user_id', $userData['id'])->update($update);
        return $user;
    }


    public function getUserSubscriptionList($user_id): Object
    {
        $userSubscription = UserSubscription::with('scheme')->where('user_id', $user_id)->get();
        return  $userSubscription;
    }

    public function addUsertoScheme(array $data): UserSubscription
    {

        $scheme = Scheme::where('id', $data['scheme_id'])->first();
        $total_period = $scheme->total_period;
        $startDate = Carbon::parse($data['start_date']);
        $endDate = $startDate->copy()->addMonths($scheme->total_period - 1)->lastOfMonth();

        return UserSubscription::create([
            'user_id' => $data['user_id'],
            'subscribe_amount' => $data['subscribe_amount'],
            'scheme_id' => $data['scheme_id'],
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'status' => $data['status'],
        ]);
    }
}
