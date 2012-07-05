<script type="text/javascript">
getCommentsContent('<?=$range_id?>');
</script>
<script type="text/javascript">
var comments_open_<?=$range_id?> = false;
</script>
<CENTER>
<DIV STYLE="padding-bottom:15px;">
  <DIV>
    <A HREF="javascript:void(0);" onClick="if(!comments_open_<?=$range_id?>){new Effect.Appear('comments_<?=$range_id?>');comments_open_<?=$range_id?>=true;}else{new Effect.Fade('comments_<?=$range_id?>');comments_open_<?=$range_id?>=false;}"><?=sprintf(dgettext('r',"Kommentare (%d) / Kommentieren"),count($comments))?></A>
  </DIV>
  <DIV ID="comments_<?=$range_id?>" STYLE="display:none; width:580px; min-width:580px; max-width:580px;">
<? if (!$GLOBALS['PERM']->have_perm('author')) : ?>
    <DIV STYLE="font-size:10px; font-weight:bold; text-align:left;">
<?=_('Sie m&uuml;ssen angemeldet sein und Schreibberechtigung besitzen,<BR>um Kommentare verfassen zu k&ouml;nnen.')?>
    </DIV>
<? endif ?>
<? if ($GLOBALS['PERM']->have_perm('author')) : ?>
    <DIV STYLE="padding-bottom:15px; padding-top:15px; text-align:left;">
      <SPAN ID="new_comment_item_<?=$range_id?>" STYLE="font-size:12px; font-weight:bold;"><?=dgettext('r',"Neuen Kommentar verfassen:")?></SPAN>
    </DIV>
    <DIV STYLE="text-align:center; padding-bottom:15px;">
      <TABLE BORDER=0>
        <TR>
          <TD STYLE="vertical-align:top;"><TEXTAREA ID="kommentar_<?=$range_id?>" ROWS="5" COLS="70"></TEXTAREA></TD>
        </TR>
        <TR>
          <TD><INPUT TYPE="image" <?=makeButton('absenden','src')?> onClick="saveComment('<?=$range_id?>','kommentar_<?=$range_id?>');"></TD>
        </TR>
      </TABLE>
    </DIV>
<? endif ?>
    <DIV ID="comments_container_<?=$range_id?>" STYLE="text-align:left;">
      <IMG SRC="images/wait24trans.gif">
    </DIV>
  </DIV>
</DIV>
</CENTER>
