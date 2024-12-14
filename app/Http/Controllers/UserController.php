<?php

namespace App\Http\Controllers;

use App\Exports\UserSubscriptionExport;
use App\Helpers\GenerateRandomNumber;
use App\Http\Requests\UserCreatePostRequest;
use App\Http\Requests\UserUpdatePostRequest;

use Illuminate\Http\Request;
use App\Services\UserService;
use App\Services\StateService;
use App\Helpers\LogActivity;
use App\Http\Requests\CreateSubscriptionRequest;
use App\Http\Requests\DepositPostRequest;
use App\Http\Requests\TransactionPostRequest;
use App\Models\Discontinue;
use App\Models\Scheme;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\SubscriptionHistory;
use App\Services\SchemeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use PDO;
use PhpParser\Node\Expr\Print_;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(UserService $userService)
    {
        $users = $userService->getUsers(10);

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(SchemeService $schemeService)
    {
        $schemes = $schemeService->getActiveSchemes();

        return view('users.create', compact('schemes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserCreatePostRequest $request, UserService $userService)
    {
        $input = $request->all();
        (isset($input['status']) && $input['status']) ? $input['status'] = 1 : $input['status'] = 0;
 
        $user = $userService->createUser($input);
        $input['user_id'] = $user->id;
        $userService->addUsertoScheme($input);
        LogActivity::addToLog('New User ' . $input['name'] . ' created by' . auth()->user()->name);

        return redirect()->route('users.index')->with('success', 'User Created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(UserService $userService, string $id)
    {
        $id = decrypt($id);
        $userSubscription = UserSubscription::with('user', 'scheme')->whereUserId($id)->first();
        $user_subscription_id = isset($userSubscription) ? $userSubscription->id : NULL;
        $user_id = isset($userSubscription) ? $userSubscription->user_id : NULL;
        $scheme_id = isset($userSubscription) ? $userSubscription->scheme_id : NULL;
        $currency = Setting::CURRENCY;

        $current_plan_history = $userService->getCurrentPlanHistory($user_subscription_id, $user_id, $scheme_id);
        $success_deposit_lists = $userService->getSuccessDepositList($user_subscription_id, $user_id, $scheme_id);
        $failed_processing_deposit_lists = $userService->getFailedDepositList($user_subscription_id, $user_id, $scheme_id);

        return view('users.show', 
            compact(
                'current_plan_history', 
                'user_subscription_id', 
                'user_id', 'scheme_id', 
                'success_deposit_lists', 
                'failed_processing_deposit_lists', 
                'userSubscription',
                'currency'
            )
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id, UserService $userService, StateService $stateService)
    {
        //
        $id = decrypt($id);
        $user = $userService->getUser($id);
        //  $states = $stateService->getStatebyCountry('1');
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdatePostRequest $request, string $id, UserService $userService)
    {
        $id = decrypt($id);
        $input = $request->all();
        $user = User::findOrFail($id);
        (isset($input['status']) && $input['status']) ? $input['status'] = 1 : $input['status'] = 0;
        $input['country_id'] = $input['country_id'];
        $input['state_id'] = $input['state_id'];
        $input['district_id'] = $input['district_id'];
        $user = $userService->getUser($id);
        $userService->updateUser($user, $input);
        LogActivity::addToLog('User ' . $input['name'] . ' updated by' . auth()->user()->name);

        return redirect()->route('users.index')->with('success', 'User Updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, UserService $userService)
    {
        //
        $id = decrypt($id);
        $user = $userService->getUser($id);
        $userService->deleteUser($user);
        LogActivity::addToLog('User ' . $user->name . ' removed by' . auth()->user()->name);
        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }
    public function getUserSubscriptions(UserService $userService)
    {
        return view('users.schemes');
    }
    public function getUserSubscriptionsList(Request $request, UserService $userService)
    {
        $input = $request->all();

        $userSubscriptionLists = $userService->getUserSubscriptionList(decrypt($input['user_id']));
        $data2 =  view('partials._user_subscription_list')
            ->with(compact('userSubscriptionLists'))
            ->render();
         return response()->json(['data' => $data2]);
    }

    public function currentPlanHistory(Request $request, UserService $userService)
    {
        $input = $request->all();
        $user_subscription_id = decrypt($input['user_subscription_id']);
        $user_id = decrypt($input['user_id']);
        $scheme_id = decrypt($input['scheme_id']);
        $current_plan_history = $userService->getCurrentPlanHistory($user_subscription_id, $user_id, $scheme_id);

        $data2 =  view('partials._plan_details')
            ->with(compact('current_plan_history', 'user_subscription_id'))
            ->render();

        return response()->json(['data' => $data2]);
    }
    // public function unPaidList(Request $request, UserService $userService)
    // {
    //     $input = $request->all();
    //     $user_subscription_id = decrypt($input['user_subscription_id']);
    //     $user_id = decrypt($input['user_id']);
    //     $scheme_id = decrypt($input['scheme_id']);
    //     $current_plan_history = $userService->getCurrentPlanHistory($user_subscription_id, $user_id, $scheme_id);
    //     $data2 =  view('partials._unpaid_list_details')
    //         ->with(compact('current_plan_history', 'user_subscription_id'))
    //         ->render();

    //     return response()->json(['data' => $data2]);
    // }

    // public function unPaidList(Request $request, UserService $userService)
    // {
    //     $input = $request->all();
    //     $user_subscription_id = decrypt($input['user_subscription_id']);
    //     $user_id = decrypt($input['user_id']);
    //     $scheme = UserSubscription::where('id', $user_subscription_id)->first();
    //     $scheme_id =  $scheme->scheme_id;
    //     $current_plan_history = $userService->getCurrentPlanHistory($user_subscription_id, $user_id, $scheme_id);
    //     $data2 =  view('partials._unpaid_list_details')
    //         ->with(compact('current_plan_history', 'user_subscription_id'))
    //         ->render();

    //     return response()->json(['data' => $data2]);
    // }

    public function unPaidList(Request $request, UserService $userService)
    {
        $input = $request->all();
        $user_subscription_id = decrypt($input['user_subscription_id']);
        $user_id = decrypt($input['user_id']);
        $scheme = UserSubscription::where('id', $user_subscription_id)->first();
        $scheme_id =  $scheme->scheme_id;
        $current_plan_history = $userService->getCurrentPlanHistory($user_subscription_id, $user_id, $scheme_id);
        $data2 =  view('partials._unpaid_list_details_for_deposit')
            ->with(compact('current_plan_history', 'user_subscription_id', 'scheme_id'))
            ->render();

        return response()->json(['data' => $data2]);
    }

    public function generateRandomNumber(UserService $userService)
    {
        try {
            $generator = new GenerateRandomNumber();
            $random_number = $generator->generateRandomNumber();
            $setting = Setting::where('option_code', 'referral-code')->first();
            $string = $setting->option_value;
            $parts = explode("-", $string);
            $middle_parts = array_slice($parts, 1, -1);
            $date_string = implode("-", $middle_parts);
            $date = date($date_string);
            $hyphen = '-';
            $referrel_code_initial_value = $parts[0];
            $referrel_code = $referrel_code_initial_value . $hyphen . $date . $hyphen . $random_number;
            return response()->json(['data' => $referrel_code]);
        } catch (\Exception $e) {
            // Handle any other generic exception
            return response()->json(['data' => '0']);
        }
    }

    public function editSchemeDetails(UserService $userService, $user_subscription_id, $user_id, $scheme_id)
    {
        $user_subscription_id = decrypt($user_subscription_id);
        $user_id = decrypt($user_id);
        $scheme_id = decrypt($scheme_id);
        $current_plan_history = $userService->getCurrentPlanHistory($user_subscription_id, $user_id, $scheme_id);
        $success_deposit_lists = $userService->getSuccessDepositList($user_subscription_id, $user_id, $scheme_id);
        $failed_processing_deposit_lists = $userService->getFailedDepositList($user_subscription_id, $user_id, $scheme_id);

        return view('users.edit-scheme-details', compact('current_plan_history', 'user_subscription_id', 'user_id', 'scheme_id', 'success_deposit_lists', 'failed_processing_deposit_lists'));
    }

    // public  function payDeposit(Request $request, UserService $userService)
    // {
    //     $input = $request->all();

    //     $userService->payDeposit($input);
    //     return response()->json(['data' => '1']);
    //     //  return redirect()->route('users.index')->with('success', 'Deposit Paid successfully');
    // }

    public function payDeposit(DepositPostRequest $request, UserService $userService)
    {
        $input = $request->all();
        $input['subscription_id'] = decrypt($input['subscription_id']);
        $deposit = $userService->payDeposit($input);
        $input['deposit_id'] = $deposit->id;
        $receipt_upload = $userService->uploadImage($request);
        $userService->saveTransactionHistory($input, $receipt_upload);
        $userService->saveBankTransfers($input, $receipt_upload);
        
        return response()->json(['data' => '1']);
        //  return redirect()->route('users.index')->with('success', 'Deposit Paid successfully');
    }
    public function saveTransactionDetails(TransactionPostRequest $request, UserService $userService)
    {
        $input = $request->all();
        $input['deposit_id'] = decrypt($input['deposit_id']);
        $receipt_upload = $userService->uploadImage($request);
        $userService->saveTransactionHistory($input, $receipt_upload);
        $userService->saveBankTransfers($input, $receipt_upload);
        $transactionDetails = $userService->getTransactionDetails($input['deposit_id']);
        $data2 =  view('partials._transaction_details')
            ->with(compact('transactionDetails'))
            ->render();
        return response()->json(['data' => '1', 'data2' => $data2]);
    }

    public function updatePlanStatus(Request $request, UserService $userService)
    {
        $input = $request->all();
        $input['subscription_id'] = decrypt($input['subscription_id']);
        $input['scheme_status'] = isset($input['scheme_status']) ? $input['scheme_status'] : '';
        $input['maturity_status'] = isset($input['maturity_status']) ? $input['maturity_status'] : '';
        $input['final_amount'] = isset($input['final_amount']) ? $input['final_amount'] : '';
        $input['settlement_amount'] = isset($input['settlement_amount']) ? $input['settlement_amount'] : '';
        $input['reason'] = isset($input['reason']) ? $input['reason'] : '';
        $userSubscription = $userService->getUserSubscription($input['subscription_id']);
        $userService->updatePlan($input, $userSubscription);

        $discontinued_details = $userService->getDiscontinuedDetails($input['subscription_id']);
        if ($discontinued_details != "") {
            $data2 =  view('partials._disticontinued_details')
                ->with(compact('discontinued_details'))
                ->render();
        }
        $input['scheme_status'] == '2' ? $response = ['scheme_status' => $input['scheme_status'], 'maturity_status' => $input['maturity_status'], 'discontinued_details' => $data2] : $response = ['scheme_status' => $input['scheme_status'], 'maturity_status' => $input['maturity_status']];
        return response()->json($response);
    }
    public function getPlanStatus(Request $request, UserService $userService)
    {
        $input = $request->all();
        $input['subscription_id'] = decrypt($input['subscription_id']);
        $user_subscription = $userService->getUserSubscription($input['subscription_id']);
        $input['scheme_status'] = $user_subscription->status;
        $input['maturity_status'] = $user_subscription->is_closed;
        $discontinued_details = $userService->getDiscontinuedDetails($input['subscription_id']);
        if ($discontinued_details != "") {
            $data2 =  view('partials._disticontinued_details')
                ->with(compact('discontinued_details'))
                ->render();
        }
        $input['scheme_status'] == '2' ? $response = ['scheme_status' => $input['scheme_status'], 'maturity_status' => $input['maturity_status'], 'discontinued_details' => $data2] : $response = ['scheme_status' => $input['scheme_status'], 'maturity_status' => $input['maturity_status']];
        return response()->json($response);
    }

    public function fetchSuccessDepositbyOrder(Request $request, UserService $userService)
    {
        $input = $request->all();
        $input['order_id'] = decrypt($input['order_id']);
        $success_deposit_by_order = $userService->getSuccessDepositByOrder($input['order_id']);
        $data2 =  view('partials._success_deposit_order_by_id_details')
            ->with(compact('success_deposit_by_order'))
            ->render();
            
        return response()->json(['data' => $data2]);
    }





    // public function saveTransactionDetails(TransactionPostRequest $request, UserService $userService)
    // {
    //     $input = $request->all();
    //     $input['deposit_id'] = decrypt($input['deposit_id']);
    //     $receipt_upload = $userService->uploadImage($request);
    //     $userService->saveTransactionDetails($input, $receipt_upload);
    //     $transactionDetails = $userService->getTransactionDetails($input['deposit_id']);
    //     $data2 =  view('partials._transaction_details')
    //         ->with(compact('transactionDetails'))
    //         ->render();
    //     return response()->json(['data' => '1', 'data2' => $data2]);
    // }

    public function saveFailedProcessStatus(Request $request, UserService $userService)
    {
        $input = $request->all();
        $input['deposit_id'] = $input['deposit_id'];
        $data = $userService->saveFailedProcessStatus($input);
        return response()->json(['data' => $data]);
    }
    public function fetchTransactionDetails(Request $request, UserService $userService)
    {
        $input = $request->all();
        $input['deposit_id'] = decrypt($input['deposit_id']);
        $transactionDetails = $userService->getTransactionDetails($input['deposit_id']);
        $data2 =  view('partials._transaction_details')
            ->with(compact('transactionDetails'))
            ->render();
        return response()->json(['data2' => $data2]);
    }
    public function fetchFailedDepositByorder(Request $request, UserService $userService)
    {
        $input = $request->all();
        $input['order_id'] = decrypt($input['order_id']);
        $failed_deposit_by_order = $userService->getFailedDepositByOrder($input['order_id']);
        $data2 =  view('partials._failed_deposit_order_by_id_details')
            ->with(compact('failed_deposit_by_order'))
            ->render();
        return response()->json(['data' => $data2]);
    }

    public function changeStatus(Request $request, UserService $userService)
    {
        $input = $request->all();
        $input['id'] = $input['user_id'];
        $input['status'] = $input['status'];
        $data = $userService->changeStatus($input);
        return response()->json(['success' => true, 'message' => 'Status Changed Successfully', 'data' => $data]);
    }

    public function userSubscriptions()
    {
        $userSubscriptions = UserSubscription::with('user', 'scheme')->get();
        
        return view('subscriptions.index', compact('userSubscriptions'));
    }

    public function subscriptionsCreate(UserService $userService, SchemeService $schemeService)
    {
        $schemes = $schemeService->getActiveSchemes();
        $users =  $userService->getActiveUsers();
        return view('subscriptions.create', compact('schemes', 'users'));
    }


    public function subscriptionsStore(CreateSubscriptionRequest $request, UserService $userService)
    {
        $input = $request->all();
        $userService->addUsertoScheme($input);

        return redirect()->route('subscriptions.index')->with('success', 'User Added to scheme successfully');;
    }

    public function subscriptionsEdit($id, UserService $userService, SchemeService $schemeService)
    {
        $id = decrypt($id);
        $user_subscription = $userService->getUserSubscription($id);
        $schemes = $schemeService->getActiveSchemes();
        $users =  $userService->getActiveUsers();
        return view('subscriptions.edit', compact('users', 'user_subscription', 'schemes'));
    }

    public function subscriptionsExport(Request $request)
    {
        $fromDate = $request->input('from_date') ? date('Y-m-d', strtotime($request->input('from_date'))) : null;
        $toDate = $request->input('to_date') ? date('Y-m-d', strtotime($request->input('to_date'))) : null;

        return Excel::download(new UserSubscriptionExport(
            $request->input('user_id'),
            $request->input('scheme_id'),
            $request->input('mature_status'),
            $request->input('scheme_status'),
            $fromDate,
            $toDate
        ), 'user-subscriptions-report.xlsx');
    }

    public function changeSubscriptionStatus(Request $request)
    {
        $inputs = $request->all();
        $validator = Validator::make($inputs, [
            'scheme_status' => ['required']
        ]);

        if($validator->fails()) {
            return response()->json([
                'success' => false, 
                'errors' => $validator->errors()
            ], 422);
        }

        $userSubscription = UserSubscription::findOrFail($inputs['subscription_id']);
        $userSubscription->reason = $inputs['reason'];
        $userSubscription->status = $inputs['scheme_status'];
        $userSubscription->update();

        SubscriptionHistory::whereSubscriptionId($inputs['subscription_id'])
            ->update([
                'status' => $inputs['scheme_status'],
                'description' => $inputs['reason']
            ]);
        
        if($inputs['scheme_status'] == UserSubscription::STATUS_DISCONTINUE)
        {
            Discontinue::updateOrCreate(
                [
                    'subscription_id' => $userSubscription->id
                ],
                [
                    'final_amount' => $inputs['final_amount'] ?? 0,
                    'settlement_amount' => $inputs['settlement_amount'] ?? 0,
                    'paid_on' => Carbon::now()->format('Y-m-d'),
                    'reason' => $inputs['reason'],
                    'status' => $inputs['scheme_status'],
                ]
            );
        }

        return response()->json(['success' => true, 'message' => 'Status changed successfully', 'data' => $userSubscription]);    
    }

    public function changeMaturityStatus(Request $request)
    {
        $inputs = $request->all();
        $validator = Validator::make($inputs, [
            'maturity_status' => ['required']
        ]);

        if($validator->fails()) {
            return response()->json([
                'success' => false, 
                'errors' => $validator->errors()
            ], 422);
        }

        $userSubscription = UserSubscription::findOrFail($inputs['subscription_id']);
        $userSubscription->reason = $inputs['maturity_reason'];
        $userSubscription->is_closed = $inputs['maturity_status'];
        $userSubscription->update();

        SubscriptionHistory::whereSubscriptionId($inputs['subscription_id'])
            ->update([
                'is_closed' => $inputs['maturity_status'],
                'description' => $inputs['maturity_reason']
            ]);

        return response()->json(['success' => true, 'message' => 'Maturity Status changed successfully', 'data' => $userSubscription]);    
    }
}
