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

	/**
	 * Creates a square whose corners are roughly $radiusKm from its centre.
	 * This makes the matching visibility radius easy to reason about.
	 */
	public static function fromSquare(float $lat, float $lng, float $radiusKm): string
	{
		$radiusKm = max(0.001, $radiusKm) / sqrt(2);
		$latitudeDegrees = $radiusKm / 111.32;
		$longitudeDegrees = $radiusKm / (111.32 * max(0.01, cos(deg2rad($lat))));
		return self::fromBounds(
			$lat - $latitudeDegrees,
			$lng - $longitudeDegrees,
			$lat + $latitudeDegrees,
			$lng + $longitudeDegrees,
		);
	}

	/** Returns the distance in kilometres from a centre point to its farthest polygon vertex. */
	public static function radiusFromCenter(array|string $polygon, float $lat, float $lng): float
	{
		if (is_string($polygon))
		{
			$polygon = json_decode($polygon, true) ?: [];
		}
		$radius = 0.0;
		$longitudeScale = 111.32 * cos(deg2rad($lat));
		foreach (($polygon['coordinates'][0] ?? []) as $point)
		{
			if (!is_array($point) || count($point) !== 2)
			{
				continue;
			}
			$northKm = ((float)$point[1] - $lat) * 111.32;
			$eastKm = ((float)$point[0] - $lng) * $longitudeScale;
			$radius = max($radius, hypot($northKm, $eastKm));
		}
		return $radius;
	}

	public function gdtDefaultLabel(): ?string
	{
		return 'polygon';
	}
}
