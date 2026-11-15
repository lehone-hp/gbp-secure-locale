@extends('admin.layouts.app')
@section('title', 'Pages')

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active">Pages</li>
                    </ol>
                </div>
                <h4 class="page-title">Pages</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    
    <div class="card card-body">
        <div class=" col-4 mb-3">
            <form action="{{ route('admin.pages.index') }}" method="GET">
                <div class="input-group">
                    <input type="text" class="form-control"
                           value="{{ request()->get('search') }}"
                           name="search" placeholder="Search by name">
                    <div class="input-group-append">
                        <button class="btn btn-success" type="submit">Search</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="table-responsive position-relative">

            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Content</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($pages as $page)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <span>{{ $page->name }}</span>
                            </div>
                        </td>
                        <td>{{ \Illuminate\Support\Str::limit(strip_tags($page->content), 100) }}</td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('admin.pages.edit', $page->id) }}"
                                   class="btn btn-sm btn-secondary"><span class="fa fa-edit"></span></a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="100%" class="text-center">No records found</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $pages->links() }}
        </div>
    </div>


@endsection

@section('footer_script')
    <script></script>
@endsection
