<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\ContactMail;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'required|email|max:255',
            'service' => 'nullable|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        try {
            $contacts = [
                settings()->get('notify_email'),
            ];

            if (settings()->get('notify_email_2')) {
                $contacts[] = settings()->get('notify_email_2');
            }

            Mail::to($contacts)->send(new ContactMail($data));

            $request->session()->flash('success', __('messages.contact_success'));
        } catch (\Exception $e) {
            Log::error("Contact form", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $request->session()->flash('error', __('messages.contact_error'));
            return  redirect()->back()->withInput();
        }

        return redirect()->route('contact');
    }
    public function index()
    {
        $services = Service::visible()->get();
        return view('web.contact', compact('services'));
    }
}
