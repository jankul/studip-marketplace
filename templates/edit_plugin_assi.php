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

var checkInput = function(part) {
	if (part == '1') {
		if ($('titel').value == '' || $('license').value == '') {
	                alert('Bitte füllen Sie alle Pflichtfelder aus!');
        	        return false;
		} else {
			return true;
		}
	} else if (part == '2') {
		if (jQuery(':hidden[class="sel_categories"]').length == 0) {
			alert('Bitte füllen Sie alle Pflichtfelder aus!');
			return false;
		} else {
			return true;
		}
	} else if (part == '3') {
		if ($('short_description').value == '') {
			alert('Bitte füllen Sie alle Pflichtfelder aus!');
			return false;
		} else {
			return checkInputLength();
		}
	} 
}

var getAvailableCategories = function() {
        $j.ajax({
                url: '<?=$GLOBALS['BASE_URI']?>ajax_dispatcher.php?ajaxcmd=get_available_categories&plugin_id=&hidden_cats=<?=json_encode($categories)?>',
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
</script>

<FORM NAME="plugin" METHOD="POST" ACTION="?">
<INPUT TYPE="hidden" NAME="dispatch" VALUE="assi">
<INPUT TYPE="hidden" NAME="part" VALUE="<?=$part?>">
<TABLE BORDER=0 WIDTH="100%">
  <TR>
    <TD COLSPAN=2>
      <?=MessageBox::info(_("F&uuml;llen Sie alle mit einem roten Sternchen markierten Felder aus, um Ihr Plugin zu beschreiben. Alle anderen Felder sind optional.<BR><BR>Schritt ".$part." von 4"))?>
    </TD>
  </TR>
<? if ($part == 1) : ?>
<INPUT TYPE="hidden" NAME="tags" VALUE="<?=htmlReady($tags)?>">
<INPUT TYPE="hidden" NAME="short_description" VALUE="<?=htmlReady($short_description)?>">
<INPUT TYPE="hidden" NAME="description" VALUE="<?=htmlReady($description)?>">
<? foreach ($categories as $c) : ?>
<INPUT TYPE="hidden" NAME="c_ids[]" VALUE="<?=$c?>">
<? endforeach ?>
  <TR>
    <TD COLSPAN=2>
      <DIV CLASS="topic">Grunddaten: <SPAN STYLE="color:red; font-weight:bold;">*</SPAN></DIV>
    </TD>
  </TR>
  <TR>
    <TD STYLE="width:150px; vertical-align:top; font-weight:bold; font-size:12px;">Titel: </TD>
    <TD><INPUT TYPE="text" STYLE="width:500px" MAXLENGTH=255 NAME="titel" ID="titel" VALUE="<?=htmlReady($titel)?>"> <SPAN STYLE="color:red; font-weight:bold;">*</SPAN></TD>
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
      <INPUT TYPE="text" STYLE="width:265px" MAXLENGTH=255 NAME="license" ID="license" VALUE="<?=htmlReady($license)?>"> <SPAN STYLE="color:red; font-weight:bold;">*</SPAN>
    </TD>
  </TR>
  <TR>
    <TD STYLE="width:150px; vertical-align:top; font-weight:bold; font-size:12px;">Im Einsatz bei: </TD>
    <TD><INPUT TYPE="text" STYLE="width:500px" MAXLENGTH=255 NAME="in_use" ID="in_use" VALUE="<?=htmlReady($in_use)?>"></TD>
  </TR>
  <TR>
    <TD STYLE="width:150px; vertical-align:top; font-weight:bold; font-size:12px;">Homepage-URL: </TD>
    <TD><INPUT TYPE="text" STYLE="width:500px" MAXLENGTH=2000 NAME="url" ID="url" VALUE="<?=htmlReady($url)?>"></TD>
  </TR>
  <TR>
    <TD STYLE="width:150px; vertical-align:top; font-weight:bold; font-size:12px;">Sprache: </TD>
    <TD>
      <INPUT TYPE="radio" NAME="language" VALUE="de" <?=($language == 'de' ? 'CHECKED' : '')?>><IMG SRC="images/languages/lang_de.gif" ALT="Deutsch" TITLE="Deutsch">&nbsp;&nbsp;
      <INPUT TYPE="radio" NAME="language" VALUE="en" <?=($language == 'en' ? 'CHECKED' : '')?>><IMG SRC="images/languages/lang_en.gif" ALT="Englisch" TITLE="Englisch">&nbsp;&nbsp;
      <INPUT TYPE="radio" NAME="language" VALUE="de_en" <?=($language == 'de_en' ? 'CHECKED' : '')?>><IMG SRC="images/languages/lang_de_en.gif" ALT="Deutsch/Englisch" TITLE="Deutsch/Englisch">
    </TD>
  </TR>
  <TR>
    <TD COLSPAN=2 STYLE="text-align:center;"><IMG <?=makeButton('weiter','src')?> STYLE="cursor:pointer;" onClick="if (checkInput('1')){document.plugin.part.value='2'; document.plugin.submit();}"></TD>
  </TR>
<? endif ?>
<? if ($part == 2) : ?>
<INPUT TYPE="hidden" NAME="titel" VALUE="<?=htmlReady($titel)?>">
<INPUT TYPE="hidden" NAME="license" VALUE="<?=htmlReady($license)?>">
<INPUT TYPE="hidden" NAME="language" VALUE="<?=htmlReady($language)?>">
<INPUT TYPE="hidden" NAME="in_use" VALUE="<?=htmlReady($in_use)?>">
<INPUT TYPE="hidden" NAME="url" VALUE="<?=htmlReady($url)?>">
<INPUT TYPE="hidden" NAME="short_description" VALUE="<?=htmlReady($short_description)?>">
<INPUT TYPE="hidden" NAME="description" VALUE="<?=htmlReady($description)?>">
  <TR>
    <TD COLSPAN=2>
      <DIV CLASS="topic">Kategorien: <SPAN STYLE="color:red; font-weight:bold;">*</SPAN></DIV>
      <TABLE BORDER=0 WIDTH="100%">
        <TR>
          <TD WIDTH="50%" STYLE="vertical-align:top;">
            <DIV CLASS="category_head">Bestehende Zuordnungen:</DIV>
            <DIV ID="current_categories">
<? foreach ($categories as $cat) : ?>
<? $c = $GLOBALS['DBM']->getCategory($cat); ?>
<DIV ID="c_<?=$c['category_id']?>">
<IMG SRC="images/trash.gif" onClick="removeCategory('<?=$c['category_id']?>');" STYLE="cursor:pointer;">&nbsp;<?=$c['name']?>
<INPUT TYPE="hidden" NAME="c_ids[]" CLASS="sel_categories" VALUE="<?=$c['category_id']?>">
</DIV>
<? endforeach ?>
            </DIV>
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
	<INPUT TYPE="text" ID="tagsautocomplete" NAME="tags" VALUE="<?=htmlReady($tags)?>" MAXLENGTH="255" STYLE="width:500px;">
        <div id="tagsautocomplete_choices" class="tagsautocomplete"></div>
        <BR><SPAN STYLE="font-size:10px;">Tags bitte mit <SPAN STYLE="font-weight:bold;">Komma</SPAN> trennen</SPAN>
    </TD>
  </TR>
<script type="text/javascript">
$j(window).load(function () {
	getAvailableCategories();
	new Ajax.Autocompleter("tagsautocomplete", "tagsautocomplete_choices", "<?=$GLOBALS['BASE_URI']?>ajax_dispatcher.php?ajaxcmd=tag_completer", {
	        paramName: "value",
        	minChars: 2
	});
});
</script>
  <TR>
    <TD COLSPAN=2 STYLE="text-align:center;"><IMG <?=makeButton('zurueck','src')?> STYLE="cursor:pointer;" onClick="document.plugin.part.value='1'; document.plugin.submit();">&nbsp;<IMG <?=makeButton('weiter','src')?> STYLE="cursor:pointer;" onClick="if (checkInput('2')){document.plugin.part.value='3'; document.plugin.submit();}"></TD>
  </TR>
<? endif ?>
<? if ($part == 3) : ?>
<INPUT TYPE="hidden" NAME="titel" VALUE="<?=htmlReady($titel)?>">
<INPUT TYPE="hidden" NAME="license" VALUE="<?=htmlReady($license)?>">
<INPUT TYPE="hidden" NAME="language" VALUE="<?=htmlReady($language)?>">
<INPUT TYPE="hidden" NAME="in_use" VALUE="<?=htmlReady($in_use)?>">
<INPUT TYPE="hidden" NAME="url" VALUE="<?=htmlReady($url)?>">
<INPUT TYPE="hidden" NAME="tags" VALUE="<?=htmlReady($tags)?>">
<INPUT TYPE="hidden" NAME="description" VALUE="<?=htmlReady($description)?>">
<? foreach ($categories as $c) : ?>
<INPUT TYPE="hidden" NAME="c_ids[]" VALUE="<?=$c?>">
<? endforeach ?>
  <TR>
    <TD COLSPAN=2>
      <DIV CLASS="topic">Kurzbeschreibung: <SPAN STYLE="color:red; font-weight:bold;">*</SPAN></DIV>
      <SPAN STYLE="font-size:10px; color:gray;">Hier ist nur Plaintext zul&auml;ssig! (max. <script>document.write(charslimitation);</script> Zeichen)</SPAN>
    </TD>
  </TR>
  <TR>
    <TD COLSPAN=2>
      <TEXTAREA MAXLENGTH="500" NAME="short_description" ID="short_description" STYLE="height:200px; width:100%;" onblur="updateCharsLeft();" onkeydown="updateCharsLeft();" onkeypress="updateCharsLeft();" onkeyup="updateCharsLeft();" onClick="updateCharsLeft();"><?=htmlReady($short_description)?></TEXTAREA>
      <SPAN STYLE="font-size:12px; font-weight:bold;"><?=_("Zeichen noch verf&uuml;gbar:")?> </SPAN><SPAN ID="chars_left" STYLE="font-size:15px; font-weight:bold; color:gray;"></SPAN><BR>
    </TD>
  </TR>
<script type="text/javascript">
$j(window).load(function () {
	updateCharsLeft();
});
</script>
  <TR>
    <TD COLSPAN=2 STYLE="text-align:center;"><IMG <?=makeButton('zurueck','src')?> STYLE="cursor:pointer;" onClick="document.plugin.part.value='2'; document.plugin.submit();">&nbsp;<IMG <?=makeButton('weiter','src')?> STYLE="cursor:pointer;" onClick="if (checkInput('3')){document.plugin.part.value='4'; document.plugin.submit();}"></TD>
  </TR>
<? endif ?>
<? if ($part == 4) : ?>
<INPUT TYPE="hidden" NAME="titel" VALUE="<?=htmlReady($titel)?>">
<INPUT TYPE="hidden" NAME="license" VALUE="<?=htmlReady($license)?>">
<INPUT TYPE="hidden" NAME="language" VALUE="<?=htmlReady($language)?>">
<INPUT TYPE="hidden" NAME="in_use" VALUE="<?=htmlReady($in_use)?>">
<INPUT TYPE="hidden" NAME="url" VALUE="<?=htmlReady($url)?>">
<INPUT TYPE="hidden" NAME="tags" VALUE="<?=htmlReady($tags)?>">
<INPUT TYPE="hidden" NAME="short_description" VALUE="<?=htmlReady($short_description)?>">
<? foreach ($categories as $c) : ?>
<INPUT TYPE="hidden" NAME="c_ids[]" VALUE="<?=$c?>">
<? endforeach ?>
  <TR>
    <TD COLSPAN=2><DIV CLASS="topic">Beschreibung:</DIV></TD>
  </TR>
  <TR>
    <TD COLSPAN=2><TEXTAREA NAME="description" ID="description" class="mceAdvanced" STYLE="height:300px; width:100%;"><?=htmlReady($description)?></TEXTAREA></TD>
  </TR>
  <TR>
    <TD COLSPAN=2 STYLE="text-align:center;"><IMG <?=makeButton('zurueck','src')?> STYLE="cursor:pointer;" onClick="document.plugin.part.value='3'; document.plugin.submit();">&nbsp;<IMG <?=makeButton('fertigstellen','src')?> STYLE="cursor:pointer;" onClick="document.plugin.part.value='5'; document.plugin.submit();"></TD>
  </TR>

<? endif ?>
  <TR>
    <TD COLSPAN=2>&nbsp;</TD>
  </TR>
</TABLE>
<SPAN STYLE="color:red; font-weight:bold;">*</SPAN> = Pflichtfeld<BR><BR>
</FORM>
