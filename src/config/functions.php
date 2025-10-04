<?php
/**
 * Redirects to a specified URL.
 * @param string $url The URL to redirect to.
 */
function redirect($url)
{
    header("Location: " . $url);
    exit();
}


/**
 * Hashes a password using PHP's password_hash function.
 *
 * @param string $password The plain text password.
 * @return string The hashed password.
 */
function hashPassword($password)
{
    return password_hash($password, PASSWORD_DEFAULT);
}


/**
 * Verifies a password against a hash.
 *
 * @param string $password The plain text password.
 * @param string $hash The hashed password from the database.
 * @return bool True if the password matches, false otherwise.
 */
function verifyPassword($password, $hash)
{
    return password_verify($password, $hash);
}


/**
 * Helper function to escape HTML special characters for output.
 * @param string $string The string to escape.
 * @return string The escaped string.
 */
function htmlEscape($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Generates a new CSRF (Cross-Site Request Forgery) token.
 *
 * Uses a cryptographically secure random byte generator to
 * create a 64-character hexadecimal token.
 *
 * @return string The newly generated CSRF token.
 */
function new_csrf_token()
{
    return bin2hex(random_bytes(32));
}

/**
 * Compares a stored CSRF token with a submitted token.
 *
 * Uses a timing attack–safe comparison to ensure that the
 * provided tokens match securely.
 *
 * @param string $csrf1 The original CSRF token stored on the server.
 * @param string $csrf_submitted The CSRF token submitted by the client.
 * @return bool True if the tokens match, false otherwise.
 */
function match_csrf_token($csrf1, $csrf_submitted)
{
    return hash_equals($csrf1, $csrf_submitted);
}


?>

