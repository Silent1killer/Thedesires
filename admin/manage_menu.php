<?php
$page_title = "Manage Menu";
require_once '../includes/db_connection.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Check if user is admin
check_admin();

// Handle form submissions
$success_message = '';
$error_message = '';

// Get categories
$categories = get_menu_categories();

// Add new menu item
if (isset($_POST['add_item'])) {
    $name = sanitize_input($_POST['name']);
    $description = sanitize_input($_POST['description']);
    $price = (float) $_POST['price'];
    $category_id = (int) $_POST['category_id'];
    $image_url = sanitize_input($_POST['image_url']);

    if (empty($name) || empty($description) || $price <= 0 || $category_id <= 0 || empty($image_url)) {
        $error_message = "All fields are required.";
    } else {
        $query = "INSERT INTO menu_items (name, description, price, category_id, image_url, created_at) 
                  VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssdis", $name, $description, $price, $category_id, $image_url);

        if ($stmt->execute()) {
            $success_message = "Menu item added successfully.";
        } else {
            $error_message = "Error adding menu item: " . $conn->error;
        }
    }
}

// Update menu item
if (isset($_POST['update_item'])) {
    $item_id = (int) $_POST['item_id'];
    $name = sanitize_input($_POST['name']);
    $description = sanitize_input($_POST['description']);
    $price = (float) $_POST['price'];
    $category_id = (int) $_POST['category_id'];
    $image_url = sanitize_input($_POST['image_url']);

    if (empty($name) || empty($description) || $price <= 0 || $category_id <= 0 || empty($image_url)) {
        $error_message = "All fields are required.";
    } else {
        $query = "UPDATE menu_items SET name = ?, description = ?, price = ?, category_id = ?, 
                  image_url = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssdisi", $name, $description, $price, $category_id, $image_url, $item_id);

        if ($stmt->execute()) {
            $success_message = "Menu item updated successfully.";
        } else {
            $error_message = "Error updating menu item: " . $conn->error;
        }
    }
}

// Delete menu item
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $item_id = (int) $_GET['delete'];

    // First check if there are any orders for this item
    $check_query = "SELECT COUNT(*) as count FROM order_items WHERE menu_item_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("i", $item_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $check_row = $check_result->fetch_assoc();

    if ($check_row['count'] > 0) {
        $error_message = "Cannot delete this item as it has been ordered by customers.";
    } else {
        $query = "DELETE FROM menu_items WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $item_id);

        if ($stmt->execute()) {
            $success_message = "Menu item deleted successfully.";
        } else {
            $error_message = "Error deleting menu item: " . $conn->error;
        }
    }
}

// Delete category
if (isset($_GET['delete_category']) && is_numeric($_GET['delete_category'])) {
    $category_id = (int) $_GET['delete_category'];

    // Check if the category has associated menu items
    $check_query = "SELECT COUNT(*) as count FROM menu_items WHERE category_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("i", $category_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $check_row = $check_result->fetch_assoc();

    if ($check_row['count'] > 0) {
        $error_message = "Cannot delete this category as it has associated menu items.";
    } else {
        // Delete the category
        $query = "DELETE FROM categories WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $category_id);

        if ($stmt->execute()) {
            $success_message = "Category deleted successfully.";
            // Refresh categories
            $categories = get_menu_categories();
        } else {
            $error_message = "Error deleting category: " . $conn->error;
        }
    }
}

// Add new category
if (isset($_POST['add_category'])) {
    $category_name = sanitize_input($_POST['category_name']);

    if (empty($category_name)) {
        $error_message = "Category name is required.";
    } else {
        $query = "INSERT INTO categories (name, created_at) VALUES (?, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $category_name);

        if ($stmt->execute()) {
            $success_message = "Category added successfully.";
            // Refresh categories
            $categories = get_menu_categories();
        } else {
            $error_message = "Error adding category: " . $conn->error;
        }
    }
}

// Get menu items
$menu_items = get_all_menu_items();

// Get item to edit if requested
$edit_item = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $item_id = (int) $_GET['edit'];
    $edit_item = get_menu_item_by_id($item_id);
}

// Include header
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - the desires</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 0;
            margin: 0;
        }
    </style>
</head>

<body>
    <div class="admin-container">

        <!-- Admin Sidebar -->
        <div class="admin-sidebar">
            <div class="admin-logo">
                <a href="../index.php">
                    <img src="../assets/logo.svg" alt="the desires Restaurant">
                </a>
            </div>
            <div class="admin-menu">
                <a href="index.php" class="admin-menu-item">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="manage_menu.php" class="admin-menu-item">
                    <i class="fas fa-utensils"></i> Manage Menu
                </a>
                <a href="manage_orders.php" class="admin-menu-item">
                    <i class="fas fa-shopping-cart"></i> Manage Orders
                </a>
                <a href="manage_reservations.php" class="admin-menu-item">
                    <i class="fas fa-calendar-alt"></i> Manage Reservations
                </a>
                <a href="manage_users.php" class="admin-menu-item">
                    <i class="fas fa-users"></i> Manage Users
                </a>
                <a href="manage_reviews.php" class="admin-menu-item">
                    <i class="fas fa-star"></i> Manage Reviews
                </a>
                <a href="manage_contacts.php" class="admin-menu-item">
                    <i class="fas fa-envelope"></i> Manage Reaches
                </a>
                <a href="../index.php" class="admin-menu-item">
                    <i class="fas fa-home"></i> Visit Website
                </a>
                <a href="../logout.php" class="admin-menu-item">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
       
        <!-- Admin Content -->
        <div class="admin-content">
            <div class="admin-header">
                <h1 class="admin-title"><?php echo $page_title; ?></h1>
                <div class="admin-user">
                    <i class="fas fa-user"></i>
                    <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                </div>
            </div>

            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-error"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <div class="admin-actions">
                <a href="manage_menu.php"
                    class="btn <?php echo (!isset($_GET['action']) && !isset($_GET['edit'])) ? 'btn-secondary' : ''; ?>">All
                    Menu Items</a>
                <a href="manage_menu.php?action=add"
                    class="btn <?php echo (isset($_GET['action']) && $_GET['action'] == 'add') ? 'btn-secondary' : ''; ?>">Add
                    New Item</a>
                <a href="manage_menu.php?action=categories"
                    class="btn <?php echo (isset($_GET['action']) && $_GET['action'] == 'categories') ? 'btn-secondary' : ''; ?>">Manage
                    Categories</a>
            </div>

            <?php if (isset($_GET['action']) && $_GET['action'] == 'add'): ?>
                <!-- Add Menu Item Form -->
                <div class="admin-form">
                    <h2 class="admin-form-title">Add New Menu Item</h2>
                    <form action="manage_menu.php" method="post">
                        <div class="admin-form-group">
                            <label for="name">Item Name</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                        <div class="admin-form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="admin-form-group">
                            <label for="price">Price</label>
                            <input type="number" id="price" name="price" class="form-control" step="0.01" min="0" required>
                        </div>
                        <div class="admin-form-group">
                            <label for="category_id">Category</label>
                            <select id="category_id" name="category_id" class="form-control" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>">
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <label for="image_url">Image URL</label>
                        <input type="text" id="image_url" name="image_url" class="form-control">
                        <small class="form-text">Enter a URL for the item image (e.g.,
                            https://source.unsplash.com/300x200/?food)</small>
                </div>
                <div class="admin-form-actions">
                    <button type="submit" name="add_item" class="btn">Add Menu Item</button>
                </div>
                </form>
            </div>
        <?php elseif (isset($_GET['action']) && $_GET['action'] == 'categories'): ?>
            <!-- Manage Categories -->
            <div class="admin-section">
                <div class="admin-form" style="margin-bottom: 30px;">
                    <h2 class="admin-form-title">Add New Category</h2>
                    <form action="manage_menu.php?action=categories" method="post">
                        <div class="admin-form-group">
                            <label for="category_name">Category Name</label>
                            <input type="text" id="category_name" name="category_name" class="form-control" required>
                        </div>
                        <div class="admin-form-actions">
                            <button type="submit" name="add_category" class="btn">Add Category</button>
                        </div>
                    </form>
                </div>

                <div class="admin-table">
                    <div class="admin-table-header">
                        <h2 class="admin-table-title">Current Categories</h2>
                    </div>
                    <div class="admin-table-content">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Menu Items</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($categories)): ?>
                                    <tr>
                                        <td colspan="3" style="text-align: center;">No categories found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($categories as $category): ?>
                                        <tr>
                                            <td><?php echo $category['id']; ?></td>
                                            <td><?php echo htmlspecialchars($category['name']); ?></td>
                                            <td>
                                                <?php
                                                // Count items in this category
                                                $count_query = "SELECT COUNT(*) as count FROM menu_items WHERE category_id = " . $category['id'];
                                                $count_result = $conn->query($count_query);
                                                $count_row = $count_result->fetch_assoc();
                                                echo $count_row['count'];
                                                ?>
                                            </td>
                                            <td>
                                                <a href="manage_menu.php?delete_category=<?php echo $category['id']; ?>" 
                                                   class="btn btn-danger"
                                                   onclick="return confirm('Are you sure you want to delete this category?')">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php elseif (isset($_GET['edit']) && $edit_item): ?>
            <!-- Edit Menu Item Form -->
            <div class="admin-form">
                <h2 class="admin-form-title">Edit Menu Item</h2>
                <form action="manage_menu.php" method="post">
                    <input type="hidden" name="item_id" value="<?php echo $edit_item['id']; ?>">
                    <div class="admin-form-group">
                        <label for="name">Item Name</label>
                        <input type="text" id="name" name="name" class="form-control"
                            value="<?php echo htmlspecialchars($edit_item['name']); ?>" required>
                    </div>
                    <div class="admin-form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="3"
                            required><?php echo htmlspecialchars($edit_item['description']); ?></textarea>
                    </div>
                    <div class="admin-form-group">
                        <label for="price">Price</label>
                        <input type="number" id="price" name="price" class="form-control" step="0.01" min="0"
                            value="<?php echo $edit_item['price']; ?>" required>
                    </div>
                    <div class="admin-form-group">
                        <label for="category_id">Category</label>
                        <select id="category_id" name="category_id" class="form-control" required>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" <?php echo ($category['id'] == $edit_item['category_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="admin-form-group">
                        <label for="image_url">Image URL</label>
                        <input type="text" id="image_url" name="image_url" class="form-control"
                            value="<?php echo htmlspecialchars($edit_item['image_url']); ?>">
                    </div>
                    <div class="admin-form-actions">
                        <a href="manage_menu.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" name="update_item" class="btn">Update Menu Item</button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <!-- Menu Items List -->
            <div class="admin-table">
                <div class="admin-table-header">
                    <h2 class="admin-table-title">Menu Items</h2>
                </div>
                <div class="admin-table-content">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Rating</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($menu_items)): ?>
                                <tr>
                                    <td colspan="7" style="text-align: center;">No menu items found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($menu_items as $item): ?>
                                    <tr>
                                        <td><?php echo $item['id']; ?></td>
                                        <td>
                                            <div
                                                style="width: 50px; height: 50px; background-image: url('<?php echo htmlspecialchars($item['image_url']); ?>'); background-size: cover; background-position: center;">
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                                        <td><?php echo htmlspecialchars($item['category_name']); ?></td>
                                        <td><?php echo format_currency($item['price']); ?></td>
                                        <td>
                                            <?php
                                            $rating = round($item['avg_rating'] ?? 0, 1);
                                            echo $rating . ' / 5.0 ';
                                            echo '(' . ((int) $item['review_count']) . ' reviews)';
                                            ?>
                                        </td>
                                        <td class="table-actions">
                                            <a href="manage_menu.php?edit=<?php echo $item['id']; ?>" class="btn">Edit</a>
                                            <a href="manage_menu.php?delete=<?php echo $item['id']; ?>" class="btn btn-danger"
                                                onclick="return confirm('Are you sure you want to delete this item?')">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
    </div>

    <script src="../js/main.js"></script>
</body>

</html>