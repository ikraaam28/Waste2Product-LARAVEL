<div class="main-header">
  <div class="main-header-logo">
    <div class="logo-header" data-background-color="dark">
      <a href="{{ url('admin') }}" class="logo">
        <img src="{{ asset('vendor/kaiadmin/img/kaiadmin/logo_light.svg') }}" alt="navbar brand" class="navbar-brand" height="20" />
      </a>
      <div class="nav-toggle">
        <button class="btn btn-toggle toggle-sidebar"><i class="gg-menu-right"></i></button>
        <button class="btn btn-toggle sidenav-toggler"><i class="gg-menu-left"></i></button>
      </div>
      <button class="topbar-toggler more"><i class="gg-more-vertical-alt"></i></button>
    </div>
  </div>
  <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
    <div class="container-fluid">
      <nav class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
        <div class="input-group">
          <div class="input-group-prepend">
            <button type="submit" class="btn btn-search pe-1">
              <i class="fa fa-search search-icon"></i>
            </button>
          </div>
          <input type="text" placeholder="Search ..." class="form-control" />
        </div>
      </nav>
      <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
        <li class="nav-item topbar-user dropdown hidden-caret">
          <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
            <div class="avatar-sm">
              <img src="{{ asset('vendor/kaiadmin/img/profile.jpg') }}" alt="..." class="avatar-img rounded-circle" />
            </div>
            <span class="profile-username"><span class="op-7">Hi,</span> <span class="fw-bold">Admin</span></span>
          </a>
          <ul class="dropdown-menu dropdown-user animated fadeIn">
            <div class="dropdown-user-scroll scrollbar-outer">
              <li>
                <div class="user-box">
                  <div class="avatar-lg">
                    <img src="{{ asset('vendor/kaiadmin/img/profile.jpg') }}" alt="image profile" class="avatar-img rounded" />
                  </div>
                  <div class="u-text">
                    <h4>Admin</h4>
                    <p class="text-muted">admin@example.com</p>
                    <a href="#" class="btn btn-xs btn-secondary btn-sm">View Profile</a>
                  </div>
                </div>
              </li>
            </div>
          </ul>
        </li>
      </ul>
    </div>
  </nav>
</div>

