<?php
namespace GDO\Maps;

use GDO\User\GDO_User;
use GDO\Core\GDT_Composite;

/**
 * Lat/Lng position GDT.
 * Adds two columns to a database table.
 * 
 * @see Position
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.2.0
 */
final class GDT_Position extends GDT_Composite
{
	public function defaultLabel() : self { return $this->label('position'); }
	
	public GDT_Lat $lat;
	public GDT_Lng $lng;
	
	protected function __construct()
	{
		parent::__construct();
		$this->horizontal();
	}
	
	public function gdoCompositeFields() : array
	{
		$name = $this->name;
		$this->lat = GDT_Lat::make("{$name}_lat");
		$this->lng = GDT_Lng::make("{$name}_lng");
		return [
			$this->lat,
			$this->lng,
		];
	}
	
	########################
	### Current Position ###
	########################
	public bool $defaultCurrent = false;
	public function defaultCurrent(bool $defaultCurrent=true) : self
	{
		if (Module_Maps::instance()->cfgRecord())
		{
			$this->defaultCurrent = $defaultCurrent;
			$user = GDO_User::current();
			$position = $user->settingValue('Maps', 'position');
			return $this->initialPosition($position);
		}
		return $this;
	}

	#############
	### Value ###
	#############
// 	public function inputToVar($input = null) : ?string
// 	{
// 		$input = trim($input, "\r\n\t []");
// 		return $input ? "[{$input}]" : null;
// 	}
	
	public function getValue()
	{
		$lat = $this->lat->getValue();
		$lng = $this->lng->getValue();
		if ( ($lat !== null) && ($lng !== null) )
		{
			return new Position($lat, $lng);
		}
		return null;
	}
	
// 	public function toVar($value) : ?string
// 	{
// 	    return $value === null ? null : json_encode(
// 	        [$value->getLat(), $value->getLng()]);
// 	}

	public function initialPosition(Position $position) : self
	{
		return $this->initialLatLng($position->getLat(), $position->getLng());
	}
	
	public function initialLatLng(float $lat, float $lng) : self
	{
		$this->lat->initial($lat);
		$this->lng->initial($lng);
		return $this;
	}
	
// 	public function initial(string $var) : self
// 	{
		
// 	}
	
	public function getLat() : string
	{
		return $this->lat->getVar();
	}

	public function getLng() : string
	{
		return $this->lng->getVar();
	}
	
	##############
	### Render ###
	##############
	public function initJSON()
	{
		return [
			'lat' => $this->getLat(),
			'lng' => $this->getLng(),
			'defaultCurrent' => $this->defaultCurrent,
		];
	}
	public function renderCLI() : string
	{
		/**
		 * @var Position $pos
		 */
		$pos = $this->getValue();
		return $pos->displayLat() . $pos->displayLng();
	}
	
// 	public function renderForm() : string
// 	{
// 		return GDT_Template::php('Maps', 'form/position.php', ['field' => $this]);
// 	}
	
// 	public function renderCell() : string
// 	{
// 		return GDT_Template::php('Maps', 'cell/position.php', ['field' => $this]);
// 	}
	
	##################
	### Validation ###
	##################
// 	public function validate($value) : bool
// 	{
// 		if ($value === null)
// 		{
// 			return $this->notNull ? $this->errorNotNull() : true;
// 		}
// 		return $this->validatePosition($value);
// 	}
	
// 	private function validatePosition(Position $pos) : bool
// 	{
// 		if (!$pos->hasValidLat())
// 		{
// 			return $this->error('err_latitude');
// 		}
// 		elseif (!$pos->hasValidLng())
// 		{
// 			return $this->error('err_longitude');
// 		}
// 		else
// 		{
// 			return true;
// 		}
// 	}

}
