<style>
  .js {
    color: #4154f1 !important;
  }
</style>

<aside id="sidebar" class="sidebar">

  <ul class="sidebar-nav" id="sidebar-nav">

    <li class="nav-item">
      <a class="nav-link {{ Route::is('home') ? '' : 'collapsed' }}" href=" {{route('home')}}">
        <i class="bi bi-house"></i>
        <span>{{ __('Dashboard') }}</span>
      </a>
    </li><!-- End Dashboard Nav -->
    @can(['schemes.index'])
    <li class="nav-item">
      <a class="nav-link {{ Route::is('schemes.*') ? '' : 'collapsed' }}" href="{{route('schemes.index')}}">
        <i class="bi bi-clipboard"></i>
        <span>{{ __('Schemes') }}</span>
      </a>
    </li>
    @endcan

    @role('superadmin')
    @can(['roles.index', 'permissions.index'])
    <li class="nav-item">
      <a class="nav-link {{ Route::is('roles.*') || Route::is('permissions.*') ? '' : 'collapsed' }}" data-bs-target="#roles-permissions-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-gear-wide-connected"></i><span>{{ __('Access Controls') }}</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="roles-permissions-nav" class="nav-content collapse  {{ Route::is('roles.*') || Route::is('permissions.*') ? 'show' : '' }}" data-bs-parent="#sidebar-nav">

        <li>
          <a class="{{ Route::is('roles.*')? 'js' : ''}}" href="{{route('roles.index')}}">
            <i class="bi bi-circle"></i><span>{{ __('Assign Roles') }}</span>
          </a>
        </li>
        <li>
          <a class="{{ Route::is('permissions.*')? 'js' : ''}}" href="{{route('permissions.index')}}">
            <i class="bi bi-circle"></i><span>{{ __('Permissions') }}</span>
          </a>
        </li>

      </ul>
    </li><!-- End Components Nav -->
    @endcan
    @endrole

    @can(['admins.index'])
    <li class="nav-item">
      <a class="nav-link {{ Route::is('admins.*') ? '' : 'collapsed' }}" href="{{ route('admins.index') }}">
        <i class="bi bi-person"></i>
        <span>{{ __('Admins') }}</span>
      </a>
    </li>
    @endcan
    
    @can(['users.index'])
    <li class="nav-item">
      <a class="nav-link {{ Route::is('users.*') ? '' : 'collapsed' }}" data-bs-target="#users-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-people"></i><span>{{ __('Customers') }}</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="users-nav" class="nav-content collapse {{ Route::is('users.*') ? 'show' : '' }} " data-bs-parent="#sidebar-nav">

        <li>
          <a class="{{ Route::is('users.index')||Route::is('users.edit')||Route::is('users.create')? 'js' : ''}}" href="{{route('users.index')}}">
            <i class="bi bi-circle"></i><span>{{ __('Customers List') }}</span>
          </a>
        </li>
        <li>
          <a class="{{ Route::is('users.get-user-subscriptions')||Route::is('users.edit-scheme-details')? 'js' : ''}}" href="{{route('users.get-user-subscriptions')}}">
            <i class="bi bi-circle"></i><span>{{ __('Subscriptions') }}</span>
          </a>
        </li>


      </ul>
    </li><!-- End Components Nav -->
    @endcan
    @can(['deposits.index'])
    <li class="nav-item">
      <a class="nav-link {{ Route::is('deposits.*') ? '' : 'collapsed' }}" href="{{route('deposits.index')}}">
        <i class="bi bi-cart-check"></i>
        <span>{{ __('Deposits') }}</span>
      </a>
    </li>
    @endcan
    @can(['payments.index'])
    <li class="nav-item">
      <a class="nav-link {{ Route::is('payments.*') ? '' : 'collapsed' }}" href="{{route('payments.index')}}">
        <i class="bi bi-wallet"></i>
        <span>{{ __('Payments') }}</span>
      </a>
    </li>
    @endcan

    @can(['transaction-details.index'])
    <li class="nav-item">
      <a class="nav-link {{ Route::is('transaction-details.*') ? '' : 'collapsed' }}" href="{{route('transaction-details.index')}}">
        <i class="bi bi-arrow-left-right"></i>
        <span>{{ __('Transaction Details') }}</span>
      </a>
    </li>
    @endcan



    @can(['goldrates.index', 'countries.index', 'settings.index', 'scheme-settings.index'])
    <li class="nav-item">
      <a class="nav-link {{ Route::is('goldrates.*') || Route::is('logactivities.*') || Route::is('countries.*') || Route::is('states.*') || Route::is('districts.*') || Route::is('settings.*') || Route::is('scheme-settings.*')  ? '' : 'collapsed' }}" data-bs-target="#gold-rate-nav" data-bs-toggle="collapse" href="#">
        <i class="ri ri-settings-3-line"></i><span>{{ __('General Settings') }}</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="gold-rate-nav" class="nav-content collapse {{ Route::is('goldrates.*') || Route::is('logactivities.*') || Route::is('countries.*') || Route::is('states.*') || Route::is('districts.*') || Route::is('settings.*') || Route::is('scheme-settings.*')  ? 'show' : '' }} " data-bs-parent="#sidebar-nav">

        <li>
          <a class="{{ Route::is('goldrates.*')? 'js' : ''}}" href="{{route('goldrates.index')}}">
            <i class="bi bi-circle"></i><span>{{ __('Gold Rate') }}</span>
          </a>
        </li>

        @role('superadmin')
        <li>
          <a class="{{ Route::is('logactivities.*')? 'js' : ''}}" href="{{route('logactivities.index')}}">
            <i class="bi bi-circle"></i><span>{{ __('User Activity Log') }}</span>
          </a>
        </li>
        @endrole

        <li>
          <a class="{{ Route::is('countries.*')? 'js' : ''}}" href="{{route('countries.index')}}">
            <i class="bi bi-circle"></i><span>{{ __('Countries') }}</span>
          </a>
        </li>
        <li>
          <a class="{{ Route::is('states.*')? 'js' : ''}}" href="{{route('states.index')}}">
            <i class="bi bi-circle"></i><span>{{ __('States') }}</span>
          </a>
        </li>
        <li>
          <a class="{{ Route::is('districts.*')? 'js' : ''}}" href="{{route('districts.index')}}">
            <i class="bi bi-circle"></i><span>{{ __('Districts') }}</span>
          </a>
        </li>

        <li>
          <a class="{{ Route::is('settings.*')? 'js' : ''}}" href="{{route('settings.index')}}">
            <i class="bi bi-circle"></i><span>{{ __('Settings') }}</span>
          </a>
        </li>
        <li>
          <a class="{{ Route::is('scheme-settings.*')? 'js' : ''}}" href="{{route('scheme-settings.index')}}">
            <i class="bi bi-circle"></i><span>{{ __('Scheme Settings') }}</span>
          </a>
        </li>



      </ul>
    </li>

    @endcan


  </ul>

</aside><!-- End Sidebar-->