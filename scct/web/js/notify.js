
/**
 * Created by eigyan on 3/22/2018.
 */
//message counter
msg_ctr = 0;

 $.ctGrowl = {
 timer: false,

 init: function(position) {
 $("#ctGrowlContainer").css(position);
 },
 
 msg: function(message, title, status,sticky=false) {
//reusable objects
 var container = $("#ctGrowlContainer");
 var clone = $("#ct-growl-clone");
 var tag = "#ctmg" + msg_ctr;

//attach cloned element and update message
 $("ul", clone).attr("id", "ctmg" + msg_ctr);
 $("li.title", clone).text(title);
 $("li.msg", clone).text(message);

 // Append this message to the queue
 container.fadeIn(5000).append(clone.html());

 $(tag).addClass(status);

 if(!this.sticky){
 	setTimeout(function() {
 		$(tag).fadeOut(2000)
 	}, 4000);
 }

// Attach close button event
 $(tag + " li span.close").on("click", function() { $(tag).fadeOut(5000); });

//increment message counter
 msg_ctr++;

 }
};

