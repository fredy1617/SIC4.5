<?php
include('Mailer/src/PHPMailer.php');
include('Mailer/src/SMTP.php');
include('Mailer/src/Exception.php');

	$mail = new PHPMailer\PHPMailer\PHPMailer();

	$mail->isSMTP();
	$mail->SMTPDebug = 0;
	$mail->SMTPAuth = true;
	$mail->SMTPSecure = 'tls';
	$mail->Host = 'smtp.gmail.com';
	$mail->Port = 587;
	$mail->Username = 'alfredomartinez6510@gmail.com';
	$mail->Password = 'n1Fredy17@';

	$mail->setFrom('no-replay@gmail.com', 'Cortes SIC');
	$mail->addAddress('alfredo.martinez@sicsom.com');
	$mail->addAddress('gabriel.valles@sicsom.com');

	$mail->isHTML(true);
	$mail->Subject = $asunto;
	$mail->Body = $Mensaje;

?>