<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TransactionDetail;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Deposit;
use App\Models\Scheme;

class TransactionDetailsFilter extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $user_id = null;
    public $scheme_id = null;
    public $search = null;
    public $status = null;
    public $order_id = null;
    public $from_date = null;
    public $to_date = null;
    public $transaction_no = null;

    public function resetFilters()
    {
        $this->reset(['user_id', 'scheme_id', 'search', 'order_id', 'transaction_no', 'from_date', 'to_date']);
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
        $transactions =  $this->transactions();
        $query = TransactionDetail::with('deposit');
        if (isset($this->user_id) || $this->user_id != '') {
            $query->whereHas('deposit', function ($query) {
                $query->whereHas('subscription', function ($query) {
                    $query->where('user_id', $this->user_id);
                });
            });
        }

        if (isset($this->scheme_id) || $this->scheme_id != '') {
            $query->whereHas('deposit', function ($query) {
                $query->whereHas('subscription', function ($query) {
                    $query->where('scheme_id', $this->scheme_id);
                });
            });
        }
        if (isset($this->scheme_id) || $this->scheme_id != '') {
            $query->whereHas('deposit', function ($query) {
                $query->whereHas('subscription', function ($query) {
                    $query->where('scheme_id', $this->scheme_id);
                });
            });
        }

        if (isset($this->order_id) || $this->order_id != '') {

            $query->whereHas('deposit', function ($query) {
                $query->where('order_id', $this->order_id);
            });
        }

        if (isset($this->transaction_no) || $this->transaction_no != '') {


            $query->where('transaction_no', $this->transaction_no);
        }

        if (isset($this->from_date) && isset($this->to_date)) {

            $query->whereHas('deposit', function ($query) {
                $query->whereDate('paid_at', '<=', date('Y-m-d', strtotime($this->to_date)))
                    ->whereDate('paid_at', '>=', date('Y-m-d', strtotime($this->from_date)));
            });
        }


        $transactionDetails = $query->orderBy('id', 'desc')->paginate(12)->appends(request()->query());

        return view('livewire.transaction-details-filter', [
            'transactionDetails' => $transactionDetails,
            'users' => $users,
            'schemes' => $schemes,
            'transactions' => $transactions,
            'orders' => $orders,
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

    protected function transactions()
    {

        $transactions = TransactionDetail::select('transaction_no')->get();
        return  $transactions;
    }
}
