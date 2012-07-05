<DIV CLASS="comment_item">
<TABLE WIDTH="100%">
  <TR>
    <TD STYLE="font-size:11px; font-weight:bold;">
      <?=date('d.m.Y H:i',$c->getMkdate())?> <A HREF="?dispatch=show_profile&username=<?=$GLOBALS['UM']->getUsernameByUserId($c->getUserId())?>"><?=htmlReady($GLOBALS['UM']->getFullnameByUserId($c->getUserId()))?></A>
<? if ($rechte) : ?>
      <A HREF="javascript:void(0);" onClick="if (confirm('<?=dgettext('r',"Soll der Eintrag wirklich gelöscht werden?")?>')){removeCommentsItem('<?=$c->getCommentId()?>','<?=$c->getRangeId()?>'); return true;}else{return false}"><IMG SRC="images/icons/16/blue/trash.png" BORDER=0 ALT="<?=_("Eintrag löschen")?>" TITLE="<?=_("Eintrag löschen")?>"></A>
<? endif ?>
    </TD>
  </TR>
  <TR>
    <TD STYLE="font-size:12px;"><?=htmlReady($c->getCommentText())?></TD>
  </TR>
</TABLE>
</DIV>
