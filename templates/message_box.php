<div class="messagebox messagebox_<?= $class ?>">
	<div class="messagebox_buttons">
		<? if (sizeof($details) && $close_details) : ?>
		<a href="#" onclick="Effect.toggle($(this).up().next('.messagebox_details'), 'blind'); $(this).select('img').each(Element.toggle); return false;">
			<IMG SRC="images/icons/maximize_inv.png" ALT="<?=_('Details anzeigen')?> TITLE="<?= _('Details anzeigen')?>>
			<IMG SRC="images/icons/minimize_inv.png" ALT="<?=_('Details ausblenden')?> TITLE="<?=_('Details ausblenden')?> STYLE="display: none;">
		</a>
		<? endif ?>
		<a href="#" onclick="$(this).up('.messagebox').fade(); return false;">
			<IMG SRC="images/icons/cross_inv.png" ALT="close" TITLE="<?=_('Nachrichtenbox schließen')?>">
		</a>
	</div>
	<?= $message ?>
	<? if (sizeof($details)) : ?>
	<div class="messagebox_details"<? if ($close_details) : ?> style="display: none;" <? endif ?>>
		<ul>
			<? foreach ($details as $li) : ?>
				<li><?= $li ?></li>
			<? endforeach ?>
		</ul>
	</div>
	<? endif ?>
</div>
