<div class="sidebar" data-background-color="dark">
  <div class="sidebar-logo">
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
  <div class="sidebar-wrapper scrollbar scrollbar-inner">
    <div class="sidebar-content">
      <ul class="nav nav-secondary">
        <li class="nav-item active">
          <a data-bs-toggle="collapse" href="#dashboard" class="collapsed" aria-expanded="false">
            <i class="fas fa-home"></i>
            <p>Dashboard</p>
            <span class="caret"></span>
          </a>
          <div class="collapse show" id="dashboard">
            <ul class="nav nav-collapse">
              <li class="active"><a href="{{ url('admin') }}"><span class="sub-item">Dashboard 1</span></a></li>
            </ul>
          </div>
        </li>
        <li class="nav-section">
          <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
          <h4 class="text-section">Components</h4>
        </li>
         <li class="nav-item">
          <a data-bs-toggle="collapse" href="#tutorials">
            <i class="fas fa-book"></i>
            <p>Tutorials</p>
            <span class="caret"></span>
          </a>
          <div class="collapse" id="tutorials">
            <ul class="nav nav-collapse">
              <li><a href="{{ route('admin.tutos.index') }}"><span class="sub-item">List Tutorials</span></a></li>
              <li><a href="{{ route('admin.tutos.create') }}"><span class="sub-item">Create Tutorial</span></a></li>
            </ul>
          </div>
        </li>
                <li class="nav-item">
          <a data-bs-toggle="collapse" href="#quizzes">
            <i class="fas fa-question-circle"></i>
            <p>Quizzes</p>
            <span class="caret"></span>
          </a>
          <div class="collapse" id="quizzes">
            <ul class="nav nav-collapse">
              <li><a href="{{ route('admin.quizzes.index') }}"><span class="sub-item">List Quizzes</span></a></li>
              <li><a href="{{ route('admin.quizzes.create') }}"><span class="sub-item">Create Quiz</span></a></li>
            </ul>
          </div>
        </li>
        <li class="nav-item">
          <a data-bs-toggle="collapse" href="#base">
            <i class="fas fa-layer-group"></i>
            <p>Base</p>
            <span class="caret"></span>
          </a>
          <div class="collapse" id="base">
            <ul class="nav nav-collapse">
              <li><a href="{{ url('admin/components/avatars') }}"><span class="sub-item">Avatars</span></a></li>
              <li><a href="{{ url('admin/components/buttons') }}"><span class="sub-item">Buttons</span></a></li>
              <li><a href="{{ url('admin/components/gridsystem') }}"><span class="sub-item">Grid System</span></a></li>
              <li><a href="{{ url('admin/components/panels') }}"><span class="sub-item">Panels</span></a></li>
              <li><a href="{{ url('admin/components/notifications') }}"><span class="sub-item">Notifications</span></a></li>
              <li><a href="{{ url('admin/components/sweetalert') }}"><span class="sub-item">Sweet Alert</span></a></li>
              <li><a href="{{ url('admin/components/font-awesome-icons') }}"><span class="sub-item">Font Awesome Icons</span></a></li>
              <li><a href="{{ url('admin/components/simple-line-icons') }}"><span class="sub-item">Simple Line Icons</span></a></li>
              <li><a href="{{ url('admin/components/typography') }}"><span class="sub-item">Typography</span></a></li>
            </ul>
          </div>
        </li>
        <li class="nav-item">
          <a data-bs-toggle="collapse" href="#sidebarLayouts">
            <i class="fas fa-th-list"></i>
            <p>Sidebar Layouts</p>
            <span class="caret"></span>
          </a>
          <div class="collapse" id="sidebarLayouts">
            <ul class="nav nav-collapse">
              <li><a href="#"><span class="sub-item">Sidebar Style 2</span></a></li>
              <li><a href="#"><span class="sub-item">Icon Menu</span></a></li>
            </ul>
          </div>
        </li>
        <li class="nav-item">
          <a data-bs-toggle="collapse" href="#forms">
            <i class="fas fa-pen-square"></i>
            <p>Forms</p>
            <span class="caret"></span>
          </a>
          <div class="collapse" id="forms">
            <ul class="nav nav-collapse">
              <li><a href="{{ url('admin/forms/forms') }}"><span class="sub-item">Basic Form</span></a></li>
            </ul>
          </div>
        </li>
          <li class="nav-item">
          <a data-bs-toggle="collapse" href="#tuttos">
            <i class="fas fa-tutto"></i>
            <p>tutto</p>
            <span class="caret"></span>
          </a>
          <div class="collapse" id="tuttos">
           <ul class="nav nav-collapse">
    <li><a href="{{ route('admin.tutos.index') }}"><span class="sub-item">List Tutorials</span></a></li>
    <li><a href="{{ route('admin.tutos.create') }}"><span class="sub-item">Create Tutorial</span></a></li>
</ul>

          </div>
        </li>
        <li class="nav-item">
          <a data-bs-toggle="collapse" href="#tables">
            <i class="fas fa-table"></i>
            <p>Tables</p>
            <span class="caret"></span>
          </a>
          <div class="collapse" id="tables">
            <ul class="nav nav-collapse">
              <li><a href="{{ url('admin/tables/tables') }}"><span class="sub-item">Basic Table</span></a></li>
              <li><a href="{{ url('admin/tables/datatables') }}"><span class="sub-item">Datatables</span></a></li>
            </ul>
          </div>
        </li>
        <li class="nav-item">
          <a data-bs-toggle="collapse" href="#maps">
            <i class="fas fa-map-marker-alt"></i>
            <p>Maps</p>
            <span class="caret"></span>
          </a>
          <div class="collapse" id="maps">
            <ul class="nav nav-collapse">
              <li><a href="{{ url('admin/maps/googlemaps') }}"><span class="sub-item">Google Maps</span></a></li>
              <li><a href="{{ url('admin/maps/jsvectormap') }}"><span class="sub-item">Jsvectormap</span></a></li>
            </ul>
          </div>
        </li>
        <li class="nav-item">
          <a data-bs-toggle="collapse" href="#charts">
            <i class="far fa-chart-bar"></i>
            <p>Charts</p>
            <span class="caret"></span>
          </a>
          <div class="collapse" id="charts">
            <ul class="nav nav-collapse">
              <li><a href="{{ url('admin/charts/charts') }}"><span class="sub-item">Chart Js</span></a></li>
              <li><a href="{{ url('admin/charts/sparkline') }}"><span class="sub-item">Sparkline</span></a></li>
            </ul>
          </div>
        </li>
        <li class="nav-item">
          <a data-bs-toggle="collapse" href="#events">
            <i class="fas fa-calendar-alt"></i>
            <p>Events</p>
            <span class="caret"></span>
          </a>
          <div class="collapse" id="events">
            <ul class="nav nav-collapse">
              <li><a href="{{ url('admin/events') }}"><span class="sub-item">Events</span></a></li>
              <li><a href="{{ url('admin/events/drop') }}"><span class="sub-item">Drop Event</span></a></li>
              <li><a href="{{ url('admin/events/feedback') }}"><span class="sub-item">Feedback</span></a></li>
            </ul>
          </div>
        </li>
        <li class="nav-item">
          <a href="{{ url('admin/widgets') }}">
            <i class="fas fa-desktop"></i>
            <p>Widgets</p>
            <span class="badge badge-success">4</span>
          </a>
        </li>
      </ul>
    </div>
  </div>
</div>
