<?php
// New plain-text password
$newPassword = "admin12345";

// Generate a bcrypt hash
$hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

// Output the hashed password
echo $hashedPassword;
?>
<!-- to add images from database
UPDATE menu_items
SET image_url = 'assets/menu/bruschetta.jpg'
WHERE name = 'Bruschetta'; -->