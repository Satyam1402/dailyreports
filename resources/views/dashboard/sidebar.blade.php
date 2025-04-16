<div class="sidebar">
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

            {{-- ADMIN MENU --}}
            @if(auth()->check() && auth()->user()->user_role === 'admin')
                {{-- <li class="nav-header"><h5>ADMIN DASHBOARD</h5></li> --}}

               {{-- Reports Management --}}
               <li class="nav-item">
                     <a href="{{ route('admin.reports.index') }}" class="nav-link bg-primary text-white {{ Request::is('admin/reports*') ? 'active' : '' }}" style="border-radius: 5px;">
                            <i class="nav-icon fas fa-tasks"></i>
                            <p>Reports</p>
                    </a>
                </li>
                {{-- 👥 User Management --}}
                <li class="nav-item">
                    <a href="{{ route('daily_reports.index') }}" class="nav-link bg-primary text-white {{ Request::is('all/users*') ? 'active' : '' }}" style="border-radius: 5px;">
                        <i class="nav-icon fas fa-users-cog"></i>
                        <p>Users</p>
                    </a>
                </li>

                {{-- Task Management --}}
                {{-- <li class="nav-item">
                    <a href="{{ route('admin.tasks.index') }}" class="nav-link bg-primary text-white {{ Request::is('admin/tasks*') ? 'active' : '' }}" style="border-radius: 5px;">
                        <i class="nav-icon fas fa-tasks"></i>
                        <p>Task Management</p>
                    </a>
                </li> --}}

            @endif

            {{-- EMPLOYEE MENU --}}
            @if(auth()->check() && auth()->user()->user_role === 'employee')
                <li class="nav-item">
                    <a href="{{ route('employee.tasks.index') }}" class="nav-link bg-primary text-white {{ Request::is('employee/tasks*') ? 'active' : '' }}" style="border-radius: 5px;">
                        <i class="nav-icon fas fa-user-shield"></i>
                        <p>Reports</p>
                    </a>
                </li>
            @endif

        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</div>
