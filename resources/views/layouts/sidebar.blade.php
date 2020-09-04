{{--------------------------------- Sidebar Penyelaras IDLEVEL= 1,7,8 -------------------------}}
@if((Auth::user()->idlevel !== 2) && (Auth::user()->idlevel !== 4))
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ url('home') }}" class="brand-link">
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
            <a href="{{ url('home') }}" class="nav-link active">
              <i class="nav-icon fas fa-bell"></i>
              <p>
                Aduan
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
               <li class="nav-item">
                    <a href="{{url('tambahaduan')}}" class="nav-link">
                      <i class="nav-icon fas fa-folder-plus"></i>
                      <p>Tambah Aduan</p>
                    </a>
                  </li>
              <li class="nav-item">
                <a href="{{url('cariAduan')}}" class="nav-link">
                  <i class="nav-icon fas fa-search"></i>
                  <p>Carian Aduan</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ url('aduanbaru') }}" class="nav-link">
                  <i class="nav-icon fas fa-folder-open"></i>
                  <p>Aduan Baru</p>
                </a>
              </li>
              <li class="nav-item">
              <a href="{{ url('aduanproses') }}" class="nav-link">
                  <i class="nav-icon fas fa-tools"></i>
                  <p>Aduan Dalam Tindakan</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ url('aduanpembekal') }}" class="nav-link">
                    <i class="nav-icon fas fa-chalkboard-teacher"></i>
                    <p>Aduan Tindakan Pembekal</p>
                  </a>
                </li>
              {{-- <li class="nav-item">
                <a href="{{ url('aduanselesai') }}" class="nav-link">
                  <i class="nav-icon fas fa-check"></i>
                  <p>Aduan Selesai</p>
                </a>
              </li> --}}
              <li class="nav-item">
                <a href="{{ url('cariSelesai') }}" class="nav-link">
                  <i class="nav-icon fas fa-check"></i>
                  <p>Carian Aduan Selesai</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ url('aduantolak') }}" class="nav-link">
                  <i class="nav-icon fas fa-trash"></i>
                  <p>Aduan Ditolak</p>
                </a>
              </li>
            </ul>
          </li>

          <li class="nav-item has-treeview menu-open">
            <a href="{{ url('home') }}" class="nav-link active">
              <i class="nav-icon fas fa-user"></i>
              <p>
                Pengguna
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">

          <li class="nav-item">
            <a href="{{ url('semakRekod')}}" class="nav-link">
                <i class="nav-icon fas fa-search"></i>
                <p>
                  Semakan Rekod Aduan
                </p>
              </a>
        </li>
          <li class="nav-item">
            <a href="{{ url('add_pengguna')}}" class="nav-link">
                <i class="nav-icon fas fa-user-plus "></i>
                <p>
                  Tambah Pengguna
                </p>
              </a>
            </li>
          <li class="nav-item">
            <a href="{{ url('aktifPengguna')}}" class="nav-link">
                <i class="nav-icon fas fa-toggle-on text-success"></i>
                <p>
                  Aktif Pengguna Baru
                </p>
              </a>
            </li>
          <li class="nav-item">
            <a href="{{ url('senaraiPengguna')}}" class="nav-link">
                <i class="nav-icon fas fa-users text-primary"></i>
                <p>
                  Senarai Pengguna
                </p>
              </a>
            </li>
            <li class="nav-item">
                <a href="{{ url('listTeknikal')}}" class="nav-link">
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
          <i class="nav-icon fas fa-user"></i>
          <p>
            Statistik
            <i class="right fas fa-angle-left"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">

        <li class="nav-item">
            <a href="{{ url('statistikTahunan')}}" class="nav-link">
                <i class="nav-icon fas fa-chart-bar"></i>
                <p>
                Statistik Tahunan
                </p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ url('statistikKategori')}}" class="nav-link">
                <i class="nav-icon fas fa-desktop"></i>
                <p>
                  Statistik Kategori
                </p>
              </a>
        </li>
        <li class="nav-item">
            <a href="{{ url('statistikJabatan')}}" class="nav-link">
                <i class="nav-icon fas fa-house-user"></i>
                <p>
                  Statistik Jabatan
                </p>
              </a>
        </li>
        <li class="nav-item">
            <a href="{{ url('statistikTechnician')}}" class="nav-link">
                <i class="nav-icon fas fa-address-book"></i>
                <p>
                  Statistik Juruteknik
                </p>
              </a>
        </li>
        <li class="nav-item">
            <a href="{{ url('statistikMaklumbalas')}}" class="nav-link">
                <i class="nav-icon far fa-comments"></i>
                <p>
                  Statistik Maklum Balas
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
                <a href="{{ url('tambahkategori')}}" class="nav-link">
                    <i class="nav-icon fas fa-list"></i>
                    <p>
                      Tambah Kategori
                    </p>
                  </a>
            </li>
            <li class="nav-item">
                <a href="{{ url('tambahsubkat')}}" class="nav-link">
                    <i class="nav-icon fas fa-ellipsis-v"></i>
                    <p>
                      Tambah Subkategori
                    </p>
                  </a>
            </li>
            <li class="nav-item">
                <a href="{{ url('tambahmodel')}}" class="nav-link">
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
{{---------------------------------Tamat Sidebar Penyelaras IDLEVEL= 1,7,8 --------------------}}


{{---------------------------------- Sidebar Juruteknik IDLEVEL= 2 ----------------------------}}
@elseif(Auth::user()->idlevel == 2)
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
                      <i class="nav-icon fas fa-folder-plus"></i>
                      <p>Tambah Aduan</p>
                    </a>
                  </li>
                <li class="nav-item">
                    <a href="{{ url('t_cariAduan') }}" class="nav-link">
                        <i class="nav-icon fas fa-search"></i>
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
              {{-- <li class="nav-item">
                <a href="{{ url('t_aduanselesai') }}" class="nav-link">
                  <i class="nav-icon fas fa-check"></i>
                  <p>Aduan Selesai</p>
                </a>
              </li> --}}
              <li class="nav-item">
                <a href="{{ url('t_cariSelesai') }}" class="nav-link">
                  <i class="nav-icon fas fa-check"></i>
                  <p>Carian Aduan Selesai</p>
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
{{-------------------------------- Tamat Sidebar Juruteknik IDLEVEL= 2 ---------------------------}}


{{------------------------------------- Sidebar Pengguna IDLEVEL= 4 ---------------------------}}
@elseif(Auth::user()->idlevel == 4)
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ url('p_home') }}" class="brand-link">
      <img src="{{asset('images/penang.png')}}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
           style="opacity: .8">
      <span class="brand-text font-weight-light">HELPDESK ICT</span>
    </a>
    <!-- Sidebar -->
    <div class="sidebar">

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

          <li class="nav-item">
            <a href="{{ url('p_tambahaduan')}}" class="nav-link">
                <i class="nav-icon fas fa-plus-square"></i>
                <p>
                  Tambah Aduan
                </p>
              </a>
            </li>
          <li class="nav-item">
            <a href="{{ url('p_home') }}" class="nav-link">
              <i class="nav-icon far fa-list-alt  "></i>
              <p>Senarai Aduan</p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
  {{---------------------------------- Tamat Sidebar Pengguna IDLEVEL= 4 -----------------------}}

  @else
  @endif
