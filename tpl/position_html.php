<?php
namespace GDO\Maps\tpl;

use GDO\Maps\GDT_Position;
use GDO\Maps\Position;

/** @var $pos Position * */
/** @var $field GDT_Position * */
$pos = $field->getValue();
if (!$pos)
{
	echo t('unknown');
}
else
{
	printf('<span>%s<br/>%s</span>', $pos->displayLat(), $pos->displayLng());
}
