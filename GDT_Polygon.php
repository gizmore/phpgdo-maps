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
	/**
	 * Creates a GeoJSON polygon that follows a radius around a centre point.
	 *
	 * The polygon deliberately uses multiple points rather than a bounding box,
	 * so it is a close match for the existing circular room geofences.
	 */
	public static function fromRadius(float $lat, float $lng, float $radiusKm, int $sides = 6): string
	{
		$sides = max(4, $sides);
		$points = [];
		$latitudeDegrees = $radiusKm / 111.32;
		$longitudeDegrees = $radiusKm / (111.32 * max(0.01, cos(deg2rad($lat))));
		for ($i = 0; $i < $sides; $i++)
		{
			$angle = (2.0 * M_PI * $i) / $sides;
			$points[] = [
				round($lng + ($longitudeDegrees * sin($angle)), 7),
				round($lat + ($latitudeDegrees * cos($angle)), 7),
			];
		}
		$points[] = $points[0];
		return self::encode([
			'type' => 'Polygon',
			'coordinates' => [$points],
		]);
	}

	/** Creates a GeoJSON rectangle from south/west/north/east bounds. */
	public static function fromBounds(float $south, float $west, float $north, float $east): string
	{
		return self::encode([
			'type' => 'Polygon',
			'coordinates' => [[
				[$west, $south],
				[$east, $south],
				[$east, $north],
				[$west, $north],
				[$west, $south],
			]],
		]);
	}

	public function gdtDefaultLabel(): ?string
	{
		return 'polygon';
	}
}
