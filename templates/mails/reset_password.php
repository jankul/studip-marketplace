Sehr geehrte<?=($u->getSalutation()=='Herr' ? 'r' : '')?> <?=$u->getSalutation()?> <?=$GLOBALS['UM']->getFullnameByUserId($u->getUserId())?>,

Sie haben am <?=date('d.m.Y',time())?> um <?=date('H:i')?> ein neues Passwort f�r den Stud.IP Plugin-Marktplatz angefordert bzw. es wurde f�r Sie neu gesetzt. Das alte Passwort verliert ab sofort seine G�ltigkeit. Ihr neues Passwort lautet:

<?=$new_pw?>


Viele Gr�sse,

Das Stud.IP Plugin-Marktplatz Team
