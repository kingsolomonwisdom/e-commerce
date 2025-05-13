<?php
// Script to create slideshow images
$sourceImages = ['airpods.jpg', 'iphone.jpg', 'watch.jpg'];
$targetImages = ['slide1.jpg', 'slide2.jpg', 'slide3.jpg'];
$sourcePath = __DIR__ . '/assets/images/';
$targetPath = $sourcePath; // Same directory

echo "<h1>Creating Slideshow Images</h1>";

// Check if the source directory exists
if (!is_dir($sourcePath)) {
    die("Error: Source directory not found: $sourcePath");
}

// Create each slideshow image
for ($i = 0; $i < count($sourceImages); $i++) {
    $source = $sourcePath . $sourceImages[$i];
    $target = $targetPath . $targetImages[$i];
    
    // Check if source image exists
    if (!file_exists($source)) {
        echo "<p>Error: Source image not found: {$sourceImages[$i]}</p>";
        continue;
    }
    
    // Copy the file
    if (copy($source, $target)) {
        echo "<p>Created: {$targetImages[$i]} from {$sourceImages[$i]}</p>";
    } else {
        echo "<p>Error: Failed to create {$targetImages[$i]}</p>";
    }
}

echo "<p>Done! <a href='index.php'>Return to homepage</a></p>";
?> 