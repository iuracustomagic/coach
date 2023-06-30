<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>
@if(backpack_user()->hasRole('SuperAdmin') || backpack_user()->hasRole('Admin') || backpack_user()->hasRole('Manager'))

    @if(backpack_user()->can('HandleCompanies') || backpack_user()->can('HandleDivisions') || backpack_user()->can('HandleBranches'))
    <!-- Companies -->
    <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-briefcase"></i> {{ trans('nav.companies') }}</a>
        <ul class="nav-dropdown-items">
            @if(backpack_user()->can('HandleCompanies'))<li class='nav-item'><a class='nav-link' href='{{ backpack_url('company') }}'><i class='nav-icon las la-building'></i> {{ trans('nav.companies') }}</a></li>@endif
            @if(backpack_user()->can('HandleDivisions'))<li class='nav-item'><a class='nav-link' href='{{ backpack_url('division') }}'><i class='nav-icon las la-store-alt'></i> {{ trans('nav.divisions') }}</a></li>@endif
            @if(backpack_user()->can('HandleBranches'))<li class='nav-item'><a class='nav-link' href='{{ backpack_url('branch') }}'><i class='nav-icon la la-store'></i> {{ trans('nav.branches') }}</a></li>@endif
        </ul>
    </li>
    @endif

    @if(backpack_user()->can('HandleRegions') || backpack_user()->can('HandleLocalities'))
    <!-- Location -->
    <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-compass"></i> {{ trans('nav.location') }}</a>
        <ul class="nav-dropdown-items">
            @if(backpack_user()->can('HandleRegions'))<li class='nav-item'><a class='nav-link' href='{{ backpack_url('region') }}'><i class='nav-icon la la-map-marked-alt'></i> {{ trans('nav.regions') }}</a></li>@endif
            @if(backpack_user()->can('HandleLocalities'))<li class='nav-item'><a class='nav-link' href='{{ backpack_url('locality') }}'><i class='nav-icon la la-map-signs'></i> {{ trans('nav.localities') }}</a></li>@endif
        </ul>
    </li>
    @endif

    @if(backpack_user()->can('HandleProfessions'))
    <li class='nav-item'><a class='nav-link' href='{{ backpack_url('profession') }}'><i class='nav-icon la la-user-tie'></i> {{ trans('nav.professions') }}</a></li>
    @endif

    @if(backpack_user()->can('HandleCourses') || backpack_user()->can('HandleLessons'))
    <!-- Courses -->
    <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-graduation-cap"></i> {{ trans('nav.studies') }}</a>
        <ul class="nav-dropdown-items">
            @if(backpack_user()->can('HandleCourses'))<li class='nav-item'><a class='nav-link' href='{{ backpack_url('course') }}'><i class='nav-icon la la-book'></i> {{ trans('nav.courses') }}</a></li>@endif
            @if(backpack_user()->can('HandleLessons'))<li class='nav-item'><a class='nav-link' href='{{ backpack_url('lesson') }}'><i class='nav-icon la la-book-open'></i> {{ trans('nav.lessons') }}</a></li>@endif
            <li class='nav-item'><a class='nav-link' href='{{ backpack_url('quiz') }}'><i class='nav-icon la la-question'></i> {{ trans('nav.quizzes') }}</a></li>
            <li class='nav-item'><a class='nav-link' href='{{ backpack_url('attempt') }}'><i class='nav-icon la la-award'></i> {{ trans('nav.attempts') }}</a></li>
        </ul>
    </li>
    @endif

    {{--<li class='nav-item'><a class='nav-link' href='{{ backpack_url('status') }}'><i class='nav-icon las la-tag'></i> Statuses</a></li>--}}

    @if(backpack_user()->can('HandleFiles'))
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('elfinder') }}"><i class="nav-icon la la-files-o"></i> <span>{{ trans('backpack::crud.file_manager') }}</span></a></li>
    @endif

    @if(backpack_user()->can('HandleUsers') || backpack_user()->can('HandleRoles') || backpack_user()->can('HandlePermissions'))
    <!-- Users, Roles, Permissions -->
    <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-users"></i> {{ trans('nav.authentication') }}</a>
        <ul class="nav-dropdown-items">
            @if(backpack_user()->can('HandleUsers'))<li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}"><i class="nav-icon la la-user"></i> <span>{{ trans('nav.users') }}</span></a></li>@endif
            @if(backpack_user()->can('HandleRoles'))<li class="nav-item"><a class="nav-link" href="{{ backpack_url('role') }}"><i class="nav-icon la la-id-badge"></i> <span>{{ trans('nav.roles') }}</span></a></li>@endif
            @if(backpack_user()->can('HandlePermissions'))<li class="nav-item"><a class="nav-link" href="{{ backpack_url('permission') }}"><i class="nav-icon la la-key"></i> <span>{{ trans('nav.permissions') }}</span></a></li>@endif
        </ul>
    </li>
    @endif

@endif

@if(backpack_user()->hasRole('Manager') || backpack_user()->hasRole('Employee'))
<!-- My courses -->
<li class='nav-item'><a class='nav-link' href='{{ url('my-courses') }}'><i class='nav-icon la la-graduation-cap'></i> {{ trans('nav.my_courses') }}</a></li>
<!-- My attempts -->
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('attempt') }}'><i class='nav-icon la la-award'></i> {{ trans('nav.attempts') }}</a></li>
@endif

@if(backpack_user()->hasRole('SuperAdmin'))
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class='nav-icon la la-chalkboard'></i> {{ trans('nav.evaluation_papers') }}</a>
    <ul class="nav-dropdown-items">
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('evaluation-criteria') }}'><i class='nav-icon la la-award'></i> {{ trans('nav.criterias') }}</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('evaluation-paper') }}'><i class='nav-icon la la-chalkboard'></i> {{ trans('nav.evaluation_papers') }}</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('skill') }}'><i class='nav-icon la la-chalkboard'></i> {{ trans('nav.skills') }}</a></li>
    </ul>
</li>
@endif

<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-chart-bar"></i> {{ trans('nav.reports') }}</a>
    <ul class="nav-dropdown-items">
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('report') }}'><i class='nav-icon la la-chart-bar'></i> {{ trans('nav.common') }}</a></li>
        @if(backpack_user()->hasRole('SuperAdmin') || backpack_user()->hasRole('Admin') || backpack_user()->hasRole('Manager'))
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('reports/branch') }}'><i class='nav-icon la la-chart-bar'></i> {{ trans('nav.branch') }}</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('report/user') }}'><i class='nav-icon la la-chart-bar'></i> {{ trans('nav.employees') }}</a></li>
        @endif
    </ul>
</li>
