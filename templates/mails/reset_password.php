Sehr geehrte<?=($u->getSalutation()=='Herr' ? 'r' : '')?> <?=$u->getSalutation()?> <?=$GLOBALS['UM']->getFullnameByUserId($u->getUserId())?>,

Sie haben am <?=date('d.m.Y',time())?> um <?=date('H:i')?> ein neues Passwort für den Stud.IP Plugin-Marktplatz angefordert bzw. es wurde für Sie neu gesetzt. Das alte Passwort verliert ab sofort seine Gültigkeit. Ihr neues Passwort lautet:

<?=$new_pw?>


Viele Grüsse,

Das Stud.IP Plugin-Marktplatz Team
