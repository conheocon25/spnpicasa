<?php
	require_once('Picasa.php');
    echo "Welcome to Picasa Class";
	$P = new \MVC\Library\Picasa();
	$P->login('picasavinhlong@gmail.com', 'admin123456789');
	$Id = $P->addAlbum("Cafe Van Xuan");
	$P->setAlbumId($Id);
    $Arr = $P->upload('test.png');
	print_r($Arr);
?>