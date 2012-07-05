<?=$css->GetHoverJSFunction()?>
<SPAN STYLE="font-size:12px; font-weight:bold;">Neuen Benutzer <IMG <?=makeButton('erstellen','src')?> STYLE="cursor:pointer;" onClick="location.href='?dispatch=show_admin_add_user'"></SPAN><BR><BR>
<TABLE BORDER=0 WIDTH="100%" CELLSPACING=0>
  <TR CLASS="topic">
<? foreach (array('&nbsp;','Username','Vorname','Nachname','E-Mail','Rechte','gesperrt','Letzter Login','Aktion') as $t) : ?>
    <TD CLASS="topic" STYLE="font-size:12px; font-weight:bold;"><?=$t?></TD>
<? endforeach ?>
  </TR>
<? foreach ($users as $u) : ?>
<? $css->switchClass(); ?>
  <TR CLASS="<?=$css->getClass()?>" <?=$css->getHover()?>>
    <TD CLASS="<?=$css->getClass()?>" VALIGN="top" STYLE="width:20px;"><?=Avatar::getAvatar($u->getUserId())->getImageTag(Avatar::SMALL)?></TD>
    <TD CLASS="<?=$css->getClass()?>" VALIGN="top"><?=$u->getUsername()?></TD>
    <TD CLASS="<?=$css->getClass()?>" VALIGN="top"><?=$u->getVorname()?></TD>
    <TD CLASS="<?=$css->getClass()?>" VALIGN="top"><?=$u->getNachname()?></TD>
    <TD CLASS="<?=$css->getClass()?>" VALIGN="top"><?=$u->getEmail()?></TD>
    <TD CLASS="<?=$css->getClass()?>" VALIGN="top"><?=$u->getPerm()?></TD>
    <TD CLASS="<?=$css->getClass()?>" VALIGN="top"><?=($u->getLocked() ? 'ja' : 'nein')?></TD>
    <TD CLASS="<?=$css->getClass()?>" VALIGN="top"><? $s = Session::getSessionParams($u->getUserId()); echo ($s[0]['lastlogin'] ? date('d.m.Y H:i',$s[0]['lastlogin']) : 'nie'); ?></TD>
    <TD CLASS="<?=$css->getClass()?>" VALIGN="top"><IMG <?=makeButton('bearbeiten','src')?> onClick="location.href='?dispatch=edit_user&user_id=<?=$u->getUserId()?>'"></TD>
  </TR>
<? endforeach ?>
</TABLE>
