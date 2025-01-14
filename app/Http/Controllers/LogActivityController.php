<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LogActivityService;

class LogActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(LogActivityService $logActivityService)
    {
        //
        $logactivities = $logActivityService->getlogActivities();
        //

        return view('logactivities.index', compact('logactivities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, LogActivityService $logActivityService)
    {
        //
        $id = decrypt($id);
        $logactivity = $logActivityService->getLogActivity($id);

        return view('logactivities.show', compact('logactivity'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
