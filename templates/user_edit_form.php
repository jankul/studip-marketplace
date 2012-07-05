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
<FORM NAME="user_edit" METHOD="POST" ACTION="?dispatch=save_user" onSubmit="return checkInput();">
<INPUT TYPE="hidden" NAME="user_id" VALUE="<?=$u->getUserId()?>">
<INPUT TYPE="hidden" ID="check_username" VALUE="ERROR">
<INPUT TYPE="hidden" ID="old_username" VALUE="<?=htmlReady($u->getUsername())?>">
<TABLE BORDER=0>
  <TR>
    <TD STYLE="font-size:12px; font-weight:bold;">Letzer Login: </TD><TD><SPAN STYLE="font-size:12px;"><? $s = Session::getSessionParams($u->getUserId()); echo ($s[0]['lastlogin'] ? date('d.m.Y H:i',$s[0]['lastlogin']) : 'nie'); ?></SPAN></TD>
  </TR>
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
    <TD STYLE="font-size:12px; font-weight:bold;">Perm: </TD><TD>
      <SELECT NAME="perm" SIZE=1>
<? foreach (array('user','author','admin') as $p) : ?>
        <OPTION VALUE="<?=$p?>" <?=($u->getPerm() == $p ? 'SELECTED' : '')?>><?=$p?></OPTION>
<? endforeach ?>
      </SELECT>
    </TD>
  </TR>
  <TR>
    <TD STYLE="font-size:12px; font-weight:bold;">Locked: </TD><TD><INPUT TYPE="checkbox" NAME="locked" VALUE="yes" <?=($u->getLocked() ? 'CHECKED' : '')?>></TD>
  </TR>
  <TR>
    <TD STYLE="font-size:12px; font-weight:bold; vertical-align:top;">Profilbild: </TD><TD><?=Avatar::getAvatar($u->getUserId())->getImageTag(Avatar::MEDIUM)?></TD>
  </TR>
  <TR>
    <TD COLSPAN="2" STYLE=text-align:center;"><INPUT TYPE="image" <?=makeButton('speichern','src')?>> <IMG <?=makeButton('abbrechen','src')?> onClick="location.href='?dispatch=user_management';"></TD>
  </TR>
</TABLE>
</FORM>
<SPAN STYLE="color:red; font-weight:bold;">*</SPAN> = Pflichtfeld
