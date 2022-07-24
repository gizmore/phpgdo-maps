<?php
use GDO\Core\GDT_String;
use GDO\Maps\GDT_Position;
use GDO\UI\GDT_Icon;
$field instanceof GDT_Position;
?>
<?php /** @var $field GDT_String **/ ?>
<div class="gdt-container<?= $field->classError(); ?>">
  <label <?=$field->htmlForID()?>><?= $field->displayLabel(); ?></label>
  <?= $field->htmlIcon(); ?>
  <input
   type="text"
   <?=$field->htmlID()?>
   <?=$field->htmlRequired()?>
   <?=$field->htmlDisabled()?>
   size="12"
   <?=$field->htmlFormName()?>
   value="<?= $field->renderVar(); ?>" />
  <?= $field->htmlError(); ?>
</div>
