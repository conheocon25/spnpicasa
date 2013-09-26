<?php
	include 'Loader.php';
    \ChipVN\Loader::registerAutoLoad();
	
	//Khởi tạo lớp dịch vụ
	$uploader = \ChipVN\Image_Uploader::factory('Picasa');
    $uploader->login('quanlycafevinhlong@gmail.com', 'admin123456789');
    
	//Gán Album và up lên 1 ảnh đã được chọn.
    $uploader->setAlbumId('5927841217528180033');	                         
    //$arr = $uploader->upload('test.png');
	//print_r($arr);
	
	//xóa thử 5927875886455859442
	$uploader->deletePhoto('5927841217528180033', "5927875886455859442");
	
	//Tạo Album và up mới 1 ảnh vào album
	//$IdAlbum = $uploader->addAlbum("Cafe Van Xuân");	
	//$uploader->setAlbumId($IdAlbum);
    //echo $uploader->upload('test.png');
	
	//Cập nhật Album
	
	//Xóa Album
	
?>