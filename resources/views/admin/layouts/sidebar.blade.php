<div class="left-side-menu">

    <div class="h-100" data-simplebar>

        <!--- Sidemenu -->
        <div id="sidebar-menu">

            <ul id="side-menu">

                <li class="menu-title">Navigation</li>
                <li class="{{ Route::is('admin.dashboard') ? 'menuitem-active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}">
                        <i data-feather="home"></i>
                        <span> Dashboards </span>
                    </a>
                </li>
                
                <li class="{{ Request::is('admin/services*') || Request::is('admin/addons*') ? 'menuitem-active' : '' }}">
                    <a href="{{ route('admin.services.index') }}">
                        <i data-feather="package"></i>
                        <span>Services</span>
                    </a>
                </li>
                
                <li class="{{ Request::is('admin/orders*') ? 'menuitem-active' : '' }}">
                    <a href="{{ route('admin.orders.index') }}">
                        <i data-feather="shopping-cart"></i>
                        <span>Orders</span>
                    </a>
                </li>
                <li class="{{ Request::is('admin/account-requests*') ? 'menuitem-active' : '' }}">
                    <a href="{{ route('admin.account-requests.index') }}">
                        <i data-feather="inbox"></i>
                        <span>Customer Requests</span>
                    </a>
                </li>
                
                <li class="{{ Request::is('admin/certifications*') ? 'menuitem-active' : '' }}">
                    <a href="{{ route('admin.certifications.index') }}">
                        <i data-feather="award"></i>
                        <span>Certifications</span>
                    </a>
                </li>
                
                <li class="{{ Request::is('admin/clients*') ? 'menuitem-active' : '' }}">
                    <a href="{{ route('admin.clients.index') }}">
                        <i data-feather="users"></i>
                        <span>Clients</span>
                    </a>
                </li>
                
                <li class="{{ Request::is('admin/settings*') ? 'menuitem-active' : '' }}">
                    <a href="{{ route('admin.settings.edit') }}">
                        <i data-feather="settings"></i>
                        <span>Settings</span>
                    </a>
                </li>

                <li class="{{ Request::is('admin/profile*') ? 'menuitem-active' : '' }}">
                    <a href="{{route('admin.profile')}}">
                        <i data-feather="user"></i>
                        <span>My Account</span>
                    </a>
                </li>

                <li class="menu-title mt-2"></li>
                <li class="{{ Request::is('admin/logout*') ? 'menuitem-active' : '' }}">
                    <a href="{{route('admin.logout')}}" class="text-danger">
                        <i data-feather="log-out"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>
        <!-- End Sidebar -->

        <div class="clearfix"></div>

    </div>
    <!-- Sidebar -left -->

</div>
