<?php

namespace App\Http\Controllers\Admin\Staff;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $staff = Staff::orderBy('rank', 'asc')->get();
        return view('admin.staff.index', compact('staff'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $staffMember = new Staff();
        return view('admin.staff.form', compact('staffMember'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = request()->validate([
            'name'          => 'required',
            'position'      => 'nullable',
            'image'         => 'nullable|image',
            'facebook_url'  => 'nullable|url',
            'twitter_url'   => 'nullable|url',
            'linkedin_url'  => 'nullable|url',
            'instagram_url' => 'nullable|url',
        ]);

        if ($request->file('image')) {
            $data['image_path'] = $request->file('image')->store('staff');
        }

        Staff::create($data);

        session()->flash('success', 'Staff created successfully');

        return redirect()->route('admin.staff.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $staffMember = Staff::findorFail($id);
        return view('admin.staff.form', compact('staffMember'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = request()->validate([
            'name'          => 'required',
            'position'      => 'nullable',
            'image'         => 'nullable|image',
            'facebook_url'  => 'nullable|url',
            'twitter_url'   => 'nullable|url',
            'linkedin_url'  => 'nullable|url',
            'instagram_url' => 'nullable|url',
        ]);

        $staffMember = Staff::findorFail($id);

        if ($request->file('image')) {
            if ($staffMember->image_path) {
                Storage::delete($staffMember->image_path);
            }
            $data['image_path'] = $request->file('image')->store('staff');
        }

        $staffMember->update($data);

        session()->flash('success', 'Staff updated successfully');
        return redirect()->route('admin.staff.index');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $staffMember = Staff::findorFail($id);
        $staffMember->delete();
        session()->flash('success', 'Staff deleted successfully');

        return redirect()->route('admin.staff.index');
    }
}
