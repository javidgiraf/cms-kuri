<?php

namespace App\Http\Controllers;

// use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

use App\Services\SchemeService;
use App\Helpers\LogActivity;
use App\Models\SchemeType;
use App\Http\Requests\SchemePostRequest;
use App\Http\Requests\SchemeUpdateRequest;
use App\Models\Scheme;

class SchemeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SchemeService $schemeService)
    {
        $schemes = $schemeService->getSchemes();

        return view('schemes.index', compact('schemes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $schemeTypes = SchemeType::all();

        return view('schemes.create', compact('schemeTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SchemePostRequest $request, SchemeService $schemeService)
    {
        $input = $request->all();
        $input['pdf_file'] = $request->file('pdf_file');
        $schemeService->createScheme($input);
        LogActivity::addToLog('New Scheme Added by ' . auth()->user()->name);

        return redirect()->route('schemes.index')->with('success', 'Scheme created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id, SchemeService $schemeService)
    {
        $id = decrypt($id);
        $scheme = $schemeService->getScheme($id);
        $schemeTypes = SchemeType::all();

        return view('schemes.edit', compact('scheme', 'schemeTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SchemeUpdateRequest $request, string $id, SchemeService $schemeService)
    {
        $id = decrypt($id);
        $input = $request->all();
        $input['pdf_file'] = $request->hasFile('pdf_file') ? 
            $request->file('pdf_file') : NULL;
        $scheme = $schemeService->getScheme($id);
        $schemeService->updateScheme($scheme, $input);
        LogActivity::addToLog('Scheme ' . $input['title'] . ' updated by ' . auth()->user()->name);

        return redirect()->route('schemes.index')->with('success', 'Scheme updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, SchemeService $schemeService)
    {
        $id = decrypt($id);
        $scheme = $schemeService->getScheme($id);
        $schemeService->deleteScheme($scheme);
        LogActivity::addToLog('Scheme ' . $scheme->title . ' removed by ' . auth()->user()->name);

        return redirect()->back()
            ->with('success', 'Scheme deleted successfully');
    }

}
