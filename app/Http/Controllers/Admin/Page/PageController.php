<?php

namespace App\Http\Controllers\Admin\Page;

use App\Models\Page;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PageController extends Controller
{

    public function index()
    {
        $pages = Page::query();
        
        if (request()->get('search')) {
            $pages->where('name', 'like', "%" . request()->get('search') . "%");
        }

        $pages = $pages->orderBy('name')->paginate(20);

        return view('admin.pages.index', compact('pages'));
    }


    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }


    public function update(Request $request, Page $page)
    {
        $data = request()->validate([
            'content'   => 'nullable'
        ]);

        $page->update($data);

        session()->flash('success', __('messages.content_updated'));
        return redirect()->back();
    }

}
