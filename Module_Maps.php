<?php
namespace GDO\Maps;

use GDO\Core\GDO_Module;
use GDO\Core\GDT_Checkbox;
use GDO\Core\GDT_Response;
use GDO\Core\GDT_Secret;
use GDO\Core\Javascript;
use GDO\Date\GDT_Duration;
use GDO\UI\GDT_DIV;
use GDO\UI\GDT_Panel;
use GDO\UI\GDT_Accordeon;
use GDO\UI\GDT_Card;
use GDO\UI\GDT_Divider;
use GDO\User\GDO_User;

/**
 * Maps API helper and geolocation services.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 4.0.0
 * @see GDT_Position
 */
final class Module_Maps extends GDO_Module
{
	public int $priority = 45;
	
	##############
	### Module ###
	##############
	public function getFriendencies() : array
	{
		return [
			'Javascript',
		];
	}
	
	public function getDependencies() : array
	{
		return [
			'JQuery',
		];
	}
	
	public function getLicenseFilenames() : array
	{
		return [
			'LICENSE',
			'GOOGLE_LICENSE.md',
		];
	}
	
	##############
	### Config ###
	##############
	public function getConfig() : array
	{
		return [
			# GOOGLE
		    GDT_Checkbox::make('maps_api_google')->initial('1'),
			GDT_Secret::make('maps_api_key')->max(64)->initial(@include($this->filePath('apikey.php'))),
			GDT_Checkbox::make('maps_sensors')->initial('0'),
			# OWN RECORDING
			GDT_Checkbox::make('maps_record')->initial('1'),
			GDT_Duration::make('maps_record_history')->initial('0s'),
			# LALALA
			GDT_Checkbox::make('hook_sidebar')->initial('0'),
		];
	}
	public function cfgGoogle() : bool { return $this->getConfigValue('maps_api_google'); }
	public function cfgApiKey() : string { return $this->getConfigVar('maps_api_key'); }
	public function cfgSensors() : bool { return $this->getConfigValue('maps_sensors'); }
	public function cfgRecord() : bool { return $this->getConfigValue('maps_record'); }
	public function cfgHistory() : bool { return $this->getConfigValue('maps_record_history'); }
	public function cfgSidebar() : bool { return $this->getConfigValue('hook_sidebar'); }
	
	################
	### Settings ###
	################
	public function getUserConfig()
	{
		if ($this->cfgRecord())
		{
			return [
				GDT_Position::make('position'),
			];
		}
	}
	
	public function getPrivacyRelatedFields(): array
	{
		return [
			GDT_Divider::make('info_div_maps_google'),
			$this->getConfigColumn('maps_api_google'),
			$this->getConfigColumn('maps_sensors'),
			GDT_Divider::make('info_div_maps_gdo'),
			$this->getConfigColumn('maps_record'),
			$this->getConfigColumn('maps_record_history'),
			$this->userSetting(GDO_User::current(), 'position'),
		];
	}
	
	############
	### Init ###
	############
	public function onLoadLanguage() : void
	{
		$this->loadLanguage('lang/maps');
	}
	
	public function onIncludeScripts() : void
	{
		if ($this->cfgGoogle())
		{
			Javascript::addJS($this->googleMapsScriptURL());
		}
		$this->addJS('js/gdo-maps.js');
		$this->addCSS('css/gdo-maps.css');
		if ($this->cfgRecord())
		{
			$this->addJS('js/gdo-maps-record.js');
			if ($interval = $this->cfgHistory())
			{
				Javascript::addJSPreInline("var GDO_MAPS_HISTORY = {$interval};");
			}
		}
	}
	
// 	public function onInitSidebar() : void
// 	{
// 	    if ($this->cfgSidebar())
// 	    {
// // 	        if (module_enabled('Angular'))
// // 	        {
// // 	            $navbar = GDT_Page::$INSTANCE->rightBar();
// // 	            $navbar->addField(GDT_Template::make()->template('Maps', 'maps-navbar.php'));
// // 	        }
// 	    }
// 	}
	
	private function googleMapsScriptURL()
	{
		$sensors = $this->cfgSensors() ? 'true' : 'false';
		$apikey = $this->cfgApiKey();
		if (!empty($apikey))
		{
			$apikey = '&key='.$apikey;
		}
		return sprintf('https://maps.google.com/maps/api/js?sensors=%s%s',
			$sensors, $apikey);
	}
	
	###########
	### API ###
	###########
	public function getMapsURL(string $searchTerm) : string
	{
		return 'https://www.google.com/maps/search/?api=1&query=' . urlencode($searchTerm);
	}
	
}
