<?php
// Fetch current user info if logged in to display in sidebar
if (isset($_SESSION['user_id'])) {
    $sidebar_user_id = $_SESSION['user_id'];
    $sideStmt = $pdo->prepare("SELECT name, profile_pic FROM users WHERE user_id = ?");
    $sideStmt->execute([$sidebar_user_id]);
    $sidebar_user = $sideStmt->fetch();
}
?>

<div class="sidebar" id="sidebar">
    <div class="sidebar-header" style="text-align: center; padding: 25px 20px;">
        <?php if (isset($sidebar_user)): ?>
            <img src="uploads/profile_pics/<?php echo htmlspecialchars($sidebar_user['profile_pic'] ?? 'default.png'); ?>" 
                 style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover; border: 2px solid #3498db; margin-bottom: 10px;">
            <h3 style="color: white; margin: 0; font-size: 1.1em;"><?php echo htmlspecialchars($sidebar_user['name']); ?></h3>
        <?php else: ?>
            <h2 style="color: white; margin: 0;">Social Blog</h2>
        <?php endif; ?>
    </div>

    <div class="sidebar-search">
        <form action="search.php" method="GET">
            <input type="text" name="query" placeholder="🔍 Search posts..." required>
        </form>
    </div>

    <div class="nav-links">
        <p class="nav-label">MAIN MENU</p>
        <a href="index.php">🏠 Home Feed</a>
        <a href="dashboard.php">📊 My Dashboard</a>
        <a href="create_post.php">➕ Create Post</a>
        
        <p class="nav-label">PERSONAL</p>
        <a href="profile.php">👤 Profile Settings</a>
        <a href="my_comments.php">💬 My Comments</a>
        <a href="liked_posts.php">❤️ Liked Posts</a>
    </div>

    <div class="sidebar-footer">
        <div class="theme-switch">
            <button onclick="toggleTheme()" id="themeBtn">
                🌙 Dark Mode
            </button>
        </div>
        <a href="/simple_blog/auth/logout.php" class="logout-link"> Logout</a>
    </div>
</div>

<style>
/* New Category Labels */
.nav-label {
    padding: 20px 25px 5px 25px;
    font-size: 11px;
    letter-spacing: 1.5px;
    color: #7f8c8d;
    font-weight: bold;
    text-transform: uppercase;
}

.sidebar-search {
    padding: 10px 20px;
}

.sidebar-search input {
    width: 100%;
    padding: 10px 15px;
    border-radius: 20px;
    border: none;
    background: #34495e;
    color: white;
    font-size: 14px;
    outline: none;
}

.sidebar-search input::placeholder {
    color: #bdc3c7;
}

/* Sidebar Container */
.sidebar {
    width: 250px; 
    height: 100vh; 
    background: #2c3e50;
    position: fixed; 
    left: 0; 
    top: 0; 
    display: flex; 
    flex-direction: column;
    box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    z-index: 1000;
}

.sidebar-header {
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.nav-links {
    flex-grow: 1;
    overflow-y: auto; 
}

.sidebar a { 
    padding: 12px 25px; 
    color: #ecf0f1; 
    text-decoration: none; 
    display: block; 
    transition: 0.3s;
    font-size: 15px;
}

.sidebar a:hover { 
    background: #34495e; 
    color: #3498db;
    padding-left: 35px;
}

.sidebar-footer {
    margin-top: auto;
    padding-bottom: 20px;
    border-top: 1px solid rgba(255,255,255,0.1);
}

.theme-switch {
    padding: 15px 25px;
}

#themeBtn {
    background: #444; 
    color: white; 
    border: none; 
    padding: 10px; 
    width: 100%; 
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s;
}

.logout-link {
    color: #ff4757 !important; 
    font-weight: bold;
}

/* Dark Mode Extensions */
body.dark-mode { background-color: #1a1a1a !important; color: white; }
body.dark-mode .post-card, body.dark-mode .auth-card { background-color: #2d2d2d; color: white; border: 1px solid #444; }
body.dark-mode .sidebar-search input { background: #1a1a1a; border: 1px solid #444; }
</style>

<script>
function toggleTheme() {
    document.body.classList.toggle('dark-mode');
    const btn = document.getElementById('themeBtn');
    
    if(document.body.classList.contains('dark-mode')) {
        btn.innerHTML = "☀️ Light Mode";
        localStorage.setItem('theme', 'dark');
    } else {
        btn.innerHTML = "🌙 Dark Mode";
        localStorage.setItem('theme', 'light');
    }
}

window.onload = function() {
    if(localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark-mode');
        document.getElementById('themeBtn').innerHTML = "☀️ Light Mode";
    }
}
</script>