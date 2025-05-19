<?php
// Include admin header
include '../includes/admin-header.php';

// Get counts for dashboard
$user_count = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$recipe_count = $conn->query("SELECT COUNT(*) as count FROM recipes")->fetch_assoc()['count'];
$service_count = $conn->query("SELECT COUNT(*) as count FROM services")->fetch_assoc()['count'];
$enrollment_count = $conn->query("SELECT COUNT(*) as count FROM enrollments")->fetch_assoc()['count'];

// Get premium users count (users with confirmed or completed enrollments)
$premium_users_count = $conn->query("SELECT COUNT(DISTINCT user_id) as count FROM enrollments WHERE status IN ('confirmed', 'completed')")->fetch_assoc()['count'];

// Get premium recipes count
$premium_recipes_count = $conn->query("SELECT COUNT(*) as count FROM recipes WHERE is_premium = 1")->fetch_assoc()['count'];

// Get recent enrollments
$enrollments_sql = "SELECT e.*, u.username, s.title FROM enrollments e 
                    JOIN users u ON e.user_id = u.user_id 
                    JOIN services s ON e.service_id = s.service_id 
                    ORDER BY e.enrollment_date DESC LIMIT 5";
$enrollments_result = $conn->query($enrollments_sql);

$enrollments = [];
if ($enrollments_result->num_rows > 0) {
    while ($row = $enrollments_result->fetch_assoc()) {
        $enrollments[] = $row;
    }
}

// Get recent recipes
$recipes_sql = "SELECT * FROM recipes ORDER BY created_at DESC LIMIT 5";
$recipes_result = $conn->query($recipes_sql);

$recipes = [];
if ($recipes_result->num_rows > 0) {
    while ($row = $recipes_result->fetch_assoc()) {
        $recipes[] = $row;
    }
}
?>

<!-- Dashboard Stats -->
<div class="dashboard-stats">
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo $user_count; ?></h3>
            <p>Total Users</p>
        </div>
    <!-- </div> -->
    
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-utensils"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo $recipe_count; ?></h3>
            <p>Total Recipes</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-concierge-bell"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo $service_count; ?></h3>
            <p>Total Services</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-clipboard-list"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo $enrollment_count; ?></h3>
            <p>Total Enrollments</p>
        </div>
    </div>
</div>

<!-- Premium Stats -->
<div class="dashboard-stats" style="margin-top: 1.5rem;">
    <div class="stat-card">
        <div class="stat-icon" style="background-color: #ffd700;">
            <i class="fas fa-crown"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo $premium_users_count; ?></h3>
            <p>Premium Users</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background-color: #ffd700;">
            <i class="fas fa-star"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo $premium_recipes_count; ?></h3>
            <p>Premium Recipes</p>
        </div>
    </div>
</div>

<!-- Recent Enrollments -->
<div class="dashboard-section">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h2>Recent Enrollments</h2>
        <a href="enrollments.php" class="btn btn-sm">View All</a>
    </div>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Service</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($enrollments)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">No enrollments found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($enrollments as $enrollment): ?>
                        <tr>
                            <td><?php echo $enrollment['enrollment_id']; ?></td>
                            <td><?php echo $enrollment['username']; ?></td>
                            <td><?php echo $enrollment['title']; ?></td>
                            <td><?php echo date('M d, Y', strtotime($enrollment['enrollment_date'])); ?></td>
                            <td>
                                <span class="status status-<?php echo $enrollment['status']; ?>">
                                    <?php echo ucfirst($enrollment['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="enrollments.php?edit=<?php echo $enrollment['enrollment_id']; ?>" class="btn btn-sm">Edit</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Recent Recipes -->
<div class="dashboard-section" style="margin-top: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h2>Recent Recipes</h2>
        <a href="recipes.php" class="btn btn-sm">View All</a>
    </div>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Difficulty</th>
                    <th>Premium</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recipes)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center;">No recipes found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recipes as $recipe): ?>
                        <tr>
                            <td><?php echo $recipe['recipe_id']; ?></td>
                            <td>
                                <img src="<?php echo '../' . $recipe['image_path']; ?>" alt="<?php echo $recipe['title']; ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                            </td>
                            <td><?php echo $recipe['title']; ?></td>
                            <td><?php echo !empty($recipe['category']) ? $recipe['category'] : 'N/A'; ?></td>
                            <td><?php echo ucfirst($recipe['difficulty']); ?></td>
                            <td>
                                <span class="status <?php echo $recipe['is_premium'] ? 'status-active' : 'status-inactive'; ?>">
                                    <?php echo $recipe['is_premium'] ? 'Premium' : 'Free'; ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="edit-recipe.php?id=<?php echo $recipe['recipe_id']; ?>" class="btn btn-sm">Edit</a>
                                    <a href="../recipe-detail.php?id=<?php echo $recipe['recipe_id']; ?>" target="_blank" class="btn btn-sm btn-outline">View</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- System Overview -->
<div class="dashboard-section" style="margin-top: 2rem;">
    <h2>System Overview</h2>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-top: 1rem;">
        <div class="card">
            <h3>User Statistics</h3>
            <ul style="list-style: none; padding: 0; margin: 1rem 0;">
                <li style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span>Total Users:</span>
                    <strong><?php echo $user_count; ?></strong>
                </li>
                <li style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span>Premium Users:</span>
                    <strong><?php echo $premium_users_count; ?></strong>
                </li>
                <li style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span>Admin Users:</span>
                    <strong><?php echo $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'admin'")->fetch_assoc()['count']; ?></strong>
                </li>
                <li style="display: flex; justify-content: space-between;">
                    <span>Guest Users:</span>
                    <strong><?php echo $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'guest'")->fetch_assoc()['count']; ?></strong>
                </li>
            </ul>
            <a href="accounts.php" class="btn btn-sm" style="width: 100%;">Manage Users</a>
        </div>
        
        <div class="card">
            <h3>Content Statistics</h3>
            <ul style="list-style: none; padding: 0; margin: 1rem 0;">
                <li style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span>Total Recipes:</span>
                    <strong><?php echo $recipe_count; ?></strong>
                </li>
                <li style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span>Premium Recipes:</span>
                    <strong><?php echo $premium_recipes_count; ?></strong>
                </li>
                <li style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span>Total Services:</span>
                    <strong><?php echo $service_count; ?></strong>
                </li>
                <li style="display: flex; justify-content: space-between;">
                    <span>Active Services:</span>
                    <strong><?php echo $conn->query("SELECT COUNT(*) as count FROM services WHERE is_active = 1")->fetch_assoc()['count']; ?></strong>
                </li>
            </ul>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                <a href="recipes.php" class="btn btn-sm">Manage Recipes</a>
                <a href="services.php" class="btn btn-sm">Manage Services</a>
            </div>
        </div>
        
        <div class="card">
            <h3>System Status</h3>
            <ul style="list-style: none; padding: 0; margin: 1rem 0;">
                <li style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span>PHP Version:</span>
                    <strong><?php echo phpversion(); ?></strong>
                </li>
                <li style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span>MySQL Version:</span>
                    <strong><?php echo $conn->server_info; ?></strong>
                </li>
                <li style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span>Server:</span>
                    <strong><?php echo $_SERVER['SERVER_SOFTWARE']; ?></strong>
                </li>
                <li style="display: flex; justify-content: space-between;">
                    <span>Current Time:</span>
                    <strong><?php echo date('Y-m-d H:i:s'); ?></strong>
                </li>
            </ul>
            <a href="../index.php" target="_blank" class="btn btn-sm" style="width: 100%;">View Site</a>
        </div>
    </div>
</div>

<?php
// Include admin footer
include '../includes/admin-footer.php';
?>