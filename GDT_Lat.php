<?php
namespace GDO\Maps;

use GDO\Core\GDT_Float;

/**
 * A latitude.
 *
 * @version 7.0.1
 * @author gizmore
 */
final class GDT_Lat extends GDT_Float
{

	protected function __construct()
	{
		parent::__construct();
		$this->icon('position');
		$this->min(-90)->max(90);
	}

	public function defaultLabel(): self { return $this->label('latitude'); }

	public function plugVars(): array
	{
		return [
			[$this->getName() => '52.31928'],
		];
	}

}
