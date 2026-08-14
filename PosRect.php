<?php
declare(strict_types=1);
namespace GDO\Maps;

/**
 * A geographic rectangle, represented by upper-left and lower-right corners.
 * Longitude ranges crossing the antimeridian are supported.
 */
final class PosRect
{
    public function __construct(
        public readonly float $upperLeftLat,
        public readonly float $upperLeftLng,
        public readonly float $lowerRightLat,
        public readonly float $lowerRightLng,
    ) {}

    public function toArray(): array
    {
        return [$this->upperLeftLat, $this->upperLeftLng, $this->lowerRightLat, $this->lowerRightLng];
    }

    public function contains(Position $position): bool
    {
        $lat = $position->getLat();
        $lng = $position->getLng();
        if ($lat === null || $lng === null || $lat > $this->upperLeftLat || $lat < $this->lowerRightLat)
        {
            return false;
        }
        return $this->upperLeftLng <= $this->lowerRightLng
            ? $lng >= $this->upperLeftLng && $lng <= $this->lowerRightLng
            : $lng >= $this->upperLeftLng || $lng <= $this->lowerRightLng;
    }
}
