<?php

/**
 * Rajdoot Nivedan Media - Hostinger Shared Hosting Root Entry Point
 *
 * This entry file allows running Laravel seamlessly when uploaded directly into
 * public_html on Hostinger or any cPanel / LiteSpeed shared hosting.
 */
$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? ''
);

// If request is for an existing static asset inside public/, allow direct server delivery
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    return false;
}

require_once __DIR__.'/public/index.php';
