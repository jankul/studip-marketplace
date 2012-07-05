<IMG SRC="images/thumb_down.png" ALT="<?=_("negativer")?>" TITLE="<?=_("negativer")?>">
<span class="inline-rating">
<ul class="<? if ($can_rate) : ?>star-rating<? else : ?>star-rated<? endif ?>" onMouseOut="$('ratinghint_<?=$range_id?>').innerHTML = '&nbsp;';">
	<li class="<? if ($can_rate) : ?>current-rating<? else : ?>current-rated<? endif ?>" style="width:<?=$rating_width?>%;">Currently <?=$current?>/<?=MAX_RATING_VALUE?> Stars.</li>
	<li><a href="javascript:void(0);" onClick="rate(1,'<?=$range_id?>');" title="1" class="one-star" onMouseOver="updateHint(1,'<?=$range_id?>');">1</a></li>
	<li><a href="javascript:void(0);" onClick="rate(2,'<?=$range_id?>');" title="2" class="two-stars" onMouseOver="updateHint(2,'<?=$range_id?>');">2</a></li>
	<li><a href="javascript:void(0);" onClick="rate(3,'<?=$range_id?>');" title="3" class="three-stars" onMouseOver="updateHint(3,'<?=$range_id?>');">3</a></li>
	<li><a href="javascript:void(0);" onClick="rate(4,'<?=$range_id?>');" title="4" class="four-stars" onMouseOver="updateHint(4,'<?=$range_id?>');">4</a></li>
	<li><a href="javascript:void(0);" onClick="rate(5,'<?=$range_id?>');" title="5" class="five-stars" onMouseOver="updateHint(5,'<?=$range_id?>');">5</a></li>
</ul></span>
<IMG SRC="images/thumb_up.png" ALT="<?=_("positiver")?>" TITLE="<?=_("positiver")?>">
