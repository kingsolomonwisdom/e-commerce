<?php
// Site configuration
define('SITE_NAME', 'Shopway');
define('SITE_DESCRIPTION', 'Your ultimate destination for the latest products with the best prices and service.');

// Base URL - should match the one in db.php
define('BASE_URL', 'http://localhost/maoni');

// Admin email
define('ADMIN_EMAIL', 'admin@shopway.com');

// Default pagination limit
define('DEFAULT_PAGINATION_LIMIT', 10);

// Upload paths
define('UPLOAD_PATH', $_SERVER['DOCUMENT_ROOT'] . '/maoni/assets/images/');
define('UPLOAD_URL', BASE_URL . '/assets/images/');

// Max upload size (in bytes) - 5MB
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024);

// Allowed image types
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/jpg', 'image/png', 'image/gif']);
?> 