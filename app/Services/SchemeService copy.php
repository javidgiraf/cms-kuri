<?php

namespace App\Services;

use App\Models\Scheme;
use Illuminate\Http\Request;

class SchemeService
{
    public function getSchemes(): Object
    {
        return Scheme::all();
    }

    public function getActiveSchemes(): Object
    {
        return Scheme::with('schemeType')->where('status', 1)->get();
    }
    
    public function createScheme(array $userData): Scheme
    {
        $create = [
            'title'    => $userData['title'],
            'total_period'    => $userData['total_period'],
            'scheme_type_id'    => $userData['scheme_type_id'],
            'pdf_file' => $userData['pdf_file'],
            'description'    => $userData['description'],
            'status'    => $userData['status'],

        ];
        $scheme = Scheme::create($create);
        return $scheme;
    }

    public function getScheme($id): Object
    {
        return Scheme::find($id);
    }


    public function updateScheme(Scheme $scheme, array $userData, string $imageUrl = null): void
    {

        $update = [
            'title'    => $userData['title'],
            'total_period'    => $userData['total_period'],
            'scheme_type_id'    => $userData['scheme_type_id'],
            'description'    => $userData['description'],
            'status'    => $userData['status'],

        ];
        $scheme->update($update);
    }

    public function deleteScheme(Scheme $scheme): void
    {
        // delete scheme
        Scheme::find($scheme->id)->delete();
    }
}
