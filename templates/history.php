<DIV CLASS="history_container">
  <A HREF="?">Home</A>
<? foreach ($_SESSION['history'] as $h) : ?>
 :: <A HREF="<?=$h['uri']?>"><?=$h['txt']?></A>
<? endforeach ?>
</DIV>
