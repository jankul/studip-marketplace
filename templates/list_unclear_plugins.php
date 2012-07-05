<?=$css->GetHoverJSFunction()?>
<TABLE BORDER=0 WIDTH="100%" CELLSPACING=0>
  <TR CLASS="topic">
<? foreach (array('Username','Plugin Name','Aktion') as $t) : ?>
    <TD CLASS="topic" STYLE="font-size:12px; font-weight:bold;"><?=$t?></TD>
<? endforeach ?>
  </TR>
<? foreach ($plugins as $p) : ?>
<? $css->switchClass(); ?>
  <TR CLASS="<?=$css->getClass()?>" <?=$css->getHover()?>>
    <TD CLASS="<?=$css->getClass()?>"><?=$GLOBALS['UM']->getUsernameByUserId($p->getUserId())?></TD>
    <TD CLASS="<?=$css->getClass()?>"><?=htmlReady($p->getName())?></TD>
    <TD CLASS="<?=$css->getClass()?>" STYLE="width:30px; max-width:30px;"><IMG <?=makeButton('bearbeiten','src')?> onClick="location.href='?dispatch=edit_plugin&plugin_id=<?=$p->getPluginId()?>';"></TD>
  </TR>
<? endforeach ?>
</TABLE>
