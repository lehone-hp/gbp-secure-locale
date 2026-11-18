<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContractTemplate;
use App\Models\Order;
use App\Services\ContractService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContractTemplateController extends Controller
{
    public function edit()
    {
        return view('admin.contract-template.edit', [
            'template' => ContractTemplate::active(),
            'versions' => ContractTemplate::latest('id')->get(),
            'orders' => Order::latest()->limit(20)->get(),
            'placeholders' => ContractService::PLACEHOLDERS,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate(['body' => 'required|string']);

        $this->activate($data['body']);

        return back()->with('success', 'Contract template saved as a new version.');
    }

    public function restore(ContractTemplate $template)
    {
        $this->activate($template->body);

        return back()->with('success', "Version {$template->version} restored as a new version.");
    }

    public function preview(Request $request, ContractService $contracts)
    {
        $data = $request->validate([
            'body' => 'required|string',
            'order_id' => 'required|exists:orders,id',
        ]);

        $template = new ContractTemplate(['version' => ContractTemplate::active()->version, 'body' => $data['body']]);
        $order = Order::findOrFail($data['order_id']);

        return response($contracts->generate($order, $template), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$contracts->filename($order).'"',
        ]);
    }

    private function activate(string $body): void
    {
        DB::transaction(function () use ($body) {
            ContractTemplate::where('is_active', true)->update(['is_active' => false]);
            ContractTemplate::create([
                'version' => ContractTemplate::nextVersion(),
                'body' => $body,
                'is_active' => true,
                'created_by' => Auth::guard('admin')->id(),
            ]);
        });
    }
}
