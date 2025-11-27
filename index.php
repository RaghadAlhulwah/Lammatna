
<?php require_once 'config.php'; ?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>لمتنا</title>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div id="notif-area" class="notif-area" aria-live="polite">
    <?php if (isset($_GET['timeout'])): ?>
      <div class="notif">تم تسجيل الخروج تلقائياً بسبب عدم النشاط.</div>
    <?php endif; ?>
  </div>

  <header class="navbar">
    <div class="logo-container">
      <a href="index.php" class="logo-link">
        <img src="../Lammatna.png" alt="لمتنا" class="logo">
      </a>
      <a href="index.php" class="site-title-link">
        <h1 class="site-title">لمتنا</h1>
      </a>
    </div>
    <nav class="nav-links">
     
      <?php if (isLoggedIn()): ?>
        <a href="profile.php">حسابي</a>
        <a href="logout.php">تسجيل الخروج</a>
        
      <?php else: ?>
    
      <?php endif; ?>
    </nav>
  </header>

  <main class="container">
    <section class="hero">
      <div class="hero-content">
        <?php if (isLoggedIn()): ?>
          <?php $user = getLoggedUser($pdo); ?>
          <h2>مرحباً، <?php echo escapeHtml($user['Name']); ?></h2>
          <p>أَوفُوا حقّ الأُنس، وأَوكِلوا أمرَ التنظيم إلى «لَمْتَنَا»</p>
        <?php else: ?>
          <h2>نظّم لَمَّتك بسهولة</h2>
          <p>منصة متكاملة لتنظيم الفعاليات الاجتماعية</p>
        <?php endif; ?>
        <div class="actions">
          <?php if (isLoggedIn()): ?>
            <a href="create.php" class="btn">إنشاء فعالية</a>
            <a href="gatherings.php" class="btn outline">فعالياتي</a>
          <?php else: ?>
            <a href="register.php" class="btn">ابدأ الآن</a>
          <?php endif; ?>
        </div>
      </div>
      <img src="../Lammatna.png" alt="شعار لمتنا" class="hero-img">
    </section>

    <section class="features">
      <h3>مميزاتـــــــنا </h3>
      <div class="grid">
        <div class="card"><strong>إنشاء فعالية</strong><p>أضف التفاصيل وأدِر فعالياتك.</p></div>
        <div class="card"><strong>تعيين المهام</strong><p>وزع المهام وتابع الإنجاز.</p></div>
        <div class="card"><strong>الانضمام بالرمز</strong><p>انضم لأي فعالية برمز مشاركة.</p></div>
        <div class="card"><strong>تذكيرات</strong><p>اختر متى تتلقى التذكير.</p></div>
      </div>
    </section>
  </main>

  <footer class="footer">
    <p>© 2025 لمتنا</p>
  </footer>
</body>
</html>
