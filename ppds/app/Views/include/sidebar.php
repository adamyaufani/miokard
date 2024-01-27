<?php helper('data_helper') ?>
<div class="sidebar-menu">
    <div class="sidebar-header">
        <div class=" logo">
            <a href="#"><img src="<?= base_url('assets/images/icon/logomiokard.png'); ?>" alt="logo"></a>
            <!-- <a href="#">
                <h2>miokard</h2>
            </a> -->
        </div>
    </div>
    <div class="main-menu">
        <div class="menu-inner">
            <nav>
                <ul class="metismenu" id="menu">
                    <li id="dashboard">
                        <a href="<?= base_url('/'); ?>">
                            <i class="ti-dashboard"></i> <span>Dashboard</span>
                        </a>
                    </li>

                    <li id="staseBerjalan">
                        <a href="<?= base_url('admin/ppds/staseberjalan'); ?>">
                            <i class="ti-layers"></i> <span>Stase Berjalan</span>
                        </a>
                    </li>


                    <li id="tugas">
                        <a href="javascript:void(0)" aria-expanded="true"><i class="ti-book"></i>
                            <span>Tugas</span>
                        </a>
                        <ul class="collapse">
                            <li class="semua-ilmiah">
                                <a href="<?= base_url('tugas/jenis/ilmiah'); ?>" aria-expanded="true">Ilmiah</a>
                            </li>
                            <li class="tugas-besar">
                                <a href=" <?= base_url('tugas/jenis/tugas_besar'); ?>" aria-expanded="true">Tugas Besar</a>
                            </li>
                        </ul>
                    </li>

                    <li id="logbook">
                        <a href="<?= base_url('/logbook'); ?>">
                            <i class="ti-book"></i> <span>Log Book</span>
                        </a>
                    </li>

                    <li id="sidang">
                        <a href="<?= base_url('/sidang'); ?>">
                            <i class="ti-dashboard"></i> <span>Sidang</span>
                        </a>
                    </li>

                    <li id="arsip-pdps">
                        <a href="javascript:void(0)" aria-expanded="true"><i class="ti-user"></i>
                            <span>Arsip PPDS</span>
                        </a>
                        <ul class="collapse">
                            <li class="tahap-1">
                                <a href="<?= base_url('admin/ppds/tahap/1'); ?>" aria-expanded="true">Tahap 1</a>
                            </li>
                            <li class="tahap-2">
                                <a href="<?= base_url('admin/ppds/tahap/2'); ?>" aria-expanded="true">Tahap 2</a>
                            </li>
                            <li class="tahap-3">
                                <a href="<?= base_url('admin/ppds/tahap/3'); ?>" aria-expanded="true">Tahap 3</a>
                            </li>
                            <li class="tahap-4">
                                <a href="<?= base_url('admin/ppds/tahap/4'); ?>" aria-expanded="true">Tahap 4</a>
                            </li>
                        </ul>
                    </li>

                    <li id="menu_spv">
                        <a href="<?= base_url('/admin/supervisor'); ?>">
                            <i class="ti-dashboard"></i> <span>Supervisor</span>
                        </a>
                    </li>
                    <li id="menu_allppds">
                        <a href="<?= base_url('admin/allppds'); ?>" aria-expanded="true"><i class="ti-user"></i>
                                <span>Semua Residen</span>
                        </a>
                    </li>

                    <li id="menu_users">
                        <a href="javascript:void(0)" aria-expanded="true"><i class="ti-user"></i>
                            <span>Users</span>
                        </a>
                       
                        <ul class="collapse">
                            <li class="daftar_users">
                                <a href="<?= base_url('admin/users'); ?>" aria-expanded="true">Daftar Users</a>
                            </li>
                            <!-- <li class="lobby_ppds">
                                <a href="<?= base_url('admin/ppds/lobby'); ?>" aria-expanded="true">
                                    Lobby PPDS
                                    <?php if (countPpdsInLobby() > 0) { ?>
                                        <span class="badge badge-pill badge-danger">
                                            <?= countPpdsInLobby(); ?>
                                        </span>
                                    <?php } ?>
                                </a>
                            </li> -->
                            <!-- <li>
                                <a href="#" aria-expanded="true">Tambah Pengguna</a>
                            </li> -->
                        </ul>
                    </li>
                   
                    <li id="menu_setting">
                        <a href="javascript:void(0)" aria-expanded="true"><i class="ti-user"></i>
                            <span>Setting</span>
                        </a>
                        <ul class="collapse">
                            <li class="setting_stase">
                                <a href="<?= base_url('admin/stase'); ?>" aria-expanded="true">Stase</a>
                            </li>
                            
                            <!-- <li>
                                <a href="#" aria-expanded="true">Tambah Pengguna</a>
                            </li> -->
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>