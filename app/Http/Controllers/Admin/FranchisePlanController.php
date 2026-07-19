<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FranchisePlan;
use Illuminate\Http\Request;

class FranchisePlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $plans = FranchisePlan::withCount('users')->get();
        return view('admin.franchise_plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.franchise_plans.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'max_clients' => 'nullable|integer|min:1',
            'max_queries_per_day_per_client' => 'required|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'features' => 'nullable|array',
            'features.*' => 'string'
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        
        FranchisePlan::create($validated);

        return redirect()->route('admin.franchise-plans.index')
            ->with('success', 'Plan creado exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FranchisePlan $franchisePlan)
    {
        return view('admin.franchise_plans.edit', compact('franchisePlan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FranchisePlan $franchisePlan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'max_clients' => 'nullable|integer|min:1',
            'max_queries_per_day_per_client' => 'required|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'features' => 'nullable|array',
            'features.*' => 'string'
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        // If features is not present, set it to empty array to remove old features
        if (!isset($validated['features'])) {
            $validated['features'] = [];
        }

        $franchisePlan->update($validated);

        return redirect()->route('admin.franchise-plans.index')
            ->with('success', 'Plan actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FranchisePlan $franchisePlan)
    {
        if ($franchisePlan->users()->exists()) {
            return back()->with('error', 'No puedes eliminar un plan que tiene franquicias asignadas.');
        }

        $franchisePlan->delete();

        return redirect()->route('admin.franchise-plans.index')
            ->with('success', 'Plan eliminado exitosamente.');
    }
}
