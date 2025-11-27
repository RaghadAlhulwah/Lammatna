<?php 
require_once 'config.php';
if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$user = getLoggedUser($pdo);
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

// Handle join by code
if (isset($_GET['joincode'])) {
    $joinCode = strtoupper(trim($_GET['joincode']));
    $stmt = $pdo->prepare("SELECT * FROM Gathering WHERE joinCode = ?");
    $stmt->execute([$joinCode]);
    $gathering = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($gathering) {
        // Check if already participant
        $stmt = $pdo->prepare("SELECT * FROM Participant WHERE UserID = ? AND GatheringID = ?");
        $stmt->execute([$_SESSION['user_id'], $gathering['GatheringID']]);
        
        if (!$stmt->fetch()) {
            $stmt = $pdo->prepare("INSERT INTO Participant (UserID, GatheringID, status) VALUES (?, ?, 1)");
            $stmt->execute([$_SESSION['user_id'], $gathering['GatheringID']]);
            $_SESSION['success'] = "تم الانضمام للفعالية بنجاح";
        }
        header("Location: gatherings.php");
        exit;
    }
}

// Build query for gatherings
$query = "SELECT g.*, u.Name as admin_name 
          FROM Gathering g 
          JOIN user u ON g.adminID = u.UserID 
          WHERE g.GatheringID IN (
              SELECT GatheringID FROM Participant WHERE UserID = ?
              UNION 
              SELECT GatheringID FROM Gathering WHERE adminID = ?
          )";
$params = [$_SESSION['user_id'], $_SESSION['user_id']];

if (!empty($search)) {
    $query .= " AND (g.name LIKE ? OR g.location LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

if (!empty($category)) {
    $query .= " AND g.category = ?";
    $params[] = $category;
}

if (!empty($from)) {
    $query .= " AND g.date >= ?";
    $params[] = $from;
}

if (!empty($to)) {
    $query .= " AND g.date <= ?";
    $params[] = $to . ' 23:59:59';
}

$query .= " ORDER BY g.date ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$gatherings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>فعالياتي — لمتنا</title>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="style.css">
  <style>
    /* Success notification */
    .success-alert {
      background: linear-gradient(135deg, var(--success), #20c997);
      color: white;
      padding: 1rem 1.5rem;
      border-radius: 12px;
      margin: 1rem 0;
      display: flex;
      align-items: center;
      gap: 1rem;
      box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
      animation: slideDown 0.5s ease;
      font-weight: 600;
    }
    
    .success-alert i {
      font-size: 1.5rem;
    }
    
    @keyframes slideDown {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    /* Calendar Modal */
    .calendar-modal {
      display: none;
      position: fixed;
      z-index: 9999;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.6);
      animation: fadeIn 0.3s ease;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }
    
    .calendar-modal-content {
      background: white;
      margin: 15% auto;
      padding: 2.5rem;
      border-radius: 20px;
      width: 90%;
      max-width: 500px;
      position: relative;
      animation: slideUp 0.3s ease;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
      border: 3px solid var(--primary);
    }
    
    @keyframes slideUp {
      from { 
        opacity: 0;
        transform: translateY(50px);
      }
      to { 
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .modal-close {
      position: absolute;
      top: 1rem;
      left: 1rem;
      font-size: 2rem;
      font-weight: bold;
      color: var(--text-light);
      cursor: pointer;
      background: none;
      border: none;
      transition: all 0.3s ease;
      line-height: 1;
      width: 35px;
      height: 35px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .modal-close:hover {
      color: var(--primary);
      transform: rotate(90deg);
    }
    
    .calendar-modal-content h3 {
      color: var(--primary);
      margin-bottom: 0.5rem;
      font-size: 1.4rem;
      text-align: center;
    }
    
    .calendar-modal-content h3 i {
      margin-left: 0.5rem;
    }
    
    .calendar-modal-content p {
      text-align: center;
      color: var(--text-light);
      margin-bottom: 2rem;
      font-size: 0.95rem;
    }
    
    .calendar-modal-buttons {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }
    
    .calendar-modal-buttons .btn {
      width: 100%;
      font-size: 1.1rem;
      padding: 1rem;
    }
    
    .skip-modal-btn {
      background: transparent;
      color: var(--text-light);
      border: 2px solid var(--border);
      margin-top: 0.5rem;
    }
    
    .skip-modal-btn:hover {
      background: var(--light);
      color: var(--primary);
      border-color: var(--primary);
    }
    
    @media (max-width: 767px) {
      .calendar-modal-content {
        margin: 30% auto;
        padding: 2rem 1.5rem;
        width: 95%;
      }
    }
  </style>
</head>

<body>
  <header class="navbar">
    <div class="logo-container">
      <a href="index.php" class="logo-link"><img src="../Lammatna.png" alt="لمتنا" class="logo"></a>
      <a href="index.php" class="site-title-link"><h1 class="site-title">لمتنا</h1></a>
    </div>
    <nav class="nav-links">
      <a href="profile.php">حسابي</a>
      <a href="logout.php">تسجيل الخروج</a>
    </nav>
  </header>

  <main class="container">
      <!-- Success Notification -->
      <?php if (isset($_SESSION['success'])): ?>
      <div class="success-alert" id="successAlert">
          <i class="fas fa-check-circle"></i>
          <span><?php echo escapeHtml($_SESSION['success']); ?></span>
      </div>
      <?php unset($_SESSION['success']); ?>
      <?php endif; ?>

      <br>
      
    <!-- Join by Code Section -->
    <div class="form-panel">
      <h3>انضم إلى فعالية</h3>
      <form method="GET" style="display: flex; gap: 1rem; align-items: center;">
        <input type="text" name="joincode" placeholder="أدخل رمز الفعالية" style="flex: 1;" 
               value="<?php echo escapeHtml($_GET['joincode'] ?? ''); ?>">
        <button type="submit" class="btn success">
          <i class="fas fa-sign-in-alt"></i> انضم
        </button>      
      </form>
    </div>
    
    <a href="create.php" class="btn" style="color: white;">
      <i class="fas fa-plus"></i> فعالية جديدة
    </a>
    <br><br>

    <!-- Filter Section -->
    <div class="filter-section">
      <form method="GET" style="display: contents;">
        <input type="text" name="search" placeholder="🔍 ابحث في الفعاليات..." style="flex: 2;"
               value="<?php echo escapeHtml($search); ?>">
        <select name="category">
          <option value="">جميع الفئات</option>
          <option value="اجتماع عائلي" <?php echo $category === 'اجتماع عائلي' ? 'selected' : ''; ?>>اجتماع عائلي</option>
          <option value="حفلة تخرج" <?php echo $category === 'حفلة تخرج' ? 'selected' : ''; ?>>حفلة تخرج</option>
          <option value="اجتماع اصدقاء" <?php echo $category === 'اجتماع اصدقاء' ? 'selected' : ''; ?>>اجتماع اصدقاء</option>
          <option value="كشتة" <?php echo $category === 'كشتة' ? 'selected' : ''; ?>>كشتة</option>
          <option value="اخرى" <?php echo $category === 'اخرى' ? 'selected' : ''; ?>>أخرى</option>
        </select>
        <input type="date" name="from" placeholder="من تاريخ" value="<?php echo escapeHtml($from); ?>">
        <input type="date" name="to" placeholder="إلى تاريخ" value="<?php echo escapeHtml($to); ?>">
        <button type="submit" class="btn">بحث</button>
        <a href="gatherings.php" class="btn outline">مسح الفلتر</a>
      </form>
    </div>

    <!-- Gatherings List -->
    <div class="form-panel">
      <section class="gathering-list">
        <?php if (empty($gatherings)): ?>
          <p class="muted">لا توجد فعاليات مطابقة.</p>
        <?php else: ?>
          <?php foreach ($gatherings as $gathering): ?>
            <div class="gathering-card">
              <div class="meta">
                <h3><?php echo escapeHtml($gathering['name']); ?></h3>
                <p>الفئة: <?php echo escapeHtml($gathering['category']); ?> — التاريخ: <?php echo date('Y-m-d H:i', strtotime($gathering['date'] . ' ' . $gathering['time'])); ?></p>
                <p>الموقع: <?php echo escapeHtml($gathering['location']); ?></p>
                <p class="muted">المنشئ: <?php echo escapeHtml($gathering['admin_name']); ?> — رمز الانضمام: <strong><?php echo escapeHtml($gathering['joinCode']); ?></strong></p>
                <div class="share-link">
                  <strong>رابط المشاركة:</strong><br>
                  <small><?php echo escapeHtml("http://$_SERVER[HTTP_HOST]/gatherings.php?joincode=" . $gathering['joinCode']); ?></small>
                  <button class="btn outline copy-link" data-link="<?php echo escapeHtml("http://$_SERVER[HTTP_HOST]/gatherings.php?joincode=" . $gathering['joinCode']); ?>" style="margin-top: 5px; padding: 5px 10px;">
                    <i class="fas fa-copy"></i> نسخ الرابط
                  </button>
                </div>
              </div>
              <div class="actions">
                <a href="gathering.php?id=<?php echo $gathering['GatheringID']; ?>" class="btn">عرض</a>
                <?php if ($gathering['adminID'] == $_SESSION['user_id']): ?>
                  <a href="create.php?edit=<?php echo $gathering['GatheringID']; ?>" class="btn outline">تعديل</a>
                  <a href="delete_gathering.php?id=<?php echo $gathering['GatheringID']; ?>" class="btn outline" onclick="return confirm('هل أنت متأكد من حذف هذه الفعالية؟')">حذف</a>
                <?php else: ?>
                  <a href="leave_gathering.php?id=<?php echo $gathering['GatheringID']; ?>" class="btn outline" onclick="return confirm('هل تريد الخروج من الفعالية؟')">الخروج</a>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </section>
    </div>
  </main>

  <!-- Calendar Modal -->
  <?php if (isset($_SESSION['google_calendar_link'])): ?>
  <div id="calendarModal" class="calendar-modal" style="display: block;">
    <div class="calendar-modal-content">
      <button class="modal-close" onclick="closeCalendarModal()">&times;</button>
      <h3><i class="fas fa-calendar-plus"></i> أضف الفعالية للتقويم</h3>
      <p>احفظ موعد الفعالية في تقويم Google</p>
      <div class="calendar-modal-buttons">
        <a href="<?php echo $_SESSION['google_calendar_link']; ?>" 
           target="_blank" 
           class="btn"
           onclick="closeCalendarModal()">
          <i class="fab fa-google"></i>
          إضافة لتقويم Google
        </a>
        <button class="btn outline skip-modal-btn" onclick="closeCalendarModal()">
          <i class="fas fa-times"></i>
          تخطي
        </button>
      </div>
    </div>
  </div>
  <?php unset($_SESSION['google_calendar_link']); ?>
  <?php endif; ?>

  <footer class="footer">
    <p>© 2025 لمتنا</p>
  </footer>

  <script>
    // Copy link functionality
    document.querySelectorAll('.copy-link').forEach(btn => {
      btn.addEventListener('click', () => {
        const link = btn.dataset.link;
        navigator.clipboard.writeText(link).then(() => {
          alert('تم نسخ رابط المشاركة إلى الحافظة!');
        });
      });
    });

    // Close calendar modal
    function closeCalendarModal() {
      const modal = document.getElementById('calendarModal');
      if (modal) {
        modal.style.animation = 'fadeOut 0.3s ease';
        setTimeout(() => modal.remove(), 300);
      }
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
      const modal = document.getElementById('calendarModal');
      if (event.target == modal) {
        closeCalendarModal();
      }
    }

    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape') {
        closeCalendarModal();
      }
    });

    // Auto-hide success alert after 5 seconds
    const successAlert = document.getElementById('successAlert');
    if (successAlert) {
      setTimeout(function() {
        successAlert.style.animation = 'slideUp 0.3s ease reverse';
        setTimeout(() => successAlert.remove(), 300);
      }, 5000);
    }
  </script>
</body>
</html>