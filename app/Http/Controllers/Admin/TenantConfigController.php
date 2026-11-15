<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\TenantConfig;
use App\Services\TenantResolver;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TenantConfigController extends Controller
{
    public function index()
    {
        $tenants = TenantConfig::orderBy('domain')->paginate(15);

        return view('admin.tenants.index', compact('tenants'));
    }

    public function create()
    {
        $gateways = Order::PAYMENT_GATEWAY_LABELS;

        return view('admin.tenants.create', compact('gateways'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'domain' => 'required|string|max:255|unique:tenant_configs,domain',
            'payment_gateway' => 'required|in:' . implode(',', Order::PAYMENT_GATEWAY_VALUES),
            'is_active' => 'boolean',
        ]);

        $tenant = TenantConfig::create([
            'domain' => $validated['domain'],
            'payment_gateway' => $validated['payment_gateway'],
            'is_active' => $request->boolean('is_active', true),
            'settings' => [],
        ]);

        TenantResolver::clearAllTenantCache();

        return redirect()
            ->route('admin.tenants.index')
            ->with('success', 'Tenant configuration created successfully.');
    }

    public function edit(TenantConfig $tenant)
    {
        $gateways = Order::PAYMENT_GATEWAY_LABELS;

        return view('admin.tenants.edit', compact('tenant', 'gateways'));
    }

    public function update(Request $request, TenantConfig $tenant)
    {
        $validated = $request->validate([
            'domain' => ['required', 'string', 'max:255', Rule::unique('tenant_configs')->ignore($tenant->id)],
            'payment_gateway' => 'required|in:' . implode(',', Order::PAYMENT_GATEWAY_VALUES),
            'is_active' => 'boolean',
        ]);

        $tenant->update([
            'domain' => $validated['domain'],
            'payment_gateway' => $validated['payment_gateway'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        TenantResolver::clearAllTenantCache();

        return redirect()
            ->route('admin.tenants.index')
            ->with('success', 'Tenant configuration updated successfully.');
    }

    public function destroy(TenantConfig $tenant)
    {
        $domain = $tenant->domain;
        $tenant->delete();

        TenantResolver::clearAllTenantCache();

        return redirect()
            ->route('admin.tenants.index')
            ->with('success', "Tenant configuration for {$domain} deleted successfully.");
    }

}
