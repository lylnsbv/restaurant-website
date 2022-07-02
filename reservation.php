<?php

	session_start();
	require "admin/includes/db.php";

	$msg = "";
	$msg_adding = "";
	$head_title = "Успешно!";
	$error = false;
	$reserve = true;
	$modal_body = "";
	$alert_type = "danger";

	$required = array('first_name', 'last_name', 'email', 'phone', 'hour', 'total_people', 'pick_date', 'pick_time');

	// Loop over field names, make sure each one exists and is not empty

	foreach($required as $field) {
	  if (empty($_POST[$field])) {
	  	$error = true;
	  }
	}

	if(!$error) {

	    // -- start reservation parametrs --

	    $visitor_name = "";
	    $visitor_email = "";
	    $visitor_phone = "";
	    $total_people = "";
	    $pick_date = "";
	    $pick_time = "";
	    $hour = "";
	    $visitor_message = "";

	    if(isset($_POST['first_name'])) {
        	$visitor_name = filter_var($_POST['first_name'], FILTER_SANITIZE_STRING);
    	}

    	if(isset($_POST['last_name'])) {
        	$visitor_name .= " " . filter_var($_POST['last_name'], FILTER_SANITIZE_STRING);
    	}

    	if(isset($_POST['email'])) {
	        $visitor_email = str_replace(array("\r", "\n", "%0a", "%0d"), '', $_POST['email']);
	        $visitor_email = filter_var($visitor_email, FILTER_VALIDATE_EMAIL);	         
	    }

	    if(isset($_POST['phone'])) {
	        $visitor_phone = filter_var($_POST['phone'], FILTER_SANITIZE_NUMBER_INT);
	    }
	     
	    if(isset($_POST['hour'])) {
	        $hour = filter_var($_POST['hour'], FILTER_SANITIZE_NUMBER_INT);
	    }

	    if(isset($_POST['total_people'])) {
	        $total_people = filter_var($_POST['total_people'], FILTER_SANITIZE_NUMBER_INT);
	    }
		
	 
	    if(isset($_POST['pick_date'])) {
	        $pick_date = $_POST['pick_date'];
	    }

	    if(isset($_POST['pick_time'])) {
	        $pick_time = $_POST['pick_time'];
	    }

	    if(isset($_POST['suggestions'])) {
	        $visitor_message = $_POST['suggestions'];
	    }

	    //  -- end reservation parametrs --

	    // -- start add to db --


	    $check = $db->query("SELECT * FROM reservation WHERE no_of_guest='".$total_people."' AND email='".$visitor_email."' AND phone='".$visitor_phone."' AND date_res='".$pick_date."' AND time='".$pick_time."' LIMIT 1");


	    if($check->num_rows) {

	    	$msg = "<p style='padding: 15px; color: red; background: #ffeeee; font-weight: bold; font-size: 13px; border-radius: 4px; text-align: center;'>Вы уже сделали бронирование с такой информацией.</p><br/>";

	    	$head_title = "Ошибка!";
	    	$alert_type = "danger";
	    	$reserve = false;

	    }else{

	    	$insert = $db->query("INSERT INTO reservation(no_of_guest, hour, name, email, phone, date_res, time, suggestions) VALUES('".$total_people."', '".$hour."', '".$visitor_name."', '".$visitor_email."', '".$visitor_phone."', '".$pick_date."', '".$pick_time."', '".$visitor_message."')");

	    	if($insert) {
						
				$ins_id = $db->insert_id;
				
				$reserve_code = "UNIQUE_$ins_id".substr($visitor_phone, 3, 8);
				
				$msg = "<p style='padding: 15px; color: #235a23; background: #eeffee; font-weight: bold; font-size: 13px; border-radius: 4px; text-align: center;'>Бронирование успешно размещено. Ваш код бронирования: $reserve_code.</p><br/>";

				$head_title = "Успешно!";
				$alert_type = "success";
				$msg_adding = "Спасибо за бронирование у нас. Мы надеемся, что вам понравится.";
				$reserve = true;
				
				
			}else{
				
				$msg = "<p style='padding: 15px; color: #d41414; background: #ffeeee; font-weight: bold; font-size: 13px; border-radius: 4px; text-align: center;'>Не удалось сделать заказ. Пожалуйста, попробуйте еще раз.</p><br/>";

				$head_title = "Ошибка!";
				$alert_type = "danger";
				$msg_adding = "Сожалеем, но электронное письмо с подтверждением бронирования не прошло.";
				$reserve = false;
				
			}
	    }

	    // -- end add to db --

	    $modal_body = '<div class="modal-dialog" role="document">
		            	<div class="modal-content">            		
		              		<div class="modal-body">
			              		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			                    <div class="table-title">
			                        <h2>'.$head_title.'</h2>
			                        <br/>
			                        <div class="alert alert-'.$alert_type.'" role="alert">
			                        	'.$msg.'
			                        	'.$msg_adding.'
									</div>
			                    </div>              		
		        			</div>
		        		</div>
		        	</div>';

	    //  -- start send mail  --

	    //mail confign
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
	    $mail->isHTML(true);
	    $mail->CharSet = 'UTF-8';

	    $email_content = "<html><body>";
	    $email_content .= "<table style='font-family: Arial;'>
	    						<tbody>
	    							<tr>
	    								<td style='background: #eee; padding: 10px;'>Имя Посетителя</td>
	    								<td style='background: #fda; padding: 10px;'>$visitor_name</td>
	    							</tr>";

	    $email_content .= "<tr>
	    						<td style='background: #eee; padding: 10px;'>Телефон</td>
	    						<td style='background: #fda; padding: 10px;'>$visitor_phone</td>
	    					</tr>";	

	    $email_content .= "<tr>
	    						<td style='background: #eee; padding: 10px;'>Электронная Почта</td>
	    						<td style='background: #fda; padding: 10px;'>$visitor_email</td>
	    					</tr>";	

	    $email_content .= "<tr>
	    						<td style='background: #eee; padding: 10px;'>Выбрана Дата/Время</td>
	    						<td style='background: #fda; padding: 10px;'>$pick_date $pick_time</td>
	    					</tr>";

	    $email_content .= "<tr>
	    						<td style='background: #eee; padding: 10px;'>Всего Человек</td>
	    						<td style='background: #fda; padding: 10px;'>$total_people</td>
	    					</tr>";	    

	    $email_content .= "<tr>
	    						<td style='background: #eee; padding: 10px;'>Кол. Часов</td>
	    						<td style='background: #fda; padding: 10px;'>$hour</td>
	    					</tr>
	    					</tbody>
	    					</table>";

	    if($visitor_message != ""){					
	    	$email_content .= "<p style='font-family: Arial; font-size: 1.25rem;'>
	    				   			<strong>Специальный запрос от $visitor_name &mdash;</strong>
	    				   			<i> $visitor_message</i>
	    				   		</p>";

	    }

	    $email_content .= '</body></html>';
	    

	    $mail->AddAddress($mail->Username);
		$mail->SetFrom($visitor_email, $visitor_name);
		$mail->Subject = $visitor_email;
		$mail->Body = $email_content;

		if($reserve) {
	     
		    if($mail->Send()) {

		        echo $modal_body;
		    } else {

		        echo '<div class="modal-dialog" role="document">
		            	<div class="modal-content">            		
		              		<div class="modal-body">
			              		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			                    <div class="table-title">
			                        <h2>Wrong!</h2>
			                        <br/>
			                        <div class="alert alert-danger" role="alert">
		  								Сожалеем, но электронное письмо с подтверждением бронирования не прошло.
									</div>
			                    </div>              		
		        			</div>
		        		</div>
		        	</div>';
		    }
	    } else {
	    	echo $modal_body;
	    }

	    //  -- end send mail  --
	     
	} else {
	    echo '<p>Все поля обязательны для заполнения.</p>';
	}
?>