<script type="text/javascript" language="javascript">
// <![CDATA[
var titel = new Array(1,2,3,4,5);
var updateHint = function (v, range) {
	$('ratinghint_'+range).innerHTML = titel[v - 1];
}

var rate = function (v, range) {
<? if ($can_rate) : ?>
	new Ajax.Updater('rating_'+range,"ajax_dispatcher.php?ajaxcmd=set_rating",
                {
                        method: "post",
                        parameters: { val: v, range_id: range },
                        onFailure: function(t) {alert('Error ' + t.status + ' -- ' + t.statusText); },
                        onSuccess: function(t) { 
				$('ratinghint_'+range).innerHTML = '<?=_("Bewertung gespeichert!")?>';
			},
                        on404: function(t) {alert('Error 404: location "' + t.statusText + '" was not found.'); }
                }
	);
<? endif ?>
}
// ]]>
</script>
