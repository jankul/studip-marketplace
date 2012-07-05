<?=$css->GetHoverJSFunction()?>
<script type="text/javascript">
Event.observe(window, 'load', function() {
        new accordion('accordion_container', {
                classNames : {
                        toggle : 'accordion_toggle',
                        toggleActive : 'accordion_toggle_active',
                        content : 'accordion_content'
                },
                direction : 'vertical'
        });
});

</SCRIPT><CENTER>
<DIV CLASS="plugin_detail">
  <TABLE BORDER=0 WIDTH="100%">
    <TR>
      <TD STYLE="width:100px; max-width:100px; vertical-align:top;">
<? if ($screen = $p->getTitleScreen()) : ?>
        <A ID="f<?=$screen->getFileId()?>" HREF="<?=$GLOBALS['BASE_URI']?>?dispatch=download&file_id=<?=$screen->getFileId()?>" TITLE="<?=htmlReady($screen->getTitel())?>"><IMG SRC="<?=$GLOBALS['DYNAMIC_CONTENT_URL'] . '/screenshots/'?><?=$screen->getFileId()?>_thumb" WIDTH="100"></A>
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
        <H4><?=htmlReady($p->getName())?> <IMG SRC="images/languages/lang_<?=$p->getLanguage()?>.gif"></H4>
        <DIV CLASS="accordion_content">
          <DIV STYLE="padding-top:5px; font-size:12px;">
            <?=Avatar::getAvatar($p->getUserId())->getImageTag(Avatar::SMALL)?><A HREF="?dispatch=show_profile&username=<?=UserManagement::getUsernameByUserId($p->getUserId())?>&plugin_id=<?=$p->getPluginId()?>"><?=UserManagement::getFullnameByUserId($p->getUserId())?></A><BR>
          <TABLE BORDER=0>
            <!-- TR><TD STYLE="font-size:12px; font-weight:bold; vertical-align:top;">Autor: </TD><TD><?=Avatar::getAvatar($p->getUserId())->getImageTag(Avatar::SMALL)?><A HREF="?dispatch=show_profile&username=<?=UserManagement::getUsernameByUserId($p->getUserId())?>&plugin_id=<?=$p->getPluginId()?>"><?=UserManagement::getFullnameByUserId($p->getUserId())?></A></TD><TR -->
<? if (count($users = $p->getParticipants())) : ?>
            <TR><TD STYLE="font-size:12px; font-weight:bold; vertical-align:top;">Mitwirkende: </TD>
                <TD STYLE="font-size:12px;">
<? foreach ($users as $u) : ?>
                  <TABLE BORDER=0><TR><TD STYLE="vertical-align:middle;"><?=Avatar::getAvatar($u->getUserId())->getImageTag(Avatar::SMALL)?></TD><TD STYLE="vertical-align:middle;"> <A HREF="?dispatch=show_profile&username=<?=UserManagement::getUsernameByUserId($u->getUserId())?>&plugin_id=<?=$p->getPluginId()?>"><?=UserManagement::getFullnameByUserId($u->getUserId())?></A></TD></TR></TABLE><BR>
<? endforeach ?>
                </TD></TR>
<? endif ?>
            <TR><TD STYLE="font-size:12px; font-weight:bold; vertical-align:top;">Eingetragen: </TD><TD STYLE="font-size:12px;"><?=date('d.m.Y',$p->getMkdate())?></TD></TR>
<? if ($url = $p->getUrl()) : ?>
            <TR><TD STYLE="font-size:12px; font-weight:bold; vertical-align:top;">Homepage: </TD><TD><A HREF="<?=htmlReady($url)?>" TARGET="_blank"><!-- IMG SRC="images/icons/world_link.png" --><?=htmlReady($url)?></A></TD></TR>
<? endif ?>
<? if ($p->getRezension()) : ?>
            <TR><TD STYLE="font-size:12px; font-weight:bold; vertical-align:top;">Rezension: </TD><TD><a href="#mpdialog" name="modal" onClick="launchWindow('#mpdialog','<?=$p->getPluginId()?>');">anzeigen</a></TD></TR>
<? endif ?>
          </TABLE>
        </DIV>
          <DIV STYLE="padding-top:15px; font-size:12px;">
            <?=htmlReady($p->getShortDescription())?>
          </DIV>
<? if (count($p->getTags()) > 0) : ?>
          <DIV STYLE="padding-top:15px;">
            <SPAN STYLE="font-weight:bold; font-size:12px;">Tags:</SPAN> <SPAN STYLE="font-size:12px;">|</SPAN>
<? foreach ($p->getTags() as $t) : ?>
            <SPAN STYLE="font-size:12px;"><A HREF="?dispatch=tagsearch&tag=<?=urlencode($t)?>"><?=htmlReady($t)?></A> | </SPAN>
<? endforeach ?>
          </DIV>
<? endif ?>
        </DIV>
<DIV ID="accordion_container">
<? if ($p->getDescription()) : ?>
        <H4 CLASS="accordion_toggle">Beschreibung</H4>
        <DIV CLASS="accordion_content">
          <?=$p->getDescription()?>
        </DIV>
<? endif ?>
<? if ($releases ) : ?>
        <H4 CLASS="accordion_toggle">Releases / Downloads (<?=count($p->getReleases())?>)</H4>
        <DIV CLASS="accordion_content">
<?=$releases?>
        </DIV>
<? endif ?>
<? if (count($shots = $p->getAllScreenshots())) : ?>
          <H4 CLASS="accordion_toggle">Screenshots (<?=count($shots)?>)</H4>
          <DIV CLASS="accordion_content">
<? foreach ($shots as $s) : ?>
            <DIV CLASS="screenshot_frame_thumb_public">
              <DIV CLASS="screenshot_frame_thumb_link">
                <A HREF="<?=$GLOBALS['DYNAMIC_CONTENT_URL'] . '/screenshots/'?><?=$s->getFileId()?>" rel="lightbox" TITLE="<?=htmlReady($s->getTitel())?>"><IMG SRC="<?=$GLOBALS['DYNAMIC_CONTENT_URL'] . '/screenshots/'?><?=$s->getFileId()?>_thumb" CLASS="screenshot_frame_thumb_img_public" TITLE="<?=htmlReady($s->getTitel())?>" ALT="<?=htmlReady($s->getTitel())?>"></A>
              </DIV>
            </DIV>
<? endforeach ?>
            <DIV STYLE="clear:both;"></DIV>
<script type="text/javascript">
$j(window).load(function() {
        $j("a[rel='lightbox']").lightBox({
                txtImage: $j(this).attr('title')
        }); 
});
</script>
          </DIV>
<? endif ?>
</DIV>
        <DIV STYLE="padding-top:10px;">
<?=$comments?>
        </DIV>
        <HR>
        <!-- DIV STYLE="float:left; margin-right:15px;">
          <SPAN STYLE="font-weight:bold; font-size:12px;"><?=_("Durchschnittliche Release-Bewertung:")?></SPAN><BR>
          <SPAN ID="rating"><?=$rating?></SPAN><BR>
          <SPAN ID="ratinghint" STYLE="font-size:12px; font-weight:bold; color:green; height:15px;">&nbsp;</SPAN>
<? if ($rates = $p->getUserRatings()) : ?>
          <DIV STYLE="margin-right:15px; font-size:10px; color:gray;">
            <?=$rates['anzahl']?> Bewertung<?=($rates['anzahl'] != 1 ? 'en' : '')?>
          </DIV>
<? endif ?>
        </DIV -->
<? if ($GLOBALS['PERM']->have_plugin_perm('author',$p->getPluginId())) : ?>
	<DIV STYLE="float:right;"><A HREF="?dispatch=edit_plugin&plugin_id=<?=$p->getPluginId()?>"><?=makeButton('bearbeiten','img')?></A></DIV>
<? endif ?>
<? if ($p->getChdate()) : ?>
        <DIV STYLE="clear:both;"></DIV>
        <DIV STYLE="margin-right:15px; font-size:10px; color:gray;">
          Aktualisiert am <?=date('d.m.Y',$p->getChdate())?>
        </DIV>
<? endif ?>
      </TD>
    </TR>
    <TR>
      <TD STYLE="width:100px; max-width:100px; vertical-align:bottom; text-align:center;">
<? if ($r = $p->getLatestRelease()) : ?>
        <A HREF="?dispatch=download&file_id=<?=$r->getFileId()?>" STYLE="margin-bottom:10px; text-decoration:none;" ALT="Download latest release" TITLE="Download latest release"><span class="download_label">Download</span><br>
        <IMG SRC="images/icons/16/blue/download.png" border=0 ALT="Download latest release" TITLE="Download latest release"></A>
<? endif ?>
      </TD>
    </TR>
  </TABLE>
</DIV>
</CENTER>
