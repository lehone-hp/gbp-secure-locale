@extends('admin.layouts.app')
@section('title', 'Dashboard')

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                    </ol>
                </div>
                <h4 class="page-title">Dashboard</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    
    <div class="row">
        <div class="col-md-6 col-lg-4 col-xl-3">
            <x-admin.dashboard.stat-card
                    title="Services"
                    total="{{ $servicesCount }}"
                    icon="fe-home"
                    url="{{ route('admin.services.index') }}"
                    color="danger"
            />
        </div>
        
        <div class="col-md-6 col-lg-4 col-xl-3">
            <x-admin.dashboard.stat-card
                    title="Addons"
                    total="{{ $addonsCount }}"
                    icon="fe-truck"
                    url="{{ route('admin.services.index') }}"
                    color="success"
            />
        </div>
    </div>
    <!-- end row-->

@endsection

@section('footer_script')
    <script></script>
@endsection

