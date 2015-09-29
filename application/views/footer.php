<script>
// Polling script

var $alertmodal, $alerticon;
var timeoutid = null;
function waitForMsg(){
        /* This requests the url "msgsrv.php"
        When it complete (or errors)*/
		if (typeof $alertmodal == "undefined") $alertmodal = $('.bs-example-modal-md_alert');
		if (typeof $alerticon == "undefined") $alerticon = $('.header_alert');
		 
        $.ajax({
            type: "GET",
            url: "<?php echo base_url(); ?>index.php/home",

            async: true, /* If set to non-async, browser shows page as "Loading.."*/
            cache: false,
            timeout:5000, /* Timeout in ms */

            success: function(data){ /* called when request to barge.php completes */
				$dom = $(data);
				//console.log("GOT",$dom);
				var $domnew_modal = $dom.find('.bs-example-modal-md_alert');
				var $domnew_alert_icon = $dom.find('.header_alert');
				
				$alertmodal.html($domnew_modal.html());
				$alerticon.html($domnew_alert_icon.html());
                //addmsg("new", data); /* Add response to a .msg div (with the "new" class)*/
                timeoutid = setTimeout(
                    waitForMsg, /* Request next message */
                    5000 /* ..after 1 seconds */
                );
            },
            error: function(XMLHttpRequest, textStatus, errorThrown){
				console.log("ERRORED");
                //addmsg("error", textStatus + " (" + errorThrown + ")");
                timeoutid = setTimeout(
                    waitForMsg, /* Try again after.. */
                    15000); /* milliseconds (15seconds) */
            }
        });
    };
	
//$(function(){waitForMsg();});
function stop() { clearTimeout(timeoutid) };
</script>
		</div>
		<!--TUTUP WRAP-->		
	</body>
</html>