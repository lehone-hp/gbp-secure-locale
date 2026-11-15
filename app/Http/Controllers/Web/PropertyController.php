<?php

namespace App\Http\Controllers\Web;

use App\Helpers\Navigator;
use App\Http\Controllers\Controller;
use App\Mail\ContactMail;
use App\Mail\PropertyMail;
use App\Models\Location;
use App\Models\Property;
use App\Models\PropertyCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PropertyController extends Controller
{
    public function index(Request $request, $type)
    {
        $forRent = ($type == Navigator::PROPERTY_TYPE_RENT);

        $properties = Property::query()->published();

        $price = $request->get('price');
        if ($price) {
            if ($price == 'custom') {
                [$min, $max] = explode(',', $request->get('custom_price'));
                $properties->whereBetween('price', [$min, $max]);
            } else {
                $properties->where('price', '<=', $price);
            }
        }

        $categories = collect($request->get('categories'))->filter(function ($category) {
            return is_numeric($category);
        })->toArray();
        if (count($categories) > 0) {
            $properties->whereIn('property_category_id', $categories);
        }

        $locations = collect($request->get('locations'))->filter(function ($location) {
            return is_numeric($location);
        })->toArray();
        if (count($locations) > 0) {
            $properties->whereIn('location_id', $locations);
        }

        $properties->where('type', $forRent ? Property::PROPERTY_TYPE_RENT : Property::PROPERTY_TYPE_SALE);

        if ($request->get('sort')) {
            switch ($request->get('sort')) {
                case 'name_asc':
                    $properties->orderBy('name');
                    break;
                case 'name_desc':
                    $properties->orderByDesc('name');
                    break;
                case 'price_asc':
                    $properties->orderBy('price');
                    break;
                case 'price_desc':
                    $properties->orderByDesc('price');
                    break;
                case 'date':
                    $properties->orderByDesc('created_at');
            }
        } else {
            $properties->orderByDesc('updated_at');
        }

        $properties = $properties->paginate(6);

        $categories = PropertyCategory::query()
            ->orderBy('rank')
            ->get();

        $locations = Location::query()
            ->whereHas('properties', function($q) {
                return $q->published();
            })
            ->orderBy('rank')
            ->get();

        return view('web.properties.index',
            compact('forRent', 'type', 'categories', 'locations', 'properties'));
    }

    public function show($type, $slug)
    {
        $property = Property::query()
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        $relatedProperties = Property::query()
            ->where('location_id', $property->location_id)
            ->published()
            ->inRandomOrder()
            ->limit(3)
            ->get();

        return view('web.properties.show', compact('property', 'relatedProperties'));
    }

    public function contact(Request $request, $slug)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'message' => 'required',
        ]);

        $property = Property::query()
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        $contacts = [
            settings()->get('notify_email'),
        ];

        if (settings()->get('notify_email_2')) {
            $contacts[] = settings()->get('notify_email_2');
        }

        try {
            Mail::to($contacts)->send(new PropertyMail($data, $property));
        } catch (\Exception $e) {
            $request->session()->flash('alert_error', 'An error occurred while sending your message. Please try again later.');
            return redirect()->back()->withInput();
        }

        $request->session()->flash('alert_success', 'Your message has been submitted successfully!');
        return redirect()->back();
    }
}
