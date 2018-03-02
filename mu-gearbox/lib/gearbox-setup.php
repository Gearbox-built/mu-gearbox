<?php

// Allow SVG upload
function gear_mime_types($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}

add_filter('upload_mimes', 'gear_mime_types');
