@if (!auth()->user()->google2fa_enabled_at)
<nav id="site-nav" class="pull-right">
    @can('read.galleries')
        <a href="{{ route('galleries.index') }}" class="first disabled">Students Gallery</a>
    @endcan

    <a href="{{ route('stats.index') }}" class="disabled">Stats</a>

    @can('read.students')
        <a href="{{ route('students.list') }}" class="disabled">Manage Students</a>
    @endcan

    @can('read.donations')
        <a href="{{ route('donations.list') }}" class="disabled">Manage Donations</a>
    @endcan

    @can('read.users')
        <a href="{{ route('users.list') }}" class="disabled">Manage Users</a>
    @endcan

    @can('read.reports')
        <a href="{{ route('reports.index') }}" class="disabled">Reports</a>
    @endcan

    <a href="{{ route('2fa.index') }}" class="last">MFA</a>
</nav>
@else
<nav id="site-nav" class="pull-right">
    @can('read.galleries')
        <a href="{{ route('galleries.index') }}" class="first">Students Gallery</a>
    @endcan

    <a href="{{ route('stats.index') }}">Stats</a>

    @can('read.students')
        <a href="{{ route('students.list') }}">Manage Students</a>
    @endcan

    @can('read.donations')
        <a href="{{ route('donations.list') }}">Manage Donations</a>
    @endcan

    @can('read.users')
        <a href="{{ route('users.list') }}">Manage Users</a>
    @endcan

    @can('read.reports')
        <a href="{{ route('reports.index') }}">Reports</a>
    @endcan

    <a href="{{ route('2fa.index') }}" class="last">MFA</a>
</nav>
@endif