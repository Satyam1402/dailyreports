<div class="sidebar">

    <!-- Sidebar Menu -->
    <!-- Admin dashboard -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

            {{-- 🛠️ Admin Dashboard --}}
            <li class="nav-header">ADMIN DASHBOARD</li>

            {{-- 👥 User Management --}}
            <li class="nav-item {{ Request::is('all/users*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ Request::is('all/users*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-users-cog"></i>
                    <p>
                        User Management
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>

                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('daily_reports.index') }}" class="nav-link {{ Request::is('all/users*') ? 'active' : '' }}">
                            <i class="far fa-user-plus nav-icon"></i>
                            <p>Add New User</p>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- ✅ Task Management --}}
            <li class="nav-item {{ Request::is('admin/tasks*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ Request::is('admin/tasks*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-tasks"></i>
                    <p>
                        Task Management
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>

                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('admin.tasks.index') }}" class="nav-link {{ Request::is('admin/tasks') ? 'active' : '' }}">
                            <i class="far fa-list-alt nav-icon"></i>
                            <p>All Tasks</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.tasks.create') }}" class="nav-link {{ Request::is('admin/tasks/create') ? 'active' : '' }}">
                            <i class="far fa-plus-square nav-icon"></i>
                            <p>Create Task</p>
                        </a>
                    </li>
                </ul>
            </li>

        </ul>
    </nav>
    <!-- End Admin dashboard -->


    <!-- /.sidebar-menu -->
</div>