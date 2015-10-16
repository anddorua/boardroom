<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 16.10.15
 * Time: 19:47
 */

namespace Helpers;


class CsvImporterTest extends \PHPUnit_Framework_TestCase
{
    public function testFileImport() {
        $imp = new \Helpers\CsvImporter('tests/DBMappers/AppointmentItemMapperData.csv', true,',');
        $data = $imp->get();
        $this->assertTrue(is_array($data));
    }

}
