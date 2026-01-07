<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>SIAPPLIST - Sistem Informasi Aplikasi To-Do List</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <style>
    * { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins', sans-serif;}
    html { scroll-behavior: smooth; }
    body { background:#f8fafc; color:#333; }

    /* ===== NAVBAR ===== */
    nav {
      position: fixed;
      top:0; left:0; width:100%;
      display:flex;
      justify-content:space-between;
      align-items:center;
      padding:15px 50px;
      background:white;
      box-shadow:0 2px 15px rgba(0,0,0,0.07);
      z-index:1000;
    }
    .logo { font-weight:700; font-size:1.6rem; background: linear-gradient(90deg, #2563eb, #7c3aed); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
    .nav-auth { display:flex; gap:15px; }
    .login, .signup {
      padding:8px 20px; border-radius:30px; font-weight:500; transition:all 0.3s ease; text-decoration:none;
    }
    .login { color:#2563eb; border:1px solid #2563eb; background:white; }
    .login:hover { background:#2563eb; color:white; box-shadow:0 5px 15px rgba(37,99,235,0.3);}
    .signup { background:#2563eb; color:white; border:none; }
    .signup:hover { background:white; color:#2563eb; border:1px solid #2563eb; box-shadow:0 5px 15px rgba(37,99,235,0.3); }

    /* ===== HERO ===== */
    .hero {
      height:100vh;
      background: linear-gradient(135deg, #1e3a8a, #7c3aed);
      color:white;
      display:flex;
      align-items:center;
      justify-content:center;
      text-align:center;
      position:relative;
      overflow:hidden;
      padding:0 20px;
    }
    .hero::before, .hero::after {
      content:""; position:absolute; border-radius:50%; opacity:0.3;
    }
    .hero::before { width:300px; height:300px; background:#ffffff; top:-100px; left:-100px;}
    .hero::after { width:200px; height:200px; background:#ffffff; bottom:-50px; right:-50px;}
    .hero-content { max-width:800px; opacity:0; transform:translateY(40px); transition:all 0.8s ease;}
    .hero-content.show { opacity:1; transform:translateY(0);}
    .hero h1 { font-size:3.2rem; margin-bottom:20px; line-height:1.2;}
    .hero p { font-size:1.2rem; margin-bottom:30px; color:#e0e7ff; }
    .hero .btn { padding:14px 30px; border-radius:50px; border:none; font-weight:600; cursor:pointer; background:white; color:#2563eb; transition:all 0.3s ease; text-decoration: none; display: inline-block;}
    .hero .btn:hover { transform:translateY(-3px); box-shadow:0 10px 20px rgba(255,255,255,0.4); }

    /* ===== SECTION ===== */
    section { padding:100px 20px; max-width:1200px; margin:auto; opacity:0; transform:translateY(40px); transition:all 0.8s ease; }
    section.show { opacity:1; transform:translateY(0); }
    section h2 { text-align:center; font-size:2.4rem; margin-bottom:20px; }
    section p { text-align:center; max-width:700px; margin:auto; line-height:1.6; color:#4b5563; }

    /* ===== SERVICES/FEATURES ===== */
    .cards { margin-top:50px; display:grid; grid-template-columns:repeat(auto-fit,minmax(250px,1fr)); gap:25px;}
    .card { background:white; padding:30px; border-radius:20px; box-shadow:0 8px 25px rgba(0,0,0,0.05); opacity:0; transform:translateY(40px); transition:all 0.8s ease; text-align: center;}
    .card.show { opacity:1; transform:translateY(0);}
    .card i { font-size: 2.5rem; color: #2563eb; margin-bottom: 15px; display: block; }
    .card h3 { margin-bottom:10px; color:#1e3a8a; font-weight:600;}
    .card p { color:#4b5563; font-size: 0.95rem; }

    /* ===== TEAM ===== */
    .team { margin-top:50px; display:grid; grid-template-columns:repeat(5,1fr); gap:25px; justify-items:center;}
    .member { background:white; padding:20px; border-radius:20px; box-shadow:0 8px 25px rgba(0,0,0,0.05); width:100%; max-width:220px; text-align:center; opacity:0; transform:scale(0.9); transition:all 0.8s ease; }
    .member.show { opacity:1; transform:scale(1); }
    /* Kode Baru (Perbaikan) */
.member img {
  width: 100px;       /* Set lebar tetap */
  height: 100px;      /* PENTING: Set tinggi tetap sama dengan lebar */
  object-fit: cover;  /* PENTING: Agar gambar dicrop rapi, tidak gepeng */
  border-radius: 50%; /* Membuat jadi lingkaran sempurna */
  margin-bottom: 10px;
  transition: all 0.3s ease;
  /* Opsional: Tambahkan border tipis agar lebih rapi */
  border: 3px solid #f1f5f9; 
}
    .member:hover img { transform:scale(1.1);}
    .member h4 { font-weight:600; margin-bottom:5px;}
    .member p { font-style:italic; color:#6b7280; }

    /* ===== CONTACT ===== */
    .contact { text-align:center; opacity:0; transform:translateY(40px); transition:all 0.8s ease;}
    .contact.show { opacity:1; transform:translateY(0);}
    .contact .btn { padding:14px 30px; border-radius:50px; border:none; font-weight:600; cursor:pointer; background:linear-gradient(90deg,#2563eb,#7c3aed); color:white; transition:all 0.3s ease; text-decoration: none; display: inline-block;}
    .contact .btn:hover { transform:translateY(-3px); box-shadow:0 10px 20px rgba(124,58,237,0.4); }

    footer { background:#1f2933; color:white; text-align:center; padding:25px; font-size:0.9rem; }

    @media(max-width:1024px){ .team{grid-template-columns:repeat(3,1fr);} }
    @media(max-width:600px){ nav{padding:15px 20px;} .team{grid-template-columns:1fr;} }
  </style>
</head>
<body>

  <nav>
    <div class="logo">SIAPPLIST</div>
    <div class="nav-auth">
      <a href="index.php" class="login">Login</a>
      <a href="register.php" class="signup">Daftar</a>
    </div>
  </nav>

  <div class="hero">
    <div class="hero-content">
      <h1>Kelola Tugas Akademik Jadi Lebih Mudah</h1>
      <p><strong>SIAPPLIST</strong> (Sistem Informasi Aplikasi To-Do List) membantu Dosen mendistribusikan tugas dan Mahasiswa mengatur deadline dengan metode Kanban Board yang interaktif.</p>
      <a href="index.php" class="btn">Mulai Sekarang</a>
    </div>
  </div>

  <section>
    <h2>Tentang SIAPPLIST</h2>
    <p>SIAPPLIST hadir sebagai solusi manajemen tugas di lingkungan kampus. Kami menghubungkan Dosen dan Mahasiswa dalam satu platform terintegrasi. Dosen dapat memantau progres pengerjaan tugas secara real-time, sementara Mahasiswa dapat fokus menyelesaikan tugas tepat waktu dengan visualisasi yang jelas.</p>
  </section>

  <section>
    <h2>Fitur Unggulan</h2>
    <div class="cards">
      <div class="card">
        <i class="bi bi-kanban"></i>
        <h3>Kanban Board</h3>
        <p>Visualisasi tugas Mahasiswa yang interaktif dengan sistem Drag & Drop (Pending ke Completed).</p>
      </div>
      <div class="card">
        <i class="bi bi-person-video3"></i>
        <h3>Distribusi Dosen</h3>
        <p>Dosen dapat mengirim tugas ke seluruh mahasiswa dalam satu Program Studi dengan sekali klik.</p>
      </div>
      <div class="card">
        <i class="bi bi-check2-all"></i>
        <h3>Verifikasi Manual</h3>
        <p>Kontrol penuh di tangan Dosen untuk memverifikasi dan menyetujui tugas yang telah selesai.</p>
      </div>
    </div>
  </section>

  <section>
    <h2>Tim Pengembang</h2>
    <div class="team">
      <div class="member"><img src="https://files.catbox.moe/89m0gy.jpg"><h4>Cahya</h4><p>Project Manager, Fullstack Dev & Tester</p></div>
      <div class="member"><img src="https://files.catbox.moe/e9s2bp.jpeg"><h4>Widhi</h4><p>UI/UX Designer</p></div>
      <div class="member"><img src="https://files.catbox.moe/lb1wle.jpeg"><h4>Alfredo</h4><p>Pembuat Laporan</p></div>
      <div class="member"><img src="https://files.catbox.moe/for485.jpeg"><h4>Yaku</h4><p>Pembuat Laporan</p></div>
      <div class="member"><img src="https://i.pravatar.cc/100?5"><h4>Asrida</h4><p>Pembuat Laporan</p></div>
    </div>
  </section>

  <section class="contact">
    <h2>Hubungi Kami</h2>
    <p>Punya pertanyaan seputar penggunaan SIAPPLIST? Tim kami siap membantu Anda.</p>
    <a href="https://www.idbbali.ac.id/" class="btn">Kontak Support</a>
  </section>

  <footer>
    © 2026 SIAPPLIST. Sistem Informasi Aplikasi To-Do List.
  </footer>

  <script>
    const revealElements = document.querySelectorAll(".hero-content, section, .card, .member, .contact");

    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if(entry.isIntersecting){
          entry.target.classList.add("show");
        } else {
          entry.target.classList.remove("show");
        }
      });
    }, { threshold: 0.1 });

    revealElements.forEach(el => observer.observe(el));
  </script>
</body>
</html>