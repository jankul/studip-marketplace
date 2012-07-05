<DIV ID="r_<?=$r->getReleaseId()?>">
<INPUT TYPE="hidden" NAME="dep_ids[]" VALUE="<?=$r->getReleaseId()?>">
<IMG SRC="images/trash.gif" onClick="removeRelease('<?=$r->getReleaseId()?>');" STYLE="cursor:pointer;">&nbsp;<? if ($p) : ?><?=htmlReady($p->getName())?>, <? endif ?><?=$r->getVersion()?>
</DIV>
