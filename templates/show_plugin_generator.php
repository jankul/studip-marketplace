<script type="text/javascript">
var checkInput = function() {
        if ($('pluginname').value == '' || $('pluginclassname').value == '' || $('pluginauthor').value == '') {
                alert('Bitte füllen Sie alle Pflichtfelder aus!');
                return false;
        } else {
                return true;;
        }
}
</script>
<FORM NAME="generator" METHOD="POST" ACTION="?dispatch=generate_plugin" onSubmit="return checkInput();">
<?=MessageBox::info("Willkommen zum Plugin-Generator!")?>
<DIV CLASS="topic">Plugin-H&uuml;llen f&uuml;r Stud.IP von Version 1.9 bis einschl. 1.11 generieren</DIV>
<TABLE BORDER=0>
<? $css->switchClass(); ?>
  <TR CLASS="<?=$css->getClass()?>">
    <TD CLASS="<?=$css->getClass()?>" STYLE="font-size:12px; font-weight:bold;">Plugin-Name: <SPAN STYLE="color:red; font-weight:bold;">*</SPAN></TD>
    <TD CLASS="<?=$css->getClass()?>"><INPUT TYPE="text" NAME="pluginname" ID="pluginname" SIZE="50" MAXLENGTH="255"></TD>
  </TR>
<? $css->switchClass(); ?>
  <TR CLASS="<?=$css->getClass()?>">
    <TD CLASS="<?=$css->getClass()?>"><SPAN STYLE="font-size:12px; font-weight:bold;">Plugin-Klassenname: </SPAN><SPAN STYLE="color:red; font-weight:bold;">*</SPAN><BR><SPAN STYLE="font-size:10px;">Bitte keine Bindestriche oder Leerzeichen verwenden.</SPAN></TD>
    <TD CLASS="<?=$css->getClass()?>" STYLE="vertical-align:top;"><INPUT TYPE="text" NAME="pluginclassname" ID="pluginclassname" SIZE="50" MAXLENGTH="255"></TD>
  </TR>
<? $css->switchClass(); ?>
  <TR>
    <TD CLASS="<?=$css->getClass()?>" STYLE="font-size:12px; font-weight:bold;">Autor:  <SPAN STYLE="color:red; font-weight:bold;">*</SPAN></TD>
    <TD CLASS="<?=$css->getClass()?>"><INPUT TYPE="text" NAME="pluginauthor" ID="pluginauthor" SIZE="50" MAXLENGTH="255"></TD>
  </TR>
<? $css->switchClass(); ?>
  <TR CLASS="<?=$css->getClass()?>">
    <TD CLASS="<?=$css->getClass()?>" STYLE="font-size:12px; font-weight:bold;">Minimale Stud.IP Version: <SPAN STYLE="color:red; font-weight:bold;">*</SPAN></TD>
    <TD CLASS="<?=$css->getClass()?>">
      <SELECT NAME="studipminversion" SIZE="1">
<? foreach (array('1.9','1.10','1.11') as $v) : ?>
        <OPTION VALUE="<?=$v?>"><?=$v?></OPTION>
<? endforeach ?>
      </SELECT>
    </TD>
  </TR>
<? $css->switchClass(); ?>
  <TR CLASS="<?=$css->getClass()?>">
    <TD CLASS="<?=$css->getClass()?>" STYLE="font-size:12px; font-weight:bold;">Maximale Stud.IP Version: <SPAN STYLE="color:red; font-weight:bold;">*</SPAN></TD>
    <TD CLASS="<?=$css->getClass()?>">
      <SELECT NAME="studipmaxversion" SIZE="1">
<? foreach (array('1.9','1.10','1.11') as $v) : ?>
        <OPTION VALUE="<?=$v?>"><?=$v?></OPTION>
<? endforeach ?>
      </SELECT>
    </TD>
  </TR>
<? $css->switchClass(); ?>
  <TR CLASS="<?=$css->getClass()?>">
    <TD CLASS="<?=$css->getClass()?>" STYLE="font-size:12px; font-weight:bold; vertical-align:top;">Art des Plugins: <SPAN STYLE="color:red; font-weight:bold;">*</SPAN></TD>
    <TD CLASS="<?=$css->getClass()?>">
<? foreach (array('Administration','Homepage','Portal','Standard','System') as $t) : ?>
      <INPUT TYPE="radio" NAME="plugintype" VALUE="<?=$t?>" <?=($t=='Administration'?'CHECKED':'')?>>&nbsp;<?=$t?><BR>
<? endforeach ?>
    </TD>
  </TR>
  <TR><TD COLSPAN="2" STYLE="text-align:center;"><INPUT TYPE="image" <?=makeButton('magic','src')?>></TD></TR>
</TABLE>
</FORM>
<SPAN STYLE="color:red; font-weight:bold;">*</SPAN> = Pflichtfeld
