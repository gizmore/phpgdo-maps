<?php
declare(strict_types=1);
namespace GDO\Maps;

use GDO\Core\GDT_Float;

/** A velocity in kilometres per hour. */
final class GDT_Velocity extends GDT_Float
{
	public function gdtDefaultLabel(): ?string
	{
		return 'velocity';
	}
}
