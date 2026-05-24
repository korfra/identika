<?php

declare(strict_types = 1);

namespace Korfra\Identika\Tests;

use Korfra\Identika\NIK;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @small
 */
#[CoversClass(NIK::class)]
class NIKTest extends TestCase
{
    private const VALID_NIK = '3273012501990001';

    private const INVALID_NIK = '1234567890123456';

    public function testSetMethodWithString(): void
    {
        $nik = NIK::set(self::VALID_NIK);

        $this->assertInstanceOf(NIK::class, $nik);
        $this->assertSame(self::VALID_NIK, $nik->number);
    }

    public function testSetMethodWithInteger(): void
    {
        $nik = NIK::set(3273012501990001);

        $this->assertInstanceOf(NIK::class, $nik);
        $this->assertSame('3273012501990001', $nik->number);
    }

    public function testValidateWithValidNik(): void
    {
        $nik = NIK::set(self::VALID_NIK);

        $this->assertTrue($nik->validate());
    }

    public function testValidateWithInvalidNik(): void
    {
        $nik = NIK::set(self::INVALID_NIK);

        $this->assertFalse($nik->validate());
    }

    public function testParseWithValidNik(): void
    {
        $nik = NIK::set(self::VALID_NIK);
        $result = $nik->parse();

        $this->assertIsObject($result);
        $this->assertTrue($result->valid);
        $this->assertSame(self::VALID_NIK, $result->number);
        $this->assertIsString($result->uniqueCode);
        $this->assertIsString($result->gender);
        $this->assertIsObject($result->born);
        $this->assertIsObject($result->age);
        $this->assertIsObject($result->nextBirthday);
        $this->assertIsObject($result->address);
        $this->assertIsString($result->postalCode);
    }

    public function testParseWithInvalidNik(): void
    {
        $nik = NIK::set(self::INVALID_NIK);
        $result = $nik->parse();

        $this->assertIsObject($result);
        $this->assertFalse($result->valid);
        $this->assertFalse(property_exists($result, 'number'));
    }

    public function testGetGenderForMale(): void
    {
        // NIK with date 25 (male)
        $nik = NIK::set('3273012501990001');

        $this->assertSame('LAKI-LAKI', $nik->getGender());
    }

    public function testGetGenderForFemale(): void
    {
        // NIK with date 65 (female: 65-40=25)
        $nik = NIK::set('3273016501990001');

        $this->assertSame('PEREMPUAN', $nik->getGender());
    }

    public function testGetUniqueCode(): void
    {
        $nik = NIK::set(self::VALID_NIK);

        // Using reflection to test private method
        $reflection = new \ReflectionClass($nik);
        $method = $reflection->getMethod('getUniqueCode');
        $method->setAccessible(true);

        $uniqueCode = $method->invoke($nik);

        $this->assertSame('0001', $uniqueCode);
    }

    public function testGetBornDate(): void
    {
        $nik = NIK::set(self::VALID_NIK);
        $bornDate = $nik->getBornDate();

        $this->assertIsObject($bornDate);
        $this->assertTrue(property_exists($bornDate, 'date'));
        $this->assertTrue(property_exists($bornDate, 'month'));
        $this->assertTrue(property_exists($bornDate, 'year'));
        $this->assertTrue(property_exists($bornDate, 'full'));

        // Check specific values for the test NIK
        $this->assertSame('25', $bornDate->date);
        $this->assertSame('01', $bornDate->month);
        $this->assertSame('1999', $bornDate->year);
        $this->assertSame('25-01-1999', $bornDate->full);
    }

    public function testGetAge(): void
    {
        $nik = NIK::set(self::VALID_NIK);
        $age = $nik->getAge();

        $this->assertIsObject($age);
        $this->assertTrue(property_exists($age, 'year'));
        $this->assertTrue(property_exists($age, 'month'));
        $this->assertTrue(property_exists($age, 'day'));

        // Age should be reasonable (born in 1999, so should be around 25 years in 2024)
        $this->assertGreaterThan(20, $age->year);
        $this->assertLessThan(30, $age->year);
    }

    public function testGetNextBirthday(): void
    {
        $nik = NIK::set(self::VALID_NIK);
        $nextBirthday = $nik->getNextBirthday();

        $this->assertIsObject($nextBirthday);
        $this->assertTrue(property_exists($nextBirthday, 'month'));
        $this->assertTrue(property_exists($nextBirthday, 'day'));

        // Values should be non-negative
        $this->assertGreaterThanOrEqual(0, $nextBirthday->month);
        $this->assertGreaterThanOrEqual(0, $nextBirthday->day);
    }

    public function testGetProvince(): void
    {
        $nik = NIK::set(self::VALID_NIK);
        $province = $nik->getProvince();

        $this->assertIsString($province);
        $this->assertNotEmpty($province);
    }

    public function testGetCity(): void
    {
        $nik = NIK::set(self::VALID_NIK);
        $city = $nik->getCity();

        $this->assertIsString($city);
        $this->assertNotEmpty($city);
    }

    public function testGetSubDistrict(): void
    {
        $nik = NIK::set(self::VALID_NIK);
        $subDistrict = $nik->getSubDistrict();

        $this->assertIsString($subDistrict);
        $this->assertNotEmpty($subDistrict);
    }

    public function testGetPostalCode(): void
    {
        $nik = NIK::set(self::VALID_NIK);
        $postalCode = $nik->getPostalCode();

        $this->assertIsString($postalCode);
        $this->assertNotEmpty($postalCode);
    }

    public function testConstructorWithCustomWilayahPath(): void
    {
        $customPath = __DIR__ . '/../src/assets/wilayah.json';
        $nik = new NIK(self::VALID_NIK, $customPath);

        $this->assertInstanceOf(NIK::class, $nik);
        $this->assertSame(self::VALID_NIK, $nik->number);
    }

    public function testReadonlyProperties(): void
    {
        $nik = NIK::set(self::VALID_NIK);

        // Test that properties are readonly (should not be able to modify them)
        $this->expectException(\Error::class);
        $nik->number = '1234567890123456';
    }

    public function testTypeSafety(): void
    {
        // Test that the class properly handles type safety
        $nik = NIK::set(self::VALID_NIK);

        $this->assertIsString($nik->number);
        $this->assertIsArray($nik->location);
    }

    public function testGetValidationErrors(): void
    {
        $nik = NIK::set(self::INVALID_NIK);
        $errors = $nik->getValidationErrors();

        $this->assertIsArray($errors);
        $this->assertNotEmpty($errors);
    }

    public function testToArray(): void
    {
        $nik = NIK::set(self::VALID_NIK);
        $array = $nik->toArray();

        $this->assertIsArray($array);
        $this->assertTrue($array['valid']);
        $this->assertSame(self::VALID_NIK, $array['number']);
        $this->assertIsString($array['uniqueCode']);
        $this->assertIsString($array['gender']);
        $this->assertIsArray($array['born']);
        $this->assertIsArray($array['age']);
        $this->assertIsArray($array['nextBirthday']);
        $this->assertIsArray($array['address']);
        $this->assertIsString($array['postalCode']);
    }

    public function testGenerate(): void
    {
        $nikString = NIK::generate();

        $this->assertSame(16, strlen($nikString));
        $this->assertTrue(ctype_digit($nikString));

        $nik = NIK::set($nikString);
        $this->assertTrue($nik->validate());
    }

    public function testGenerateWithParams(): void
    {
        $birthDate = '170845';
        $province = '32'; // Jawa Barat
        $nikString = NIK::generate($province, null, null, 'PEREMPUAN', $birthDate);

        $this->assertSame(16, strlen($nikString));
        $nik = NIK::set($nikString);
        $this->assertTrue($nik->validate());
        $this->assertSame('PEREMPUAN', $nik->getGender());
        $this->assertSame('17', $nik->getBornDate()->date);
        $this->assertSame('08', $nik->getBornDate()->month);
        $this->assertSame('1945', $nik->getBornDate()->year);
        $this->assertSame('JAWA BARAT', $nik->getProvince());
    }

    public function testInvalidNikWithShortLength(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        NIK::set('123456789012345');
    }

    public function testInvalidNikWithLongLength(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        NIK::set('12345678901234567');
    }

    public function testInvalidNikWithNonNumeric(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        NIK::set('123456789012345a');
    }
}
