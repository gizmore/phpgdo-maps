<?php
namespace GDO\Maps;

use GDO\Core\GDO;
use GDO\Core\GDT_AutoInc;
use GDO\User\GDO_User;
use GDO\User\GDT_User;
use GDO\Core\GDT_CreatedAt;

/**
 * This table tracks user positions.
 * This behaviour is disabled by default.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.1
 */
final class GDO_UserPosition extends GDO
{
	public function gdoCached() : bool { return false; }
	
	public function gdoColumns(): array
	{
		return [
			GDT_AutoInc::make('up_id'),
			GDT_User::make('up_user')->notNull(),
			GDT_Position::make('up_pos')->notNull(),
			GDT_CreatedAt::make('up_created'),
		];
	}

	public static function record(GDO_User $user, Position $position): static
	{
		return self::blank([
			'up_user' => $user->getID(),
			'up_pos_lat' => $position->getLat(),
			'up_pos_lng' => $position->getLng(),
		])->insert();
	}
	
}
