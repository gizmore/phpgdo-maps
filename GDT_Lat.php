<?php
namespace GDO\Maps;

use GDO\Core\GDT_Float;

/**
 * A latitude.
 * 
 * @author gizmore
 * @version 7.0.1
 */
final class GDT_Lat extends GDT_Float
{
	public function defaultLabel() : self { return $this->label('latitude'); }
	
	protected function __construct()
	{
		parent::__construct();
		$this->icon('position');
		$this->min(-90)->max(90);
	}
	
	public function plugVars() : array
	{
		return [
			[$this->getName() => '52.31928'],
		];
	}
	
}
