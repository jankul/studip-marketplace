<DIV CLASS="profile">
<TABLE BORDER=0>
  <TR>
    <TD STYLE="width:80px; vertical-align:top;"><?=Avatar::getAvatar($user_id)->getImageTag(Avatar::MEDIUM)?></TD>
    <TD STYLE="width:500px; vertical-align:top;">
      <TABLE BORDER=0>
        <TR>
          <TD COLSPAN=2><SPAN STYLE="font-size:12px; font-weight:bold;"><?=UserManagement::getFullnameByUserId($user_id)?></SPAN></TD>
        </TR>
<? if ($u->getUrl()) : ?>
        <TR>
          <TD><SPAN STYLE="font-size:12px; font-weight:bold;">Homepage: </SPAN></TD><TD><A HREF="<?=htmlReady($u->getUrl())?>" TARGET="_blank"><?=htmlReady($u->getUrl())?></A></TD>
        </TR>
<? endif ?>
<? if ($u->getWorkplace()) : ?>
        <TR>
          <TD><SPAN STYLE="font-size:12px; font-weight:bold;">Arbeitsstelle: </SPAN></TD><TD><?=htmlReady($u->getWorkplace())?></TD>
        </TR>
<? endif ?>
      </TABLE>
    </TD>
  </TR>
<? if ($GLOBALS['USER'] && $GLOBALS['USER']['user_id'] == $user_id) : ?>
  <TR>
    <TD COLSPAN="2" STYLE="text-align:center;">
      <A HREF="?dispatch=edit_profile"><IMG <?=makeButton('bearbeiten','src')?>></A>
    </TD>
  </TR>
<? endif ?>
</TABLE>
</DIV>
