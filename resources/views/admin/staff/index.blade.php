@extends('admin.layouts.app')
@section('title', 'Our Team')

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Our Team</li>
                    </ol>
                </div>
                <h4 class="page-title">Our Team</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    
    <div class="card card-body">
        <div class="text-right mb-3">
            <a href="{{ route('admin.staff.create') }}"
               class="btn btn-success">
                New Team Member
            </a>
        </div>
        
        <x-admin.sortable-table model-class="Staff">
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($staff as $staffMember)
                    <tr data-id="{{ $staffMember->id }}">
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="fa fa-sort mr-2" data-toggle="tooltip" title="Sort item."></span>
                                <img src="{{ $staffMember->image_url }}" class="table-image-thumb"> &nbsp;
                                {{ $staffMember->name }}
                            </div>
                        </td>
                        <td>
                            {{ $staffMember->position ?? '--' }}
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('admin.staff.edit', $staffMember->id) }}"
                                   class="btn btn-sm btn-secondary"><span class="fa fa-edit"></span></a>
                        
                                <form method="POST" action="{{ route('admin.staff.destroy', $staffMember->id) }}">
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
        </x-admin.sortable-table>
    </div>
@endsection

@section('footer_script')
    <script></script>
@endsection
