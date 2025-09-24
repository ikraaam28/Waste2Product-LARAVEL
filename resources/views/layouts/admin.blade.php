<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>@yield('title', 'Admin Dashboard') - Kaiadmin</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="{{ asset('vendor/kaiadmin/img/kaiadmin/favicon.ico') }}" type="image/x-icon" />

    <!-- Fonts and icons -->
    <script src="{{ asset('vendor/kaiadmin/js/plugin/webfont/webfont.min.js') }}"></script>
    <script>
      WebFont.load({
        google: { families: ["Public Sans:300,400,500,600,700"] },
        custom: {
          families: [
            "Font Awesome 5 Solid",
            "Font Awesome 5 Regular",
            "Font Awesome 5 Brands",
            "simple-line-icons",
          ],
          urls: ["{{ asset('vendor/kaiadmin/css/fonts.min.css') }}"],
        },
        active: function () {
          sessionStorage.fonts = true;
        },
      });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('vendor/kaiadmin/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('vendor/kaiadmin/css/plugins.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('vendor/kaiadmin/css/kaiadmin.min.css') }}" />

    @stack('styles')
  </head>
  <body>
    <div class="wrapper">
      @include('admin.partials.sidebar')
      <div class="main-panel">
        @include('admin.partials.header')
        @yield('content')
        @include('admin.partials.footer')
      </div>
    </div>

    <!--   Core JS Files   -->
    <script src="{{ asset('vendor/kaiadmin/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('vendor/kaiadmin/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('vendor/kaiadmin/js/core/bootstrap.min.js') }}"></script>

    <!-- jQuery Scrollbar -->
    <script src="{{ asset('vendor/kaiadmin/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>

    <!-- Chart JS -->
    <script src="{{ asset('vendor/kaiadmin/js/plugin/chart.js/chart.min.js') }}"></script>

    <!-- jQuery Sparkline -->
    <script src="{{ asset('vendor/kaiadmin/js/plugin/jquery.sparkline/jquery.sparkline.min.js') }}"></script>

    <!-- Chart Circle -->
    <script src="{{ asset('vendor/kaiadmin/js/plugin/chart-circle/circles.min.js') }}"></script>

    <!-- Datatables -->
    <script src="{{ asset('vendor/kaiadmin/js/plugin/datatables/datatables.min.js') }}"></script>

    <!-- Bootstrap Notify -->
    <script src="{{ asset('vendor/kaiadmin/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>

    <!-- jQuery Vector Maps -->
    <script src="{{ asset('vendor/kaiadmin/js/plugin/jsvectormap/jsvectormap.min.js') }}"></script>
    <script src="{{ asset('vendor/kaiadmin/js/plugin/jsvectormap/world.js') }}"></script>

    <!-- Sweet Alert -->
    <script src="{{ asset('vendor/kaiadmin/js/plugin/sweetalert/sweetalert.min.js') }}"></script>

    <!-- Kaiadmin JS -->
    <script src="{{ asset('vendor/kaiadmin/js/kaiadmin.min.js') }}"></script>

    @stack('scripts')
  </body>
  </html>


