<script>

//$j(document).ready(function() {	
var launchWindow = function(id, plugin_id) {
	//select all the a tag with name equal to modal
	// $j('a[name=modal]').click(function(e) {
	//$j(o).click(function(e) {
		//Cancel the link behavior
		/*$j(id).click(function(e) {
			e.preventDefault();
		});*/
		
		//Get the A tag
		//var id = $j(this).attr('href');
		elPosition = $j(id).position();
	
		//Get the screen height and width
		var maskHeight = $j(document).height();
		var maskWidth = $j(window).width();
	
		//Set heigth and width to mask to fill up the whole screen
		$j('#mpmask').css({'width':maskWidth,'height':maskHeight});
		
		//transition effect		
		$j('#mpmask').fadeIn(1000);	
		$j('#mpmask').fadeTo("slow",0.8);	
	
		//Get the window height and width
		var winH = $j(window).height();
		var winW = $j(window).width();
              
		//Set the popup window to center
		/*$j(id).css('top',  winH/2-$j(id).height()/2);
		$j(id).css('left', winW/2-$j(id).width()/2);*/
		//$j(id).css('top',  elPosition.top+$j(id).height()/4);
		$j(id).css('top',  jQuery(window).height()/2-$j(id).height()/2+elPosition.top+$j(id).height()/4);
		//$j(id).css('left', $j(id).width()/2);
		$j(id).css('left', jQuery(window).width()/2-$j(id).outerWidth()/2);
	
		//transition effect
		$j(id).fadeIn(2000); 
		
		$j.ajax({
			type: 'POST',
			url: 'ajax_dispatcher.php',
			data: { ajaxcmd: 'get_rezension', plugin_id: plugin_id},
			cache: false,
	                dataType: 'html',
			success: function(html_resp, textStatus) {
				$j('#previewcontent').html(html_resp);
			},
			error: function(xhr, textStatus, errorThrown) {
				alert('An error occurred! ' + (errorThrown ? errorThrown : xhr.status));
			}
		});
	
	//});
	$j(document).keyup(function(e) {  
		if(e.keyCode == 13) {  
			$j('#mpmask').hide();  
			$j('.mpwindow').hide();  
		}  
	});  
	
	//if close button is clicked
	$j('.mpwindow .close').click(function (e) {
		//Cancel the link behavior
		e.preventDefault();
		$j('#previewcontent').html('<IMG SRC="images/wait24trans.gif">');
		$j('#mpmask').hide();
		$j('.mpwindow').hide();
	});		
	
	//if mask is clicked
	$j('#mpmask').click(function () {
		$j('#previewcontent').html('<IMG SRC="images/wait24trans.gif">');
		$j(this).hide();
		$j('.mpwindow').hide();
	});			

}
	
//});

</script>

<div id="mpboxes">

<div id="mpdialog" class="mpwindow">
<SPAN STYLE="font-weight:bold;"><?=dgettext('r','Details')?></SPAN> | 
<a href="#" class="close"><?=dgettext('r','schliessen')?></a>
<DIV STYLE="border-top:1px solid gray; overflow:scroll; padding-top:5px; margin-top:5px; min-width:698px; width:698px; max-width:698px; min-height:480px; height:480px; max-height:480px;">
  <DIV ID="previewcontent"><IMG SRC="images/wait24trans.gif"></DIV>
</DIV>
</div>
  
<!-- Mask to cover the whole screen -->
  <div id="mpmask"></div>
</div>
