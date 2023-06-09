<?php
declare(strict_types=1);
namespace GDO\Maps;

use GDO\Core\GDO;
use GDO\Core\GDT_AutoInc;
use GDO\Core\GDT_CreatedAt;
use GDO\User\GDO_User;
use GDO\User\GDT_User;

/**
 * This table tracks user positions.
 * This behaviour is disabled by default.
 *
 * @version 7.0.3
 * @since 7.0.1
 * @author gizmore
 */
final class GDO_UserPosition extends GDO
{

	public static function record(GDO_User $user, Position $position): self
	{
        $blank = self::blank([
                'up_user' => $user->getID(),
                'up_pos_lat' => $position->getLat(),
                'up_pos_lng' => $position->getLng(),
            ]);
        if ($user->isPersisted())
        {
            $blank->insert();
        }
        return $blank;
	}

	public function gdoCached(): bool { return false; }

	public function gdoColumns(): array
	{
		return [
			GDT_AutoInc::make('up_id'),
			GDT_User::make('up_user')->notNull(),
			GDT_Position::make('up_pos')->notNull(),
			GDT_CreatedAt::make('up_created'),
		];
	}

}
