<?php
// Page configuration
$pageTitle = "Error - Shopway";

// Include header
require_once 'db.php';
require_once 'includes/header.php';

// Get error code
$errorCode = isset($_GET['code']) ? (int)$_GET['code'] : 404;

// Set error message based on code
switch ($errorCode) {
    case 500:
        $errorTitle = "Internal Server Error";
        $errorMessage = "Something went wrong on our server. We're working to fix it.";
        break;
    case 403:
        $errorTitle = "Access Forbidden";
        $errorMessage = "You don't have permission to access this resource.";
        break;
    case 404:
    default:
        $errorTitle = "Page Not Found";
        $errorMessage = "The page you're looking for doesn't exist or has been moved.";
        $errorCode = 404; // Default to 404 for unknown codes
        break;
}
?>

<div class="container" style="padding: 50px 20px; text-align: center;">
    <div style="max-width: 600px; margin: 0 auto;">
        <div style="font-size: 120px; color: #f1c40f; margin-bottom: 20px;">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        
        <h1 style="font-size: 36px; margin-bottom: 15px;"><?php echo $errorCode; ?> - <?php echo $errorTitle; ?></h1>
        
        <p style="color: #777; margin-bottom: 30px; font-size: 18px;">
            <?php echo $errorMessage; ?>
        </p>
        
        <div style="margin-top: 40px;">
            <a href="<?php echo BASE_URL; ?>" style="display: inline-block; background-color: #007bff; color: white; padding: 12px 25px; border-radius: 5px; text-decoration: none; font-weight: 500; margin-right: 15px;">
                <i class="fas fa-home" style="margin-right: 8px;"></i> Go Home
            </a>
            
            <a href="javascript:history.back()" style="display: inline-block; background-color: #6c757d; color: white; padding: 12px 25px; border-radius: 5px; text-decoration: none; font-weight: 500;">
                <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Go Back
            </a>
        </div>
    </div>
</div>

<?php
// Include footer
require_once 'includes/footer.php';
?> 