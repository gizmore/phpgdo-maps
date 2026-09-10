<?php
declare(strict_types=1);
namespace GDO\Maps;

use GDO\Core\GDT_JSON;

/**
 * A GeoJSON polygon.
 *
 * Coordinates follow GeoJSON's [longitude, latitude] convention, and the
 * enclosing ring repeats its first coordinate as its final coordinate.
 */
final class GDT_Polygon extends GDT_JSON
{
	public function gdtDefaultLabel(): ?string
	{
		return 'polygon';
	}
}
