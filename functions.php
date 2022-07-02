<?php
	

	// send email with mail

	/*if(isset($_POST)){
		$to = "youremail@example.com";
		$subject = $_POST['subject'];
		$message = $_POST['message'];
		$headers = 'From: email@example.com' . "\r\n" . 
		'Reply-To: youremail@example.com' . "\r\n" . 
		'X-Mailer: PHP/' . phpversion();

		$result = mail($to, $subject, $message, $headers);

		if($result){
        	echo "<div class='alert alert-success alert-dismissible' role='alert'>
        			<button type='button' class='close'><label aria-hidden='true'>×</label></button>
					<label class='alert-message'>Thank You! Your inquiry submitted successfully. We will contact you very soon.</label>
        		  </div>";
    	}else {
        	echo "<div class='alert alert-danger alert-dismissible' role='alert'>
    				<button type='button' class='close'><label aria-hidden='true'>×</label></button>
					<label class='alert-message'>Oops! Something went wrong. We couldn't send your message. Please try again later.</label>
    			 </div>";
    	}
	}*/

	// send email with PHPMailer

	require_once('phpmailer/PHPMailerAutoload.php'); 
	 //include("phpmailer/class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded

	$mail = new PHPMailer();	

	// SMTP server $mail->SMTPDebug = 2; // enables SMTP debug information (for testing) // 1 = errors and messages // 2 = messages only

	$mail->IsSMTP();
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = "ssl or tls";
    $mail->Host = "your_email_smtp";
    $mail->Port = email_port;
    $mail->Username = "your_email";
    $mail->Password = "your_email_password_or_app_password";
    $mail->CharSet = 'UTF-8';
	
	$first_name = $_POST['first_name'];
	$last_name = $_POST['last_name'];
	$from_email = $_POST['email'];
	$subject = $_POST['subject'];
	$message = $_POST['message'];	

    $mail->AddAddress($mail->Username);
	$mail->SetFrom($from_email, $first_name . " " . $last_name);
	$mail->Subject = $from_email . ", " . $subject;
	$mail->Body = $message;	

	if($mail->Send()){
		echo "<div class='alert alert-success alert-dismissible' role='alert'>
        		<button type='button' class='close'><label aria-hidden='true'>×</label></button>
				<label class='alert-message'>Благодарю вас! Ваш запрос успешно отправлен. Мы свяжемся с вами в ближайшее время.</label>
        	</div>";
		
	} else {
		echo "<div class='alert alert-danger alert-dismissible' role='alert'>
    			<button type='button' class='close'><label aria-hidden='true'>×</label></button>
				<label class='alert-message'>Ой! Что-то пошло не так. Мы не смогли отправить ваше сообщение. Пожалуйста, повторите попытку позже.</label>
    		</div>"; 		
	}
?>