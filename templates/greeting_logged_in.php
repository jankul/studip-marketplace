<div style="margin-left: 1.5em;">
<? if ($GLOBALS['PERM']->have_perm('admin')) : ?>
  <a class="click_me" href="#" onClick="location.href='?dispatch=user_management';">
    <div>
      <span class="click_head"> Benutzerverwaltung </span>
      <p>Bearbeiten von Nutzerdetails und Neueintragen von Personen.</p>
    </div>
  </a>
  <a class="click_me" href="#" onClick="location.href='?dispatch=clearing';">
    <div>
      <span class="click_head"> Plugins freischalten </span>
      <p>Neu eingetragene Plugins f&uuml;r die &Ouml;ffentlichkeit freischalten.</p>
    </div>
  </a>
<? endif ?>
<? if ($GLOBALS['PERM']->have_perm('author') && !$GLOBALS['PERM']->have_perm('admin')) : ?>
  <a class="click_me" href="#" onClick="location.href='?dispatch=assi';">
    <div>
      <span class="click_head"> Plugin eintragen </span>
      <p>Tragen Sie hier ein neues Plugin ein. Anschlie&szlig;end wird es von einem Administrator gepr&uuml;ft und freigegeben.</p>
    </div>
  </a>
  <a class="click_me" href="#" onClick="location.href='?dispatch=view_own_plugins';">
    <div>
      <span class="click_head"> Meine Plugins </span>
      <p>Hier erhalten Sie eine &Uuml;bersicht &uuml;ber die von Ihnen eingetragenen Plugins.</p>
    </div>
  </a>
<? endif ?>
  <a class="click_me" href="#" onClick="location.href='?dispatch=show_profile';">
    <div>
      <span class="click_head"> Mein Profil </span>
      <p>Bearbeiten Sie hier Ihre pers&ouml;nlichen Nutzerdaten innerhalb des Plugin-Marktplatzes.</p>
    </div>
  </a>
  <a class="click_me" href="#" onClick="location.href='?dispatch=faq';">
    <div>
      <span class="click_head"> FAQ </span>
      <p>Haben Sie Fragen zum Umgang mit dem Plugin-Marktplatz oder allgemein zu Plugins? Hier finden Sie sicherlich eine Antwort.</p>
    </div>
  </a>
</div>
<!--
<? if ($GLOBALS['PERM']->have_perm('admin')) : ?>
<DIV CLASS="logged_in_big_button" onClick="location.href='?dispatch=user_management';">
  <DIV CLASS="logged_in_big_button_label">Benutzerverwaltung</DIV>
</DIV>
<DIV CLASS="logged_in_big_button" onClick="location.href='?dispatch=clearing';">
  <DIV CLASS="logged_in_big_button_label">Plugins freischalten</DIV>
</DIV>
<? endif ?>
<? if ($GLOBALS['PERM']->have_perm('author') && !$GLOBALS['PERM']->have_perm('admin')) : ?>
<DIV CLASS="logged_in_big_button" onClick="location.href='?dispatch=assi';">
  <DIV CLASS="logged_in_big_button_label">Plugin eintragen</DIV>
</DIV>
<DIV CLASS="logged_in_big_button" onClick="location.href='?dispatch=view_own_plugins';">
  <DIV CLASS="logged_in_big_button_label">Meine Plugins</DIV>
</DIV>
<? endif ?>
<DIV CLASS="logged_in_big_button" onClick="location.href='?dispatch=show_profile';">
  <DIV CLASS="logged_in_big_button_label">Mein Profil</DIV>
</DIV>
<DIV CLASS="logged_in_big_button" onClick="location.href='?dispatch=faq';">
  <DIV CLASS="logged_in_big_button_label">FAQ</DIV>
</DIV>

-->
