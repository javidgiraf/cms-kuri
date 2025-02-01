<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Deposit;
use App\Models\Scheme;

class OrderSubscriptionFilter extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $user_id = '';
    public $scheme_id = '';
    public $search = null;
    public $status = '';
    public $order_id = '';
    public $from_date = null;
    public $to_date = null;


    public function resetFilters()
    {
        $this->reset(['user_id', 'scheme_id', 'search', 'status', 'order_id', 'from_date', 'to_date']);
    }

    public function resetOnClick()
    {
        $this->user_id = null;
        $this->dispatch('resetSelect');
        $this->resetFilters();

        //     $this->emit('refreshComponent');
    }




    public function user_table_filer()
    {
        $this->user_id;
    }
    public function scheme_table_filer()
    {
        $this->scheme_id;
    }

    public function status_filer()
    {
        $this->status;
    }
    public function order_id_filer()
    {
        $this->order_id;
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
        $orders =  $this->orders();
        $query = Deposit::with('subscription');
    
        $query->when($this->scheme_id, function ($query) {
            $query->whereHas('subscription', function ($query) {
                $query->where('scheme_id', $this->scheme_id);
            });
        });
    
        $query->when($this->user_id, function ($query) {
            $query->whereHas('subscription', function ($query) {
                $query->where('user_id', $this->user_id);
            });
        });
        
        $query->when($this->status, function ($query) {
            $query->where('status', $this->status);
        });
        
        $query->when($this->order_id, function ($query) {
            $query->where('order_id', $this->order_id);
        });
    
        $query->when($this->from_date && $this->to_date, function ($query) {
            // Consider using database-specific date functions for performance
            $query->whereBetween('paid_at', [
                date('Y-m-d', strtotime($this->from_date)),
                date('Y-m-d', strtotime($this->to_date))
            ]);
        });
    
    $deposits = $query->orderBy('id', 'desc')->paginate(10)->appends(request()->query());
        
        return view('livewire.order-subscription-filter', [
            'deposits' => $deposits,
            'users' => $users,
            'schemes' => $schemes,
            'orders' => $orders,
            // 'schemes' => $schemes,
        ]);
    }
    protected function users()
    {

        $users = User::whereHas('roles', function ($query) {
            $query->whereName('customer');
        })->with('roles')->with('customer')->where('is_admin', false)->get();
        return  $users;
    }
    protected function schemes()
    {

        $schemes = Scheme::all();
        return  $schemes;
    }
    protected function orders()
    {

        $orders = Deposit::select('order_id')->get();
        return  $orders;
    }
}
