<?=$css->GetHoverJSFunction()?>
<LINK REL="stylesheet" HREF="css/tags_autocompleter.css" TYPE="text/css" />
<script type="text/javascript">
var charslimitation = 500;

var updateCharsLeft = function() {
	$j('#chars_left').text(charslimitation - document.plugin.short_description.value.length);
        if (document.plugin.short_description.value.length > charslimitation) {
                $('chars_left').style.color = 'red';
        } else {
                $('chars_left').style.color = 'gray';
        }
}

var checkInputLength = function() {
        if ($('short_description').value.length > charslimitation) {
                alert("<?=_("Die Eingabe ist zu lang, bitte kürzen!")?>");
                return false;
        } else {
                return true;
        }
}


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
	if (jQuery(':hidden[class="sel_categories"]').length == 0 || $('titel').value == '' || $('license').value == '' || $('short_description').value == '') {
		alert('Bitte füllen Sie alle Pflichtfelder aus!');
		return false;
	} else {
		return checkInputLength();
	}
}

var getCurrentCategories = function() {
	$j.ajax({
		url: '<?=$GLOBALS['BASE_URI']?>ajax_dispatcher.php?ajaxcmd=get_current_categories&plugin_id=<?=$p->getPluginId()?>',
		cache: false,
		dataType: 'html',
		success: function(data) {
			$j('#current_categories').html(data);
		}
	});
}

var getAvailableCategories = function() {
	$j.ajax({
		url: '<?=$GLOBALS['BASE_URI']?>ajax_dispatcher.php?ajaxcmd=get_available_categories&plugin_id=<?=$p->getPluginId()?>',
		cache: false,
		dataType: 'html',
		success: function(data) {
			$j('#available_categories').html(data);
		}
	});
}

var removeCategory = function(cid) {
	$j('#c_'+cid).hide().remove();
	$j('#ca_'+cid).fadeIn()
}

var addCategory = function(cid) {
	$j('#ca_'+cid).hide();
	$j.ajax({
		url: '<?=$GLOBALS['BASE_URI']?>ajax_dispatcher.php?ajaxcmd=get_category_item&category_id='+cid,
		cache: false,
		dataType: 'html',
		success: function(data) {
			$j(data).appendTo('#current_categories');
		}
	});
}

var removeParticipant = function(uid) {
	$j('#pa_'+uid).hide();
	$j.ajax({
		url: '<?=$GLOBALS['BASE_URI']?>ajax_dispatcher.php?ajaxcmd=remove_participant&user_id='+uid+'&plugin_id=<?=$p->getPluginId()?>',
		cache: false,
		dataType: 'html',
		success: function(data) {
		}
	});
}


$j(window).load(function () {
	getAvailableCategories();
	getCurrentCategories();
	new Ajax.Autocompleter("tagsautocomplete", "tagsautocomplete_choices", "<?=$GLOBALS['BASE_URI']?>ajax_dispatcher.php?ajaxcmd=tag_completer", {
	        paramName: "value",
        	minChars: 2
	});
	updateCharsLeft();
});

</script>

<FORM NAME="plugin" METHOD="POST" ACTION="?dispatch=save_plugin" onSubmit="return checkInput();">
<INPUT TYPE="hidden" NAME="plugin_id" VALUE="<?=$p->getPluginId()?>">
<? if ($GLOBALS['PERM']->have_perm('admin')) : ?>
<FIELDSET STYLE="border:2px solid red; padding:10px;">
  <LEGEND STYLE="font-weight:bold; font-size:12px;">Admin-Bereich</LEGEND>
  <DIV STYLE="display:inline; float:left; margin-left:4px; text-align:left;">
    <SPAN STYLE="font-weight:bold; font-size:12px;">Benutzer zuweisen: </SPAN>
    <SELECT NAME="new_user_id" ID="new_user_id" SIZE="1" STYLE="width:150px;">
<? foreach ($GLOBALS['DBM']->getAllUsers() as $u) : ?>
     <OPTION VALUE="<?=$u->getUserId()?>" <?=($u->getUserId() == $p->getUserId() ? " SELECTED STYLE=\"color:gray;\"" : "")?>><?=htmlReady(UserManagement::getFullnameByUserId($u->getUserId()))?> (<?=htmlReady($u->getUsername())?>)</OPTION>
<? endforeach ?>
    </SELECT>&nbsp;<IMG <?=makeButton('zuweisen','src')?> STYLE="cursor:pointer;" onClick="location.href='?dispatch=set_plugin_user&plugin_id=<?=$p->getPluginId()?>&user_id='+$('new_user_id').value;">
  </DIV>
  <DIV STYLE="display:inline; float:right; margin-left:4px; text-align:right;"><IMG <?=makeButton('rezension','src')?> onClick="location.href='?dispatch=edit_rezension&plugin_id=<?=$p->getPluginId()?>'"></DIV>
  <DIV STYLE="display:inline; float:right; text-align:right;">
<? if ($p->getApproved() == 0) : ?>
    <IMG <?=makeButton('freischalten','src')?> onClick="location.href='?dispatch=do_clearing&plugin_id=<?=$p->getPluginId()?>'">
<? else : ?>
    <IMG <?=makeButton('sperren','src')?> onClick="if (confirm('Soll dieses Plugin wirklich gesperrt werden?')){location.href='?dispatch=do_suspend&plugin_id=<?=$p->getPluginId()?>'}">
<? endif ?>
  </DIV>
<!-- SPAN STYLE="font-weight:bold; font-size:12px;">Klassifikation: </SPAN>
<SELECT NAME="classification" SIZE="1">
<? foreach (array('none','firstclass','secondclass') as $cl) : ?>
  <OPTION VALUE="<?=$cl?>" <?=($p->getClassification() == $cl ? 'SELECTED' : '')?>><?=$cl?></OPTION>
<? endforeach ?>
</SELECT -->
</FIELDSET>
<? endif ?>
<TABLE BORDER=0 WIDTH="100%">
  <TR>
    <TD COLSPAN=2>
<? if (!$p->getApproved() && $p->getPluginId()) : ?>
      <!-- DIV STYLE="float:right;"><IMG SRC="images/icons/delete.png" ALT="Plugin noch nicht freigegeben" TITLE="Plugin noch nicht freigegeben"></DIV -->
      <?=MessageBox::error("Das Plugin ist noch nicht freigegeben. Die Freigabe erfolgt durch einen Pluginbeauftragten.")?>
<? endif ?>
<? if (!$p->getPluginId()) : ?>
      <?=MessageBox::info(_("F&uuml;llen Sie alle mit einem roten Sternchen markierten Felder aus, um Ihr Plugin zu beschreiben. Alle anderen Felder sind optional."))?>
<? endif ?>
      <DIV STYLE="clear:both;"></DIV>
      <DIV CLASS="topic">Grunddaten: <SPAN STYLE="color:red; font-weight:bold;">*</SPAN></DIV>
    </TD>
  </TR>
<? if ($p->getPluginId()) : ?>
  <TR>
    <TD STYLE="font-size:12px; font-weight:bold; vertical-align:top;">Autor: </TD><TD><?=Avatar::getAvatar($p->getUserId())->getImageTag(Avatar::SMALL)?> <A HREF="?dispatch=show_profile&username=<?=UserManagement::getUsernameByUserId($p->getUserId())?>&plugin_id=<?=$p->getPluginId()?>"><?=UserManagement::getFullnameByUserId($p->getUserId())?></A></TD>
  </TR>
  <TR><TD STYLE="font-size:12px; font-weight:bold; vertical-align:top;">Mitwirkende: </TD>
      <TD STYLE="font-size:12px;">
<? $parts = array(); ?>
<? array_push($parts, $p->getUserId()); ?>
<? if (count($users = $p->getParticipants())) : ?>
<? foreach ($users as $u) : ?>
<? array_push($parts, $u->getUserId()); ?>
       <SPAN ID="pa_<?=$u->getUserId()?>"><TABLE BORDER=0><TR><TD STYLE="vertical-align:middle;"><IMG SRC="images/trash.gif" STYLE="cursor:pointer;" onClick="removeParticipant('<?=$u->getUserId()?>');"></TD><TD STYLE="vertical-align:middle;"><?=Avatar::getAvatar($u->getUserId())->getImageTag(Avatar::SMALL)?></TD><TD STYLE="vertical-align:middle;"> <A HREF="?dispatch=show_profile&username=<?=UserManagement::getUsernameByUserId($u->getUserId())?>&plugin_id=<?=$p->getPluginId()?>"><?=UserManagement::getFullnameByUserId($u->getUserId())?></A></TD></TR></TABLE><BR></SPAN>
<? endforeach ?>
<? endif ?>
       <SELECT NAME="new_participant_id" ID="new_participant_id" SIZE="1" STYLE="width:150px;">
<? foreach ($GLOBALS['DBM']->getAllUsers() as $u) : ?>
         <? if (!in_array($u->getUserId(),$parts) || !in_array($p->getUserId(),$parts)) : ?>
         <OPTION VALUE="<?=$u->getUserId()?>"><?=htmlReady(UserManagement::getFullnameByUserId($u->getUserId()))?> (<?=htmlReady($u->getUsername())?>)</OPTION>
         <? endif ?>
<? endforeach ?>
       </SELECT>&nbsp;<IMG <?=makeButton('zuweisen','src')?> STYLE="cursor:pointer;" onClick="location.href='?dispatch=set_plugin_participant&plugin_id=<?=$p->getPluginId()?>&user_id='+$('new_participant_id').value;">
     </TD>
   </TR>
<? endif ?>
  <TR>
    <TD STYLE="width:150px; vertical-align:top; font-weight:bold; font-size:12px;">Titel: </TD>
    <TD><INPUT TYPE="text" STYLE="width:500px" MAXLENGTH=255 NAME="titel" ID="titel" VALUE="<?=htmlReady($p->getName())?>"> <SPAN STYLE="color:red; font-weight:bold;">*</SPAN></TD>
  </TR>
  <TR>
    <TD STYLE="width:150px; vertical-align:top; font-weight:bold; font-size:12px;">Lizenz: </TD>
    <TD>
      <SELECT NAME="template" STYLE="width:230px;" SIZE="1" onChange="document.plugin.license.value=document.plugin.template[document.plugin.template.selectedIndex].value;">
        <OPTION VALUE=""><?=_("ausw&auml;hlen oder wie Eingabe")?> --&gt;</OPTION>
<? foreach (array('GPL','LGPL','MIT','Apache','Creative Commons') as $l) : ?>
        <OPTION VALUE="<?=$l?>"><?=$l?></OPTION>
<? endforeach ?>
      </SELECT>
      <INPUT TYPE="text" STYLE="width:265px" MAXLENGTH=255 NAME="license" ID="license" VALUE="<?=htmlReady($p->getLicense())?>"> <SPAN STYLE="color:red; font-weight:bold;">*</SPAN>
    </TD>
  </TR>
  <TR>
    <TD STYLE="width:150px; vertical-align:top; font-weight:bold; font-size:12px;">Im Einsatz bei: </TD>
    <TD><INPUT TYPE="text" STYLE="width:500px" MAXLENGTH=255 NAME="in_use" ID="in_use" VALUE="<?=htmlReady($p->getInUse())?>"></TD>
  </TR>
  <TR>
    <TD STYLE="width:150px; vertical-align:top; font-weight:bold; font-size:12px;">Homepage-URL: </TD>
    <TD><INPUT TYPE="text" STYLE="width:500px" MAXLENGTH=2000 NAME="url" ID="url" VALUE="<?=htmlReady($p->getUrl())?>"></TD>
  </TR>
  <TR>
    <TD STYLE="width:150px; vertical-align:top; font-weight:bold; font-size:12px;">Sprache: </TD>
    <TD>
      <INPUT TYPE="radio" NAME="language" VALUE="de" <?=($p->getLanguage() == 'de' ? 'CHECKED' : '')?>><IMG SRC="images/languages/lang_de.gif" ALT="Deutsch" TITLE="Deutsch">&nbsp;&nbsp;
      <INPUT TYPE="radio" NAME="language" VALUE="en" <?=($p->getLanguage() == 'en' ? 'CHECKED' : '')?>><IMG SRC="images/languages/lang_en.gif" ALT="Englisch" TITLE="Englisch">&nbsp;&nbsp;
      <INPUT TYPE="radio" NAME="language" VALUE="de_en" <?=($p->getLanguage() == 'de_en' ? 'CHECKED' : '')?>><IMG SRC="images/languages/lang_de_en.gif" ALT="Deutsch/Englisch" TITLE="Deutsch/Englisch">
    </TD>
  </TR>
  <TR>
    <TD COLSPAN=2><DIV STYLE="margin-top:15px; margin-bottom:15px;"></DIV></TD>
  </TR>
  <TR>
    <TD COLSPAN=2>
      <DIV CLASS="topic">Kategorien: <SPAN STYLE="color:red; font-weight:bold;">*</SPAN></DIV>
      <TABLE BORDER=0 WIDTH="100%">
        <TR>
          <TD WIDTH="50%" STYLE="vertical-align:top;">
            <DIV CLASS="category_head">Bestehende Zuordnungen:</DIV>
            <DIV ID="current_categories"><CENTER><IMG SRC="images/wait24trans.gif"></CENTER></DIV>
          </TD>
          <TD WIDTH="50%" STYLE="vertical-align:top;">
            <DIV CLASS="category_head">Bitte w&auml;hlen:</DIV>
            <DIV ID="available_categories"><CENTER><IMG SRC="images/wait24trans.gif"></DIV>
          </TD>
        </TR>
      </TABLE>
    </TD>
  </TR>
  <TR>
    <TD COLSPAN=2><DIV STYLE="margin-top:15px; margin-bottom:15px;"></DIV></TD>
  </TR>
  <TR>
    <TD COLSPAN=2>
      <DIV CLASS="topic">Tags:</DIV>
	<INPUT TYPE="text" ID="tagsautocomplete" NAME="tags" VALUE="" MAXLENGTH="255" STYLE="width:500px;">
        <div id="tagsautocomplete_choices" class="tagsautocomplete"></div>
        <BR><SPAN STYLE="font-size:10px;">Tags bitte mit <SPAN STYLE="font-weight:bold;">Komma</SPAN> trennen</SPAN>
<? if (count($p->getTags()) > 0) : ?>
        <BR><SPAN STYLE="font-size:10px;">|</SPAN>
<? foreach ($p->getTags() as $t) : ?>
          <SPAN STYLE="font-size:10px;"><?=htmlReady($t)?> <A HREF="?dispatch=remove_ptag&plugin_id=<?=$p->getPluginId()?>&tag=<?=urlencode($t)?>"><IMG SRC="images/trash2.gif" BORDER=0></A> | </SPAN>
<? endforeach ?>
<? endif ?>
    </TD>
  </TR>
  <TR>
    <TD COLSPAN=2><DIV STYLE="margin-top:15px; margin-bottom:15px;"></DIV></TD>
  </TR>
  <TR>
    <TD COLSPAN=2>
      <DIV CLASS="topic">Kurzbeschreibung: <SPAN STYLE="color:red; font-weight:bold;">*</SPAN></DIV>
      <SPAN STYLE="font-size:10px; color:gray;">Hier ist nur Plaintext zul&auml;ssig! (max. <script>document.write(charslimitation);</script> Zeichen)</SPAN>
    </TD>
  </TR>
  <TR>
    <TD COLSPAN=2>
      <TEXTAREA MAXLENGTH="500" NAME="short_description" ID="short_description" STYLE="height:200px; width:100%;" onblur="updateCharsLeft();" onkeydown="updateCharsLeft();" onkeypress="updateCharsLeft();" onkeyup="updateCharsLeft();" onClick="updateCharsLeft();"><?=$p->getShortDescription()?></TEXTAREA>
      <SPAN STYLE="font-size:12px; font-weight:bold;"><?=_("Zeichen noch verf&uuml;gbar:")?> </SPAN><SPAN ID="chars_left" STYLE="font-size:15px; font-weight:bold; color:gray;"></SPAN><BR>
    </TD>
  </TR>
  <TR>
    <TD COLSPAN=2>&nbsp;</TD>
  </TR>
  <TR>
    <TD COLSPAN=2><DIV CLASS="topic">Beschreibung:</DIV></TD>
  </TR>
  <TR>
    <TD COLSPAN=2><TEXTAREA NAME="description" ID="description" class="mceAdvanced" STYLE="height:300px; width:100%;"><?=$p->getDescription()?></TEXTAREA></TD>
  </TR>
  <TR>
    <TD COLSPAN=2>&nbsp;</TD>
  </TR>
  <TR>
    <TD COLSPAN=2 STYLE="text-align:center;"><INPUT TYPE="image" <?=makeButton('speichern','src')?>> <IMG <?=makeButton('abbrechen','src')?> onClick="location.href='?';"> <? if ($p->getPluginId()) : ?><IMG <?=makeButton('loeschen','src')?> onClick="if (confirm('Wollen Sie das Plugin wirklich löschen?')){location.href='?dispatch=remove_plugin&plugin_id=<?=$p->getPluginId()?>';}"><? endif ?></TD>
  </TR>
</TABLE>
<SPAN STYLE="color:red; font-weight:bold;">*</SPAN> = Pflichtfeld<BR><BR>
<? if ($p->getPluginId()) : ?>
<? if ($releases = $p->getReleases()) : ?>
<DIV CLASS="topic">Releases:</DIV>
<TABLE BORDER=0 WIDTH="100%" CELLSPACING=0>
<? foreach ($releases as $r) : ?>
<? $css->switchClass(); ?>
  <TR CLASS="<?=$css->getClass()?>" <?=$css->getHover()?>>
    <TD CLASS="<?=$css->getClass()?>"><A HREF="?dispatch=edit_release&release_id=<?=$r->getReleaseId()?>&plugin_id=<?=$r->getPluginId()?>">Version <?=$r->getVersion()?></A></TD>
    <TD ALIGN="right" CLASS="<?=$css->getClass()?>"><IMG <?=makeButton('bearbeiten','src')?> STYLE="cursor:pointer" onClick="location.href='?dispatch=edit_release&release_id=<?=$r->getReleaseId()?>&plugin_id=<?=$p->getPluginId()?>'"> <IMG <?=makeButton('loeschen','src')?> STYLE="cursor:pointer" onClick="if (confirm('Wollen Sie das Release wirklich löschen?')){location.href='?dispatch=remove_release&release_id=<?=$r->getReleaseId()?>&plugin_id=<?=$p->getPluginId()?>'}"></TD>
  </TR>
<? endforeach ?>
</TABLE>
<? endif ?>
<IMG <?=makeButton('releases','src')?> onClick="location.href='?dispatch=edit_release&plugin_id=<?=$p->getPluginId()?>'">
<IMG <?=makeButton('screenshots','src')?> onClick="location.href='?dispatch=show_edit_screenshots&plugin_id=<?=$p->getPluginId()?>'">
<? endif ?>
</FORM>
