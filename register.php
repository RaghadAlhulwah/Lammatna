<?php 
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = strtolower(trim($_POST['email']));
    $password = $_POST['password'];
    
    $errors = [];
    
    if (empty($name) || empty($email) || empty($password)) {
        $errors[] = "يرجى ملء جميع الحقول";
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "البريد الإلكتروني غير صالح";
    }
    
    // Check if email exists
    $stmt = $pdo->prepare("SELECT UserID FROM user WHERE Email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $errors[] = "البريد الإلكتروني مستخدم سابقاً";
    }
    
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            // الحل اليدوي: جلب أعلى UserID وتزيد عليه 1
            $stmt = $pdo->query("SELECT COALESCE(MAX(UserID), 0) + 1 as next_id FROM user");
            $nextId = $stmt->fetch(PDO::FETCH_ASSOC)['next_id'];
            
            // إدخال المستخدم مع UserID محدد
            $stmt = $pdo->prepare("INSERT INTO user (UserID, Name, Email, Password) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nextId, $name, $email, $hashedPassword]);
            
            // Auto login after registration
            $_SESSION['user_id'] = $nextId;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $name;
            
            $_SESSION['success'] = "تم إنشاء الحساب بنجاح!";
            header("Location: index.php");
            exit;
            
        } catch(PDOException $e) {
            $errors[] = "حدث خطأ أثناء إنشاء الحساب: " . $e->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>تسجيل حساب — لمتنا</title>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div id="notif-area" class="notif-area">
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
 
  </header>

  <main class="container">
    <div class="form-panel">
      <h2>تسجيل حساب جديد</h2>
      <form method="POST" autocomplete="off">
        <label for="name">اسم المستخدم</label>
        <input id="name" name="name" type="text" required value="<?php echo escapeHtml($_POST['name'] ?? ''); ?>">

        <label for="email">البريد الإلكتروني</label>
        <input id="email" name="email" type="email" required value="<?php echo escapeHtml($_POST['email'] ?? ''); ?>">

        <label for="password">كلمة المرور</label>
        <input id="password" name="password" type="password" required>

        <br><br>

        <button class="btn" type="submit">إنشاء الحساب</button>
        <br><br>
        <p class="muted">لديك حساب؟ <a href="login.php">تسجيل الدخول</a></p>
      </form>
    </div>
  </main>

  <footer class="footer">
    <p>© 2025 لمتنا</p>
  </footer>
</body>
</html>