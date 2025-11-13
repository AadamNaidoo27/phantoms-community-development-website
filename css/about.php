<?php
include __DIR__ . '/../db_connect.php';

// Record visitor IP
$ip = $_SERVER['REMOTE_ADDR'];
$stmt = $conn->prepare("INSERT INTO visits (ip_address) VALUES (?)");
$stmt->bind_param("s", $ip);
$stmt->execute();
$stmt->close();

$org_name = "Phantoms Community Development";
$org_description = "$org_name is a non-profit organization dedicated to building stronger, self-sustaining communities. We focus on empowering individuals through education, entrepreneurship, and social development initiatives that drive long-term positive change.";
$mission = "To create a platform that supports community-driven change through education, mentorship, and collaboration. We aim to nurture individuals with the skills and confidence to make a meaningful impact within their communities.";
$vision = "A future where every community thrives through innovation, education, and collective growth — where no one is left behind due to lack of resources or opportunity.";

$core_values = ["Integrity", "Innovation", "Empowerment", "Sustainability", "Collaboration", "Compassion"];

$projects = [
    ["title" => "Education Support", "emoji" => "🎓", "description" => "Providing tutoring programs, school supply drives, and literacy campaigns to promote equal access to education."],
    ["title" => "Skills Development", "emoji" => "💼", "description" => "Hosting workshops on entrepreneurship, coding, and leadership to help youth and adults find career direction."],
    ["title" => "Community Outreach", "emoji" => "🤝", "description" => "Offering food parcels, hygiene kits, and emergency aid to families in need."],
    ["title" => "Youth Empowerment", "emoji" => "🌍", "description" => "Creating mentorship programs, youth camps, and leadership events to build tomorrow’s change-makers."],
    ["title" => "Sustainability Projects", "emoji" => "🌱", "description" => "Supporting environmental awareness, recycling drives, and community garden initiatives."]
];

$leaders = [
    [
        "role" => "Directors",
        "name" => "Achmat Harris, Yusuf Jattiem, Ghumaid Johnson, Wahieba Van De Merwe and Allan Samuels",
        "bio" => "A passionate leadership team with over 10 years of combined experience in social development, entrepreneurship, and youth empowerment. This leadership team is known as the BIG 5. They have a bond like no other — many have tried to break it, but through all these years they have remained unbreakably united. Many have asked where the name 'Phantoms' came from, and they said: 'A phantom is a ghost, and by our previous teams we were always the ghosts. So why not start our own team instead of guiding others?' They met on a previous team, got to know each other, and decided to create their own platform to make a difference in children’s and adults’ lives. The BIG 5 remain strong, striving for 1300–1500 members in the upcoming 2025-2026 season."
    ]
];

$people_reached = 1500;
$year_started = 2024;
$current_year = date("Y");

// Sponsorship form
$confirmationMessage = "";
$whatsappLink = "";
if (isset($_POST['sponsor_submit'])) {
    $name = htmlspecialchars($_POST['name'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $message = htmlspecialchars($_POST['message'] ?? '');
    $confirmationMessage = "Thank you, $name! Your sponsorship interest has been received.";

    $whatsappNumber = "27660848345";
    $whatsappMessage = urlencode("Hello Phantoms Community 👋, my name is $name. I would like to sponsor or collaborate with your organization. Here are my details: Email: $email Message: $message");
    $whatsappLink = "https://wa.me/$whatsappNumber?text=$whatsappMessage";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>About Us | <?= $org_name ?></title>
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body {
    margin:0;
    font-family:'Poppins', Arial, sans-serif;
    background-color:#00b7eb;
    color:#fff;
    line-height:1.6;
}
.navbar {
    background-color:#2c3e50;
    padding:10px 20px;
    position:fixed;
    top:0;
    width:100%;
    z-index:1000;
    box-shadow:0 3px 10px rgba(0,0,0,0.3);
}
.nav-container { display:flex; justify-content:space-between; align-items:center; max-width:1200px; margin:auto; flex-wrap:wrap; }
.nav-logo { display:flex; align-items:center; gap:10px; }
.logo-img { border-radius:50%; border:2px solid #ff6b35; width:55px; height:55px; object-fit:cover; }
.logo-text { color:#ff6b35; font-weight:bold; font-size:1.4rem; }
.nav-menu { list-style:none; display:flex; gap:15px; margin:0; padding:0; flex-wrap:wrap; align-items:center; }
.nav-link { color:white; text-decoration:none; font-weight:500; }
.nav-link:hover { color:#ff6b35; }

section { padding:3rem 1rem; max-width:1000px; margin:auto; }
.section-box { background:white; color:#333; padding:2rem; border-radius:10px; box-shadow:0 5px 15px rgba(0,0,0,0.2); margin-bottom:2rem; text-align:left; }
.section-box h2 { color:#ff6b35; margin-bottom:0.8rem; }
.confirmation {
    background-color:#4CAF50;
    color:white;
    text-align:center;
    padding:1rem;
    font-weight:bold;
    position:fixed;
    top:0;
    width:100%;
    z-index:2000;
    display:none;
}
.social-links { display:flex; justify-content:center; gap:20px; margin-top:1rem; }
.social-icon { font-size:2rem; color:white; transition:transform 0.3s,color 0.3s; }
.social-icon:hover { transform:scale(1.2); }
.instagram:hover { color:#E4405F; }
.tiktok:hover { color:#000; }
.facebook:hover { color:#1877F2; }
footer { background:#2c3e50; text-align:center; padding:2rem; margin-top:2rem; }
footer p { color:#bbb; margin-top:1rem; }
.video-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:1rem; }
.video-box video { width:100%; border-radius:8px; }
@media(max-width:768px){ .nav-menu{ flex-direction:column; gap:10px; } }
</style>
</head>
<body>

<?php if ($confirmationMessage): ?>
<div id="confirmation-message" class="confirmation"><?= $confirmationMessage ?></div>
<script>
document.addEventListener("DOMContentLoaded", function(){
    const msg = document.getElementById("confirmation-message");
    msg.style.display = "block";
    setTimeout(()=>{ msg.style.opacity="0"; msg.style.transition="opacity 1s ease"; setTimeout(()=>msg.style.display="none",1000); },5000);
});
</script>
<?php endif; ?>

<!-- Navbar -->
<nav class="navbar">
<div class="nav-container">
    <div class="nav-logo">
        <a href="index.php"><img src="images/official logo.jpg" class="logo-img" alt="Logo"></a>
        <span class="logo-text"><?= $org_name ?></span>
    </div>
    <ul class="nav-menu">
        <li><a href="index.php" class="nav-link">Home</a></li>
        <li><a href="about.php" class="nav-link" style="color:#ff6b35;">About</a></li>
        <li><a href="shop.php" class="nav-link">Shop</a></li>
        <li><a href="events.php" class="nav-link">Events</a></li>
        <li><a href="contact.php" class="nav-link">Contact</a></li>
    </ul>
</div>
</nav>

<section style="margin-top:80px;">
<h1 style="color:#ff6b35;text-align:center;margin-bottom:1.5rem;">About Us</h1>
<div class="section-box">
<h2>Who We Are</h2>
<p><?= $org_description ?></p>
<p>Since <?= $year_started ?>, we’ve reached over <?= number_format($people_reached) ?> people.</p>

<h2>Our Mission</h2><p><?= $mission ?></p>
<h2>Our Vision</h2><p><?= $vision ?></p>

<h2>Our Programs & Initiatives</h2>
<ul>
<?php foreach ($projects as $p): ?>
<li><?= $p['emoji'] ?> <strong><?= $p['title'] ?>:</strong> <?= $p['description'] ?></li>
<?php endforeach; ?>
</ul>

<h2>Leadership Team</h2>
<?php foreach ($leaders as $leader): ?>
<div style="background:#f4f4f4; padding:1rem; border-radius:8px; margin-bottom:1rem;">
<h3><?= $leader['name'] ?> – <span style="color:#ff6b35;"><?= $leader['role'] ?></span></h3>
<p><?= $leader['bio'] ?></p>
</div>
<?php endforeach; ?>

<h2>Highlights from Last Year</h2>
<div class="video-grid">
<?php
$videos = [
    ["file"=>"Klopse Jol.mp4","desc"=>"The Klopse Jol, greeting of the stadium, where bands showcase their hard work and music skills."],
    ["file"=>"Best Band.mp4","desc"=>"The Best Band item where the band performs a mix of genres to test their musical abilities."],
    ["file"=>"Junior.mp4","desc"=>"Junior Combine Chorus — children singing in competition."],
    ["file"=>"Senior.mp4","desc"=>"Senior Combine Chorus — adults competing for the best singing group title (Singpak)."]
];
foreach($videos as $v): ?>
<div class="video-box">
<video controls>
<source src="../uploads/<?= $v['file'] ?>" type="video/mp4">
</video>
<p style="color:#333;"><?= $v['desc'] ?></p>
</div>
<?php endforeach; ?>
</div>
</div>
</section>

<section>
<h2 style="color:#ff6b35;text-align:center;">Sponsor the Phantoms</h2>
<div class="section-box">
<form method="POST">
<label>Full Name:</label>
<input type="text" name="name" required style="width:100%;margin-bottom:1rem;padding:0.6rem;">
<label>Email:</label>
<input type="email" name="email" required style="width:100%;margin-bottom:1rem;padding:0.6rem;">
<label>Message:</label>
<textarea name="message" rows="4" required style="width:100%;margin-bottom:1rem;padding:0.6rem;"></textarea>
<button type="submit" name="sponsor_submit" style="background:#ff6b35;color:white;border:none;padding:0.8rem 2rem;border-radius:5px;">Submit</button>
</form>
<?php if($whatsappLink): ?>
<p style="margin-top:1rem;text-align:center;">
<a href="<?= $whatsappLink ?>" target="_blank" style="background:#25D366;color:white;padding:0.8rem 1.5rem;border-radius:8px;text-decoration:none;">📱 Contact via WhatsApp</a>
</p>
<?php endif; ?>
</div>
</section>

<!-- Footer -->
<footer>
<div>
<h3>Follow <?= $org_name ?></h3>
<div class="social-links">
<a href="https://www.instagram.com/phantoms_community?utm_source=qr&igsh=MTZnbjl3dWcwM25tMg==" target="_blank" class="social-icon instagram"><i class="fab fa-instagram"></i></a>
<a href="https://www.tiktok.com/@phantomscommunitydevelop?_r=1&_t=ZS-917gHOLIUdg" target="_blank" class="social-icon tiktok"><i class="fab fa-tiktok"></i></a>
<a href="https://www.facebook.com/share/14MhVDLjPY5/" target="_blank" class="social-icon facebook"><i class="fab fa-facebook"></i></a>
</div>
<p>&copy; <?= $current_year ?> <?= $org_name ?>. All Rights Reserved.</p>
</div>
</footer>

</body>
</html>
