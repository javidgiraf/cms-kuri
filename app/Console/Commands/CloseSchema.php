<?php

namespace App\Console\Commands;

use App\Models\DepositPeriod;
use App\Models\SchemeType;
use App\Models\SubscriptionHistory;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CloseSchema extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'close:scheme';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '11 Months after hold Kuri Scheme';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->closeScheme();

        return true;
    }

    private function closeScheme()
    {
        try {
            $userSubscriptions = UserSubscription::with('scheme.schemeType', 'deposits.deposit_periods')->get();

            collect($userSubscriptions)->map(function ($userSubscription) {
                $currentDate = now();
                $startDate = Carbon::parse($userSubscription->start_date);
                $endDate = Carbon::parse($userSubscription->end_date);
                $duration = $userSubscription->scheme->total_period;
                $flexibility_duration = $userSubscription->scheme->schemeType->flexibility_duration;

                // Check if there are unpaid deposit periods
                $hasUnpaidPeriods = $userSubscription->deposits
                    ->pluck('id')
                    ->flatMap(function ($depositId) {
                        return DepositPeriod::where('deposit_id', $depositId)
                            ->where('scheme_amount', 0)
                            ->where('status', false)
                            ->exists();
                    })
                    ->contains(true);

                // Check if the scheme's duration is over
                if (
                    $startDate->diffInMonths($currentDate) >= $duration
                    ||
                    (
                        $startDate->diffInMonths($currentDate) >= $flexibility_duration
                        && $userSubscription->scheme->scheme_type_id !== SchemeType::FIXED_PLAN
                        && $hasUnpaidPeriods
                    )
                ) {

                    $userSubscription->update(
                        [
                            'reason' => 'Scheme period of months completed',
                            'is_closed' => true
                        ]
                    );

                    // Update or create the subscription history
                    SubscriptionHistory::updateOrCreate(
                        ['subscription_id' => $userSubscription->id],
                        [
                            'description' => 'Scheme period of months completed',
                            'is_closed' => true
                        ]
                    );

                    Log::info('Scheme closed successfully for subscription ID: ' . $userSubscription->id);
                }
            });
        } catch (\Exception $e) {
            Log::error('Error in Closing Scheme: ' . $e->getMessage());
        }
    }
}
