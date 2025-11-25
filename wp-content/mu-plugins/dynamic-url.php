<?php
/**
 * Plugin Name: Dynamic Site URL
 * Description: Allows WordPress to work from any hostname (localhost, IP, or .local) for multi-device development access
 * Version: 1.0.0
 * Author: MyDefenseLaw Development
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get current request's scheme and host
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost:8088';

// Security: Only allow local network patterns
// - localhost and 127.0.0.1 (loopback)
// - 192.168.x.x (Class C private)
// - 10.x.x.x (Class A private)
// - 172.16-31.x.x (Class B private)
// - *.local (mDNS/Bonjour)
$is_allowed = (
    strpos($host, 'localhost') === 0 ||
    strpos($host, '127.0.0.1') === 0 ||
    preg_match('/^(192\.168\.|10\.|172\.(1[6-9]|2[0-9]|3[0-1])\.)/', $host) ||
    preg_match('/\.local(:\d+)?$/', $host)
);

if (!$is_allowed) {
    return;
}

// Sanitize host (allow alphanumeric, hyphens, dots, colons for port)
$host = preg_replace('/[^a-zA-Z0-9\-\.:]/','', $host);
$dynamic_url = $scheme . '://' . $host;

// Define WordPress home and site URLs dynamically
// These constants override database values when defined
if (!defined('WP_HOME')) {
    define('WP_HOME', $dynamic_url);
}
if (!defined('WP_SITEURL')) {
    define('WP_SITEURL', $dynamic_url);
}
