<?php
namespace MVC\Library;
require_once ("Http.php");
/*	
	* IdAlbum của Album (tượng trưng trong thư mục 1 cấp - không cho phép lồng cấp)
    * Tài khoản tải hình bị các giới hạn sau:
    * 
    * Kích thước Max ảnh 		: mỗi ảnh tải lên có dung lượng không quá 20 MB và không quá 50 Megapixel.
    * Kích thước Max Video 		: mỗi video tải lên có dung lượng không quá 1GB.
    * Số lượng Max Album 		: 20.000
    * Số lượng Max hình/Album	: 1.000
    * Tổng dung lượng			: 15 GB bản miễn phí, nếu đóng tiền 100 GB 60 USD/1 năm,  
    * 
	 		
	(1) login($username, $password);
	
	(2) setAlbumId($IdAlbums);	
	* Thiết lập gán IdAlbum hiện hành. 
	* Chúng ta có thể thiết lập IdAlbum dạng mảng, sau đó phương thức này sẽ lấy về Id ngẫu nhiên
	* 
	* @param string|array
		
	(3) upload($filePath);	
    * Thực hiện việc tải một hình lên và trà về url của ảnh up lên thành công
    *
    * @return ($IdPhoto, $Width, $Height, $Url)
    * @throws Exception nếu xuất hiện lỗi
     
	(4) deletePhoto($IdAlbum, $photoId);
	* Xóa một Photo dựa theo IdAlbum và IdPhoto
	*
	* @param string	IdPhoto
    * @return boolean TRUE nếu photo được xóa
    * @throws Exception nếu Http có lỗi
		
	(6) addAlbum($title, $access = 'public', $description = '');
	* Tạo mới 1 Album
	*
	* @param	string	Tiêu đề của Album
	* @param	string	mức độ truy cập public|private
	* @param	string	Mô tả của Album
	* @return	string  IdAlbum
    * @throws Exception if Http has an error
	
	(5) deleteAlbum($IdAlbum);
	* Xóa một Album bằng Id
	*
	* @param string	IdAlbum
    * @return boolean TRUE nếu đã được xóa
    * @throws Exception nếu Http có lỗi
		
*/
class Picasa{
	
	public $http;
	
	private $_Size 		= NULL;
	private $_AlbumId 	= 'default';
	private $_UserName 	= '';
    private $_Password 	= '';
    private $_ApiKey 	= '';
    private $_ImagePath = '';
    private $_ImageUrl 	= '';
	    	
    public function __construct(){        
        if (!session_id()) {
			session_start();
        }
        $this->http = new Http();
    }
	
	//Xử lí session để lưu các thông tin về dịch vụ
	function _getKeyForSession($key){return "picasa_" . $this->_UserName . $key;}	
	function set($key, $value){$key = $this->_getKeyForSession($key);$_SESSION[$key] = $value;}    
    function get($key){
		$key = $this->_getKeyForSession($key);
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        return FALSE;
	}	
	function _checkPermission($method){if( ! $this->get('cookieLogin')) {echo "không có quyền";}}
	
    public function login($username, $password){
		$username = preg_replace('#@.*?$#', '', $username);
		$this->_UserName = $username;
		$this->_Password = $password;
		return $this->_doLogin();
	}    
    protected function _doLogin(){
        if( ! $this->get('cookieLogin') OR $this->get('loginTime') + 300 < $_SERVER['REQUEST_TIME']) {
			$this->http->reset();
			$this->http->setParam(array(
				'accountType' 	=> 'HOSTED_OR_GOOGLE',  
				'Email' 		=> $this->_UserName,  
				'Passwd' 		=> $this->_Password,  
				'source'		=> __CLASS__,  
				'service'		=> 'lh2'
			));
			$this->http->execute('https://www.google.com/accounts/ClientLogin', 'POST');
			
            if($this->http->errors) {
                //$this->_throwHttpError(__METHOD__);
				echo "Lỗi thứ 1";
            }
            else if(preg_match('#Auth=([a-z0-9_\-]+)#i', $this->http->getResponseText(), $match)) {
				$this->set('cookieLogin', $match[1]);
				$this->set('loginTime', $_SERVER['REQUEST_TIME']);
			}
			else {
				$this->set('cookieLogin', NULL);
				echo $this->http->getResponseText();				
			}
		}
		return TRUE;	
    }
	public function setApi($apiKeys){$this->_apiKey = is_array($apiKeys) ? $apiKeys[array_rand($apiKeys, 1)] : $apiKeys;}	    
    public function setSize($size){$this->_Size = $size;}        
	public function setAlbumId($albumIds){$this->_AlbumId = is_array($albumIds) ? $albumIds[array_rand($albumIds, 1)] : $albumIds;}
    	
	public function upload($filePath){
        if (!$realFilePath = realpath($filePath)) {
			echo "Tập tin này không tồn tại";
        }
        if (getimagesize($realFilePath) === FALSE) {
            echo "Tập tin này không phải là tập tin ảnh";
        }
        $this->_ImagePath = $realFilePath;
        return $this->_doUpload();
    }
    protected function _doUpload(){	
        $this->_checkPermission(__METHOD__);
        $this->http->reset();	
		$this->http->setSubmitMultipart('related');
		$this->http->setHeader(array(
			"Authorization: GoogleLogin auth=" . $this->get('cookieLogin'),
			"MIME-Version: 1.0",
		));
		$this->http->setRawPost("Content-Type: application/atom+xml\r\n
			<entry xmlns='http://www.w3.org/2005/Atom'>
			<title>".preg_replace('#\..*?$#i', '', basename($this->_ImagePath))."</title>
			<category scheme=\"http://schemas.google.com/g/2005#kind\" term=\"http://schemas.google.com/photos/2007#photo\"/>
			</entry>
		 "); 
		$this->http->setParam(array(
			'data' => '@' . $this->_ImagePath
		));
		$this->http->execute('https://picasaweb.google.com/data/feed/api/user/'.$this->_UserName.'/albumid/' . $this->_AlbumId);
		
        if($this->http->errors) {
            //$this->_throwHttpError(__METHOD__);
			echo "Lỗi thứ 3";
        }
        else if($this->http->getResponseStatus() != 201) { //201  Created 			
			echo "Tải file lên bị lỗi ".$this->http->getResponseText();
		}
        $result = $this->http->getResponseText();
		
		//In ra xem cấu trúc của Photo		
		preg_match('#<gphoto:id>(\d+)</gphoto:id>#', $result, $match);
		$id = $match[1];
        preg_match('#<gphoto:width>(\d+)</gphoto:width>#', $result, $match);
		$width = $match[1];
		preg_match('#<gphoto:height>(\d+)</gphoto:height>#', $result, $match);
		$height = $match[1];
		preg_match('#src=\'([^\'"]+)\'#', $result, $match);
		$url = $match[1];
        
		$size = ($this->_Size !== NULL) ? $this->_Size :  max($width, $height);
        $url = str_replace(basename($url), 's' . $size . '/' . basename($url), $url);
        
		return array($id, $width, $height, $url);
    }	
    
    protected function _doTransload(){
        $this->_throwException(':method: Currently, this plugin doesn\'t support transload image.', array(
            ':method' => __METHOD__
        ));
    }
	
	/**
	 * Delete an album by photoId
	 *
	 * @param string	photoid
     * @return boolean TRUE if photo was deleted
     * @throws Exception if Http has an error
	*/
	public function deletePhoto($albumId, $photoId)
	{
		$this->_checkPermission(__METHOD__);		
		$this->http->setHeader(array(
			"Authorization: GoogleLogin auth=" . $this->get('cookieLogin'),
			"MIME-Version: 1.0",
			"GData-Version: 3.0",
			"If-Match: *"
		));
		$this->http->execute('https://picasaweb.google.com/data/entry/api/user/' . $this->_UserName. '/albumid/' . $albumId."/photoid/".$photoId, 'DELETE');
		if($this->http->errors) {
            //$this->_throwHttpError(__METHOD__);
			echo "Lỗi thứ 5 - Không xóa được hình";
        }
		return ($this->http->getResponseHeaders('status') == 200);
	}
    
    /**
	 * Delete an album by albumid
	 *
	 * @param string	albumid
     * @return boolean TRUE if album was deleted
     * @throws Exception if Http has an error
	*/
	public function deleteAlbum($albumId)
	{
		$this->_checkPermission(__METHOD__);
		
		$this->http->setHeader(array(
			"Authorization: GoogleLogin auth=" . $this->get('cookieLogin'),
			"MIME-Version: 1.0",
			"GData-Version: 3.0",
			"If-Match: *"
		));
		$this->http->execute('https://picasaweb.google.com/data/entry/api/user/' . $this->_UserName. '/albumid/' . $albumId, 'DELETE');
		if($this->http->errors) {
            //$this->_throwHttpError(__METHOD__);
			echo "Lỗi thứ 6 - Không xóa được Album";
        }
		return ($this->http->getResponseHeaders('status') == 200);
	}
	
	/**
	* Create new album and return albumId was created
	*
	* @param	string	the title of the album
	* @param	string	access public|private
	* @param	string	the description of the album
	* @return	string  AlbumId was created
    * @throws Exception if Http has an error
	*/
	public function addAlbum($title, $access = 'public', $description = '')
	{
		$this->_checkPermission(__METHOD__);
		
		$this->http->setHeader(array(
			"Authorization: GoogleLogin auth=" . $this->get('cookieLogin'),
			"MIME-Version: 1.0",	
		));
		$this->http->setMimeContentType("application/atom+xml");
		$this->http->setRawPost("<entry xmlns='http://www.w3.org/2005/Atom' xmlns:media='http://search.yahoo.com/mrss/' xmlns:gphoto='http://schemas.google.com/photos/2007'>
		  <title type='text'>" . $title. "</title>
		  <summary type='text'>" . $description . "</summary>
		  <gphoto:access>" . $access . "</gphoto:access>
		  <category scheme='http://schemas.google.com/g/2005#kind' term='http://schemas.google.com/photos/2007#album'></category>
		</entry>");
		$this->http->execute('https://picasaweb.google.com/data/feed/api/user/' . $this->_UserName, 'POST');
        
		if($this->http->errors) {
            $this->_throwHttpError(__METHOD__);
        }
		if(preg_match('#<id>.+?albumid/(.+?)</id>#i', $this->http->getResponseText(), $match)) {
			return $match[1];
		}
        return FALSE;
	}            
}