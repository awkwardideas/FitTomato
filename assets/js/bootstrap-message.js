//Requires jQuery and Bootstrap
var Bootstrap_Messages = (function() {
	// "Private" variables
	var _messages;
	        
	// constructor
	function Bootstrap_Messages(){
            if($(".bootstrap-message-container").length == 0){
                var msgBox = $("<div/>").addClass("bootstrap-message-container");
                        msgBox.css("position","fixed").css("top","20px").css("right","20px").css("z-index","9999");
                $("body").prepend(msgBox);
            }
		
	}
	
        
	Bootstrap_Messages.prototype.addSuccess = function(msg){
		this.addMessage(msg,"success");
	}
        
	Bootstrap_Messages.prototype.addInfo = function(msg){
		this.addMessage(msg,"info");
	}
	
	Bootstrap_Messages.prototype.addWarning = function(msg){
		this.addMessage(msg,"warning");
	}
	
	Bootstrap_Messages.prototype.addDanger = function(msg){
		this.addMessage(msg,"danger");
	}
	
	Bootstrap_Messages.prototype.addMessage = function(msg, type){
		var msgBox = $("<div/>").addClass("alert alert-dismissable alert-"+type);
                var msgDismiss = $("<button/>").addClass("close").attr("type","button").attr("data-dismiss","alert").attr("aria-hidden","true").html("&times;").appendTo(msgBox);
                $(msgBox).append(msg);
		$(msgBox).appendTo(".bootstrap-message-container").delay(8000).animate(
			{opacity: .01},
			'normal',
			function() {
    			$(this).animate(
					{height: 0, opacity: 0},
					'normal',
					function() {
		    			$(this).remove();
					}
				);
			}
		);
	}
	
	return Bootstrap_Messages;	
})();