<?php

namespace App\Console\Commands;

use App\Models\SubscriptionHistory;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Console\Command;

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
        $userSubscriptions = UserSubscription::with('scheme', 'deposits.deposit_periods')->get();

        collect($userSubscriptions)->map(function ($userSubscription) {
            $startDate = Carbon::parse($userSubscription->start_date);
            $endDate = Carbon::parse($userSubscription->end_date);
            $totalPeriodMonths = $userSubscription->scheme->total_period - 1;
            $expectedEndDate = $startDate->copy()->addMonths($totalPeriodMonths);

            // Get the last due date for active deposit periods
            $lastDueDate = $userSubscription->deposits
                ->flatMap(function ($deposit) {
                    return $deposit->deposit_periods;
                })
                ->filter(function ($depositPeriod) {
                    return $depositPeriod->status == true;
                })
                ->pluck('due_date')
                ->map(function ($dueDate) {
                    return $dueDate ? Carbon::parse($dueDate) : null; 
                })
                ->filter() 
                ->sort()
                ->last();

            
            if (!$lastDueDate) {
                return; 
            }

            // Check if the scheme should be closed
            if (
                Carbon::now()->greaterThan($startDate) || 
                $lastDueDate->greaterThanOrEqualTo($expectedEndDate)   
            ) {
                
                $userSubscription->update(['is_closed' => true]);

                // Update or create the subscription history
                SubscriptionHistory::updateOrCreate(
                    ['subscription_id' => $userSubscription->id],
                    ['is_closed' => true]
                );
            }
        });
    } catch (\Exception $e) {
        Log::error('Error in Closing Scheme: ' . $e->getMessage());
    }
}



}
