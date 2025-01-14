<?php

namespace App\Console\Commands;

use App\Models\SubscriptionHistory;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Console\Command;
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
            $userSubscriptions = UserSubscription::with('scheme')->get();
            $currentDate = Carbon::now();

            collect($userSubscriptions)->each(function ($userSubscription) use ($currentDate) {
                $subscriptionStart = Carbon::parse($userSubscription->start_date);
                $subscriptionEnd = Carbon::parse($userSubscription->end_date);
                $dueDate = $user_subscription->deposits
                    ->flatMap(function ($deposit) {
                        return $deposit->deposit_periods;
                    })
                    ->filter(function ($depositPeriod) {
                        return $depositPeriod->status == true; // Only consider active statuses
                    })
                    ->pluck('due_date')
                    ->map(function ($dueDate) {
                        return $dueDate ? Carbon::parse($dueDate) : null; // Ensure Carbon parsing
                    })
                    ->filter()
                    ->sort()
                    ->last();


                if ($subscriptionStart->diffInDays($dueDate) >= 11 || $subscriptionEnd->diffInDays($dueDate) >= 11) {

                    $userSubscription->update(['status' => UserSubscription::STATUS_ONHOLD]);

                    // Update or create subscription history
                    SubscriptionHistory::updateOrCreate(
                        ['subscription_id' => $userSubscription->id],
                        [
                            'status' => UserSubscription::STATUS_ONHOLD,
                            'is_closed' => false
                        ]
                    );
                }
            });
        } catch (\Exception $e) {
            // Log the error for debugging purposes
            Log::error('Error in Hold Scheme: ' . $e->getMessage());
        }
    }
}
