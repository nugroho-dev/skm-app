<aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
    <div class="container-fluid">
      <!-- BEGIN NAVBAR TOGGLER -->
      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#sidebar-menu"
        aria-controls="sidebar-menu"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>
      <!-- END NAVBAR TOGGLER -->
      <!-- BEGIN NAVBAR LOGO -->
      <div class="navbar-brand navbar-brand-autodark">
        <a href="#">
          <img src="{{ asset('img/sisukma-high-resolution-logo-transparent.png')}}" width="110" height="32" alt="Tabler logo" class="navbar-brand-image" nonce="">
        </a>
      </div>
      <!-- END NAVBAR LOGO -->
      <div class="navbar-nav flex-row d-lg-none">
        <div class="nav-item d-none d-lg-flex me-3">
          <div class="btn-list">
            <a href="https://github.com/tabler/tabler" class="btn btn-5" target="_blank" rel="noreferrer">
              <!-- Download SVG icon from http://tabler.io/icons/icon/brand-github -->
              <svg xmlns="http://www.w3.org/2000/svg" width="24"  height="24"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="icon icon-2"
              >
                <path
                  d="M9 19c-4.3 1.4 -4.3 -2.5 -6 -3m12 5v-3.5c0 -1 .1 -1.4 -.5 -2c2.8 -.3 5.5 -1.4 5.5 -6a4.6 4.6 0 0 0 -1.3 -3.2a4.2 4.2 0 0 0 -.1 -3.2s-1.1 -.3 -3.5 1.3a12.3 12.3 0 0 0 -6.2 0c-2.4 -1.6 -3.5 -1.3 -3.5 -1.3a4.2 4.2 0 0 0 -.1 3.2a4.6 4.6 0 0 0 -1.3 3.2c0 4.6 2.7 5.7 5.5 6c-.6 .6 -.6 1.2 -.5 2v3.5"
                />
              </svg>
              Source code
            </a>
            <a href="https://github.com/sponsors/codecalm" class="btn btn-6" target="_blank" rel="noreferrer">
              <!-- Download SVG icon from http://tabler.io/icons/icon/heart -->
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="icon text-pink icon-2"
              >
                <path d="M19.5 12.572l-7.5 7.428l-7.5 -7.428a5 5 0 1 1 7.5 -6.566a5 5 0 1 1 7.5 6.572" />
              </svg>
              Sponsor
            </a>
          </div>
        </div>
        <div class="d-none d-lg-flex">
          <div class="nav-item">
            <a href="?theme=dark" class="nav-link px-0 hide-theme-dark" title="Enable dark mode" data-bs-toggle="tooltip" data-bs-placement="bottom">
              <!-- Download SVG icon from http://tabler.io/icons/icon/moon -->
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                <path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z" />
              </svg>
            </a>
            <a href="?theme=light" class="nav-link px-0 hide-theme-light" title="Enable light mode" data-bs-toggle="tooltip" data-bs-placement="bottom">
              <!-- Download SVG icon from http://tabler.io/icons/icon/sun -->
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1" >
                <path d="M12 12m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                <path d="M3 12h1m8 -9v1m8 8h1m-9 8v1m-6.4 -15.4l.7 .7m12.1 -.7l-.7 .7m0 11.4l.7 .7m-12.1 -.7l-.7 .7" />
              </svg>
            </a>
          </div>
        </div>
        <div class="nav-item dropdown">
          <a href="#" class="nav-link d-flex lh-1 p-0 px-2" data-bs-toggle="dropdown" aria-label="Open user menu">
            <span class="avatar avatar-sm" style="background-image: url(./static/avatars/000m.jpg)" nonce=""> </span>
            <div class="d-none d-xl-block ps-2">
              <div>Paweł Kuna</div>
              <div class="mt-1 small text-secondary">UI Designer</div>
            </div>
          </a>
          <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
            <a href="./profile.html" class="dropdown-item">Profile</a>
            <div class="dropdown-divider"></div>
            <a href="./settings.html" class="dropdown-item">Settings</a>
            <a href="./sign-in.html" class="dropdown-item">Logout</a>
          </div>
        </div>
      </div>
      <div class="collapse navbar-collapse" id="sidebar-menu">
        <!-- BEGIN NAVBAR MENU -->
        <ul class="navbar-nav pt-lg-3">
           @foreach ($menus as $menu)
              @if(isset($menu['children']))
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                  <span class="nav-link-icon d-md-none d-lg-inline-block"><!-- Download SVG icon from http://tabler.io/icons/icon/home --> 
                  {!! $menu['icon'] !!}
                  </span>
                  <span class="nav-link-title"> {{ $menu['label'] }} </span>
                </a>
                <ul class="dropdown-menu">
                  @foreach ($menu['children'] as $child)
                    <li>
                      <a class="dropdown-item" href="{{ $child['url'] }}">
                        {{ $child['label'] }}
                      </a>
                    </li>
                  @endforeach
                </ul>
              @else
              <li class="nav-item">
                <a class="nav-link" href="{{ $menu['url'] }}">
                  <span class="nav-link-icon d-md-none d-lg-inline-block"><!-- Download SVG icon from http://tabler.io/icons/icon/home --> 
                  {!! $menu['icon'] !!}
                  </span>
                  <span class="nav-link-title"> {{ $menu['label'] }} </span>
                </a>
              </li>
              @endif
          @endforeach
          
        </ul>
        <!-- END NAVBAR MENU -->
      </div>
    </div>
  </aside>
