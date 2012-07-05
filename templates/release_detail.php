<DIV ID="r_<?=$r->getReleaseId()?>" STYLE="margin-left:15px; padding:5 0 5 0; border:1px solid gray;">
  <DIV STYLE="padding:5px; font-size:14px; font-weight:bold;"><A HREF="?dispatch=show_plugin_details&plugin_id=<?=$p->getPluginId()?>"><?=htmlReady($p->getName())?></A></DIV>
  <TABLE BORDER=0 WIDTH="100%">
    <TR>
      <TD STYLE="vertical-align:top; text-align:center; width:35px; text-align:center;">
        <A HREF="?dispatch=download&file_id=<?=$r->getFileId()?>" ALT="Download" TITLE="Download" style="text-decoration:none;"><span class="download_label">Download</span><br><IMG SRC="images/icons/16/blue/download.png" border=0 ALT="Download" TITLE="Download"></A>
      </TD>
      <TD STYLE="vertical-align:top;">
	  <SPAN STYLE="font-size:12px; font-weight:bold;">Version: </SPAN><SPAN STYLE="font-size:12px;"><?=htmlReady($r->getVersion())?></SPAN>, <SPAN STYLE="font-size:12px; font-weight:bold;">Dateiname: </SPAN><SPAN STYLE="font-size:12px;"><A HREF="?dispatch=download&file_id=<?=$r->getFileId()?>"><?=htmlReady($r->getFile()->getFileName())?></A></SPAN><BR>
	<? if ($r->getOrigin()) : ?>
	  <SPAN STYLE="font-size:10px; font-weight:bold;">Hersteller: </SPAN><SPAN STYLE="font-size:10px;"><?=htmlReady($r->getOrigin())?></SPAN><BR>
	<? endif ?>
	<? if ($r->getStudipMinVersion()) : ?>
	  <SPAN STYLE="font-size:10px; font-weight:bold;">Minimale Stud.IP Version: </SPAN><SPAN STYLE="font-size:10px;"><?=htmlReady($r->getStudipMinVersion())?></SPAN><BR>
	<? endif ?>
	<? if ($r->getStudipMaxVersion()) : ?>
	  <SPAN STYLE="font-size:10px; font-weight:bold;">Maximale Stud.IP Version: </SPAN><SPAN STYLE="font-size:10px;"><?=htmlReady($r->getStudipMaxVersion())?></SPAN><BR>
	<? endif ?>
	<? if ($r->getReleaseType()) : ?>
	  <SPAN STYLE="font-size:10px; font-weight:bold;">Release Art: </SPAN><SPAN STYLE="font-size:10px;"><?=htmlReady($r->getReleaseType())?></SPAN><BR>
	<? endif ?>
	<? if ($deps = $r->getDependencies()) : ?>
	  <IMG SRC="images/ausruf_small3.gif">&nbsp;<SPAN STYLE="font-size:12px; font-weight:bold;">Dieses Release ben&ouml;tigt zwingend die folgenden weiteren Releases: </SPAN><BR>
	  <UL>
	<? foreach ($deps as $d) : ?>
	<? $p = new Plugin(); $p->load($r->getPluginId()); ?>
	    <LI><A HREF="?dispatch=show_release_details&release_id=<?=$r->getReleaseId()?>"><?=htmlReady($p->getName())?>, <?=htmlReady($r->getVersion())?></A></LI>
	<? endforeach ?>
	  </UL>
	<? endif ?>
	  <!-- DIV STYLE="float:right; margin-right:15px;">
	    <SPAN STYLE="font-weight:bold; font-size:12px;"><?=_("Bewertung der Nutzer:")?></SPAN><BR>
	    <SPAN ID="rating"><?=$rating?></SPAN><BR>
	    <SPAN ID="ratinghint_<?=$r->getReleaseId()?>" STYLE="font-size:12px; font-weight:bold; color:green; height:15px;">&nbsp;</SPAN>
	    <BR><SPAN ID="rating_txt" STYLE="font-size:0.7em; font-weight:bold; white-space:nowrap;"></span>
	  </DIV>
	  <DIV STYLE="clear:both;"></div -->
      </TD>
    </TR>
  </TABLE>
  <DIV STYLE="padding-top:10px;">
<?=$comments?>
  </DIV>
</DIV>
