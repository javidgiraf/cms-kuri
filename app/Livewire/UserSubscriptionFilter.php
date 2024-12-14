<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\UserSubscription;
use App\Models\User;
use App\Models\Scheme;
use Livewire\WithPagination;


class UserSubscriptionFilter extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $user_id = null;
    public $scheme_id = null;
    public $scheme_status = null;
    public $mature_status = null;
    public $from_date = null;
    public $to_date = null;
    public function resetFilters()
    {
        $this->reset(['user_id', 'scheme_id', 'scheme_status', 'mature_status', 'from_date', 'to_date']);
    }

    public function resetOnClick()
    {

        $this->resetFilters();
        //     $this->emit('refreshComponent');
    }
    public function user_table_filer()
    {
        $this->user_id;
    }
    public function scheme_table_filer()
    {
        $this->scheme_status;
    }
    public function scheme_status_table_filer()
    {
        $this->scheme_status;
    }
    public function mature_status_table_filer()
    {
        $this->mature_status;
    }
    public function date_id_filer()
    {
        $this->from_date;
        $this->to_date;
    }
    public function render()
    {
        $users =  $this->users();
        $schemes =  $this->schemes();

        $query = UserSubscription::query();
        if (isset($this->user_id)) {

            $query->where('user_id',  $this->user_id);
        }
        if (isset($this->scheme_id)) {

            $query->where('scheme_id',  $this->scheme_id);
        }
        if (isset($this->scheme_status)) {

            $query->where('status',  $this->scheme_status);
        }
        if (isset($this->mature_status)) {

            $query->where('is_closed',  $this->mature_status);
        }
        if (isset($this->from_date) && isset($this->to_date)) {

            $query->whereDate('start_date', '<=', date('Y-m-d', strtotime($this->to_date)))
                ->whereDate('start_date', '>=', date('Y-m-d', strtotime($this->from_date)));
        }
        $userSubscriptions = $query->orderBy('id', 'desc')->paginate(12)->appends(request()->query());
        

        return view('livewire.user-subscription-filter', [
            'userSubscriptions' => $userSubscriptions,
            'users' => $users,
            'schemes' => $schemes,
        ]);
    }

    protected function users()
    {

        $users = User::whereHas('roles', function ($query) {
            $query->whereName('customer');
        })->with('roles')->with('customer')->get();
        return  $users;
    }

    protected function schemes()
    {

        $schemes = Scheme::all();
        return  $schemes;
    }

    public function afterDomUpdate()
    {
        $this->dispatchBrowserEvent('livewire:afterDomUpdate');
    }
}
