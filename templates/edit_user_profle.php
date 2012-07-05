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
	if ($('check_username').value != 'OK' && $('old_username').value != $('username').value) {
                alert('Der Username existiert bereits, bitte wählen Sie einen anderen!');
                return false;
        }
        if ($('username').value == '' || $('vorname').value == '' || $('nachname').value == '' || $('email').value == '' || $('passwort').value != $('passwort2').value) {
                alert('Bitte füllen Sie alle Pflichtfelder aus!');
                return false;
        } else {
                return true;
        }
}
</script>
<FORM ENCTYPE="multipart/form-data" NAME="user_profile" METHOD="POST" ACTION="?dispatch=update_user_profile" onSubmit="return checkInput();">
<INPUT TYPE="hidden" ID="check_username" VALUE="ERROR">
<INPUT TYPE="hidden" ID="old_username" VALUE="<?=htmlReady($u->getUsername())?>">
<DIV CLASS="topic">Profil bearbeiten</DIV>
<TABLE BORDER=0>
  <TR>
    <TD STYLE="font-size:12px; font-weight:bold;">Username: </TD><TD><INPUT TYPE="text" ID="username" NAME="username" SIZE="30" VALUE="<?=htmlReady($u->getUsername())?>"> <SPAN STYLE="color:red; font-weight:bold;">*</SPAN></TD>
  </TR>
  <TR>
    <TD STYLE="font-size:12px; font-weight:bold;">Anrede: </TD><TD>
      <SELECT NAME="salutation" SIZE=1>
<? foreach (array('Herr','Frau') as $p) : ?>
        <OPTION VALUE="<?=$p?>" <?=($u->getSalutation() == $p ? 'SELECTED' : '')?>><?=$p?></OPTION>
<? endforeach ?>
      </SELECT>
    </TD>
  </TR>
  <TR>
    <TD STYLE="font-size:12px; font-weight:bold;">Vorname: </TD><TD><INPUT TYPE="text" ID="vorname" NAME="vorname" SIZE="30" VALUE="<?=htmlReady($u->getVorname())?>"> <SPAN STYLE="color:red; font-weight:bold;">*</SPAN></TD>
  </TR>
  <TR>
    <TD STYLE="font-size:12px; font-weight:bold;">Nachname: </TD><TD><INPUT TYPE="text" ID="nachname" NAME="nachname" SIZE="30" VALUE="<?=htmlReady($u->getNachname())?>"> <SPAN STYLE="color:red; font-weight:bold;">*</SPAN></TD>
  </TR>
  <TR>
    <TD STYLE="font-size:12px; font-weight:bold;">E-Mail: </TD><TD><INPUT TYPE="text" ID="email" NAME="email" SIZE="30" VALUE="<?=htmlReady($u->getEmail())?>"> <SPAN STYLE="color:red; font-weight:bold;">*</SPAN></TD>
  </TR>
  <TR>
    <TD STYLE="font-size:12px; font-weight:bold;">Passwort: </TD><TD><INPUT TYPE="password" ID="passwort" NAME="passwort" SIZE="30" VALUE=""></TD>
  </TR>
  <TR>
    <TD STYLE="font-size:12px; font-weight:bold;">Passwort (wiederholen): </TD><TD><INPUT TYPE="password" ID="passwort2" NAME="passwort2" SIZE="30" VALUE=""></TD>
  </TR>
  <TR>
    <TD STYLE="width:150px; vertical-align:top; font-weight:bold;">Profilbild: </TD>
    <TD><INPUT TYPE="file" NAME="userfile" ID="userfile" SIZE="45" style="width:400px"></TD>
  </TR>
  <TR>
    <TD STYLE="font-size:12px; font-weight:bold;">Homepage: </TD><TD><INPUT TYPE="text" ID="url" NAME="url" SIZE="30" VALUE="<?=htmlReady($u->getUrl())?>"></TD>
  </TR>
  <TR>
    <TD STYLE="font-size:12px; font-weight:bold;">Arbeitsstelle: </TD><TD><INPUT TYPE="text" ID="workplace" NAME="workplace" SIZE="30" VALUE="<?=htmlReady($u->getWorkplace())?>"></TD>
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
    <TD COLSPAN=2 STYLE="text-align:center;"><INPUT TYPE="image" <?=makeButton('speichern','src')?>> <IMG <?=makeButton('abbrechen','src')?> onClick="location.href='?dispatch=show_profile'"></TD>
  </TR>
</TABLE>
</FORM>
<SPAN STYLE="color:red; font-weight:bold;">*</SPAN> = Pflichtfeld
