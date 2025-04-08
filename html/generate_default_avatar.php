<?php
// Create a 200x200 image
$image = imagecreatetruecolor(200, 200);

// Set background color (light gray)
$bg_color = imagecolorallocate($image, 240, 240, 240);
imagefill($image, 0, 0, $bg_color);

// Set text color (dark gray)
$text_color = imagecolorallocate($image, 120, 120, 120);

// Add a circle
$circle_color = imagecolorallocate($image, 200, 200, 200);
imagefilledellipse($image, 100, 100, 180, 180, $circle_color);

// Add a person icon
$icon_color = imagecolorallocate($image, 150, 150, 150);
// Head
imagefilledellipse($image, 100, 70, 50, 50, $icon_color);
// Body
imagefilledrectangle($image, 75, 95, 125, 140, $icon_color);
// Arms
imagefilledrectangle($image, 50, 100, 75, 120, $icon_color);
imagefilledrectangle($image, 125, 100, 150, 120, $icon_color);
// Legs
imagefilledrectangle($image, 75, 140, 90, 170, $icon_color);
imagefilledrectangle($image, 110, 140, 125, 170, $icon_color);

// Save the image
imagepng($image, 'uploads/profile_pictures/default_profile.png');

// Free up memory
imagedestroy($image);

echo "Default profile picture generated successfully!";
?> 