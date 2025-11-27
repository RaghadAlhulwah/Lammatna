<?php 

require_once 'config.php';
if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$edit_id = $_GET['edit'] ?? null;
$gathering = null;

if ($edit_id) {
    $stmt = $pdo->prepare("SELECT * FROM Gathering WHERE GatheringID = ? AND adminID = ?");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt->execute([$edit_id, $_SESSION['user_id']]);
    $gathering = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$gathering) {
        header("Location: gatherings.php");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $category = $_POST['category'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $location = trim($_POST['location']);
    $latitude = $_POST['latitude'] ?? null;
    $longitude = $_POST['longitude'] ?? null;
    
    
    $errors = [];
    
    if (empty($name) || empty($category) || empty($date) || empty($time) || empty($location)) {
        $errors[] = "يرجى ملء جميع الحقول المطلوبة";
    }
    
   
    if (empty($errors)) {
        if ($edit_id && $gathering) {
            // Update existing gathering
            try {
                $stmt = $pdo->prepare("UPDATE Gathering 
                    SET name = ?, category = ?, date = ?, time = ?, location = ?, latitude = ?, longitude = ?, reminder = ? 
                    WHERE GatheringID = ?");
                $stmt->execute([$name, $category, $date, $time, $location, $latitude, $longitude, $reminder, $edit_id]);
                
                $_SESSION['success'] = "تم تعديل الفعالية بنجاح";
                header("Location: gathering.php?id=$edit_id");
                exit;
            } catch (PDOException $e) {
                // عرض الخطأ في حالة فشل التحديث
                $errors[] = "فشل تحديث الفعالية: " . $e->getMessage();
            }
    

    } else {
        // Create new gathering
        try {
            $joinCode = strtoupper(substr(md5(uniqid()), 0, 8));
            
            // Get next available ID
            $stmt = $pdo->query("SELECT COALESCE(MAX(GatheringID),0)+1 as next_id FROM Gathering");
            $nextId = $stmt->fetch(PDO::FETCH_ASSOC)['next_id'];
            // ... داخل try { ...
    
    $gatheringId = $nextId; 
 
    // Insert Gathering
    $stmt = $pdo->prepare("INSERT INTO Gathering 
        (GatheringID, date, category, name, location, time, joinCode, adminID, latitude, longitude) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $gatheringId, 
        $date,
        $category,
        $name,
        $location,
        $time,
        $joinCode,
        $_SESSION['user_id'],
        $latitude,
        $longitude,
       
    ]);

    
    
    // Add creator as participant
    $stmt = $pdo->prepare("INSERT INTO Participant (UserID, GatheringID, status) VALUES (?, ?, 1)");
    $stmt->execute([$_SESSION['user_id'], $gatheringId]); // <--- قد يفشل هنا
                


                $_SESSION['success'] = "تم إنشاء الفعالية بنجاح";

                // Google Calendar link
                $eventTitle = urlencode($name);
                $eventDetails = urlencode("حيّ هلا،" . $userName . "،\n\nهذا تذكير بالفعالية القادمة:\n📌 الفعالية: " . $name . "\n📅 التاريخ: " . $date . "\n📍 الموقع: " . $location . "\n\nأحبابك ينتظرونك بالفعالية!\n\n");
                $eventLocation = urlencode($location);
                $startDateTime = date("Ymd\THis", strtotime("$date $time"));
                $endDateTime = date("Ymd\THis", strtotime("$date $time +2 hours"));
                $googleCalendarUrl = "https://calendar.google.com/calendar/render?action=TEMPLATE"
                    . "&text=$eventTitle"
                    . "&dates={$startDateTime}/{$endDateTime}"
                    . "&details=$eventDetails"
                    . "&location=$eventLocation";
                $_SESSION['google_calendar_link'] = $googleCalendarUrl;

                header("Location: gatherings.php");
                exit;

            } catch (PDOException $e) {
                // **عرض رسالة الخطأ في حالة فشل الإضافة في قاعدة البيانات**
                $errors[] = "فشل إنشاء الفعالية: " . $e->getMessage();
            }
        }
    }
}
?>
<html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title><?php echo $edit_id ? 'تعديل فعالية' : 'إنشاء فعالية'; ?> — لمتنا</title>
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="style.css">
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD6GsOIGEvdoe6YnEL_un8pIXrlbOLN35U&libraries=places&language=ar&region=SA"></script>
<style>
.map-container { height: 300px; width: 100%; margin:10px 0; border-radius:12px; overflow:hidden; border:2px solid var(--border); }
.location-search { position: relative; margin-bottom: 10px; }
.pac-container { z-index: 1000; font-family:'Tajawal',sans-serif; }
.coordinates { display:none; }
</style>
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
  <nav class="nav-links">
    <a href="gatherings.php">فعالياتي</a>
    <a href="profile.php">حسابي</a>
    <a href="logout.php">تسجيل الخروج</a>
  </nav>
</header>

<main class="container">
<div class="form-panel">
  <h2><?php echo $edit_id ? 'تعديل الفعالية' : 'إنشاء فعالية جديدة'; ?></h2>
  <form method="POST" id="gatheringForm">
    <label for="name">اسم الفعالية</label>
    <input id="name" name="name" type="text" required placeholder="أدخل اسم الفعالية" 
           value="<?php echo escapeHtml($gathering['name'] ?? $_POST['name'] ?? ''); ?>"><br><br>

    <label for="category">الفئة</label>
    <select id="category" name="category" required>
      <option value="">اختر فئة الفعالية</option>
      <option value="اجتماع عائلي" <?php echo ($gathering['category'] ?? $_POST['category'] ?? '')==='اجتماع عائلي'?'selected':''; ?>>اجتماع عائلي</option>
      <option value="حفلة تخرج" <?php echo ($gathering['category'] ?? $_POST['category'] ?? '')==='حفلة تخرج'?'selected':''; ?>>حفلة تخرج</option>
      <option value="اجتماع اصدقاء" <?php echo ($gathering['category'] ?? $_POST['category'] ?? '')==='اجتماع اصدقاء'?'selected':''; ?>>اجتماع اصدقاء</option>
      <option value="كشتة" <?php echo ($gathering['category'] ?? $_POST['category'] ?? '')==='كشتة'?'selected':''; ?>>كشتة</option>
      <option value="اخرى" <?php echo ($gathering['category'] ?? $_POST['category'] ?? '')==='اخرى'?'selected':''; ?>>أخرى</option>
    </select><br><br>

    <label for="date">التاريخ</label>
    <input id="date" name="date" type="date" required value="<?php echo escapeHtml($gathering['date'] ?? $_POST['date'] ?? ''); ?>">
    <label for="time">الوقت</label>
    <input id="time" name="time" type="time" required value="<?php echo escapeHtml($gathering['time'] ?? $_POST['time'] ?? ''); ?>">

    <label for="location">الموقع</label>
    <div class="location-search">
      <input id="location" name="location" type="text" required placeholder="ابحث عن موقع أو أدخل العنوان يدوياً" 
             value="<?php echo escapeHtml($gathering['location'] ?? $_POST['location'] ?? ''); ?>">
    </div>
    <input type="hidden" id="latitude" name="latitude" value="<?php echo escapeHtml($gathering['latitude'] ?? $_POST['latitude'] ?? ''); ?>">
    <input type="hidden" id="longitude" name="longitude" value="<?php echo escapeHtml($gathering['longitude'] ?? $_POST['longitude'] ?? ''); ?>">
    <div id="map" class="map-container"></div>
    <small class="muted">يمكنك سحب المؤشر لتحديد الموقع بدقة</small>

    <br><br>

    <button class="btn" type="submit"><i class="fas fa-save"></i> <?php echo $edit_id?'تحديث الفعالية':'حفظ الفعالية'; ?></button>
    <?php if($edit_id): ?>
      <a href="gathering.php?id=<?php echo $edit_id; ?>" class="btn outline"><i class="fas fa-times"></i> إلغاء التعديل</a>
    <?php endif; ?>
  </form>
</div>
</main> 

<footer class="footer"><p>© 2025 لمتنا</p></footer>

<script>
let map, marker, geocoder, autocomplete;

function initMap() {
    const defaultCenter = { lat: 24.7136, lng: 46.6753 };
    map = new google.maps.Map(document.getElementById("map"), { center: defaultCenter, zoom: 10 });
    geocoder = new google.maps.Geocoder();
    autocomplete = new google.maps.places.Autocomplete(document.getElementById('location'), { types: ['establishment','geocode'], componentRestrictions:{country:'sa'} });
    autocomplete.addListener('place_changed', onPlaceChanged);
    marker = new google.maps.Marker({ map: map, draggable: true, animation: google.maps.Animation.DROP });
    marker.addListener('dragend', onMarkerDragEnd);

    const initialLat = <?php echo !empty($gathering['latitude'])?$gathering['latitude']:'null'; ?>;
    const initialLng = <?php echo !empty($gathering['longitude'])?$gathering['longitude']:'null'; ?>;
    const initialLocation = <?php echo !empty($gathering['location'])?"'".addslashes($gathering['location'])."'":'null'; ?>;

    if(initialLat && initialLng){ marker.setPosition({lat:parseFloat(initialLat),lng:parseFloat(initialLng)}); map.setCenter(marker.getPosition()); map.setZoom(15); }
    else if(initialLocation){ geocodeAddress(initialLocation); }

    map.addListener('click', e=>{ marker.setPosition(e.latLng); map.panTo(e.latLng); updateCoordinates(e.latLng); reverseGeocode(e.latLng); });
}

function onPlaceChanged() {
    const place = autocomplete.getPlace();
    if(!place.geometry){ alert("لم يتم العثور على الموقع المحدد"); return; }
    map.setCenter(place.geometry.location); map.setZoom(15); marker.setPosition(place.geometry.location); updateCoordinates(place.geometry.location);
    document.getElementById('location').value = place.formatted_address;
}

function onMarkerDragEnd(){ const pos=marker.getPosition(); updateCoordinates(pos); reverseGeocode(pos); }
function updateCoordinates(latLng){ document.getElementById('latitude').value=latLng.lat(); document.getElementById('longitude').value=latLng.lng(); }
function geocodeAddress(address){ geocoder.geocode({address:address}, (results,status)=>{ if(status==="OK"){ map.setCenter(results[0].geometry.location); marker.setPosition(results[0].geometry.location); updateCoordinates(results[0].geometry.location); } }); }
function reverseGeocode(latLng){ geocoder.geocode({location:latLng}, (results,status)=>{ if(status==="OK" && results[0]){ document.getElementById('location').value=results[0].formatted_address; } }); }

google.maps.event.addDomListener(window,'load',initMap);

document.getElementById('gatheringForm').addEventListener('submit', function(e){
    const lat=document.getElementById('latitude').value;
    const lng=document.getElementById('longitude').value;
    if(!lat || !lng){ e.preventDefault(); alert('يرجى تحديد الموقع على الخريطة'); return false; }
});
</script>
</body>
</html>
