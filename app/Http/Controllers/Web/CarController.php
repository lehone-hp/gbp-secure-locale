<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\CarMail;
use App\Models\Brand;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CarController extends Controller
{
    public function index(Request $request)
    {
        $cars = Car::query()->published();

        $price = $request->get('price');
        if ($price) {
            if ($price == 'custom') {
                [$min, $max] = explode(',', $request->get('custom_price'));
                $cars->whereBetween('price', [$min, $max]);
            } else {
                $cars->where('price', '<=', $price);
            }
        }

        $brands = collect($request->get('brands'))->filter(function ($brand) {
            return is_numeric($brand);
        })->toArray();
        if (count($brands) > 0) {
            $cars->whereIn('brand_id', $brands);
        }

        if ($request->get('sort')) {
            switch ($request->get('sort')) {
                case 'name_asc':
                    $cars->orderBy('name');
                    break;
                case 'name_desc':
                    $cars->orderByDesc('name');
                    break;
                case 'price_asc':
                    $cars->orderBy('price');
                    break;
                case 'price_desc':
                    $cars->orderByDesc('price');
                    break;
                case 'date':
                    $cars->orderByDesc('created_at');
            }
        } else {
            $cars->orderByDesc('updated_at');
        }

        $cars = $cars->paginate(6);

        $brands = Brand::query()
            ->orderBy('rank')
            ->get();

        return view('web.cars.index',
            compact('brands', 'cars'));
    }

    public function show($slug)
    {
        $car = Car::query()
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        $relatedCars = Car::query()
            ->where('brand_id', $car->brand_id)
            ->where('id', '!=', $car->id)
            ->published()
            ->inRandomOrder()
            ->limit(3)
            ->get();

        return view('web.cars.show', compact('car', 'relatedCars'));
    }

    public function contact(Request $request, $slug)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'message' => 'required',
        ]);

        $car = Car::query()
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
            Mail::to($contacts)->send(new CarMail($data, $car));
        } catch (\Exception $e) {
            $request->session()->flash('alert_error', 'An error occurred while sending your message. Please try again later.');
            return redirect()->back()->withInput();
        }

        $request->session()->flash('alert_success', 'Your message has been submitted successfully!');
        return redirect()->back();
    }
}
