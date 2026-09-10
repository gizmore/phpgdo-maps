<?php
declare(strict_types=1);
namespace GDO\Maps\Test;

use GDO\Maps\GDO_UserPosition;
use GDO\Maps\GDT_Position;
use GDO\Maps\GDT_Polygon;
use GDO\Maps\GDT_PosRect;
use GDO\Maps\Method\Record;
use GDO\Maps\Module_Maps;
use GDO\Maps\Position;
use GDO\Tests\TestCase;


/**
 * Maps module test.
 * @version 7.0.3
 */
final class MapsTest extends TestCase
{

	public function testPosition()
	{
		$pos = GDT_Position::make('position')->initialLatLng(30, 40);
		$result = $pos->renderCLI();
		$this->assertOK('Test if a positions does not crash.');
		self::assertStringContainsString('°', $result, 'Test if position renders CLI.');
	}

	public function testPositionRectangle(): void
	{
		$rect = GDT_PosRect::make('rect')->initialCorners(53.0, 10.0, 52.0, 11.0);
		self::assertSame('[53,10,52,11]', $rect->getVar());
		self::assertTrue($rect->getValue()->contains(new Position(52.5, 10.5)));
		self::assertFalse($rect->getValue()->contains(new Position(51.5, 10.5)));
		self::assertSame($rect->getVar(), GDT_PosRect::make('rect')->var($rect->getVar())->getVar());
	}

	public function testPolygon(): void
	{
		$coordinates = [
			'type' => 'Polygon',
			'coordinates' => [[[10.22, 52.32], [10.23, 52.32], [10.23, 52.33], [10.22, 52.32]]],
		];
		$polygon = GDT_Polygon::make('polygon')->value($coordinates);
		self::assertSame($coordinates, $polygon->getValue());
		self::assertStringContainsString('polygon JSON', $polygon->gdoColumnDefine());
	}

	public function testRadiusPolygon(): void
	{
		$polygon = json_decode(GDT_Polygon::fromRadius(52.32, 10.23, 0.15), true, 512, JSON_THROW_ON_ERROR);
		self::assertSame('Polygon', $polygon['type']);
		self::assertCount(17, $polygon['coordinates'][0]);
		self::assertSame($polygon['coordinates'][0][0], $polygon['coordinates'][0][16]);
	}

	public function testRecording(): void
	{
		$i = [
			'pos_lat' => (string)30.37,
			'pos_lng' => (string)40.42,
		];
		$r = Record::make()->executeWithInputs($i);
		$r = $r->render();
		self::assertStringContainsString('Your position has been recorded.', $r, 'Test if current position recording is working.');
	}

	public function testHistoryRecording(): void
	{
		Module_Maps::instance()->saveConfigVar('maps_record_history', '1');
		$i = [
			'pos_lat' => (string)30.34,
			'pos_lng' => (string)40.24,
		];
		$r = Record::make()->executeWithInputs($i)->render();
		self::assertStringContainsString('Your position has been recorded.', $r, 'Test if position history recording is not crashing.');
		$n = GDO_UserPosition::table()->countWhere();
		self::assertGreaterThanOrEqual(1, $n, 'Test if user position history is recording.');
	}

}
