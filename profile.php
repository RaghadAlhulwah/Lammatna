<?php 
require_once 'config.php';
if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$user = getLoggedUser($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = strtolower(trim($_POST['email']));
    $password = $_POST['password'];
    
    $errors = [];
    
    if (empty($name) || empty($email)) {
        $errors[] = "يرجى ملء جميع الحقول المطلوبة";
    }
    
    // استخدام طريقة أبسط للتحقق من الاسم
    if (strlen($name) < 3) {
        $errors[] = "اسم المستخدم يجب أن يكون 3 أحرف على الأقل";
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "البريد الإلكتروني غير صالح";
    }
    
    // Check if email exists for other users
    $stmt = $pdo->prepare("SELECT UserID FROM user WHERE Email = ? AND UserID != ?");
    $stmt->execute([$email, $_SESSION['user_id']]);
    if ($stmt->fetch()) {
        $errors[] = "البريد الإلكتروني مستخدم";
    }
    
    // Check if username exists for other users
    $stmt = $pdo->prepare("SELECT UserID FROM user WHERE Name = ? AND UserID != ?");
    $stmt->execute([$name, $_SESSION['user_id']]);
    if ($stmt->fetch()) {
        $errors[] = "اسم المستخدم مستخدم";
    }
    
    if (empty($errors)) {
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE user SET Name = ?, Email = ?, Password = ? WHERE UserID = ?");
            $stmt->execute([$name, $email, $hashedPassword, $_SESSION['user_id']]);
        } else {
            $stmt = $pdo->prepare("UPDATE user SET Name = ?, Email = ? WHERE UserID = ?");
            $stmt->execute([$name, $email, $_SESSION['user_id']]);
        }
        
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        $_SESSION['success'] = "تم تحديث الملف الشخصي بنجاح";
        header("Location: profile.php");
        exit;
    }
}
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>ملفي — لمتنا</title>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div id="notif-area" class="notif-area">
    <?php if (isset($_SESSION['success'])): ?>
      <div class="notif"><?php echo escapeHtml($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
      <?php foreach ($errors as $error): ?>
        <div class="notif" style="border-left-color: var(--danger);"><?php echo escapeHtml($error); ?></div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <header class="navbar">
    <div class="logo-container">
      <a href="index.php" class="logo-link"><img src="../Lammatna.png" alt="لمتنا" class="logo"></a>
      <a href="index.php" class="site-title-link"><h1 class="site-title">لمتنا</h1></a>
    </div>
    <nav class="nav-links">
      <a href="gatherings.php">فعالياتي</a>
      <a href="logout.php">تسجيل الخروج</a>
    </nav>
  </header>

  <main class="container">
    <div class="form-panel">
      <h2>معلومات الحساب</h2>
      <form method="POST" class="form-container">
        <label>اسم المستخدم</label>
        <input name="name" value="<?php echo escapeHtml($user['Name']); ?>" required />

        <label>البريد الإلكتروني</label>
        <input name="email" type="email" value="<?php echo escapeHtml($user['Email']); ?>" required />

        <label>كلمة المرور الجديدة (اختياري)</label>
        <input name="password" type="password" placeholder="اتركه فارغاً إن لم ترغب بالتغيير" />

        <div style="margin-top:10px; display:flex; gap:8px;">
          <button class="btn" type="submit">حفظ</button>
        </div>
      </form>
    </div>
  </main>

  <footer class="footer">
    <p>© 2025 لمتنا</p>
  </footer>
</body>
</html>