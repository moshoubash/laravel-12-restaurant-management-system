<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::with('domains')->orderBy('created_at', 'desc')->get();

        return view('central.tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('central.tenants.form', [
            'tenant' => new Tenant(),
            'domains' => '',
            'action' => route('central.tenants.store'),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:tenants,slug'],
            'plan' => ['nullable', 'string', 'max:50'],
            'max_staff' => ['nullable', 'integer', 'min:1'],
            'max_branches' => ['nullable', 'integer', 'min:1'],
            'currency' => ['required', 'string', 'max:3'],
            'timezone' => ['required', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'domains' => ['required', 'string'],
        ]);

        $domains = $this->parseDomains($data['domains']);
        if (empty($domains)) {
            return back()->withInput()->withErrors(['domains' => 'At least one tenant domain is required.']);
        }

        $tenant = Tenant::create([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'domain' => $domains[0],
            'plan' => $data['plan'] ?? 'basic',
            'max_staff' => $data['max_staff'] ?? 10,
            'max_branches' => $data['max_branches'] ?? 1,
            'currency' => $data['currency'],
            'timezone' => $data['timezone'],
            'is_active' => $request->has('is_active'),
        ]);

        foreach ($domains as $domain) {
            $tenant->domains()->create(['domain' => $domain]);
        }

        return redirect()->route('central.tenants.index')->with('success', 'Tenant created successfully.');
    }

    public function edit(Tenant $tenant)
    {
        $domains = $tenant->domains->pluck('domain')->implode("\n");

        return view('central.tenants.form', [
            'tenant' => $tenant,
            'domains' => $domains,
            'action' => route('central.tenants.update', $tenant),
            'method' => 'PUT',
        ]);
    }

    public function update(Request $request, Tenant $tenant)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:tenants,slug,' . $tenant->id],
            'plan' => ['nullable', 'string', 'max:50'],
            'max_staff' => ['nullable', 'integer', 'min:1'],
            'max_branches' => ['nullable', 'integer', 'min:1'],
            'currency' => ['required', 'string', 'max:3'],
            'timezone' => ['required', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'domains' => ['required', 'string'],
        ]);

        $domains = $this->parseDomains($data['domains']);
        if (empty($domains)) {
            return back()->withInput()->withErrors(['domains' => 'At least one tenant domain is required.']);
        }

        $tenant->update([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'domain' => $domains[0],
            'plan' => $data['plan'] ?? $tenant->plan,
            'max_staff' => $data['max_staff'] ?? $tenant->max_staff,
            'max_branches' => $data['max_branches'] ?? $tenant->max_branches,
            'currency' => $data['currency'],
            'timezone' => $data['timezone'],
            'is_active' => $request->has('is_active'),
        ]);

        $tenant->domains()->delete();
        foreach ($domains as $domain) {
            $tenant->domains()->create(['domain' => $domain]);
        }

        return redirect()->route('central.tenants.index')->with('success', 'Tenant updated successfully.');
    }

    public function destroy(Tenant $tenant)
    {
        $tenant->delete();

        return redirect()->route('central.tenants.index')->with('success', 'Tenant deleted successfully.');
    }

    private function parseDomains(?string $domains): array
    {
        if (! $domains) {
            return [];
        }

        return collect(explode("\n", str_replace(["\r\n", "\r"], "\n", $domains)))
            ->map(fn($domain) => trim($domain))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
