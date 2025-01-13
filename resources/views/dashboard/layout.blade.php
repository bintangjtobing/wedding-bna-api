<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>@yield('title')</title>

  <!-- Prevent the demo from appearing in search engines -->
  <meta name="robots" content="noindex">

  <!-- Perfect Scrollbar -->
  <link type="text/css" href="{{asset('assets/vendor/perfect-scrollbar.css')}}" rel="stylesheet">

  <!-- App CSS -->
  <link type="text/css" href="{{asset('assets/css/app.css')}}" rel="stylesheet">
  <link type="text/css" href="{{asset('assets/css/app.rtl.css')}}" rel="stylesheet">

  <!-- Material Design Icons -->
  <link type="text/css" href="{{asset('assets/css/vendor-material-icons.css')}}" rel="stylesheet">
  <link type="text/css" href="{{asset('assets/css/vendor-material-icons.rtl.css')}}" rel="stylesheet">

  <!-- Font Awesome FREE Icons -->
  <link type="text/css" href="{{asset('assets/css/vendor-fontawesome-free.css')}}" rel="stylesheet">
  <link type="text/css" href="{{asset('assets/css/vendor-fontawesome-free.rtl.css')}}" rel="stylesheet">

  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-xxxxxx-x"></script>
  <script>
    window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());
            gtag('config', 'UA-xxxxxx-x');
  </script>

  <!-- Flatpickr -->
  <link type="text/css" href="{{asset('assets/css/vendor-flatpickr.css')}}" rel="stylesheet">
  <link type="text/css" href="{{asset('assets/css/vendor-flatpickr.rtl.css')}}" rel="stylesheet">
  <link type="text/css" href="{{asset('assets/css/vendor-flatpickr-airbnb.css')}}" rel="stylesheet">
  <link type="text/css" href="{{asset('assets/css/vendor-flatpickr-airbnb.rtl.css')}}" rel="stylesheet">

  <!-- Vector Maps -->
  <link type="text/css" href="{{asset('assets/vendor/jqvmap/jqvmap.min.css')}}" rel="stylesheet">

</head>

<body class="layout-default">

  <div class="preloader"></div>

  <!-- Header Layout -->
  <div class="mdk-header-layout js-mdk-header-layout">

    <!-- Header -->

    <div id="header" class="mdk-header js-mdk-header m-0" data-fixed>
      <div class="mdk-header__content">

        <div class="navbar navbar-expand-sm navbar-main navbar-dark bg-dark  pr-0" id="navbar" data-primary>
          <div class="container-fluid p-0">

            <!-- Navbar toggler -->

            <button class="navbar-toggler navbar-toggler-right d-block d-lg-none" type="button" data-toggle="sidebar">
              <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar Brand -->
            <a href="/" class="navbar-brand ">

              <svg class="mr-2" xmlns="http://www.w3.org/2000/svg" fill="currentColor" style="width:20px;"
                viewBox="0 0 40 40">
                <path
                  d="M40 34.16666667c.01-3.21166667-2.58333333-5.82333334-5.795-5.835-1.94-.00666667-3.75666667.955-4.84166667 2.565-.10166666.155-.295.22333333-.47166666.16666666L11.94 25.66666667c-.19-.06-.31-.245-.28833333-.44333334.01-.07333333.015-.14833333.015-.22333333 0-.06833333-.005-.13833333-.01333334-.20666667-.02166666-.20166666.105-.39.3-.44666666l17.96-5.13c.13833334-.04.28666667-.005.39333334.09166666 1.05.97333334 2.42833333 1.51666667 3.86 1.525C37.38833333 20.83333333 40 18.22166667 40 15s-2.61166667-5.83333333-5.83333333-5.83333333C32.52 9.17166667 30.95333333 9.87833333 29.86 11.11c-.11.12166667-.28.16833333-.43666667.11833333L11.91 5.65333333c-.16-.05-.27333333-.19166666-.28833333-.35833333-.30333334-3.20166667-3.14333334-5.55166667-6.345-5.24833333S-.275 3.19.02833333 6.39166667c.28166667 2.99333333 2.79833334 5.28 5.805 5.275 1.64666667-.005 3.21333334-.71333334 4.30666667-1.945.11-.12166667.28-.16833334.43666667-.11833334l16.57 5.27166667c.22.06833333.34166666.30333333.27166666.52333333-.04166666.13333334-.14833333.23833334-.28333333.275L9.90333333 20.59666667c-.13333333.03833333-.275.00833333-.38166666-.08-1.03333334-.86833334-2.33833334-1.34666667-3.68833334-1.35C2.61166667 19.16666667 0 21.77833333 0 25s2.61166667 5.83333333 5.83333333 5.83333333c1.355-.005 2.665-.485 3.7-1.35833333.10833334-.09166667.25833334-.12.39333334-.07666667l18.29 5.81833334c.14.04333333.24666666.15666666.28.3.75666666 3.13166666 3.90833333 5.05666666 7.04 4.3C38.14833333 39.185 39.99 36.85333333 40 34.16666667z" />
              </svg>

              <span>Wedding API BE</span>
            </a>

            <form class="search-form d-none d-sm-flex flex" action="/">
              <button class="btn" type="submit"><i class="material-icons">search</i></button>
              <input type="text" class="form-control" placeholder="Search">
            </form>

            <ul class="nav navbar-nav ml-auto d-none d-md-flex">
              <li class="nav-item dropdown">
                <a href="#notifications_menu" class="nav-link dropdown-toggle" data-toggle="dropdown"
                  data-caret="false">
                  <i class="material-icons nav-icon navbar-notifications-indicator">notifications</i>
                </a>
                <div id="notifications_menu" class="dropdown-menu dropdown-menu-right navbar-notifications-menu">
                  <div class="dropdown-item d-flex align-items-center py-2">
                    <span class="flex navbar-notifications-menu__title m-0">Notifications</span>
                    <a href="javascript:void(0)" class="text-muted"><small>Clear all</small></a>
                  </div>
                </div>
              </li>
            </ul>

            <ul class="nav navbar-nav d-none d-sm-flex border-left navbar-height align-items-center">
              <li class="nav-item dropdown">
                <a href="#account_menu" class="nav-link dropdown-toggle" data-toggle="dropdown" data-caret="false">
                  <span class="mr-1 d-flex-inline">
                    <span class="text-light">Bintang Tobing</span>
                  </span>
                  <img src="{{ asset('assets/images/avatar/demi.png') }}" class="rounded-circle" width="32"
                    alt="Frontted">
                </a>
                <div id="account_menu" class="dropdown-menu dropdown-menu-right">
                  <div class="dropdown-item-text dropdown-item-text--lh">
                    <div><strong>Bintang Tobing</strong></div>
                    <div class="text-muted">@bcjlt</div>
                  </div>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item active" href="/"><i class="material-icons">dvr</i> Dashboard</a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="/"><i class="material-icons">exit_to_app</i>
                    Logout</a>
                </div>
              </li>
            </ul>

          </div>
        </div>

      </div>
    </div>

    <!-- // END Header -->

    <!-- Header Layout Content -->
    <div class="mdk-header-layout__content">

      <div class="mdk-drawer-layout js-mdk-drawer-layout" data-push data-responsive-width="992px">
        <div class="mdk-drawer-layout__content page">

          <div class="container-fluid page__heading-container">
            <div class="page__heading d-flex align-items-end">
              <div class="flex">
                <nav aria-label="breadcrumb">
                  <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">@yield('title')</li>
                  </ol>
                </nav>
                <h1 class="m-0">@yield('title')</h1>
              </div>
              <div class="flatpickr-wrapper ml-3">
                <div id="recent_orders_date" data-toggle="flatpickr" data-flatpickr-wrap="true"
                  data-flatpickr-mode="range" data-flatpickr-alt-format="d/m/Y" data-flatpickr-date-format="d/m/Y">
                  <a href="javascript:void(0)" class="link-date" data-toggle>13/03/2018 to 20/03/2018</a>
                  <input class="flatpickr-hidden-input" type="hidden" value="13/03/2018 to 20/03/2018" data-input>
                </div>
              </div>
            </div>
          </div>

          <div class="container-fluid page__container">
            <div class="row card-group-row">
              @yield('content')
            </div>
          </div>

        </div>
        <!-- // END drawer-layout__content -->

        <div class="mdk-drawer js-mdk-drawer" id="default-drawer" data-align="start">
          <div class="mdk-drawer__content">
            <div class="sidebar sidebar-light sidebar-left sidebar-p-t" data-perfect-scrollbar>
              <!-- Sidebar Heading -->
              <div class="sidebar-heading">Menu</div>

              <!-- Sidebar Menu -->
              <ul class="sidebar-menu">
                <!-- User Menu -->
                <li class="sidebar-menu-item">
                  <a class="sidebar-menu-button" href="/users">
                    <i class="sidebar-menu-icon sidebar-menu-icon--left material-icons">person</i>
                    <span class="sidebar-menu-text">User</span>
                  </a>
                </li>

                <!-- Tamu Menu -->
                <li class="sidebar-menu-item">
                  <a class="sidebar-menu-button" href="/guests">
                    <i class="sidebar-menu-icon sidebar-menu-icon--left material-icons">group</i>
                    <span class="sidebar-menu-text">Tamu</span>
                  </a>
                </li>
              </ul>

              <!-- Sidebar Footer (Optional) -->
              <div class="d-flex align-items-center sidebar-p-a border-top sidebar-account">
                <a href="/" class="flex d-flex align-items-center text-underline-0 text-body">
                  <span class="avatar avatar-sm mr-2">
                    <img src="{{ asset('assets/images/avatar/demi.png') }}" alt="avatar"
                      class="avatar-img rounded-circle">
                  </span>
                  <span class="flex d-flex flex-column">
                    <strong>Bintang Tobing</strong>
                    <small class="text-muted text-uppercase">Dashboard</small>
                  </span>
                </a>
                <div class="dropdown ml-auto">
                  <a href="#" data-toggle="dropdown" data-caret="false" class="text-muted">
                    <i class="material-icons">more_vert</i>
                  </a>
                  <div class="dropdown-menu dropdown-menu-right">
                    <div class="dropdown-item-text dropdown-item-text--lh">
                      <div><strong>Bintang Tobing</strong></div>
                      <div>@bcjlt</div>
                    </div>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item active" href="dashboard.html">Dashboard</a>
                    <a class="dropdown-item" href="/">My profile</a>
                    <a class="dropdown-item" href="/">Edit account</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="/">Logout</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
      <!-- // END drawer-layout -->

    </div>
    <!-- // END header-layout__content -->

  </div>
  <!-- // END header-layout -->

  <!-- App Settings FAB -->
  <div id="app-settings">
    <app-settings layout-active="default" :layout-location="{
      'default': '/'
    }"></app-settings>
  </div>

  <!-- jQuery -->
  <script src="{{asset('assets/vendor/jquery.min.js')}}"></script>

  <!-- Bootstrap -->
  <script src="{{asset('assets/vendor/popper.min.js')}}"></script>
  <script src="{{asset('assets/vendor/bootstrap.min.js')}}"></script>

  <!-- Perfect Scrollbar -->
  <script src="{{asset('assets/vendor/perfect-scrollbar.min.js')}}"></script>

  <!-- DOM Factory -->
  <script src="{{asset('assets/vendor/dom-factory.js')}}"></script>

  <!-- MDK -->
  <script src="{{asset('assets/vendor/material-design-kit.js')}}"></script>

  <!-- App -->
  <script src="{{asset('assets/js/toggle-check-all.js')}}"></script>
  <script src="{{asset('assets/js/check-selected-row.js')}}"></script>
  <script src="{{asset('assets/js/dropdown.js')}}"></script>
  <script src="{{asset('assets/js/sidebar-mini.js')}}"></script>
  <script src="{{asset('assets/js/app.js')}}"></script>

  <!-- App Settings (safe to remove) -->
  <script src="{{asset('assets/js/app-settings.js')}}"></script>

  <!-- Flatpickr -->
  <script src="{{asset('assets/vendor/flatpickr/flatpickr.min.js')}}"></script>
  <script src="{{asset('assets/js/flatpickr.js')}}"></script>

  <!-- Global Settings -->
  {{-- <script src="{{asset('assets/js/settings.js')}}"></script> --}}

  <!-- Moment.js -->
  <script src="{{asset('assets/vendor/moment.min.js')}}"></script>
  <script src="{{asset('assets/vendor/moment-range.js')}}"></script>

  <!-- Chart.js -->
  <script src="{{asset('assets/vendor/Chart.min.js')}}"></script>

  <!-- App Charts JS -->
  <script src="{{asset('assets/js/charts.js')}}"></script>
  <script src="{{asset('assets/js/chartjs-rounded-bar.js')}}"></script>

  <!-- Chart Samples -->
  <script src="{{asset('assets/js/page.dashboard.js')}}"></script>
  <script src="{{asset('assets/js/progress-charts.js')}}"></script>

  <!-- Vector Maps -->
  <script src="{{asset('assets/vendor/jqvmap/jquery.vmap.min.js')}}"></script>
  <script src="{{asset('assets/vendor/jqvmap/maps/jquery.vmap.world.js')}}"></script>
  <script src="{{asset('assets/js/vector-maps.js')}}"></script>

</body>

</html>