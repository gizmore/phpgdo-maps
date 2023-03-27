<?php
namespace GDO\Maps\Method;

use GDO\Core\GDT;
use GDO\Core\MethodAjax;
use GDO\Maps\GDO_UserPosition;
use GDO\Maps\GDT_Position;
use GDO\Maps\Module_Maps;
use GDO\User\GDO_User;

/**
 * Record user location.
 *
 * @author gizmore
 */
final class Record extends MethodAjax
{

	public function gdoParameters(): array
	{
		return [
			GDT_Position::make('pos')->notNull(),
		];
	}

	public function execute(): GDT
	{
		$module = Module_Maps::instance();
		$gdt = $this->gdoParameter('pos');
		if ($module->cfgRecord())
		{
			$module->saveSetting('position', $gdt->getVar());
		}
		if ($module->cfgHistory())
		{
			GDO_UserPosition::record(GDO_User::current(), $gdt->getValue());
		}
		return $this->message('msg_location_recorded');
	}

}
