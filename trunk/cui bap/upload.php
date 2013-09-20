<?php 

define('DIR', dirname(__FILE__));

$tempfolder = DIR . '/temp/';
$sitename = 'picasavinhlong.com';
$isWatermark = ($_REQUEST['watermark'] == 'yes') ? true : false; 
	
if($_FILES['Filedata']){
	$error = false;
	$file = $_FILES['Filedata'];
	$filePath = $tempfolder . $sitename . time().'.'.end(explode('.',basename($file['name'])));
	if (!isset($file) || !is_uploaded_file($file['tmp_name'])) {
		$error = 'Invalid Upload';
	}

	if (!$error && $file['size'] > 2 * 1024 * 1024)
	{
		$error = 'Please upload only files smaller than 2Mb!';
	}

	if (!$error && !($size = @getimagesize($file['tmp_name']) ) )
	{
		$error = 'Please upload only images, no other files are supported.';
	}

	if (!$error && !in_array($size[2], array(1, 2, 3, 7, 8) ) )
	{
		$error = 'Please upload only images of type JPEG, GIF or PNG.';
	}

	if($error) die('image='.$error);
	
	//upload file to server
	move_uploaded_file($file['tmp_name'], $filePath);
	//resize
	$resizes = array(
		1	=> 100, 
		2	=> 150,
		3	=> 320,
		4	=> 640,
		5	=> 800,
		6	=> 1024
	);
	$resize = intval($_REQUEST['resize']);
	if(in_array($resize, array_keys($resizes)))
	{
		if(!file_exists(DIR. '/inc/phpThumb/ThumbLib.inc.php'))
		{
			throw new Exception('Missing file <em>'.DIR. '/inc/phpThumb/ThumbLib.inc.php</em>');
		}
		else
		{
			require_once(DIR. '/inc/phpThumb/ThumbLib.inc.php');
			$thumb = PhpThumbFactory::create($filePath);
			$thumb->resize($resizes[$resize], 0);
			$thumb->save($filePath);
		}
	}
	//watermark
	if($isWatermark && ($size[0] > 150) && ($size[1] > 35)){
		$watermark_path = DIR . '/logo1.png';
		$watermark_id = imagecreatefrompng($watermark_path);
		imagealphablending($watermark_id, false);
		imagesavealpha($watermark_id, true);
		
		$info_img = getimagesize($filePath);
		$info_wtm = getimagesize($watermark_path);
		$fileType = strtolower($info_img['mime']);
		
		$image_w 		= $info_img[0];
		$image_h 		= $info_img[1];
		$watermark_w	= $info_wtm[0];
		$watermark_h	= $info_wtm[1];
		$is_gif = false;	
		switch($fileType)
		{
			case	'image/gif':	$is_gif = true;break;
			case	'image/png': 	$image_id = imagecreatefrompng($filePath);	break;
			default:				$image_id = imagecreatefromjpeg($filePath);	break;
		}
		if(!$is_gif){
			/* Watermark in the bottom right of image*/
			$dest_x  = ($image_w - $watermark_w); 
			$dest_y  = ($image_h  - $watermark_h);
			
			/* Watermark in the middle of image 
			$dest_x = round(( $image_height / 2 ) - ( $logo_h / 2 ));
			$dest_y = round(( $image_w / 2 ) - ( $logo_w / 2 ));
			*/
			imagecopy($image_id, $watermark_id, $dest_x, $dest_y, 0, 0, $watermark_w, $watermark_h);
			
			//override to image
			switch($fileType)
			{
				case	'image/png': 	imagepng ($image_id, $filePath); 		break;
				default:				imagejpeg($image_id, $filePath); 		break;
			}       		 
			imagedestroy($image_id);
			imagedestroy($watermark_id);
		}
	}
	
	// load classes
	require_once 'Zend/Loader.php';
	Zend_Loader::loadClass('Zend_Gdata');
	Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
	Zend_Loader::loadClass('Zend_Gdata_Photos');
	Zend_Loader::loadClass('Zend_Http_Client');	
	
	$serviceName = Zend_Gdata_Photos::AUTH_SERVICE_NAME;
	$user = "picasavinhlong@gmail.com";
	$pass = "admin123456789";
	$albumId = "5925180481188894705";
	$client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $serviceName);

	// update the second argument to be CompanyName-ProductName-Version
	$gp = new Zend_Gdata_Photos($client, "Google-DevelopersGuide-1.0");
	$username = "default";
	$filename = $filePath;
	$photoName = preg_replace('/\s+/','_',basename($file['name']));
	$photoCaption = $photoName;
	$photoTags = "";

	$fd = $gp->newMediaFileSource($filename);
	$fd->setContentType(strtolower($size['mime']));

	// Create a PhotoEntry
	$photoEntry = $gp->newPhotoEntry();

	$photoEntry->setMediaSource($fd);
	$photoEntry->setTitle($gp->newTitle($photoName));
	$photoEntry->setSummary($gp->newSummary($photoCaption));

	// add some tags
	$keywords = new Zend_Gdata_Media_Extension_MediaKeywords();
	$keywords->setText($photoTags);
	$photoEntry->mediaGroup = new Zend_Gdata_Media_Extension_MediaGroup();
	$photoEntry->mediaGroup->keywords = $keywords;

	// We use the AlbumQuery class to generate the URL for the album
	$albumQuery = $gp->newAlbumQuery();

	$albumQuery->setUser($username);
	$albumQuery->setAlbumId($albumId);

	// We insert the photo, and the server returns the entry representing
	// that photo after it is uploaded
	$insertedEntry = $gp->insertPhotoEntry($photoEntry, $albumQuery->getQueryUrl()); 
	$contentUrl = "";
	//$firstThumbnailUrl = "";

	if ($insertedEntry->getMediaGroup()->getContent() != null) {
	  $mediaContentArray = $insertedEntry->getMediaGroup()->getContent();
	  $contentUrl = $mediaContentArray[0]->getUrl();
	}	
	if(file_exists($filePath))
	{
		unlink($filePath);
	}		
	if($contentUrl) echo 'image=' . $contentUrl;
	else echo 'image=Upload failed.';
}

if($_POST['url']){
	echo 'Nothing';
}
?>