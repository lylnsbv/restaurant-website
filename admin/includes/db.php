<?php 
	
	$db = new mysqli("localhost", "root", "", "mfors");

	if($db->connect_errno) {
		
		echo "ПОЖАЛУЙСТА, СВЯЗАТЬСЯ С НАМИ, КАК МЫ РАБОТАЕМ НА НАШЕМ САЙТЕ !!!! ПОЖАЛУЙСТА, ВЕРНИТЕСЬ ПОЗЖЕ";
		
	}
	
?>