<?php
namespace GDO\Maps;

use GDO\Core\GDT_Float;

/**
 * A longitude.
 *
 * @version 7.0.1
 * @author gizmore
 */
final class GDT_Lng extends GDT_Float
{

	protected function __construct()
	{
		parent::__construct();
		$this->icon('position');
		$this->min(-180)->max(180);
	}

	public function gdtDefaultLabel(): ?string
    { return 'longitude'; }

	public function plugVars(): array
	{
		return [
			[$this->getName() => '10.2352'],
		];
	}

}
