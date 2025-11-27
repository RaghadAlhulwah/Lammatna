<?php 
require_once 'config.php';
if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$gathering_id = $_GET['id'] ?? null;
if (!$gathering_id) {
    header("Location: gatherings.php");
    exit;
}

// Get gathering details
$stmt = $pdo->prepare("SELECT g.*, u.Name as admin_name 
                      FROM Gathering g 
                      JOIN user u ON g.adminID = u.UserID 
                      WHERE g.GatheringID = ?");
$stmt->execute([$gathering_id]);
$gathering = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$gathering) {
    header("Location: gatherings.php");
    exit;
}

// Check if user is participant or admin
$stmt = $pdo->prepare("SELECT * FROM Participant WHERE UserID = ? AND GatheringID = ?");
$stmt->execute([$_SESSION['user_id'], $gathering_id]);
$is_participant = $stmt->fetch();

$is_admin = ($gathering['adminID'] == $_SESSION['user_id']);

if (!$is_participant && !$is_admin) {
    header("Location: gatherings.php");
    exit;
}

// Get participants
$stmt = $pdo->prepare("SELECT u.UserID, u.Name, u.Email 
                      FROM Participant p 
                      JOIN user u ON p.UserID = u.UserID 
                      WHERE p.GatheringID = ?");
$stmt->execute([$gathering_id]);
$participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get tasks
$stmt = $pdo->prepare("SELECT t.*, u.Name as assigned_name 
                      FROM Task t 
                      LEFT JOIN user u ON t.UserID = u.UserID 
                      WHERE t.GatheringID = ?");
$stmt->execute([$gathering_id]);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle task operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_task'])) {
        $description = trim($_POST['description']);
        $note = trim($_POST['note']);
        $type = $_POST['type'];
        $assigned_to = !empty($_POST['assigned_to']) ? $_POST['assigned_to'] : null;
        
        if (!empty($description)) {
            // Get the next available TaskID
            $stmt = $pdo->query("SELECT COALESCE(MAX(TaskID), 0) + 1 as next_id FROM Task");
            $nextTaskId = $stmt->fetch(PDO::FETCH_ASSOC)['next_id'];
            
            // Always include UserID, but set to NULL if not assigned
            $stmt = $pdo->prepare("INSERT INTO Task (TaskID, Description, note, type, status, UserID, GatheringID) VALUES (?, ?, ?, ?, 'pending', ?, ?)");
            $stmt->execute([$nextTaskId, $description, $note, $type, $assigned_to, $gathering_id]);
            
            $_SESSION['success'] = "تم إضافة " . ($type === 'item' ? 'الغرض' : 'المهمة') . " بنجاح";
            header("Location: gathering.php?id=$gathering_id");
            exit;
        }
    }
    
    if (isset($_POST['edit_task'])) {
        $task_id = $_POST['task_id'];
        $description = trim($_POST['description']);
        $note = trim($_POST['note']);
        $type = $_POST['type'];
        $assigned_to = !empty($_POST['assigned_to']) ? $_POST['assigned_to'] : null;
        
        if (!empty($description)) {
            $stmt = $pdo->prepare("UPDATE Task SET Description = ?, note = ?, type = ?, UserID = ? WHERE TaskID = ? AND GatheringID = ?");
            $stmt->execute([$description, $note, $type, $assigned_to, $task_id, $gathering_id]);
            
            $_SESSION['success'] = "تم تعديل " . ($type === 'item' ? 'الغرض' : 'المهمة') . " بنجاح";
            header("Location: gathering.php?id=$gathering_id");
            exit;
        }
    }
    
    if (isset($_POST['toggle_task'])) {
        $task_id = $_POST['task_id'];
        $stmt = $pdo->prepare("UPDATE Task SET status = IF(status = 'pending', 'completed', 'pending') WHERE TaskID = ? AND GatheringID = ?");
        $stmt->execute([$task_id, $gathering_id]);
        header("Location: gathering.php?id=$gathering_id");
        exit;
    }
    
    if (isset($_POST['delete_task'])) {
        $task_id = $_POST['task_id'];
        $stmt = $pdo->prepare("DELETE FROM Task WHERE TaskID = ? AND GatheringID = ?");
        $stmt->execute([$task_id, $gathering_id]);
        header("Location: gathering.php?id=$gathering_id");
        exit;
    }
}
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?php echo escapeHtml($gathering['name']); ?> — لمتنا</title>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="style.css">
  <style>
    /* Tabs styling */
    .tabs-container {
      margin-top: 2rem;
    }
    
    .tabs-header {
      display: flex;
      gap: 0.5rem;
      border-bottom: 2px solid var(--border);
      margin-bottom: 1.5rem;
    }
    
    .tab-btn {
      background: transparent;
      border: none;
      padding: 1rem 1.5rem;
      font-size: 1rem;
      font-weight: 700;
      color: var(--text-light);
      cursor: pointer;
      transition: all 0.3s ease;
      border-bottom: 3px solid transparent;
      position: relative;
      font-family: 'Tajawal', sans-serif;
    }
    
    .tab-btn:hover {
      color: var(--primary);
      background: rgba(139, 69, 19, 0.05);
    }
    
    .tab-btn.active {
      color: var(--primary);
      border-bottom-color: var(--primary);
    }
    
    .tab-btn i {
      margin-left: 0.5rem;
    }
    
    .tab-content {
      display: none;
      animation: fadeIn 0.3s ease;
    }
    
    .tab-content.active {
      display: block;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    /* Edit form modal */
    .edit-modal {
      display: none;
      position: fixed;
      z-index: 9999;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.6);
      animation: fadeIn 0.3s ease;
      overflow-y: auto;
    }
    
    .edit-modal.active {
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem 1rem;
    }
    
    .edit-modal-content {
      background: white;
      padding: 2rem;
      border-radius: 20px;
      width: 100%;
      max-width: 500px;
      position: relative;
      animation: slideUp 0.3s ease;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
      border: 3px solid var(--primary);
    }
    
    @keyframes slideUp {
      from { opacity: 0; transform: translateY(50px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .modal-close-btn {
      position: absolute;
      top: 1rem;
      left: 1rem;
      background: transparent;
      border: none;
      font-size: 2rem;
      cursor: pointer;
      color: var(--text-light);
      transition: all 0.3s ease;
      line-height: 1;
      width: 35px;
      height: 35px;
    }
    
    .modal-close-btn:hover {
      color: var(--primary);
      transform: rotate(90deg);
    }
    
    .edit-modal-content h3 {
      color: var(--primary);
      margin-bottom: 1.5rem;
      font-size: 1.3rem;
    }
    
    .edit-modal-content h3 i {
      margin-left: 0.5rem;
    }
    
    /* Success alert */
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
    
    @media (max-width: 767px) {
      .tab-btn {
        padding: 0.8rem 1rem;
        font-size: 0.9rem;
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
      <a href="gatherings.php">فعالياتي</a>
      <a href="profile.php">حسابي</a>
      <a href="logout.php">تسجيل الخروج</a>
    </nav>
  </header>

  <main class="container">
    <?php if (isset($_SESSION['success'])): ?>
    <div class="success-alert" id="successAlert">
        <i class="fas fa-check-circle"></i>
        <span><?php echo escapeHtml($_SESSION['success']); ?></span>
    </div>
    <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <section class="panel">
      <div class="top-row">
        <h2><?php echo escapeHtml($gathering['name']); ?></h2>
        <div>
          <?php if ($is_admin): ?>
            <a href="create.php?edit=<?php echo $gathering_id; ?>" class="btn">تعديل</a>
            <a href="delete_gathering.php?id=<?php echo $gathering_id; ?>" class="btn outline" onclick="return confirm('هل تريد حذف الفعالية نهائياً؟')">حذف</a>
          <?php endif; ?>
          <?php if ($is_participant && !$is_admin): ?>
            <a href="leave_gathering.php?id=<?php echo $gathering_id; ?>" class="btn outline" onclick="return confirm('هل تريد الخروج من الفعالية؟')">الخروج من الفعالية</a>
          <?php endif; ?>
        </div>
      </div>

      <div class="card" style="margin-top:10px">
        <p><strong>الفئة:</strong> <?php echo escapeHtml($gathering['category']); ?></p>
        <?php
        // Generate Google Calendar link
        $eventTitle = urlencode($gathering['name']);
        $eventDetails = urlencode("حي هلا،\n\nهذا تذكير بالفعالية القادمة:\n📌 الفعالية: " . $gathering['name'] . "\n📅 التاريخ: " . $gathering['date'] . "\n📍 الموقع: " . $gathering['location'] . "\n\nأحبابك ينتظرونك بالفعالية!\n\n");
        $eventLocation = urlencode($gathering['location']);

        $startDateTime = date("Ymd\THis", strtotime($gathering['date'] . ' ' . $gathering['time']));
        $endDateTime = date("Ymd\THis", strtotime($gathering['date'] . ' ' . $gathering['time'] . ' +2 hours'));

        $googleCalendarUrl = "https://calendar.google.com/calendar/render?action=TEMPLATE"
            . "&text=$eventTitle"
            . "&dates={$startDateTime}/{$endDateTime}"
            . "&details=$eventDetails"
            . "&location=$eventLocation";
        ?>

        <p>
            <strong>التاريخ والوقت:</strong> 
            <a href="<?php echo $googleCalendarUrl; ?>" target="_blank" class="date-calendar-link">
                <i class="fas fa-calendar-plus"></i>
                <?php echo date('Y-m-d H:i', strtotime($gathering['date'] . ' ' . $gathering['time'])); ?>
            </a>
        </p>
        <p><strong>الموقع:</strong> <?php echo escapeHtml($gathering['location']); ?></p>
        <p><strong>رمز الانضمام:</strong> <code><?php echo escapeHtml($gathering['joinCode']); ?></code></p>
        <div class="share-link">
          <strong>رابط المشاركة:</strong><br>
          <small style="word-break: break-all; display: block; margin: 0.5rem 0;"><?php echo escapeHtml("http://$_SERVER[HTTP_HOST]/gatherings.php?joincode=" . $gathering['joinCode']); ?></small>
          <button class="btn copy-link" data-link="<?php echo escapeHtml("http://$_SERVER[HTTP_HOST]/gatherings.php?joincode=" . $gathering['joinCode']); ?>">
            <i class="fas fa-copy"></i> نسخ الرابط
          </button>
        </div>
        <p class="muted"><strong>المنشئ:</strong> <?php echo escapeHtml($gathering['admin_name']); ?></p>
      </div>

      <!-- Tabs Container -->
      <div class="tabs-container">
        <div class="tabs-header">
          <button class="tab-btn active" onclick="switchTab('participants')">
            <i class="fas fa-users"></i>
            المشاركون (<?php echo count($participants); ?>)
          </button>
          <button class="tab-btn" onclick="switchTab('tasks')">
            <i class="fas fa-tasks"></i>
            المهام والأغراض (<?php echo count($tasks); ?>)
          </button>
        </div>

        <!-- Participants Tab -->
        <div id="participants-tab" class="tab-content active">
          <div class="participant-list">
            <?php foreach ($participants as $participant): ?>
              <div class="participant-item">
                <div class="meta">
                  <h3><i class="fas fa-user"></i> <?php echo escapeHtml($participant['Name']); ?></h3>
                  <p class="muted"><?php echo escapeHtml($participant['Email']); ?></p>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Tasks Tab -->
        <div id="tasks-tab" class="tab-content">
          <button class="btn" onclick="openAddModal()" style="margin-bottom: 1.5rem;">
            <i class="fas fa-plus"></i> إضافة مهمة أو غرض
          </button>
          
          <div class="task-list">
            <?php if (empty($tasks)): ?>
              <p class="muted" style="text-align: center; padding: 2rem;">لا توجد مهام أو أغراض بعد</p>
            <?php else: ?>
              <?php foreach ($tasks as $task): ?>
                <div class="task-item <?php echo $task['status'] === 'completed' ? 'completed' : 'pending'; ?>">
                  <div class="meta">
                    <h3>
                      
                      <?php echo escapeHtml($task['Description']); ?> 
                      <?php echo $task['status'] === 'completed' ? '✔️' : ''; ?>
                    </h3>
                    <?php if (!empty($task['note'])): ?>
                      <p><?php echo escapeHtml($task['note']); ?></p>
                    <?php endif; ?>
                    <p class="muted">
                      <?php echo $task['type'] === 'item' ? 'غرض سيتم إحضاره' : 'مهمة'; ?> - 
                      معين لـ: <?php echo escapeHtml($task['assigned_name'] ?? 'غير مخصص'); ?> — 
                      الحالة: <?php echo $task['status'] === 'completed' ? 'مكتمل' : 'قيد الانتظار'; ?>
                    </p>
                  </div>
                  <div class="actions">
                    <button class="btn outline" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($task)); ?>)">
                      <i class="fas fa-edit"></i> تعديل
                    </button>
                    <form method="POST" style="display: inline;">
                      <input type="hidden" name="task_id" value="<?php echo $task['TaskID']; ?>">
                      <button class="btn outline" type="submit" name="toggle_task">
                        <i class="fas fa-<?php echo $task['status'] === 'completed' ? 'undo' : 'check'; ?>"></i>
                        <?php echo $task['status'] === 'completed' ? 'إلغاء' : 'تم'; ?>
                      </button>
                    </form>
                    <form method="POST" style="display: inline;" onsubmit="return confirm('هل تريد حذف هذا العنصر؟')">
                      <input type="hidden" name="task_id" value="<?php echo $task['TaskID']; ?>">
                      <button class="btn outline" type="submit" name="delete_task">
                        <i class="fas fa-trash"></i> حذف
                      </button>
                    </form>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- Add Task Modal -->
  <div id="addModal" class="edit-modal">
    <div class="edit-modal-content">
      <button class="modal-close-btn" onclick="closeAddModal()">&times;</button>
      <h3><i class="fas fa-plus"></i> إضافة مهمة أو غرض جديد</h3>
      
      <form method="POST">
        <label for="add_description">العنوان</label>
        <input id="add_description" name="description" type="text" required placeholder="أدخل عنوان المهمة أو الغرض" />
        <br><br>
        
        <label for="add_note">ملاحظة (اختياري)</label>
        <input id="add_note" name="note" type="text" placeholder="أضف ملاحظة إن وجدت" />
        <br><br>
        
        <label for="add_type">النوع</label>
        <select id="add_type" name="type">
          <option value="task">مهمة</option>
          <option value="item">غرض</option>
        </select>
        <br><br>
        
        <label for="add_assigned_to">تعيين إلى</label>
        <select id="add_assigned_to" name="assigned_to">
          <option value="">غير مخصص</option>
          <?php foreach ($participants as $participant): ?>
            <option value="<?php echo $participant['UserID']; ?>">
              <?php echo escapeHtml($participant['Name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
        <br><br>
        
        <div style="display: flex; gap: 0.5rem;">
          <button class="btn" type="submit" name="add_task">
            <i class="fas fa-save"></i> إضافة
          </button>
          <button class="btn outline" type="button" onclick="closeAddModal()">
            <i class="fas fa-times"></i> إلغاء
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Task Modal -->
  <div id="editModal" class="edit-modal">
    <div class="edit-modal-content">
      <button class="modal-close-btn" onclick="closeEditModal()">&times;</button>
      <h3><i class="fas fa-edit"></i> تعديل المهمة أو الغرض</h3>
      
      <form method="POST" id="editForm">
        <input type="hidden" name="task_id" id="edit_task_id">
        
        <label for="edit_description">العنوان</label>
        <input id="edit_description" name="description" type="text" required />
        <br><br>
        
        <label for="edit_note">ملاحظة (اختياري)</label>
        <input id="edit_note" name="note" type="text" />
        <br><br>
        
        <label for="edit_type">النوع</label>
        <select id="edit_type" name="type">
          <option value="task">مهمة</option>
          <option value="item">غرض</option>
        </select>
        <br><br>
        
        <label for="edit_assigned_to">تعيين إلى</label>
        <select id="edit_assigned_to" name="assigned_to">
          <option value="">غير مخصص</option>
          <?php foreach ($participants as $participant): ?>
            <option value="<?php echo $participant['UserID']; ?>">
              <?php echo escapeHtml($participant['Name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
        <br><br>
        
        <div style="display: flex; gap: 0.5rem;">
          <button class="btn" type="submit" name="edit_task">
            <i class="fas fa-save"></i> حفظ التعديلات
          </button>
          <button class="btn outline" type="button" onclick="closeEditModal()">
            <i class="fas fa-times"></i> إلغاء
          </button>
        </div>
      </form>
    </div>
  </div>

  <footer class="footer">
    <p>© 2025 لمتنا</p>
  </footer>

  <script>
    // Participants data for edit modal
    const participants = <?php echo json_encode($participants); ?>;

    // Tab switching
    function switchTab(tabName) {
      document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
      });
      
      document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
      });
      
      document.getElementById(tabName + '-tab').classList.add('active');
      event.target.classList.add('active');
    }

    // Open add modal
    function openAddModal() {
      document.getElementById('addModal').classList.add('active');
      document.body.style.overflow = 'hidden';
    }

    // Close add modal
    function closeAddModal() {
      document.getElementById('addModal').classList.remove('active');
      document.body.style.overflow = 'auto';
    }

    // Open edit modal
    function openEditModal(task) {
      document.getElementById('edit_task_id').value = task.TaskID;
      document.getElementById('edit_description').value = task.Description;
      document.getElementById('edit_note').value = task.note || '';
      document.getElementById('edit_type').value = task.type;
      document.getElementById('edit_assigned_to').value = task.UserID || '';
      
      document.getElementById('editModal').classList.add('active');
      document.body.style.overflow = 'hidden';
    }

    // Close edit modal
    function closeEditModal() {
      document.getElementById('editModal').classList.remove('active');
      document.body.style.overflow = 'auto';
    }

    // Close modals when clicking outside
    document.getElementById('addModal').addEventListener('click', function(e) {
      if (e.target === this) closeAddModal();
    });
    
    document.getElementById('editModal').addEventListener('click', function(e) {
      if (e.target === this) closeEditModal();
    });

    // Close modals with Escape key
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        closeAddModal();
        closeEditModal();
      }
    });

    // Copy link functionality
    document.querySelectorAll('.copy-link').forEach(btn => {
      btn.addEventListener('click', () => {
        const link = btn.dataset.link;
        navigator.clipboard.writeText(link).then(() => {
          // Show success message
          const originalText = btn.innerHTML;
          btn.innerHTML = '<i class="fas fa-check"></i> تم النسخ!';
          btn.style.background = 'linear-gradient(135deg, var(--success), #20c997)';
          
          setTimeout(() => {
            btn.innerHTML = originalText;
            btn.style.background = '';
          }, 2000);
        });
      });
    });

    // Auto-hide success alert after 5 seconds
    const successAlert = document.getElementById('successAlert');
    if (successAlert) {
      setTimeout(function() {
        successAlert.style.animation = 'slideDown 0.3s ease reverse';
        setTimeout(() => successAlert.remove(), 300);
      }, 5000);
    }
  </script>
</body>
</html>