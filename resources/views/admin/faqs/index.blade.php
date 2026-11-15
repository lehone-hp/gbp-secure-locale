@extends('admin.layouts.app')
@section('title', 'FAQs')

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">FAQs</li>
                    </ol>
                </div>
                <h4 class="page-title">FAQs</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    
    <div class="card card-body">
        <div class="text-right mb-3">
            <a href="{{ route('admin.faqs.create') }}"
               class="btn btn-success">
                New FAQ
            </a>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Question</th>
                    <th>Answer</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($faqs as $faq)
                    <tr>
                        <td>
                            {{ $loop->index + 1 }}
                        </td>
                        <td>
                            {{ $faq->question }}
                        </td>
                        <td>
                            {{ \Illuminate\Support\Str::limit(strip_tags($faq->answer), 100) }}
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('admin.faqs.edit', $faq->id) }}"
                                   class="btn btn-sm btn-secondary"><span class="fa fa-edit"></span></a>
                        
                                <form method="POST" action="{{ route('admin.faqs.destroy', $faq->id) }}">
                                    @csrf
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="button"
                                            class="confirm-btn btn btn-danger btn-sm"
                                            data-message="Are you sure you want to delete this item?">
                                        <span class="fa fa-trash"></span>
                                    </button>
                                </form>
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
            {{ $faqs->links() }}
        </div>
    </div>
@endsection

@section('footer_script')
    <script></script>
@endsection
