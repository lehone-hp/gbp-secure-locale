<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\ContactMail;
use App\Models\Design;
use App\Models\DesignAttributeGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class DesignController extends Controller
{
    public function index()
    {
        $designs = Design::query()
            ->published()
            ->orderByDesc('updated_at')
            ->paginate(9);

        return view('web.designs.index', compact('designs'));
    }

    public function show($slug)
    {
        $design = Design::query()
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        $attributeGroups = [];

        $groups = DesignAttributeGroup::whereHas('designAttributes', function ($query) use ($design) {
            return $query->where('design_id', $design->id);
        })->orderBy('rank')->get();

        foreach ($groups as $group) {
            $attributeGroups[$group->id] = $group->designAttributes()->where('design_id', $design->id)->get();
        }

        $emptyItems = $design->designAttributes()->whereNull('design_attribute_group_id')->get();
        if ($emptyItems->isNotEmpty()) {
            $attributeGroups['empty'] = $emptyItems;
        }

        $relatedDesigns = Design::query()
            ->where('id', '!=', $design->id)
            ->published()
            ->inRandomOrder()
            ->limit(3)
            ->get();

        return view('web.designs.show', compact('design', 'relatedDesigns', 'attributeGroups'));
    }

    public function form()
    {
        return view('web.designs.form');
    }

    public function submit(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'phone' => 'nullable|string',
            'address' => 'required|string',
            'location' => 'required|string',
            'plan_type' => 'required',
            'message' => 'required|string',
        ]);
        try {
            $contacts = [
                settings()->get('notify_email'),
            ];

            if (settings()->get('notify_email_2')) {
                $contacts[] = settings()->get('notify_email_2');
            }

            Mail::to($contacts)->send(new ContactMail($data));
            $request->session()->flash('success', 'Your message has been submitted successfully!');
        } catch (\Exception $e) {
            $request->session()->flash('error', 'An error occurred while sending your message. Please try again later.');
            return  redirect()->back()->withInput();
        }

        return  redirect()->back();
    }
}
