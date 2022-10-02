<?php
namespace GDO\Maps;

use GDO\User\GDO_User;
use GDO\Core\GDT_Composite;
use GDO\UI\TextStyle;

/**
 * Lat/Lng position GDT.
 * Adds two columns to a database table.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.2.0
 * @see Position
 */
final class GDT_Position extends GDT_Composite
{
	public function isSerializable() : bool { return true; }
	public function isSearchable() :  bool { return false; }
	
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
	public bool $initialCurrent = false;
	public function initialCurrent(bool $initialCurrent=true) : self
	{
		if (Module_Maps::instance()->cfgRecord())
		{
			$this->initialCurrent = $initialCurrent;
			$user = GDO_User::current();
			$position = $user->settingValue('Maps', 'position');
			return $this->initialPosition($position);
		}
		return $this;
	}
	
	###########
	### Var ###
	###########
	public function toVar($value) : ?string
	{
		if ($value === null)
		{
			return null;
		}
		/** @var $value Position **/
		return json_encode([$value->getLat(), $value->getLng()]);
	}
	
	public function toValue($var = null)
	{
		if ($var === null)
		{
			return null;
		}
		list($lat, $lng) = json_decode($var, true);
		return new Position($lat, $lng);
	}
	
	public function getVar() : ?string
	{
		$lat = $this->lat->getVar();
		$lng = $this->lng->getVar();
		if ($lat && $lng)
		{
			return json_encode([$lat, $lng]);
		}
		return null;
	}
	
	public function var(string $var = null): self
	{
		if ($var !== null)
		{
			$pos = $this->toValue($var);
			$this->lat->var((string)$pos->getLat());
			$this->lng->var((string)$pos->getLng());
		}
		return $this;
	}

// 	public function getGDOData() : array
// 	{
// 		return [
// 			$this->lat->name => $this->lat->getVar(),
// 			$this->lng->name => $this->lng->getVar(),
// 		];
// 	}
	
	#############
	### Value ###
	#############
	public function getPosition() : ?Position
	{
		return $this->getValue();
	}
	
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
	
	public function initialPosition(?Position $position) : self
	{
		if ($position === null)
		{
			return $this->initialLatLng(null, null);
		}
		else
		{
			return $this->initialLatLng($position->getLat(), $position->getLng());
		}
	}
	
	public function initialLatLng(?float $lat, ?float $lng) : self
	{
		$this->lat->initial($lat);
		$this->lng->initial($lng);
		return $this;
	}
	
	public function getLat() : ?string
	{
		return $this->lat->getVar();
	}

	public function getLng() : ?string
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
			'initialCurrent' => $this->initialCurrent,
		];
	}
	
	public function renderCLI() : string
	{
		if ($label = $this->renderLabel())
		{
			$label .= ': ';
		}
		
		if ($pos = $this->getPosition())
		{
			$pos = $pos->displayLat() . $pos->displayLng();
		}
		else
		{
			$pos = TextStyle::italic(t('unknown'));
		}
		
		return $label . $pos;
	}

}
