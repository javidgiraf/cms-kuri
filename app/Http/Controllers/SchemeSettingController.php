<?php

// app/Http/Controllers/SchemeSettingController.php
namespace App\Http\Controllers;

use App\Models\SchemeSetting;
use App\Models\Scheme;
use Illuminate\Http\Request;

class SchemeSettingController extends Controller
{
  public function index()
  {
    $schemeSettings = SchemeSetting::with('scheme')->paginate(10);

    return view('scheme_settings.index', compact('schemeSettings'));
  }

  public function create()
  {
    $schemes = Scheme::all();

    return view('scheme_settings.create', compact('schemes'));
  }

  public function store(Request $request)
  {
    $request->validate([
      'scheme_id' => 'required|integer',
      'max_payable_amount' => 'required|numeric',
      'min_payable_amount' => 'required|numeric',
      'due_duration' => 'required|integer',
      'start_from' => 'integer',
      'end_to' => 'integer',
      'status' => 'required|boolean',
    ]);

    SchemeSetting::create($request->all());
    return redirect()->route('scheme-settings.index')->with('success', 'Scheme Setting created successfully.');
  }

  public function show(SchemeSetting $schemeSetting)
  {
    return view('scheme_settings.show', compact('schemeSetting'));
  }

  public function edit($id)
  {
    $schemeSetting = SchemeSetting::findOrFail($id);
    $schemes = Scheme::all();
    return view('scheme_settings.edit', compact('schemeSetting', 'schemes'));
  }

  public function update(Request $request, SchemeSetting $schemeSetting)
  {
    $request->validate([
      'scheme_id' => 'required|integer',
      'max_payable_amount' => 'required|numeric',
      'min_payable_amount' => 'required|numeric',
      'due_duration' => 'required|integer',
      'start_from' => 'integer',
      'end_to' => 'integer',
      'status' => 'required|boolean',
    ]);

    $schemeSetting->update($request->all());
    return redirect()->route('scheme-settings.index')->with('success', 'Scheme Setting updated successfully.');
  }

  public function destroy(SchemeSetting $schemeSetting)
  {
    $schemeSetting->delete();
    return redirect()->route('scheme-settings.index')->with('success', 'Scheme Setting deleted successfully.');
  }
}
