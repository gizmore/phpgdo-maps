<?php
declare(strict_types=1);
namespace GDO\Maps;

use GDO\Core\GDO_Module;
use GDO\Core\GDT;
use GDO\Core\GDT_Checkbox;
use GDO\Core\GDT_Secret;
use GDO\Core\Javascript;
use GDO\Date\GDT_Duration;
use GDO\UI\GDT_Divider;
use GDO\User\GDO_User;

/**
 * Maps API helper and geolocation services.
 *
 * @version 7.0.3
 * @since 4.0.0
 * @author gizmore
 * @see GDT_Position
 */
final class Module_Maps extends GDO_Module
{

	public int $priority = 45;

	##############
	### Module ###
	##############
	public function getFriendencies(): array
	{
		return [
			'Javascript',
		];
	}

	public function getDependencies(): array
	{
		return [
			'JQuery',
		];
	}

	public function getLicenseFilenames(): array
	{
		return [
			'LICENSE',
			'GOOGLE_LICENSE.md',
		];
	}

	public function getClasses(): array
	{
		return [
			GDO_UserPosition::class,
		];
	}

	##############
	### Config ###
	##############
	public function getConfig(): array
	{
		return [
			# GOOGLE
			GDT_Checkbox::make('maps_api_google')->initial('1'),
			GDT_Secret::make('maps_api_key')->max(64)->initial((string)@include($this->filePath('apikey.php'))),
			GDT_Checkbox::make('maps_sensors')->initial('0'),
			# OWN RECORDING
			GDT_Checkbox::make('maps_record')->initial('1'),
			GDT_Duration::make('maps_record_history')->initial('60s')->min(30),
			# LALALA
			GDT_Checkbox::make('hook_sidebar')->initial('0'),
		];
	}

	public function getUserConfig(): array
	{
		return [
			GDT_Position::make('position'),
		];
	}

	public function cfgRecord(): bool { return $this->getConfigValue('maps_record'); }

	public function getPrivacyRelatedFields(): array
	{
		$back = [
			GDT_Divider::make('info_div_maps_google'),
			$this->getConfigColumn('maps_api_google'),
			$this->getConfigColumn('maps_sensors'),
			GDT_Divider::make('info_div_maps_gdo'),
			$this->getConfigColumn('maps_record'),
		];
		if ($this->cfgRecord())
		{
			$back[] = $this->getConfigColumn('maps_record_history');
			$back[] = $this->userSetting(GDO_User::current(), 'position');
		}
		return $back;
	}
	public function onLoadLanguage(): void
	{
		$this->loadLanguage('lang/maps');
	}

	public function onIncludeScripts(): void
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
				Javascript::addJSPreInline("window.GDO_MAPS_HISTORY = {$interval};");
			}
		}
	}

	public function cfgGoogle(): bool { return $this->getConfigValue('maps_api_google'); }

	################
	### Settings ###
	################

	private function googleMapsScriptURL(): string
	{
		$sensors = $this->cfgSensors() ? 'true' : 'false';
		$apikey = $this->cfgApiKey();
		if (!empty($apikey))
		{
			$apikey = '&key=' . $apikey;
		}
		return sprintf('https://maps.google.com/maps/api/js?sensors=%s%s',
			$sensors, $apikey);
	}

	public function cfgSensors(): bool { return $this->getConfigValue('maps_sensors'); }

	############
	### Init ###
	############

	public function cfgApiKey(): string { return $this->getConfigVar('maps_api_key'); }

	public function cfgHistory(): float { return $this->getConfigValue('maps_record_history'); }

	public function cfgSidebar(): bool { return $this->getConfigValue('hook_sidebar'); }

	###########
	### API ###
	###########

	public function getMapsURL(string $searchTerm): string
	{
		return 'https://www.google.com/maps/search/?api=1&query=' . urlencode($searchTerm);
	}

}
