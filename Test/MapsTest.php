<?php
namespace GDO\Maps\Test;

use GDO\Tests\TestCase;
use GDO\Maps\GDT_Position;

final class MapsTest extends TestCase
{
    public function testPosition()
    {
        $pos = GDT_Position::make('position')->initialLatLng(30, 40);
        $result = $pos->renderCLI();
        
        
    }
}
