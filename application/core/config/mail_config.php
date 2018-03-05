<?php

namespace tinyframe\core\config;

# ---------------------------------------------------------------
# EMAIL CONFIGURATION
# ---------------------------------------------------------------
#
# Set your email configuration here
# NOTE: Leave them blank if you not use email

define('MAIL_HOST', 'mail.bsu.edu.ru'); // SMTP servers
define('MAIL_USER', 'nikitin_o@bsu.edu.ru'); // SMTP username
define('MAIL_PASSWORD', '153043qaz'); // SMTP password
define('MAIL_PORT', 25); // TCP port to connect to
define('MAIL_FROM', 'nikitin_o@bsu.edu.ru'); // Mailer
define('MAIL_FROM_NAME', 'Admin'); // Mailer name
define('MAIL_REPLY', 'nikitin_o@bsu.edu.ru'); // Reply-to
define('MAIL_REPLY_NAME', 'Admin'); // Reply-to name
/*
	SMTP debugging:
	0 = off (for production use)
	1 = client messages
	2 = client and server messages
*/
define('SMTP_DEBUG', 0);
