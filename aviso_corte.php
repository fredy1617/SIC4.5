<?php
	$mail->setFrom('no-replay@gmail.com', 'Cortes SIC');
	#$mail->addAddress('rocio.gonzalez@sicsom.com');
	$mail->addAddress('ernestina.duenez@sicsom.com');
	$mail->addAddress('alfredo.martinez@sicsom.com');

	$mail->isHTML(true);
	$mail->Subject = 'Aviso de: '.$asunto;
	$mail->Body = $Aviso;
?>