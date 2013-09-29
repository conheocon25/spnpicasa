$(document).ready(function(){
	//add upload button
	var self = $("#uploader");
	if(self.attr("auto")=="false")
	{
		self.after("<input type='button' value='" + (self.attr("buttonCaption")==undefined ? '':self.attr("buttonCaption")) + "' id='start' class='uploadButton' />");
		$("#start").click(function(){
			self.uploadifyUpload();					
		});
	}
	var fileExt = "";
	if(self.attr("fileExt")!=undefined)
	{
		var fileExtSplit = self.attr("fileExt").split(",");
		for(var i=0; i<fileExtSplit.length; i++)
		{
			fileExt += "*."+fileExtSplit[i];
			if(i+1<fileExtSplit.length)
				fileExt += ";";
		}
	}
	var fileDesc = "";
	if(self.attr("fileDesc")!=undefined)
		fileDesc = self.attr("fileDesc");
	var buttonImg = "";
	if(self.attr("button")!=undefined)
		buttonImg = self.attr("button");
	var bwidth = 110;
	if(self.attr("bwidth")!=undefined)
		bwidth = self.attr("bwidth");
	var bheight = 30;
	if(self.attr("bheight")!=undefined)
		bheight = self.attr("bheight");
	var customQueueId = "";
	if(self.attr("queueId")!=undefined)
		customQueueId = self.attr("queueId");
	var maxSize = "";
	if(self.attr("maxSize")!=undefined)
		maxSize = self.attr("maxSize");
	self.uploadify({
		'uploader'  : 'upload.swf',
		'script'    : 'upload.php',
		'scriptData': {'action':'upload', 'path':self.attr('path')},
		'queueID'	: customQueueId,
		'cancelImg' : 'close.png',
		'fileExt'	: fileExt,
		'fileDesc'	: fileDesc,
		'buttonImg'	: buttonImg,
		'wmode'		: 'transparent',
		'width'		: bwidth,
		'height'	: bheight,
		'sizeLimit'	: maxSize,
		'auto'      : (self.attr("auto")=="false" ? false:true),
		'multi'		: (self.attr("multi")=="true" ? true:false),
		'onSelect' : function(event, queueId, fileObj){
			if(self.attr("multi")!="true")
				if($("#"+queueId).html()!="")
					if(customQueueId!="")
						$("#"+customQueueId).html("");
					else
						$("#uploaderQueue").html("");
		},
		'onComplete' : function(event, queueId, fileObj, response, data){
			if(self.attr("afterUpload")=="link")
			{
				$("#uploader" + queueId + " .uploadifyProgress").animate({opacity: 0}, 500, function(){$(this).remove();});
				if(response!="1")
					$("#uploader" + queueId).append(response);
				else
					$("#uploader" + queueId).html("<div class='cancel'><input class='button_cancel' name='removeFile' fileName='"+fileObj.name.replace("'", "%27")+"' type='button'></div><a href='" + self.attr("path") + fileObj.name.replace("'", "%27") + "' target='_blank'><span class='fileName'>"+fileObj.name+"</span></a>");
				return false;
			}
		},
		'onAllComplete' : function(event, data){
			if(self.attr("afterUpload")!=undefined && self.attr("afterUpload")!="link")
			{
				if(data.errors==0)
					$("#uploaderQueue").html(self.attr("afterUpload"));
			}
		},
		'onError': function (event, queueID ,fileObj, errorObj) {
			/* if you got some problems just uncomment this code for debugging */
			/*var msg;
			if (errorObj.info == 404)
			   msg = 'Could not find upload script.';
			else if(errorObj.type === "HTTP")
			   msg = errorObj.type+ ": " +errorObj.info;
			else if(errorObj.type ==="File Size")
			   msg = fileObj.name+ ": " +errorObj.type;
			else
			   msg = errorObj.type+ ": " +errorObj.info;
			alert(msg);
			return false;*/
		}
	});
	$("[name=removeFile]").live("click", function(){
		var selfRemove = $(this);
		$.ajax({
					url: "upload.php",
                    data: "action=remove&path="+self.attr("path")+"&filename="+selfRemove.attr("fileName"),
					type: "POST",
                    async: false,
                    success: function(data){
							if(data!="")
								alert(data);
							else
								selfRemove.parent().parent().animate({opacity: 0}, 500, function(){$(this).remove();});
					}
			});
	});
});

