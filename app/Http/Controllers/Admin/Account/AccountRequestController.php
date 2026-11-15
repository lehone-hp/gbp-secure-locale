<?php

namespace App\Http\Controllers\Admin\Account;

use App\Http\Controllers\Controller;
use App\Models\AccountRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AccountRequestController extends Controller
{
    public function index()
    {
        $requests = AccountRequest::with(['user', 'order'])->latest()->paginate(20);

        return view('admin.account-requests.index', compact('requests'));
    }

    public function update(Request $request, AccountRequest $accountRequest)
    {
        $data = $request->validate(['status' => ['required', Rule::in(AccountRequest::STATUSES)]]);

        $accountRequest->update($data);

        session()->flash('success', 'Request updated');

        return redirect()->route('admin.account-requests.index');
    }
}
