<CENTER>
<DIV CLASS="plugin_item">
  <TABLE BORDER=0 WIDTH="100%">
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
      <TD STYLE="width:600px; vertical-align:top;">
        <DIV STYLE="float:left; font-weight:bold; font-size:16px;"><A HREF="?dispatch=show_plugin_details&plugin_id=<?=$p->getPluginId()?>"><?=htmlReady($p->getName())?></A> <IMG SRC="images/languages/lang_<?=$p->getLanguage()?>.gif"></DIV>
<? if (!$p->getApproved()) : ?>
        <DIV STYLE="float:right;"><IMG SRC="images/icons/delete.png" ALT="Plugin noch nicht freigegeben" TITLE="Plugin noch nicht freigegeben"></DIV>
<? /* else : ?>
<? if ($p->getClassification() == 'firstclass') : ?>
        <DIV STYLE="float:right;"><IMG SRC="images/icons/award_star_gold_1.png" ALT="Premium Gold Plugin" TITLE="Premium Gold Plugin"></DIV>
<? endif ?>
<? if ($p->getClassification() == 'secondclass') : ?>
        <DIV STYLE="float:right;"><IMG SRC="images/icons/award_star_silver_1.png" ALT="Premium Silver Plugin" TITLE="Premium Silver Plugin"></DIV>
<? endif */ ?>
<? endif ?>
        <DIV STYLE="clear:both; padding-bottom:5px; padding-top:5px;">
        <DIV STYLE="padding-top:5px; font-size:12px;">
          <?=Avatar::getAvatar($p->getUserId())->getImageTag(Avatar::SMALL)?> <A HREF="?dispatch=show_profile&username=<?=UserManagement::getUsernameByUserId($p->getUserId())?>&plugin_id=<?=$p->getPluginId()?>"><?=UserManagement::getFullnameByUserId($p->getUserId())?></A>
          <!-- TABLE BORDER=0>
            <TR><TD STYLE="font-size:12px; font-weight:bold; vertical-align:top;">Autor: </TD><TD><?=Avatar::getAvatar($p->getUserId())->getImageTag(Avatar::SMALL)?> <A HREF="?dispatch=show_profile&username=<?=UserManagement::getUsernameByUserId($p->getUserId())?>&plugin_id=<?=$p->getPluginId()?>"><?=UserManagement::getFullnameByUserId($p->getUserId())?></A></TD></TR>
          </TABLE -->
        </DIV>
      </TD>
    </TR>
    <TR>
      <TD STYLE="width:100px; min-width:100px; vertical-align:bottom; text-align:center;">
<? if ($r = $p->getLatestRelease()) : ?>
        <A HREF="?dispatch=download&file_id=<?=$r->getFileId()?>" STYLE="margin-bottom:10px; text-decoration:none;" ALT="Download latest release" TITLE="Download latest release">
        <span class="download_label">Download</span><br><IMG SRC="images/icons/16/blue/download.png" border=0 ALT="Download latest release" TITLE="Download latest release"></A>
<? endif ?>
      </TD>
      <TD>
        <DIV STYLE="padding-top:15px;"><?=htmlReady($p->getShortDescription())?><BR>
<? if (count($p->getTags()) > 0) : ?>
        <DIV STYLE="padding-top:15px;"><SPAN STYLE="font-weight:bold; font-size:12px;">Tags:</SPAN> <SPAN STYLE="font-size:12px;">|</SPAN>
<? foreach ($p->getTags() as $t) : ?>
          <SPAN STYLE="font-size:12px;"><A HREF="?dispatch=tagsearch&tag=<?=urlencode($t)?>"><?=htmlReady($t)?></A> | </SPAN>
<? endforeach ?>
        </DIV>
<? endif ?>
        <HR>
<? if ($p->getChdate()) : ?>
        <DIV STYLE="float:left; margin-right:15px; font-size:10px; color:gray;">
          Aktualisiert am <?=date('d.m.Y',$p->getChdate())?>
        </DIV>
<? endif ?>
        <DIV STYLE="float:right;"><A HREF="?dispatch=show_plugin_details&plugin_id=<?=$p->getPluginId()?>">mehr...</A></DIV>
        <DIV STYLE="clear:both;"></DIV>
        <!-- HR -->
        <!-- DIV STYLE="float:left; margin-right:15px;">
          <SPAN STYLE="font-weight:bold; font-size:12px;"><?=_("Bewertung der Nutzer:")?></SPAN><BR>
          <SPAN ID="rating"><?=$rating?></SPAN><BR>
          <SPAN ID="ratinghint" STYLE="font-size:12px; font-weight:bold; color:green; height:15px;">&nbsp;</SPAN>
        </DIV -->
<? if ($GLOBALS['PERM']->have_plugin_perm('author',$p->getPluginId())) : ?>
        <DIV STYLE="float:right;"><A HREF="?dispatch=edit_plugin&plugin_id=<?=$p->getPluginId()?>"><?=makeButton('bearbeiten','img')?></A></DIV>
<? endif ?>
      </TD>
    </TR>
  </TABLE>
</DIV>
</CENTER>
