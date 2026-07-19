<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Tenant::with('domains')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tenants,slug',
            'domain' => 'required|string|max:255',
            'plan' => 'nullable|string|max:50',
        ]);

        $tenant = Tenant::create([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'plan' => $data['plan'] ?? 'free',
        ]);

        $tenant->domains()->create(['domain' => $data['domain']]);

        return response()->json($tenant->load('domains'), 201);
    }

    public function show(Tenant $tenant): JsonResponse
    {
        return response()->json($tenant->load('domains'));
    }

    public function update(Request $request, Tenant $tenant): JsonResponse
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'plan' => 'nullable|string|max:50',
        ]);

        $tenant->update($data);

        return response()->json($tenant->load('domains'));
    }

    public function destroy(Tenant $tenant): JsonResponse
    {
        $tenant->delete();

        return response()->json(['message' => 'Tenant deleted.']);
    }
}
