<?php

declare(strict_types = 1);

namespace Korfra\Identika\Tests;

use Korfra\Identika\KK;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @small
 */
#[CoversClass(KK::class)]
class KKTest extends TestCase
{
    private const VALID_KK = '3273012501990001';

    private const INVALID_KK = '1234567890123456';

    public function testSetMethodWithString(): void
    {
        $kk = KK::set(self::VALID_KK);

        $this->assertInstanceOf(KK::class, $kk);
        $this->assertSame(self::VALID_KK, $kk->number);
    }

    public function testSetMethodWithInteger(): void
    {
        $kk = KK::set(3273012501990001);

        $this->assertInstanceOf(KK::class, $kk);
        $this->assertSame('3273012501990001', $kk->number);
    }

    public function testValidateWithValidKk(): void
    {
        $kk = KK::set(self::VALID_KK);

        $this->assertTrue($kk->validate());
    }

    public function testValidateWithInvalidKk(): void
    {
        $kk = KK::set(self::INVALID_KK);

        $this->assertFalse($kk->validate());
    }

    public function testParseWithValidKk(): void
    {
        $kk = KK::set(self::VALID_KK);
        $result = $kk->parse();

        $this->assertIsObject($result);
        $this->assertTrue($result->valid);
        $this->assertSame(self::VALID_KK, $result->number);
        $this->assertIsObject($result->address);
        $this->assertIsString($result->postalCode);
    }

    public function testParseWithInvalidKk(): void
    {
        $kk = KK::set(self::INVALID_KK);
        $result = $kk->parse();

        $this->assertIsObject($result);
        $this->assertFalse($result->valid);
        $this->assertFalse(property_exists($result, 'number'));
    }

    public function testGetProvince(): void
    {
        $kk = KK::set(self::VALID_KK);
        $province = $kk->getProvince();

        $this->assertIsString($province);
        $this->assertNotEmpty($province);
    }

    public function testGetCity(): void
    {
        $kk = KK::set(self::VALID_KK);
        $city = $kk->getCity();

        $this->assertIsString($city);
        $this->assertNotEmpty($city);
    }

    public function testGetSubDistrict(): void
    {
        $kk = KK::set(self::VALID_KK);
        $subDistrict = $kk->getSubDistrict();

        $this->assertIsString($subDistrict);
        $this->assertNotEmpty($subDistrict);
    }

    public function testGetPostalCode(): void
    {
        $kk = KK::set(self::VALID_KK);
        $postalCode = $kk->getPostalCode();

        $this->assertIsString($postalCode);
        $this->assertNotEmpty($postalCode);
    }

    public function testConstructorWithCustomWilayahPath(): void
    {
        $customPath = __DIR__ . '/../src/assets/wilayah.json';
        $kk = new KK(self::VALID_KK, $customPath);

        $this->assertInstanceOf(KK::class, $kk);
        $this->assertSame(self::VALID_KK, $kk->number);
    }

    public function testReadonlyProperties(): void
    {
        $kk = KK::set(self::VALID_KK);

        // Test that properties are readonly (should not be able to modify them)
        $this->expectException(\Error::class);
        $kk->number = '1234567890123456';
    }

    public function testTypeSafety(): void
    {
        // Test that the class properly handles type safety
        $kk = KK::set(self::VALID_KK);

        $this->assertIsString($kk->number);
        $this->assertIsArray($kk->location);
    }

    public function testAddressObjectStructure(): void
    {
        $kk = KK::set(self::VALID_KK);
        $result = $kk->parse();

        $this->assertIsObject($result->address);
        $this->assertTrue(property_exists($result->address, 'province'));
        $this->assertTrue(property_exists($result->address, 'city'));
        $this->assertTrue(property_exists($result->address, 'subDistrict'));

        $this->assertIsString($result->address->province);
        $this->assertIsString($result->address->city);
        $this->assertIsString($result->address->subDistrict);
    }

    public function testGenerate(): void
    {
        $kkString = KK::generate();

        $this->assertSame(16, strlen($kkString));
        $this->assertTrue(ctype_digit($kkString));

        $kk = KK::set($kkString);
        $this->assertTrue($kk->validate());
    }

    public function testInvalidKkWithShortLength(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        KK::set('123456789012345');
    }

    public function testInvalidKkWithLongLength(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        KK::set('12345678901234567');
    }

    public function testInvalidKkWithNonNumeric(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        KK::set('123456789012345a');
    }

    public function testGetValidationErrors(): void
    {
        $kk = KK::set(self::INVALID_KK);
        $errors = $kk->getValidationErrors();

        $this->assertIsArray($errors);
        $this->assertNotEmpty($errors);
    }

    public function testToArray(): void
    {
        $kk = KK::set(self::VALID_KK);
        $array = $kk->toArray();

        $this->assertIsArray($array);
        $this->assertTrue($array['valid']);
        $this->assertSame(self::VALID_KK, $array['number']);
    }

    public function testGetFormattedNumber(): void
    {
        $kk = KK::set(self::VALID_KK);
        $formatted = $kk->getFormattedNumber();

        $this->assertIsString($formatted);
        $this->assertSame('3273-0125-0199-0001', $formatted);
    }

    public function testGetRawNumber(): void
    {
        $kk = KK::set(self::VALID_KK);
        $raw = $kk->getRawNumber();

        $this->assertIsString($raw);
        $this->assertSame(self::VALID_KK, $raw);
    }
}
