<DIV ID="r_<?=$r->getReleaseId()?>" CLASS="release_body">
  <TABLE BORDER=0 WIDTH="100%">
    <TR>
      <TD STYLE="vertical-align:top; width:35px; text-align:center;">
        <A HREF="?dispatch=download&file_id=<?=$r->getFileId()?>" style="text-decoration:none;" ALT="Download" TITLE="Download"><span class="download_label">Download</span><br><IMG SRC="images/icons/16/blue/download.png" ALT="Download" TITLE="Download"></A>
      </TD>
      <TD STYLE="vertical-align:top;">
  <SPAN STYLE="font-size:12px; font-weight:bold;">Version: </SPAN><SPAN STYLE="font-size:12px;"><?=htmlReady($r->getVersion())?></SPAN>, <SPAN STYLE="font-size:12px; font-weight:bold;">Dateiname: </SPAN><img src="images/icons/16/blue/file-archive.png"> <SPAN STYLE="font-size:12px;"><A HREF="?dispatch=download&file_id=<?=$r->getFileId()?>"><?=htmlReady($r->getFile()->getFileName())?></A></SPAN><BR>
<? if ($r->getOrigin()) : ?>
  <SPAN STYLE="font-size:12px; font-weight:bold;">Hersteller: </SPAN><SPAN STYLE="font-size:12px;"><?=htmlReady($r->getOrigin())?></SPAN><BR>
<? endif ?>
<? if ($r->getStudipMinVersion()) : ?>
  <SPAN STYLE="font-size:12px; font-weight:bold;">Minimale Stud.IP Version: </SPAN><SPAN STYLE="font-size:12px;"><?=htmlReady($r->getStudipMinVersion())?></SPAN><BR>
<? endif ?>
<? if ($r->getStudipMaxVersion()) : ?>
  <SPAN STYLE="font-size:12px; font-weight:bold;">Maximale Stud.IP Version: </SPAN><SPAN STYLE="font-size:12px;"><?=htmlReady($r->getStudipMaxVersion())?></SPAN><BR>
<? endif ?>
<? if ($r->getReleaseType()) : ?>
  <SPAN STYLE="font-size:12px; font-weight:bold;">Release Art: </SPAN><SPAN STYLE="font-size:12px;"><?=htmlReady($r->getReleaseType())?></SPAN><BR>
<? endif ?>
  <SPAN STYLE="font-size:12px; font-weight:bold;">Downloads: </SPAN><SPAN STYLE="font-size:12px;"><?=$r->getDownloads()?></SPAN><BR>
<? if ($r->getChdate()) : ?>
        <DIV STYLE="float:left; margin-right:15px; font-size:12px; color:gray;">
          Aktualisiert am <?=date('d.m.Y',$r->getChdate())?>
        </DIV>
<? endif ?>
         <!-- DIV STYLE="float:right; margin-right:15px;">
           <SPAN STYLE="font-weight:bold; font-size:12px;"><?=_("Bewertung der Nutzer:")?></SPAN><BR>
           <SPAN ID="rating_<?=$r->getReleaseId()?>"><?=$rating?></SPAN><BR>
           <SPAN ID="ratinghint_<?=$r->getReleaseId()?>" STYLE="font-size:12px; font-weight:bold; color:green; height:15px;">&nbsp;</SPAN>
           <BR><SPAN ID="rating_txt_<?=$r->getReleaseId()?>" STYLE="font-size:0.7em; font-weight:bold; white-space:nowrap;">
         </DIV -->
         <DIV STYLE="clear:both;" />
        <DIV STYLE="float:right;"><A HREF="?dispatch=show_release_details&release_id=<?=$r->getReleaseId()?>">mehr...</A></DIV>
      </TD>
    </TR>
  </TABLE>
</DIV>
