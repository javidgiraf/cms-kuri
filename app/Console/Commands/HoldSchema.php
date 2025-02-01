<?php

namespace App\Console\Commands;

use App\Models\SchemeType;
use App\Models\SubscriptionHistory;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HoldSchema extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hold:scheme';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '10 Days after hold Kuri Scheme';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->holdScheme();

        return true;
    }

    private function holdScheme()
    {
        try {
            $currentDate = now();
            $subscriptionCount = UserSubscription::count();

            UserSubscription::with('scheme.schemeSetting', 'deposits')->chunk($subscriptionCount, function ($userSubscriptions) use ($currentDate) {
                $userSubscriptions->each(function ($userSubscription) use ($currentDate) {

                    $startDate = Carbon::parse($userSubscription->start_date);
                    $endDate = Carbon::parse($userSubscription->end_date);
                    $holdDates = [];
                    $currentHoldDate = $startDate->copy();

                    while ($currentHoldDate->lessThanOrEqualTo($endDate)) {
                        $holdDates[] = $currentHoldDate->copy();
                        $currentHoldDate->addMonthNoOverflow()->startOfMonth();
                    }

                    Log::info("Hold Dates for Subscription ID {$userSubscription->id}: " . implode(', ', array_map(fn($date) => $date->format('Y-m-d'), $holdDates)));

                    collect($holdDates)->each(function ($holdDate) use ($currentDate, $userSubscription) {
                        $duration = $userSubscription->scheme->schemeSetting->due_duration;
                        $flexibility_duration = SchemeType::findOrFail($userSubscription->scheme->scheme_type_id)->flexibility_duration;

                        if (
                            $currentDate->greaterThanOrEqualTo($holdDate) &&
                            $currentDate->diffInDays($holdDate) >= $duration &&
                            $userSubscription->scheme->scheme_type_id == SchemeType::FIXED_PLAN
                            ||
                            $currentDate->greaterThanOrEqualTo(
                                $holdDate->addMonths($flexibility_duration)->addDays($duration)
                            ) &&
                            $userSubscription->scheme->scheme_type_id != SchemeType::FIXED_PLAN
                        ) {
                            DB::transaction(function () use ($userSubscription, $currentDate) {
                                $userSubscription->update(['status' => UserSubscription::STATUS_ONHOLD]);

                                SubscriptionHistory::updateOrCreate(
                                    ['subscription_id' => $userSubscription->id],
                                    [
                                        'scheme_id' => $userSubscription->scheme->id,
                                        'subscribe_amount' => $userSubscription->subscribe_amount,
                                        'start_date' => $userSubscription->start_date,
                                        'end_date' => $userSubscription->end_date,
                                        'hold_date' => $currentDate->format('Y-m-d'),
                                        'total_collected_amount' => $userSubscription->deposits->sum('total_scheme_amount'),
                                        'status' => UserSubscription::STATUS_ONHOLD,
                                        'is_closed' => false,
                                    ]
                                );
                            });

                            Log::info("Subscription ID {$userSubscription->id} has been placed on hold as of {$currentDate->format('Y-m-d')}.");
                        }
                    });
                });
            });
        } catch (\Exception $e) {
            // Log any errors
            Log::error('Error in Hold Scheme: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
