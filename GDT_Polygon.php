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

	/**
	 * Whether a latitude/longitude point lies in the first GeoJSON polygon ring.
	 * Points directly on an edge count as inside, which avoids GPS jitter kicking
	 * a user out of a room at its exact boundary.
	 */
	public static function containsPoint(array|string $polygon, float $lat, float $lng): bool
	{
		if (is_string($polygon))
		{
			$polygon = json_decode($polygon, true) ?: [];
		}
		$ring = $polygon['coordinates'][0] ?? [];
		if (($polygon['type'] ?? null) !== 'Polygon' || count($ring) < 4)
		{
			return false;
		}
		$inside = false;
		$last = count($ring) - 1;
		for ($i = 0, $j = $last; $i <= $last; $j = $i++)
		{
			if (!isset($ring[$i][0], $ring[$i][1], $ring[$j][0], $ring[$j][1]))
			{
				return false;
			}
			[$xi, $yi] = [(float)$ring[$i][0], (float)$ring[$i][1]];
			[$xj, $yj] = [(float)$ring[$j][0], (float)$ring[$j][1]];
			$crossProduct = (($lng - $xi) * ($yj - $yi)) - (($lat - $yi) * ($xj - $xi));
			if (abs($crossProduct) < 0.0000000001 &&
				$lng >= min($xi, $xj) && $lng <= max($xi, $xj) &&
				$lat >= min($yi, $yj) && $lat <= max($yi, $yj))
			{
				return true;
			}
			$cross = (($yi > $lat) !== ($yj > $lat)) &&
				($lng < (($xj - $xi) * ($lat - $yi) / ($yj - $yi)) + $xi);
			if ($cross)
			{
				$inside = !$inside;
			}
		}
		return $inside;
	}

	/** Point-in-polygon with a small outside tolerance in kilometres. */
	public static function containsOrNear(array|string $polygon, float $lat, float $lng, float $toleranceKm): bool
	{
		if (self::containsPoint($polygon, $lat, $lng))
		{
			return true;
		}
		if (is_string($polygon))
		{
			$polygon = json_decode($polygon, true) ?: [];
		}
		$ring = $polygon['coordinates'][0] ?? [];
		if (($polygon['type'] ?? null) !== 'Polygon' || count($ring) < 4)
		{
			return false;
		}
		$longitudeKm = 111.32 * max(.01, cos(deg2rad($lat)));
		$minDistance = INF;
		for ($i = 1, $count = count($ring); $i < $count; $i++)
		{
			if (!isset($ring[$i - 1][0], $ring[$i - 1][1], $ring[$i][0], $ring[$i][1]))
			{
				return false;
			}
			$ax = ((float)$ring[$i - 1][0] - $lng) * $longitudeKm;
			$ay = ((float)$ring[$i - 1][1] - $lat) * 111.32;
			$bx = ((float)$ring[$i][0] - $lng) * $longitudeKm;
			$by = ((float)$ring[$i][1] - $lat) * 111.32;
			$dx = $bx - $ax;
			$dy = $by - $ay;
			$lengthSquared = ($dx * $dx) + ($dy * $dy);
			$factor = $lengthSquared ? max(0., min(1., -(($ax * $dx) + ($ay * $dy)) / $lengthSquared)) : 0.;
			$minDistance = min($minDistance, hypot($ax + ($factor * $dx), $ay + ($factor * $dy)));
		}
		return $minDistance <= max(0., $toleranceKm);
	}

	public function gdtDefaultLabel(): ?string
	{
		return 'polygon';
	}
}
