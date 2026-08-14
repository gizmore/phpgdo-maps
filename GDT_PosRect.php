<?php
declare(strict_types=1);
namespace GDO\Maps;

use GDO\Core\GDT_Composite;

/**
 * A geo-coordinate rectangle composed of its upper-left and lower-right
 * corners. It adds four latitude/longitude columns to a GDO table.
 */
final class GDT_PosRect extends GDT_Composite
{
    public GDT_Lat $upperLeftLat;
    public GDT_Lng $upperLeftLng;
    public GDT_Lat $lowerRightLat;
    public GDT_Lng $lowerRightLng;

    protected function __construct()
    {
        parent::__construct();
        $this->horizontal();
    }

    public function isSerializable(): bool
    {
        return true;
    }

    public function isSearchable(): bool
    {
        return false;
    }

    public function gdtDefaultLabel(): ?string
    {
        return 'position_rectangle';
    }

    public function gdoCompositeFields(): array
    {
        $n = $this->getName();
        $this->upperLeftLat = GDT_Lat::make("{$n}_ul_lat");
        $this->upperLeftLng = GDT_Lng::make("{$n}_ul_lng");
        $this->lowerRightLat = GDT_Lat::make("{$n}_lr_lat");
        $this->lowerRightLng = GDT_Lng::make("{$n}_lr_lng");
        return [
            $this->upperLeftLat,
            $this->upperLeftLng,
            $this->lowerRightLat,
            $this->lowerRightLng,
        ];
    }

    public function initialCorners(?float $upperLeftLat, ?float $upperLeftLng, ?float $lowerRightLat, ?float $lowerRightLng): self
    {
        $this->upperLeftLat->initial($upperLeftLat === null ? null : (string)$upperLeftLat);
        $this->upperLeftLng->initial($upperLeftLng === null ? null : (string)$upperLeftLng);
        $this->lowerRightLat->initial($lowerRightLat === null ? null : (string)$lowerRightLat);
        $this->lowerRightLng->initial($lowerRightLng === null ? null : (string)$lowerRightLng);
        return $this;
    }

    public function getValue(): ?PosRect
    {
        $upperLeftLat = $this->upperLeftLat->getValue();
        $upperLeftLng = $this->upperLeftLng->getValue();
        $lowerRightLat = $this->lowerRightLat->getValue();
        $lowerRightLng = $this->lowerRightLng->getValue();
        if ($upperLeftLat === null || $upperLeftLng === null || $lowerRightLat === null || $lowerRightLng === null)
        {
            return null;
        }
        return new PosRect($upperLeftLat, $upperLeftLng, $lowerRightLat, $lowerRightLng);
    }

    public function getVar(): ?string
    {
        return ($rect = $this->getValue()) ? $this->toVar($rect) : null;
    }

    public function toVar(null|bool|int|float|string|object|array $value): ?string
    {
        if ($value === null)
        {
            return null;
        }
        if (!$value instanceof PosRect)
        {
            return null;
        }
        return json_encode($value->toArray());
    }

    public function var(?string $var): static
    {
        $this->upperLeftLat->var(null);
        $this->upperLeftLng->var(null);
        $this->lowerRightLat->var(null);
        $this->lowerRightLng->var(null);
        if (($rect = $this->toValue($var)) !== null)
        {
            $this->upperLeftLat->var((string)$rect->upperLeftLat);
            $this->upperLeftLng->var((string)$rect->upperLeftLng);
            $this->lowerRightLat->var((string)$rect->lowerRightLat);
            $this->lowerRightLng->var((string)$rect->lowerRightLng);
        }
        return $this;
    }

    public function toValue(null|string|array $var): ?PosRect
    {
        if ($var === null)
        {
            return null;
        }
        $corners = is_array($var) ? $var : json_decode($var, true);
        if (!is_array($corners) || count($corners) !== 4 || array_filter($corners, static fn($value): bool => !is_numeric($value)))
        {
            return null;
        }
        return new PosRect((float)$corners[0], (float)$corners[1], (float)$corners[2], (float)$corners[3]);
    }
}
