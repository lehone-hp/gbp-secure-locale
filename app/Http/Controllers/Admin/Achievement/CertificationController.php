<?php

namespace App\Http\Controllers\Admin\Achievement;

use App\Http\Controllers\Controller;
use App\Http\Requests\CertificationRequest;
use App\Models\Certification;
use Illuminate\Support\Facades\Storage;

class CertificationController extends Controller
{
    /**
     * Display a listing of certifications.
     */
    public function index()
    {
        $certifications = Certification::orderBy('display_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.certifications.index', compact('certifications'));
    }

    /**
     * Show the form for creating a new certification.
     */
    public function create()
    {
        $locales = config('app.available_locales');
        $localeNames = config('app.locale_names');
        
        return view('admin.certifications.create', compact('locales', 'localeNames'));
    }

    /**
     * Store a newly created certification in storage.
     */
    public function store(CertificationRequest $request)
    {
        $validated = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('certifications', 'public');
            $validated['image'] = $imagePath;
        }

        // Set is_active to true by default for new certifications
        $validated['is_active'] = true;

        Certification::create($validated);

        return redirect()
            ->route('admin.certifications.index')
            ->with('success', 'Certification created successfully.');
    }

    /**
     * Show the form for editing the specified certification.
     */
    public function edit(Certification $certification)
    {
        $locales = config('app.available_locales');
        $localeNames = config('app.locale_names');
        
        return view('admin.certifications.edit', compact('certification', 'locales', 'localeNames'));
    }

    /**
     * Update the specified certification in storage.
     */
    public function update(CertificationRequest $request, Certification $certification)
    {
        $validated = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($certification->image && Storage::disk('public')->exists($certification->image)) {
                Storage::disk('public')->delete($certification->image);
            }

            $imagePath = $request->file('image')->store('certifications', 'public');
            $validated['image'] = $imagePath;
        }

        // Handle is_active checkbox
        $validated['is_active'] = $request->has('is_active');

        $certification->update($validated);

        return redirect()
            ->route('admin.certifications.index')
            ->with('success', 'Certification updated successfully.');
    }

    /**
     * Remove the specified certification from storage.
     */
    public function destroy(Certification $certification)
    {
        // Delete image file
        if ($certification->image && Storage::disk('public')->exists($certification->image)) {
            Storage::disk('public')->delete($certification->image);
        }

        $certification->delete();

        return redirect()
            ->route('admin.certifications.index')
            ->with('success', 'Certification deleted successfully.');
    }
}
