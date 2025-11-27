<?php 
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email']));
    $password = $_POST['password'];
    
    if (!empty($email) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM user WHERE Email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['Password'])) {
            $_SESSION['user_id'] = $user['UserID'];
            $_SESSION['user_email'] = $user['Email'];
            $_SESSION['user_name'] = $user['Name'];
            header("Location: index.php?login=success");
            exit;
        } else {
            $error = "البريد الإلكتروني أو كلمة المرور غير صحيحة";
        }
    } else {
        $error = "يرجى ملء جميع الحقول";
    }
}
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>تسجيل الدخول — لمتنا</title>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div id="notif-area" class="notif-area">
    <?php if (isset($error)): ?>
      <div class="notif" style="border-left-color: var(--danger);"><?php echo escapeHtml($error); ?></div>
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
      <h2>تسجيل الدخول</h2>
      <form method="POST" autocomplete="off">
        <label for="email">البريد الإلكتروني</label>
        <input id="email" name="email" type="email" required value="<?php echo escapeHtml($_POST['email'] ?? ''); ?>">
        <br><br>

        <label for="password">كلمة المرور</label>
        <input id="password" name="password" type="password" required>
        <br><br>

        <button class="btn" type="submit">دخول</button>
        <br><br>
        <p class="muted">ليس لديك حساب؟ <a href="register.php">سجّل الآن</a></p>
      </form>
    </div>
  </main>

  <footer class="footer">
    <p>© 2025 لمتنا</p>
  </footer>
</body>
</html>