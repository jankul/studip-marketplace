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
</script>

<FORM NAME="content_edit" METHOD="POST" ACTION="?dispatch=save_content">
<INPUT TYPE="hidden" NAME="key" VALUE="<?=$c->getKey()?>">
<TABLE BORDER=0>
  <TR>
    <TD COLSPAN=2><H3>Inhalt (<?=$c->getKey()?>) bearbeiten</H3></TD>
  </TR>
  <TR>
    <TD COLSPAN=2>&nbsp;</TD>
  </TR>
  <TR>
    <TD COLSPAN=2 STYLE="width:150px; vertical-align:top; font-weight:bold; font-size:12px;">Inhalt:</TD>
  </TR>
  <TR>
    <TD COLSPAN=2><TEXTAREA NAME="content_txt" ID="content_txt" class="mceAdvanced" STYLE="height:400px; width:100%;"><?=$c->getContentTxt()?></TEXTAREA></TD>
  </TR>
  <TR>
    <TD COLSPAN=2>&nbsp;</TD>
  </TR>
  <TR>
    <TD COLSPAN=2 STYLE="text-align:center;"><INPUT TYPE="image" <?=makeButton('speichern','src')?>> <IMG <?=makeButton('abbrechen','src')?> onClick="location.href='?';"></TD>
  </TR>
</TABLE>
</FORM>
