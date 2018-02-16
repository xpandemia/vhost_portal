<?php

namespace tinyframe\core\helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail_Helper
{
	/*
		Mails processing
	*/
	
	/**
     * Sends email.
     *
     * @return string
     */
	public function sendEmail($to, $to_name, $subject, $body, $ccs = null, $bccs = null, $body_plain = null, $attachment = null, $attachment_name = null)
	{
		$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
		try {
			// Server settings
		    $mail->SMTPDebug = 2;                                 // Enable verbose debug output
		    $mail->isSMTP();                                      // Set mailer to use SMTP
		    $mail->Host = MAIL_HOST;  							  // Specify main and backup SMTP servers
		    $mail->SMTPAuth = true;                               // Enable SMTP authentication
		    $mail->Username = MAIL_USER;                 		  // SMTP username
		    $mail->Password = MAIL_PASSWORD;                      // SMTP password
		    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
		    $mail->Port = MAIL_PORT;                              // TCP port to connect to

		    // Recipients
		    $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
		    $mail->addAddress($to, $to_name); // Add a recipient, Name is optional
		    $mail->addReplyTo(MAIL_REPLY, MAIL_REPLY_NAME);
		    // Copies
		    if (isset($ccs) && is_array($ccs)) {
				foreach ($ccs as $cc) {
					$mail->addCC($cc);	
				}
			}
			// Hidden copies
			if (isset($bccs) && is_array($bccs)) {
				foreach ($bccs as $bcc) {
					$mail->addBCC($bcc);
				}
			}

		    // Attachments
		    if (!empty($attachment)) {
				$mail->addAttachment($attachment, $attachment_name); // Add attachments, Name is optional
			}

		    // Content
		    $mail->isHTML(true);                                  // Set email format to HTML
		    $mail->CharSet = 'UTF-8';							  // Set email encoding
		    $mail->Subject = $subject;
		    $mail->Body = $body;
		    if (!empty($body_plain)) {
				$mail->AltBody = $body_plain;
			}

		    $mail->send();
		    echo 'Message has been sent';
		    return TRUE;
		}
		catch (Exception $e) {
			echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
			return FALSE;
		}
	}
}
