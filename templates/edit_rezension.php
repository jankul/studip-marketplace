<script type="text/javascript">
tinyMCE.init({
	mode : "textareas",
	theme : "advanced",
	editor_selector : "mceAdvanced",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	plugins : "safari,pagebreak,style,layer,table,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
	theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
	theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
	theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
	cleanup_on_startup : true,
        invalid_elements : "script"
});

var checkInput = function() {
	if ($('rezension').value == '') {
		alert('Bitte füllen Sie alle aus!');
		return false;
	} else {
		return true;
	}
}
</script>
<IMG <?=makeButton('zurueck','src')?> onClick="location.href='?dispatch=edit_plugin&plugin_id=<?=$p->getPluginId()?>'">
<DIV CLASS="topic" STYLE="margin-top:10px;">Rezension zu einem Plugin verfassen</DIV>
<FORM NAME="rezension" METHOD="POST" ACTION="?dispatch=save_rezension" onSubmit="return checkInput();">
<INPUT TYPE="hidden" NAME="plugin_id" VALUE="<?=$p->getPluginId()?>">
<TABLE BORDER=0 WIDTH="100%">
  <TR>
    <TD STYLE="width:150px; vertical-align:top; font-weight:bold; font-size:12px;">Plugin-Name: </TD>
    <TD><SPAN STYLE="font-size:12px; font-weight:bold;"><?=$p->getName()?></SPAN></TD>
  </TR>
  <TR>
    <TD COLSPAN=2>&nbsp;</TD>
  </TR>
  <TR>
    <TD COLSPAN=2 STYLE="width:150px; vertical-align:top; font-weight:bold; font-size:12px;">Rezension:</TD>
  </TR>
  <TR>
    <TD COLSPAN=2><TEXTAREA NAME="rezension_txt" ID="rezension_txt" class="mceAdvanced" STYLE="height:400px; width:100%;"><?=$p->getRezension()?></TEXTAREA></TD>
  </TR>
  <TR>
    <TD COLSPAN=2>&nbsp;</TD>
  </TR>
  <TR>
    <TD COLSPAN=2 STYLE="text-align:center;"><INPUT TYPE="image" <?=makeButton('speichern','src')?>> <IMG <?=makeButton('abbrechen','src')?> onClick="location.href='?dispatch=edit_plugin&plugin_id=<?=$p->getPluginId()?>'"></TD>
  </TR>
</TABLE>
</FORM>
