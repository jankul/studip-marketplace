<CENTER>
<DIV STYLE="padding:10px; margin-top:150px; text-align:left; width:350px; min-width:350px; max-width:350px; border:1px solid gray;">
<A HREF="?"><IMG SRC="images/marketplace_header_small.png"></A><BR><BR>
<SPAN STYLE="font-size:14px; font-weight:bold;">Melden Sie sich f&uuml;r den Plugin-Marktplatz an</SPAN>
<FORM NAME="login" METHOD="POST" ACTION="?dispatch=do_login">
<TABLE BORDER=0 WIDTH="100%">
  <TR>
    <TD><SPAN STYLE="font-size:12px; font-weight:bold;">Username: </SPAN></TD>
    <TD><INPUT TYPE="text" SIZE="30" MAXLENGTH="255" VALUE="" NAME="username" ID="username"></TD>
  </TR>
  <TR>
    <TD><SPAN STYLE="font-size:12px; font-weight:bold;">Passwort: </SPAN></TD>
    <TD><INPUT TYPE="password" SIZE="30" MAXLENGTH="255" VALUE="" NAME="passwort" ID="passwort"></TD>
  </TR>
  <TR>
    <TD COLSPAN=2>
      <SPAN STYLE="font-size:12px;">Passwort vergessen? <A HREF="?dispatch=new_password">Neues Passwort anfordern</A> </SPAN><BR><BR>
      <CENTER><INPUT TYPE="image" <?=makeButton('login','src')?>> <IMG <?=makeButton('abbrechen','src')?> onClick="location.href='?';"></CENTER>
    </TD>
  </TR>
</TABLE>
</FORM>
<DIV ID="msg" STYLE="font-size:12px; font-weight:bold;">
<? if ($_SESSION['msg']) : ?>
<?=MessageBox::$_SESSION['msg_type']($_SESSION['msg'])?>
<? setMessage('info',''); ?>
<? endif ?>
</DIV>
</DIV>
</CENTER>
<script>
        $j(window).load(function () {
		$j('#username').focus();
        });
</script>

