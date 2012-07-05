<LINK REL="stylesheet" HREF="<?=$css_uri?>/tagcloud.css" TYPE="text/css" />
<DIV ID="cloud">
<!-- UL ID="cloud" -->
<? foreach ($tags as $t) : ?>
  <LI><A CLASS="tag<?=$t['tag_weight']?>" HREF="?dispatch=tagsearch&tag=<?=urlencode(stripslashes($t['tag']))?>" onMouseOver="this.style.backgroundColor='#FFFF80';" onMouseOut="this.style.backgroundColor='';"><?=stripslashes($t['tag'])?></A></LI>
<? endforeach ?>
<!-- /UL -->
</DIV>
