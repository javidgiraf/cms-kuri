<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchemeSetting extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'scheme_id', 'max_payable_amount', 'min_payable_amount', 'denomination', 'due_duration', 'start_from', 'end_to', 'status'
    ];

    public function scheme()
    {
        return $this->belongsTo(Scheme::class, 'scheme_id', 'id');
    }
}
