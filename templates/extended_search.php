<DIV CLASS="topic">Erweiterte Suche</DIV>
<FORM NAME="search" METHOD="POST" ACTION="?dispatch=extended_search">
<TABLE BORDER=0>
  <TR>
    <TD STYLE="font-size:12px; font-weight:bold;">Suchbegriff:</TD>
    <TD><INPUT TYPE="text" style="width:200px;"" class="jq_watermark" placeholder="Suchbegriff" NAME="search_txt" ID="search_txt"></TD>
  </TR>
  <TR>
    <TD STYLE="font-size:12px; font-weight:bold;">Volltextsuche:</TD>
    <TD><INPUT TYPE="checkbox" NAME="fulltext" ID="fulltext" VALUE="yes"></TD>
  </TR>
  <TR>
    <TD STYLE="font-size:12px; font-weight:bold;">Kategorie:</TD>
    <TD>
      <SELECT SIZE="1" NAME="category_id" style="width:200px;">
        <OPTION VALUE="all">Alle Kategorien</OPTION>
<? foreach ($GLOBALS['DBM']->getCategories() as $c) : ?>
        <OPTION VALUE="<?=$c['category_id']?>"><?=$c['name']?></OPTION>
<? endforeach ?>
      </SELECT>
    </TD>
  </TR>
  <TR>
    <TD STYLE="font-size:12px; font-weight:bold;">Sprache:</TD>
    <TD>
      <SELECT SIZE="1" NAME="language" style="width:200px;">
        <OPTION VALUE="all">Alle Sprachen</OPTION>
<? foreach (array('de'=>'Deutsch','en'=>'Englisch','de_en'=>'Deutsch/Englisch') as $idx=>$l) : ?>
        <OPTION VALUE="<?=$idx?>"><?=$l?></OPTION>
<? endforeach ?>
      </SELECT>
    </TD>
  </TR>
  <TR><TD COLSPAN=2>&nbsp;</TD></TR>
  <TR><TD COLSPAN=2 STYLE="text-align:center;"><INPUT TYPE="image" <?=makeButton('suchen','src')?>></TD></TR>
</TABLE>
</FORM>
