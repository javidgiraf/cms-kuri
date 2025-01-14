<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Scheme extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'total_period',
        'pdf_file',
        'description',
        'status',
        'scheme_type_id'
    ];

    public function getFormattedTotalAmountAttribute()
    {
        return number_format($this->attributes['total_amount'], 2);
    }
    public function getFormattedScheduleAmountAttribute()
    {
        return number_format($this->attributes['schedule_amount'], 2);
    }
    public function schemeType()
    {
        return $this->belongsTo(SchemeType::class, 'scheme_type_id');
    }

    public function schemeSetting()
    {
        return $this->hasOne(SchemeSetting::class, 'scheme_id', 'id');
    }

    public function setPdfFileAttribute($file)
    {
        if (!$file) {
            $this->attributes['pdf_file'] = null;
            return;
        }

        // Delete the existing file if it exists
        if (!empty($this->attributes['pdf_file'])) {
            $existingFilePath = 'schemes/' . $this->attributes['pdf_file'];
            if (Storage::disk('public')->exists($existingFilePath)) {
                Storage::disk('public')->delete($existingFilePath);
            }
        }

        // Store the new file
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('schemes', $fileName, 'public');
        $this->attributes['pdf_file'] = $fileName;
    }


    public function getPdfFileAttribute()
    {
        return asset('storage/schemes/' . $this->attributes['pdf_file']);
    }
}
