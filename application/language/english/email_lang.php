<?php

$lang['email_must_be_array'] = "The email validation method must be passed an array.";
$lang['email_invalid_address'] = "Invalid email address: %s";
$lang['email_attachment_missing'] = "Unable to locate the following email attachment: %s";
$lang['email_attachment_unreadable'] = "Unable to open this attachment: %s";
$lang['email_no_recipients'] = "You must include recipients: To, Cc, or Bcc";
$lang['email_send_failure_phpmail'] = "Unable to send email using PHP mail().  Your server might not be configured to send mail using this method.";
$lang['email_send_failure_sendmail'] = "Unable to send email using PHP Sendmail.  Your server might not be configured to send mail using this method.";
$lang['email_send_failure_smtp'] = "Unable to send email using PHP SMTP.  Your server might not be configured to send mail using this method.";
$lang['email_sent'] = "Your message has been successfully sent using the following protocol: %s";
$lang['email_no_socket'] = "Unable to open a socket to Sendmail. Please check settings.";
$lang['email_no_hostname'] = "You did not specify a SMTP hostname.";
$lang['email_smtp_error'] = "The following SMTP error was encountered: %s";
$lang['email_no_smtp_unpw'] = "Error: You must assign a SMTP username and password.";
$lang['email_failed_smtp_login'] = "Failed to send AUTH LOGIN command. Error: %s";
$lang['email_smtp_auth_un'] = "Failed to authenticate username. Error: %s";
$lang['email_smtp_auth_pw'] = "Failed to authenticate password. Error: %s";
$lang['email_smtp_data_failure'] = "Unable to send data: %s";
$lang['email_exit_status'] = "Exit status code: %s";

$lang['email_reg_subject']				= 'D-diary: confirm registration';
$lang['email_reg_message']				= '<strong>Thank you, you have successfully registered an account in <a href="http://simplecal.loc">D-diary</a>!<br/><br/>To start using your diary please activate your new account by visiting the following link:</strong><br/>';
$lang['email_reg_message_2']			= '<br/> This activation link is valid till ';
$lang['email_reg_cancel']				= 'If you accidentaly recived this message, please cancel the registration using the following link:<br />';

$lang['email_new_pass_subject']			= 'D-diary: unlinking of this facebook account';
$lang['email_new_pass_user']			= 'Username: ';
$lang['email_new_pass_pass'] 			= 'Password: ';
$lang['email_new_pass_message']			= '<span><strong>This facebook profile was successfuly unlinked form your D-diary account.<br/>To get access to your D-diary account please use the following information:<br/></span><br/>';
$lang['email_new_pass_message_2']		= '<br/><span>This facebook profile can no longer be used to create a new D-diary account. It can only be linked to another D-diary account or back to your old account.</span>';
/* End of file email_lang.php */
/* Location: ./system/language/english/email_lang.php */