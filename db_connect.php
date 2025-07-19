<?php 
    
    
	$conn = mysqli_connect('localhost', 'root', '', 'loan');

	
	if(!$conn){
		echo 'Connection error: '. mysqli_connect_error();
	}

?>