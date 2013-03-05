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

$lang['email_reg_subject'] = 'D-diary: apstipriniet reģistrāciju';
$lang['email_reg_message'] = '<strong>Paldies, jūsu reģistrācija <a href="http://simplecal.loc">D-diary</a> bija veiksmīga!<br/><br/>Lai sākt lietot savu kalendāru lūdzu apstipriniet reģistrāciju izmantojot šo saiti:</strong><br/>';
$lang['email_reg_message_2'] = '<br/> Šī aktivācijas saite ir derģa līdz ';
$lang['email_reg_cancel'] = 'Ja jūs nejauši saņemāt šo e-pastu, jūs varat atcelt reģistrāciju izmantojot šo saiti:<br />';

$lang['email_new_pass_subject']	= 'D-diary: facebook konta atsaiste';
$lang['email_new_pass_user'] = 'Lietotājvārds: ';
$lang['email_new_pass_pass'] = 'Parole: ';
$lang['email_new_pass_message']	= '<span><strong>Šīs facebook profils bija veiksmīgi atslēgts no D-diary konta!<br/>Lai piekļūt savam D-diary kontam lūgums izmantot šo informāciju:<br/></span><br/>';
$lang['email_new_pass_message_2'] = '<br/><span>Šīs facebook profils jau nevar tikt izmantots jauna D-diary konta izveidei, tikai atkal piesaistīts jūsu vecam vai citam D-diary kontam.</span>';

/* End of file email_lang.php */
/* Location: ./system/language/english/email_lang.php */