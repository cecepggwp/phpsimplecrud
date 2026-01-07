
<aside class="app-sidebar bg-body-secondary shadow" >
    <div class="sidebar-brand">
        <a href="home.php" class="brand-link">
            <img src="assets/img/logo.png" alt="AdminLTE Logo" class="brand-image" />
            <span class="brand-text">SIAPPLIST</span>
        </a>
    </div>

    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation" aria-label="Main navigation" data-accordion="false" id="navigation">
                <li class="nav-header">APLIKASI</li>
                <li class="nav-item">
                    <a href="home.php" class="nav-link">
                        <i class="nav-icon bi bi-house-door-fill"></i>
                        <p>Beranda</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="data-input.php" class="nav-link">
                        <i class="nav-icon bi bi-clipboard-data-fill"></i>
                        <p>Input Tugas Baru</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="data-list.php" class="nav-link">
                        <i class="nav-icon bi bi-card-list"></i>
                        <p>Lihat Daftar Tugas Kamu</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="calendar.php" class="nav-link">
                        <i class="nav-icon bi bi-calendar-check-fill"></i>
                        <p>Kalender Akademik</p>
                    </a>
                </li>
                <!--
                <li class="nav-item">
                    <a href="data-search.php" class="nav-link">
                        <i class="nav-icon bi bi-search-heart-fill"></i>
                        <p>Cari Tugas Kamu</p>
                    </a>
                </li> -->
                <?php 
                if($_SESSION["role"] == "1"){
                ?>

                <li class="nav-header">MONITORING</li>
                <li class="nav-item">
                    <a href="admin-monitoring.php" class="nav-link">
                        <i class="nav-icon bi bi-eye-fill"></i>
                        <p>Monitoring Semua Tugas</p>
                    </a>
                </li>

                <li class="nav-header">MASTER DATA</li>
                <li class="nav-item">
                    <a href="master-kategori-list.php" class="nav-link">
                        <i class="nav-icon bi bi-people-fill"></i>
                        <p>Kategori Tugas </p>
                    </a>
                </li>
                 <?php } ?>

                <?php 
                if($_SESSION["role"] == "1"){
                ?>
                <li class="nav-item">
                    <a href="master-prodi-list.php" class="nav-link">
                        <i class="nav-icon bi bi-people-fill"></i>
                        <p>Data Prodi</p>
                    </a>
                </li>
                 <?php } ?>
                
                 <?php 
                if($_SESSION["role"] == "1"){
                ?>
                <li class="nav-item">
                    <a href="master-mk-list.php" class="nav-link">
                        <i class="nav-icon bi bi-people-fill"></i>
                        <p>Data Mata Kuliah</p>
                    </a>
                </li>
                 <?php } ?>

                 <?php 
                if($_SESSION["role"] == "1"){
                ?>
                <li class="nav-item">
                    <a href="master-user-list.php" class="nav-link">
                        <i class="nav-icon bi bi-people-fill"></i>
                        <p>Data User</p>
                    </a>
                </li>
                 <?php } ?>
                <!-- <li class="nav-item">
                    <a href="master-provinsi-list.php" class="nav-link">
                        <i class="nav-icon bi bi-briefcase-fill"></i>
                        <p>Tugas</p>
                    </a>
                </li> -->
            </ul>
        </nav>
    </div> 

</aside>