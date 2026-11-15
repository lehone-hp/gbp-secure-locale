<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        $settings = settings()->all($fresh = true);
        return view('admin.settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'contact_email'   => 'required|email|max:191',
            'contact_email_2' => 'nullable|email|max:191',
            'contact_phone'   => 'required|max:191',
            'contact_phone_2' => 'nullable|max:191',
            'notify_email'    => 'required|email|max:191',
            'notify_email_2'  => 'nullable|email|max:191',

            'work_hours' => 'required|string|max:191',
            'address'    => 'required|string|max:191',

            'facebook_url'  => 'nullable|url|max:191',
            'twitter_url'   => 'nullable|url|max:191',
            'instagram_url' => 'nullable|url|max:191',
            'linkedin_url'  => 'nullable|url|max:191',

            'square_exchange_rate' => 'required|numeric|min:0.0001',
            'rate_cad' => 'required|numeric|min:0.0001',
            'rate_eur' => 'required|numeric|min:0.0001',
        ]);

        settings()->set($data);

        session()->flash('success', __('messages.settings_updated'));
        return redirect()->back();
    }
}
