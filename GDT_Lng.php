<?php
namespace GDO\Maps;

use GDO\Core\GDT_Float;

/**
 * A longitude.
 * 
 * @author gizmore
 * @version 7.0.1
 */
final class GDT_Lng extends GDT_Float
{
	public function defaultLabel() : self { return $this->label('longitude'); }
	
	protected function __construct()
	{
		parent::__construct();
		$this->icon('position');
		$this->min(-180)->max(180);
	}
	
	public function plugVars() : array
	{
		return [
			[$this->getName() => '10.2352'],
		];
	}

}
