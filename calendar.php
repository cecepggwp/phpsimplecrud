<?php
session_start();
if(!isset($_SESSION["id"])){
	header("Location: index.php");
    exit();
}
?>
<!doctype html>
<html lang="en">
	<head>
		<?php include 'template/header.php'; ?>
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
        <style>
            /* Sedikit styling agar kalender terlihat rapi */
            #calendar {
                max-width: 1100px;
                margin: 0 auto;
                padding: 20px;
                background: white;
                border-radius: 8px;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
            }
            .fc-event {
                cursor: pointer; /* Ubah kursor jadi telunjuk saat hover event */
            }
            .fc-day-today {
                background-color: #e8f4ff !important; /* Highlight hari ini */
            }
        </style>
	</head>

	<body class="layout-fixed fixed-header fixed-footer sidebar-expand-lg sidebar-open bg-body-tertiary">
		<div class="app-wrapper">
			<?php include 'template/navbar.php'; ?>
			<?php include 'template/sidebar.php'; ?>

			<main class="app-main">
				<div class="app-content-header">
					<div class="container-fluid">
						<div class="row">
							<div class="col-sm-6"><h3 class="mb-0">Kalender Akademik</h3></div>
                            <div class="col-sm-6">
								<ol class="breadcrumb float-sm-end">
									<li class="breadcrumb-item"><a href="home.php">Beranda</a></li>
									<li class="breadcrumb-item active" aria-current="page">Kalender</li>
								</ol>
							</div>
						</div>
					</div>
				</div>

				<div class="app-content">
					<div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card card-outline card-primary">
                                    <div class="card-body">
                                        <div class="mb-3 text-center">
                                            <span class="badge bg-danger me-2">🔴 Belum Selesai</span>
                                            <span class="badge bg-success me-2">🟢 Selesai</span>
                                            <?php if($_SESSION['role'] == '2') echo '<span class="badge bg-primary">🔵 Tugas Dosen</span>'; ?>
                                        </div>
                                        
                                        <div id='calendar'></div>
                                    </div>
                                </div>
                            </div>
                        </div>
					</div>
				</div>
			</main>
			<?php include 'template/footer.php'; ?>
		</div>
		<?php include 'template/script.php'; ?>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var calendarEl = document.getElementById('calendar');
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth', // Tampilan Bulanan
                    locale: 'id', // Bahasa Indonesia
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,listMonth'
                    },
                    buttonText: {
                        today: 'Hari Ini',
                        month: 'Bulan',
                        week: 'Minggu',
                        list: 'List Agenda'
                    },
                    events: 'proses/api-calendar.php', // Mengambil data JSON dari file API
                    eventClick: function(info) {
                        // Fitur tambahan: Saat diklik, bisa ada aksi lain jika mau
                        // Defaultnya akan membuka URL yang ada di JSON
                    }
                });
                calendar.render();
            });
        </script>
	</body>
</html>