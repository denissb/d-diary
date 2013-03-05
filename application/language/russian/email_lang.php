<?php
/* https://github.com/chernavin/CodeIgniter-Russian-Language */

$lang['email_must_be_array']			= 'В метод проверки адреса электронной почты должен быть передан массив.';
$lang['email_invalid_address']			= 'Некорректный адрес электронной почты: %s';
$lang['email_attachment_missing']		= 'Не удалось найти вложение: %s';
$lang['email_attachment_unreadable']	= 'Невозможно открыть вложение: %s';
$lang['email_no_recipients']			= 'Вы должны включить получателей: To, Cc или Bcc';
$lang['email_send_failure_phpmail']		= 'Невозможно отправить электронную почту с помощью PHP mail(). Ваш сервер может быть не настроен для отправки почты с помощью этого метода.';
$lang['email_send_failure_sendmail']	= 'Невозможно отправить электронную почту с помощью PHP Sendmail. Ваш сервер может быть не настроен для отправки почты с помощью этого метода.';
$lang['email_send_failure_smtp']		= 'Невозможно отправить электронную почту с помощью PHP SMTP. Ваш сервер может быть не настроен для отправки почты с помощью этого метода.';
$lang['email_sent']						= 'Ваше сообщение было успешно отправлено по следующему протоколу: %s';
$lang['email_no_socket']				= 'Невозможно открыть сокет для Sendmail. Пожалуйста, проверьте настройки.';
$lang['email_no_hostname']				= 'Вы не указали имя хоста SMTP.';
$lang['email_smtp_error']				= 'Была обнаружена следующая ошибка SMTP: %s';
$lang['email_no_smtp_unpw']				= 'Ошибка: Вы должны указать имя пользователя и пароль SMTP.';
$lang['email_failed_smtp_login']		= 'Невозможно отправить команду AUTH LOGIN. Ошибка: %s';
$lang['email_smtp_auth_un']				= 'Сбой при проверке имени пользователя. Ошибка: %s';
$lang['email_smtp_auth_pw']				= 'Сбой при проверке пароля. Ошибка: %s';
$lang['email_smtp_data_failure']		= 'Невозможно отправить данные: %s';
$lang['email_exit_status']				= 'Статус код: %s';

$lang['email_reg_subject']				= 'D-diary: подтверждение регистрации';
$lang['email_reg_message']				= '<strong>Спасибо, вы успешно зарегестрировались в приложении <a href="http://simplecal.loc">D-diary!</a><br/>. Чтобы начать пользоватся своим ежедневником пожалуйсто активируйте учетную запись по ссылке:</strong><br/>';
$lang['email_reg_message_2']			= 'Срок активации учетной записи до ';
$lang['email_reg_cancel']				= 'Если вы случайно получили это письмо, пожалуйсто отмените регистрацию по ссылке:<br />';

$lang['email_new_pass_subject']			= 'D-diary: отсоединение профиля facebook';
$lang['email_new_pass_user']			= 'Имя пользователя: ';
$lang['email_new_pass_pass'] 			= 'Пароль: ';
$lang['email_new_pass_message']			= '<span><strong>Этот профиль facebook был успешно отключен от вашей учетной записи в D-diary.<br/>Чтобы получить доступ к вашей учетной записи в D-diary пожалуйсто используйте следующую информацию:<br/></span><br/>';
$lang['email_new_pass_message_2']		= '<br/><span>Этот профиль facebook уже не может быть использован для создания нового пользователя в D-diary, только снова привязан к вашей или другой учетной записи.</span>';
/* End of file email_lang.php */
/* Location: ./system/language/russian/email_lang.php */
