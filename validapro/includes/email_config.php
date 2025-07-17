<?php
// Configuração exclusiva de e-mail para o ValidaPro

if (!defined('SMTP_HOST')) define('SMTP_HOST', 'smtp.zoho.com');
if (!defined('SMTP_USER')) define('SMTP_USER', 'validapro@agencialed.com');
if (!defined('SMTP_PASS')) define('SMTP_PASS', 'Valida@2025'); // Troque pela senha real ou senha de app Zoho
if (!defined('SMTP_SECURE')) define('SMTP_SECURE', 'tls');
if (!defined('SMTP_PORT')) define('SMTP_PORT', 587);
if (!defined('FROM_EMAIL')) define('FROM_EMAIL', 'validapro@agencialed.com');
if (!defined('FROM_NAME')) define('FROM_NAME', 'ValidaPro'); 