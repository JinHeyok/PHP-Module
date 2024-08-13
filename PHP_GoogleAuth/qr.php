<?php
	header("Content-Type: image/jpeg");
	$fp = file_get_contents($_GET['images']);
	print_r($fp);
	// dump the picture and stop the script
	//fpassthru($fp);
	//exit;
?>
