<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="picasa, image hosting, free photo sharing and video sharing. Upload your photos and share them with friends and family."/>
<meta name="keywords" content="picasa, album, free image hosting, image hosting, video hosting, photo image hosting site"/>
<title>Free Images Hosting - Powered by Movie2Share.NET</title>
<link id="page_favicon" href="favicon.ico" rel="icon" type="image/x-icon" />
<script type="text/javascript" src="script.js"></script> 
<link rel="stylesheet" href="style.css" type="text/css" />
</head>
<body>
<div id="wrapper">
	<div class="block">
    	<h1>Upload ảnh miễn phí</h1>
        <div>
            <a href="https://picasaweb.google.com/lwa7411/Movie2Share?authuser=0&feat=directlink" target="_blank">Thư Viện Ảnh</a>
        </div>
        
        <div>
        	Đóng dấu ảnh: 
            <input type="radio" name="watermark" id="watermark" onclick="setWatermark(true);" checked="checked"/> Có
        	<input type="radio" name="watermark" onclick="setWatermark(false);"/> Không
        </div>
        <div>
        	Chọn logo:
        	<label><input type="radio" name="logo" id="logo" onclick="setLogo(1)" checked="checked" value="1" />Logo bé</label>
            
        </div>
        <div>
        	Resize ảnh:
            <select id="resize" onchange="chooseResize();">
            	<option value="0">No resize</option>
                <option value="1">100x...</option>
                <option value="2">150x...</option>
                <option value="3">320x...</option>
                <option value="4" selected="selected">640x...</option>
                <option value="5">800x...</option>
                <option value="6">1024x...</option>
            </select>
            (Resize theo chiều rộng của bức ảnh. Chú ý ảnh chỉ thu nhỏ chứ ko phóng to)
        	
        </div>
        
        
        
            Nhấn Browser để chọn file upload (có thể chọn nhiều ảnh):<br />
            <div id="inputfile" style="height:20px;width:250px;">
                 <div style="float: left;">
                    <input type="text" style="height:23px;border:1px solid #ccc;width:170px;">
                </div>
                <div style="float: left;" id="flash_upload"></div>
            </div>
            <script>setWatermark(true);</script>

    </div>    
   	<div class="block">
        <div id="result"></div>
        <div id="loading"></div>
        <div id="getcode" style="display:none">
            <div>
                <a href="javascript:showcode('bbcode');">Chèn vào Forum</a> | 
                <a href="javascript:showcode('html');">Chèn vào Website</a> | 
                <a href="javascript:showcode('none');">Link trực tiếp</a>
             </div>
            <div><textarea id="showcode" style="height:130px;width:100%" onclick="this.select();"></textarea></div>
        </div>
     </div>   
</div><!--/#wrapper-->
<div align="center">© 2011 - <a href="http://Movie2Share.NET/" title="Free Images Hosting">Movie2Share.NET</a></div>
</body>
</html>