<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Deposit;
use App\Models\Scheme;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use App\Services\LogActivityService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(LogActivityService $logActivityService)
    {
        $logactivities = $logActivityService->getlogActivities();
        $activeUsersCount = User::whereHas('customer', function ($query) {
            $query->where('status', true);
        })->where('is_admin', false)->count();
        $schemesCount = Scheme::where('status', true)->count();

        $latestPayments = Deposit::with('userSubscription')->latest()->take(5)->get();

        $schemes = [];
        Scheme::all()->each(function ($scheme) use (&$schemes) {
            $schemeAmount = Deposit::query()
                ->with('userSubscription')
                ->whereHas('userSubscription', function ($query) use ($scheme) {
                    $query->where('scheme_id', $scheme->id);
                })
                ->sum('total_scheme_amount');

            $schemes[] = [
                'name' => $scheme->title,
                'value' => $schemeAmount,
            ];
        });


        return view('home', [
            'logactivities' => $logactivities,
            'activeUsersCount' => $activeUsersCount,
            'schemesCount' => $schemesCount,
            'latestPayments' => $latestPayments,
            'schemes' => $schemes
        ]);
    }

    public function getSchemeChartData()
    {
        $chartData = [];
        $xAxisCategories = [];

        $months = collect(range(1, 12))->map(function ($month) {
            $start = Carbon::create(date('Y'), $month, 1)->startOfMonth();
            $end = $start->copy()->endOfMonth();
            return [
                'start' => $start,
                'end' => $end,
                'month' => $start->format('F'),
            ];
        });

        Scheme::all()->each(function ($scheme) use (&$chartData, $months) {
            $monthlyData = $months->map(function ($month) use ($scheme) {
                $schemeAmount = Deposit::query()
                    ->with('userSubscription')
                    ->whereHas('userSubscription', function ($query) use ($scheme, $month) {
                        $query->where('scheme_id', $scheme->id);
                    })
                    ->where(function ($q) use ($month) {
                        $q->whereBetween('paid_at', [$month['start'], $month['end']])
                            ->orWhereBetween('paid_at', [$month['start'], $month['end']]);
                    })
                    ->sum('total_scheme_amount');

                return $schemeAmount ?: 0;
            });

            $chartData[] = [
                'name' => $scheme->title,
                'data' => $monthlyData->toArray(),
            ];
        });

        $xAxisCategories = $months->pluck('month')->toArray();

        return response()->json([
            'chartData' => $chartData,
            'categories' => $xAxisCategories,
        ]);
    }
}
