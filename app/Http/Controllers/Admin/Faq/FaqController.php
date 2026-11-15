<?php

namespace App\Http\Controllers\Admin\Faq;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $faqs = Faq::orderBy('updated_at', 'desc')->paginate();
        return view('admin.faqs.index', compact('faqs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $faq = new Faq();
        $locales = config('app.available_locales');
        $localeNames = config('app.locale_names');
        return view('admin.faqs.form', compact('faq', 'locales', 'localeNames'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $data = $this->validated($request);

        Faq::create($data);

        session()->flash('success', 'Faq created successfully');

        return redirect()->route('admin.faqs.index');
    }

    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function show(Faq $faq)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit(Faq $faq)
    {
        $locales = config('app.available_locales');
        $localeNames = config('app.locale_names');
        return view('admin.faqs.form', compact('faq', 'locales', 'localeNames'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(Request $request, Faq $faq)
    {
        $data = $this->validated($request);

        $faq->update($data);

        session()->flash('success', 'Faq updated successfully');
        return redirect()->route('admin.faqs.index');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(Faq $faq)
    {
        $faq->delete();

        session()->flash('success', 'Faq deleted successfully');
        return redirect()->route('admin.faqs.index');
    }

    /**
     * Validate the per-locale fields and fold them into translation arrays.
     */
    private function validated(Request $request): array
    {
        $rules = [];
        foreach (config('app.available_locales') as $locale) {
            $rules["question_{$locale}"] = 'required|string|max:255';
            $rules["answer_{$locale}"] = 'required|string';
        }

        $validated = $request->validate($rules);

        $data = ['question' => [], 'answer' => []];
        foreach (config('app.available_locales') as $locale) {
            $data['question'][$locale] = $validated["question_{$locale}"];
            $data['answer'][$locale] = $validated["answer_{$locale}"];
        }

        return $data;
    }
}
