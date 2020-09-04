<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ url('t_home') }}" class="brand-link">
      <img src="{{asset('images/penang.png')}}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
           style="opacity: .8">
      <span class="brand-text font-weight-light">HELPDESK ICT</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item has-treeview menu-open">
            <a href="#" class="nav-link active">
              <i class="nav-icon fas fa-bell"></i>
              <p>
                Dashboard
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ url('t_tambahaduan') }}" class="nav-link">
                      <i class="far fa-circle nav-icon text-light"></i>
                      <p>Tambah Aduan</p>
                    </a>
                  </li>
                <li class="nav-item">
                    <a href="{{ url('t_cariAduan') }}" class="nav-link">
                      <i class="far fa-circle nav-icon text-light"></i>
                      <p>Carian Aduan</p>
                    </a>
                  </li>
              <li class="nav-item">
                <a href="{{ url('t_aduanbaru') }}" class="nav-link">
                  <i class="nav-icon fas fa-folder-open"></i>
                  <p>Aduan Baru</p>
                </a>
              </li>
              <li class="nav-item">
              <a href="{{ url('t_aduanproses') }}" class="nav-link">
                  <i class="nav-icon fas fa-tools"></i>
                  <p>Aduan Dalam Tindakan</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ url('t_aduanselesai') }}" class="nav-link">
                  <i class="nav-icon fas fa-check"></i>
                  <p>Aduan Selesai</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ url('t_aduantolak') }}" class="nav-link">
                  <i class="nav-icon fas fa-trash"></i>
                  <p>Aduan Ditolak</p>
                </a>
              </li>
            </ul>
          </li>


          <li class="nav-item has-treeview menu-open">
            <a href="{{ url('t_home') }}" class="nav-link active">
              <i class="nav-icon fas fa-user"></i>
              <p>
                Pengguna
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ url('t_semakRekod')}}" class="nav-link">
                        <i class="nav-icon fas fa-search"></i>
                        <p>
                          Semakan Rekod Aduan
                        </p>
                      </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('t_add_pengguna')}}" class="nav-link">
                        <i class="nav-icon fas fa-user-plus"></i>
                        <p>
                          Tambah Pengguna
                        </p>
                      </a>
                    </li>
                <li class="nav-item">
                    <a href="{{ url('t_senaraipengguna')}}" class="nav-link">
                        <i class="nav-icon fas fa-users text-primary"></i>
                        <p>
                        Senarai Pengguna
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('t_listTeknikal')}}" class="nav-link">
                        <i class="nav-icon fas fa-users text-info"></i>
                        <p>
                          Senarai Teknikal Jabatan
                        </p>
                      </a>
                </li>
            </ul>
        </li>
        <li class="nav-item has-treeview menu-open">
            <a href="{{ url('home') }}" class="nav-link active">
              <i class="nav-icon far fa-list-alt"></i>
              <p>
                Tambahan
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ url('t_tambahkategori')}}" class="nav-link">
                        <i class="nav-icon fas fa-list"></i>
                        <p>
                          Tambah Kategori
                        </p>
                      </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('t_tambahsubkat')}}" class="nav-link">
                        <i class="nav-icon fas fa-ellipsis-v"></i>
                        <p>
                          Tambah Subkategori
                        </p>
                      </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('t_tambahmodel')}}" class="nav-link">
                        <i class="nav-icon fas fa-keyboard"></i>
                        <p>
                          Tambah Model
                        </p>
                      </a>
                </li>
            </ul>
        </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
