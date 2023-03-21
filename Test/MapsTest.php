<?php
namespace GDO\Maps\Test;

use GDO\Maps\GDT_Position;
use GDO\Tests\TestCase;
use function PHPUnit\Framework\assertStringContainsString;

final class MapsTest extends TestCase
{

	public function testPosition()
	{
		$pos = GDT_Position::make('position')->initialLatLng(30, 40);
		$result = $pos->renderCLI();
		$this->assertOK('Test if a positions does not crash.');
		assertStringContainsString('°', $result, 'Test if position renders CLI.');
	}

}
