<script type="text/javascript">
var checkUsername = function() {
        $j.ajax({
                url: '<?=$GLOBALS['BASE_URI']?>ajax_dispatcher.php?ajaxcmd=check_username&username='+$('username').value,
                cache: false,
		async: false,
                dataType: 'text',
                success: function(data) {
			$('check_username').value = data;
                }
        });
}

var checkInput = function() {
	checkUsername();
	if ($('check_username').value != 'OK') {
		alert('Der Username existiert bereits, bitte wählen Sie einen anderen!');
		return false;
	}
	if ($('check_username').value != 'OK' || $('username').value == '' || $('vorname').value == '' || $('nachname').value == '' || $('email').value == '' || $('email2').value == '' || $('email').value != $('email2').value || $('passwort').value == '' || $('passwort2').value == '' || $('passwort').value != $('passwort2').value || $('captcha_code').value == '') {
		alert("Bitte alle Felder korrekt ausfüllen!");
		return false;
	} else {
		return true;
	}
}
</script>

<FORM NAME="register" METHOD="POST" ACTION="?dispatch=do_register" onSubmit="return checkInput();">
<INPUT TYPE="hidden" ID="check_username" VALUE="ERROR">
<DIV CLASS="topic">Registrieren Sie sich hier für die Nutzung des Marktplatzes</DIV>
<TABLE BORDER=0>
  <TR>
    <TD STYLE="width:200px; vertical-align:top; font-weight:bold;">Anrede: </TD>
    <TD>
      <SELECT NAME="salutation" ID="salutation" SIZE="1">
        <OPTION VALUE="Herr" <?=($_REQUEST['salutation'] == 'Herr' ? 'CHECKED' : '')?>>Herr</OPTION>
        <OPTION VALUE="Frau" <?=($_REQUEST['salutation'] == 'Frau' ? 'CHECKED' : '')?>>Frau</OPTION>
      </SELECT>
      <SPAN STYLE="color:red; font-weight:bold;">*</SPAN></TD>
  </TR>
  <TR>
    <TD STYLE="width:200px; vertical-align:top; font-weight:bold;">Username: </TD>
    <TD><INPUT TYPE="text" SIZE=50 MAXLENGTH=255 NAME="username" ID="username" VALUE="<?=htmlReady($_REQUEST['username'])?>"> <SPAN STYLE="color:red; font-weight:bold;">*</SPAN></TD>
  </TR>
  <TR>
    <TD STYLE="width:200px; vertical-align:top; font-weight:bold;">Vorname: </TD>
    <TD><INPUT TYPE="text" SIZE=50 MAXLENGTH=255 NAME="vorname" ID="vorname" VALUE="<?=htmlReady($_REQUEST['vorname'])?>"> <SPAN STYLE="color:red; font-weight:bold;">*</SPAN></TD>
  </TR>
  <TR>
    <TD STYLE="width:200px; vertical-align:top; font-weight:bold;">Nachname: </TD>
    <TD><INPUT TYPE="text" SIZE=50 MAXLENGTH=255 NAME="nachname" ID="nachname" VALUE="<?=htmlReady($_REQUEST['nachname'])?>"> <SPAN STYLE="color:red; font-weight:bold;">*</SPAN></TD>
  </TR>
  <TR>
    <TD STYLE="width:200px; vertical-align:top; font-weight:bold;">E-Mail Adresse: </TD>
    <TD><INPUT TYPE="text" SIZE=50 MAXLENGTH=255 NAME="email" ID="email" VALUE="<?=htmlReady($_REQUEST['email'])?>"> <SPAN STYLE="color:red; font-weight:bold;">*</SPAN></TD>
  </TR>
  <TR>
    <TD STYLE="width:200px; vertical-align:top; font-weight:bold;">E-Mail Adresse (wiederholen): </TD>
    <TD><INPUT TYPE="text" SIZE=50 MAXLENGTH=255 NAME="email2" ID="email2" VALUE="<?=htmlReady($_REQUEST['email2'])?>"> <SPAN STYLE="color:red; font-weight:bold;">*</SPAN></TD>
  </TR>
  <TR>
    <TD STYLE="width:200px; vertical-align:top; font-weight:bold;">Passwort: </TD>
    <TD><INPUT TYPE="password" SIZE=50 MAXLENGTH=255 NAME="passwort" ID="passwort" VALUE=""> <SPAN STYLE="color:red; font-weight:bold;">*</SPAN></TD>
  </TR>
  <TR>
    <TD STYLE="width:200px; vertical-align:top; font-weight:bold;">Passwort (wiederholen): </TD>
    <TD><INPUT TYPE="password" SIZE=50 MAXLENGTH=255 NAME="passwort2" ID="passwort2" VALUE=""> <SPAN STYLE="color:red; font-weight:bold;">*</SPAN></TD>
  </TR>
  <TR>
    <TD STYLE="width:200px; vertical-align:top;">Geben Sie bitte die nebenstehende Zeichenfolge zur Verifikation ein: </TD>
    <TD><IMG ID="captcha" STYLE="border:1px solid black;" alt="CAPTCHA Image" SRC="<?=$GLOBALS['BASE_URI']?>lib/captcha/securimage_show.php">
      <object type="application/x-shockwave-flash" data="lib/captcha/securimage_play.swf?audio=lib/captcha/securimage_play.php&amp;bgColor1=#fff&amp;bgColor2=#fff&amp;iconColor=#777&amp;borderWidth=1&amp;borderColor=#000" height="19" width="19">
        <param name="movie" value="lib/captcha/securimage_play.swf?audio=lib/captcha/securimage_play.php&amp;bgColor1=#fff&amp;bgColor2=#fff&amp;iconColor=#777&amp;borderWidth=1&amp;borderColor=#000" />
      </object>
      <BR><BR><a href="#" onclick="document.getElementById('captcha').src = '<?=$GLOBALS['BASE_URI']?>lib/captcha/securimage_show.php?' + Math.random(); return false">Bild unleserlich? Neues generieren</a><BR><INPUT TYPE="text" SIZE=20 MAXLENGTH=255 NAME="captcha_code" ID="captcha_code" VALUE=""> <SPAN STYLE="color:red; font-weight:bold;">*</SPAN></TD>
  </TR>
  <TR>
    <TD COLSPAN=2>&nbsp;</TD>
  </TR>
  <TR>
    <TD COLSPAN=2 STYLE="text-align:center;"><INPUT TYPE="image" <?=makeButton('absenden','src')?>> <IMG <?=makeButton('abbrechen','src')?> onClick="location.href='?';"></TD>
  </TR>
</TABLE>
<SPAN STYLE="color:red; font-weight:bold;">*</SPAN> = Pflichtfeld
</FORM>
