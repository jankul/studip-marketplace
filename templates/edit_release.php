<LINK REL="stylesheet" HREF="css/tags_autocompleter.css" TYPE="text/css" />
<script type="text/javascript">
<? if ($r->getReleaseId()) : ?>
var waiting = "<CENTER><IMG SRC=\"images/wait24trans.gif\"></CENTER>";
var getCurrentReleases = function() {
        $j.ajax({
                url: '<?=$GLOBALS['BASE_URI']?>ajax_dispatcher.php?ajaxcmd=get_current_dependencies&plugin_id=<?=$r->getPluginId()?>&release_id=<?=$r->getReleaseId()?>',
                cache: false,
                dataType: 'html',
                success: function(data) {
                        $j('#current_releases').html(data);
                }
        });
}

var getAvailablePlugins = function() {
        $j.ajax({
                url: '<?=$GLOBALS['BASE_URI']?>ajax_dispatcher.php?ajaxcmd=get_available_dependency_plugins',
                cache: false,
                dataType: 'html',
                success: function(data) {
                        $j('#available_plugins').html(data);
                }
        });
}

var getAvailableReleases = function(pid) {
	$j('#available_releases').html(waiting);
        $j.ajax({
                url: '<?=$GLOBALS['BASE_URI']?>ajax_dispatcher.php?ajaxcmd=get_available_dependency_releases&release_id=<?=$r->getReleaseId()?>&plugin_id='+pid,
                cache: false,
                dataType: 'html',
                success: function(data) {
                        $j('#available_releases').html(data);
                }
        });
}

var removeRelease = function(cid) {
        $j('#r_'+cid).hide().remove();
        $j('#ra_'+cid).fadeIn()
}

var addDepRelease = function(cid) {
        $j('#ra_'+cid).hide();
        $j.ajax({
                url: '<?=$GLOBALS['BASE_URI']?>ajax_dispatcher.php?ajaxcmd=get_release_item&release_id='+cid,
                cache: false,
                dataType: 'html',
                success: function(data) {
                        $j(data).appendTo('#current_releases');
                }
        });
}

$j(window).load(function () {
        getAvailablePlugins();
	getAvailableReleases();
        getCurrentReleases();
});
<? endif ?>
$j(window).load(function () {
	new Ajax.Autocompleter("tagsautocomplete", "tagsautocomplete_choices", "<?=$GLOBALS['BASE_URI']?>ajax_dispatcher.php?ajaxcmd=tag_completer", {
                paramName: "value",
                minChars: 2
        });
});
</script>

<FORM ENCTYPE="multipart/form-data" NAME="release" METHOD="POST" ACTION="?dispatch=save_release">
<INPUT TYPE="hidden" NAME="release_id" VALUE="<?=$r->getReleaseId()?>">
<INPUT TYPE="hidden" NAME="plugin_id" VALUE="<?=$r->getPluginId()?>">
<DIV CLASS="topic">Release bearbeiten</DIV>
<TABLE BORDER=0 WIDTH="100%">
  <TR>
    <TD STYLE="width:150px; vertical-align:top; font-weight:bold;">Origin: </TD>
    <TD><INPUT TYPE="text" SIZE=50 MAXLENGTH=255 NAME="origin" ID="origin" VALUE="<?=htmlReady($r->getOrigin())?>" disabled="disabled" STYLE="background-color:lightgray;"> <SPAN STYLE="color:red; font-weight:bold;">*</SPAN></TD>
  </TR>
  <TR>
    <TD STYLE="width:150px; vertical-align:top; font-weight:bold;">Version: </TD>
    <TD><INPUT TYPE="text" SIZE=50 MAXLENGTH=255 NAME="version" ID="version" VALUE="<?=htmlReady($r->getVersion())?>" disabled="disabled" STYLE="background-color:lightgray;"> <SPAN STYLE="color:red; font-weight:bold;">*</SPAN></TD>
  </TR>
  <TR>
    <TD STYLE="width:150px; vertical-align:top; font-weight:bold;">Stud.IP Min-Version: </TD>
    <TD><INPUT TYPE="text" SIZE=50 MAXLENGTH=255 NAME="studip_min_version" ID="studip_min_version" VALUE="<?=htmlReady($r->getStudipMinVersion())?>" disabled="disabled" STYLE="background-color:lightgray;"> <SPAN STYLE="color:red; font-weight:bold;">*</SPAN></TD>
  </TR>
  <TR>
    <TD STYLE="width:150px; vertical-align:top; font-weight:bold;">Stud.IP Max-Version: </TD>
    <TD><INPUT TYPE="text" SIZE=50 MAXLENGTH=255 NAME="studip_max_version" ID="studip_max_version" VALUE="<?=htmlReady($r->getStudipMaxVersion())?>" disabled="disabled" STYLE="background-color:lightgray;"> <SPAN STYLE="color:red; font-weight:bold;">*</SPAN></TD>
  </TR>
  <TR>
    <TD STYLE="width:150px; vertical-align:top; font-weight:bold;">Release Art: </TD>
    <TD>
      <SELECT NAME="template" STYLE="width:230px;" SIZE="1" onChange="document.release.release_type.value=document.release.template[document.release.template.selectedIndex].value;">
        <OPTION VALUE=""><?=_("ausw&auml;hlen oder wie Eingabe")?> --&gt;</OPTION>
        <OPTION VALUE="Servicerelease" <?=($r->getReleaseType() == 'Servicerelease' ? 'SELECTED' : '')?>><?=_("Servicerelease")?></OPTION>
        <OPTION VALUE="Experimental" <?=($r->getReleaseType() == 'Experimental' ? 'SELECTED' : '')?>><?=_("Experimental")?></OPTION>
        <OPTION VALUE="Beta" <?=($r->getReleaseType() == 'Beta' ? 'SELECTED' : '')?>><?=_("Beta")?></OPTION>
      </SELECT>
      <INPUT TYPE="text" NAME="release_type" ID="release_type" SIZE="30" VALUE="<?=htmlReady($r->getReleaseType())?>">
    </TD>
  </TR>
  <TR>
    <TD STYLE="width:150px; vertical-align:top;">
      <SPAN STYLE="font-weight:bold; font-size:12px;">Tags:</SPAN>
    </TD>
    <TD>
        <INPUT TYPE="text" ID="tagsautocomplete" NAME="tags" VALUE="" MAXLENGTH="255" SIZE="50">
        <div id="tagsautocomplete_choices" class="tagsautocomplete"></div>
        <BR><SPAN STYLE="font-size:10px;">Tags bitte mit <SPAN STYLE="font-weight:bold;">Komma</SPAN> trennen</SPAN>
<? if (count($r->getTags()) > 0) : ?>
        <BR><SPAN STYLE="font-size:10px;">|</SPAN>
<? foreach ($r->getTags() as $t) : ?>
          <SPAN STYLE="font-size:10px;"><?=htmlReady($t)?> <A HREF="?dispatch=remove_rtag&release_id=<?=$r->getReleaseId()?>&tag=<?=urlencode($t)?>&plugin_id=<?=$r->getPluginId()?>"><IMG SRC="images/trash2.gif" BORDER=0></A> | </SPAN>
<? endforeach ?>
<? endif ?>
    </TD>
  </TR>
  <TR>
    <TD STYLE="width:150px; vertical-align:top; font-weight:bold;">Dateiname (ZIP-Datei): </TD>
    <TD><INPUT TYPE="file" NAME="releasefile" ID="releasefile" SIZE="45" style="width:400px"></TD>
  </TR>
  <TR>
    <TD STYLE="width:150px; vertical-align:top; font-weight:bold;">Bisherige Datei: </TD>
    <TD><?=htmlReady($f->getFileName())?></TD>
  </TR>
<? if ($r->getReleaseId()) : ?>
  <TR>
    <TD COLSPAN=2>
      <DIV CLASS="topic" STYLE="margin-top:10px;">Abh&auml;ngigkeiten:</DIV>
      <TABLE BORDER=0 WIDTH="100%">
        <TR>
          <TD WIDTH="33%" STYLE="vertical-align:top;">
            <DIV CLASS="category_head">Bitte Plugin w&auml;hlen:</DIV>
            <DIV ID="available_plugins"><CENTER><IMG SRC="images/wait24trans.gif"></DIV>
          </TD>
          <TD WIDTH="33%" STYLE="vertical-align:top;">
            <DIV CLASS="category_head">Bitte Release w&auml;hlen:</DIV>
            <DIV ID="available_releases"><CENTER><IMG SRC="images/wait24trans.gif"></DIV>
          </TD>
          <TD WIDTH="33%" STYLE="vertical-align:top;">
            <DIV CLASS="category_head">Bestehende Zuordnungen:</DIV>
            <DIV ID="current_releases"><CENTER><IMG SRC="images/wait24trans.gif"></CENTER></DIV>
          </TD>
        </TR>
      </TABLE>
    </TD>
  </TR>
<? endif ?>
  <TR>
    <TD COLSPAN=2>&nbsp;</TD>
  </TR>
  <TR>
    <TD COLSPAN=2 STYLE="text-align:center;"><INPUT TYPE="image" <?=makeButton('speichern','src')?>> <IMG <?=makeButton('abbrechen','src')?> onClick="location.href='?dispatch=edit_plugin&plugin_id=<?=$r->getPluginId()?>'"> <? if ($r->getReleaseId()) : ?><IMG <?=makeButton('loeschen','src')?> onClick="if (confirm('Wollen Sie das Release wirklich löschen?')){location.href='?dispatch=remove_release&release_id=<?=$r->getReleaseId()?>&plugin_id=<?=$r->getPluginId()?>';}"><? endif ?></TD>
  </TR>
</TABLE>
<SPAN STYLE="color:red; font-weight:bold;">*</SPAN> = Daten werden aus dem Manifest &uuml;bernommen.
</FORM>
