<?php
declare(strict_types=1);
namespace GDO\Maps\Test;

use GDO\Maps\GDO_UserPosition;
use GDO\Maps\GDT_Position;
use GDO\Maps\Method\Record;
use GDO\Maps\Module_Maps;
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
