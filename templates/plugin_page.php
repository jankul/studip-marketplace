<DIV CLASS="plugin_page">
  <TABLE BORDER=0 WIDTH="100%" STYLE="height:200px;">
    <TR>
      <TD STYLE="width:100px; min-width:100px; vertical-align:top;">
<? if ($screen = $p->getTitleScreen()) : ?>
        <A ID="f<?=$screen->getFileId()?>" HREF="<?=$GLOBALS['BASE_URI']?>?dispatch=download&file_id=<?=$screen->getFileId()?>" rel="lightbox" TITLE="<?=htmlReady($screen->getTitel())?>"><IMG SRC="<?=$GLOBALS['DYNAMIC_CONTENT_URL'] . '/screenshots/'?><?=$screen->getFileId()?>_thumb" WIDTH="100"></A>
<script type="text/javascript">
        $j("a[id='f<?=$screen->getFileId()?>']").lightBox({
                txtImage: $j(this).attr('title')
        }); 
</script>
<? else :?>
        <IMG SRC="<?=$image_uri?>/unknown-plugin.png" ALT="Kein Titel-Screenshot angegeben" TITLE="Kein Titel-Screenshot angegeben">
<? endif ?>
      </TD>
      <TD ROWSPAN="2" STYLE="width:600px; vertical-align:top;">
        <DIV STYLE="clear:both; padding-bottom:5px; padding-top:5px;">
        <DIV STYLE="padding-top:5px; font-size:12px;">
          <?=Avatar::getAvatar($p->getUserId())->getImageTag(Avatar::SMALL)?> <A HREF="?dispatch=show_profile&username=<?=UserManagement::getUsernameByUserId($p->getUserId())?>&plugin_id=<?=$p->getPluginId()?>"><?=UserManagement::getFullnameByUserId($p->getUserId())?></A>
        <IMG SRC="images/languages/lang_<?=$p->getLanguage()?>.gif">
        </DIV>
        <DIV STYLE="padding-top:15px;"><?=htmlReady(mila($p->getShortDescription(),170))?><BR>
<? if ($p->getChdate()) : ?>
        <DIV STYLE="float:left; margin-right:15px; font-size:10px; color:gray;">
          Aktualisiert am <?=date('d.m.Y',$p->getChdate())?>
        </DIV>
<? endif ?>
      </TD>
    </TR>
    <TR>
      <TD STYLE="width:100px; min-width:100px; vertical-align:bottom; text-align:center;">
<? if ($r = $p->getLatestRelease()) : ?>
        <A HREF="?dispatch=download&file_id=<?=$r->getFileId()?>" STYLE="margin-bottom:10px;"><IMG SRC="images/icons/16/blue/download.png" ALT="Download latest release" TITLE="Download latest release"></A>
<? endif ?>
      </TD>
      <!-- TD>
        <DIV STYLE="padding-top:15px;"><?=htmlReady(mila($p->getShortDescription(),170))?><BR>
<? if ($p->getChdate()) : ?>
        <DIV STYLE="float:left; margin-right:15px; font-size:10px; color:gray;">
          Aktualisiert am <?=date('d.m.Y',$p->getChdate())?>
        </DIV>
<? endif ?>
      </TD -->
    </TR>
  </TABLE>
</DIV>
