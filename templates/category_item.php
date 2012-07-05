<DIV ID="c_<?=$c['category_id']?>">
<IMG SRC="images/trash.gif" onClick="removeCategory('<?=$c['category_id']?>');" STYLE="cursor:pointer;">&nbsp;<?=$c['name']?>
<INPUT TYPE="hidden" NAME="c_ids[]" CLASS="sel_categories" VALUE="<?=$c['category_id']?>">
</DIV>
