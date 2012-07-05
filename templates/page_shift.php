<script type="text/javascript">
<? $offset = rand(0,$instance->getPageCount()-1); ?>
$j(function()
	{
		var offset = <?=$offset?>;
		var maxOffset = <?=($instance->getPageCount() - 1)?>;
		var pageId = "<?=$page_uid?>";
	
		function prevPage()
		{
			if(offset == 0)
				//return;
				offset = <?=$instance->getPageCount()?>;
			offset--;
			var curOffset = offset;
			$j("#page_" + pageId).effect("slide", { direction: 'right', mode: 'hide' }, 250, function() { loadPage(curOffset); });
			$j("#page_" + pageId).effect("slide", { direction: 'left' }, 250);
		};
		
		function nextPage()
		{
			if(offset == maxOffset)
				//return;
				offset = -1;
			offset++;
			var curOffset = offset;
			$j("#page_" + pageId).effect("slide", { direction: 'left', mode: 'hide' }, 250, function() { loadPage(curOffset); });
			$j("#page_" + pageId).effect("slide", { direction: 'right' }, 250);
		};
		
		function loadPage(curOffset)
		{
			$j("#page_title_" + pageId).html("Lade...");
			$j("#page_desc_" + pageId).html("Lade..."); // <center><img src='page_loader.gif'/></center>

			$j.get("ajax_dispatcher.php", {ajaxcmd: 'page_load', page_dispatcher: '<?=$page_dispatcher?>', page_number: curOffset}, function(data)
			{
				var c = data.split("<?=$instance->getDispatherSign()?>");
				if(c.length == 2)
				{
					if(curOffset == offset)
					{
						$j("#page_title_" + pageId).html(c[0]);
						$j("#page_desc_" + pageId).html(c[1]);
					}
				}
			}, 'text');
		};
		
		$j("#page_buttonPrev_" + pageId).click(function()
		{
			prevPage();
			return false;
		});
		
		$j("#page_buttonNext_" + pageId).click(function()
		{
			nextPage();
			return false;
		});
	});
</script>

<div class="page_holder">
	<div id="page_<?=$page_uid?>" class="ui-widget-content ui-corner-all page">
		<h3 id="page_title_<?=$page_uid?>" class="ui-widget-header ui-corner-all"><?=($instance->getPageCount() == 0 ? 'Keine Inhalte' : $instance->getPageTitle($offset))?></h3>
		<div id="page_desc_<?=$page_uid?>"><?=($instance->getPageCount() == 0 ? 'Momentan gibt es keine Inhalte, die dargestellt werden könnten.' : $instance->getPageContent($offset))?></div>
	</div>
	<br />
	<a href="#" id="page_buttonPrev_<?=$page_uid?>" class="sliderbutton ui-state-default ui-corner-all" style="float: left"><?=(isset($page_prev_txt) ? $page_prev_txt : 'Zurück')?></a>
	<a href="#" id="page_buttonNext_<?=$page_uid?>" class="sliderbutton ui-state-default ui-corner-all" style="float: right"><?=(isset($page_next_txt) ? $page_next_txt : 'Weiter')?></a>
</div>
<DIV STYLE="clear:both;"></DIV>
