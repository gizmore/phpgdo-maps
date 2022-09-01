<?php
namespace GDO\Maps\tpl;
/** @var $pos \GDO\Maps\Position **/
/** @var $field \GDO\Maps\GDT_Position **/
$pos = $field->getValue();
if (!$pos)
{
	echo t('unknown');
}
else
{
	printf('<span>%s<br/>%s</span>', $pos->displayLat(), $pos->displayLng());
}
