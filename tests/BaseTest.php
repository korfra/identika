<?php

declare(strict_types = 1);

namespace Korfra\Identika\Tests;

use Korfra\Identika\Base;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @small
 */
#[CoversClass(Base::class)]
class BaseTest extends TestCase
{
    private const TEST_NUMBER = '3273012501990001';

    public function testConstructor(): void
    {
        $base = self::createTestBase(self::TEST_NUMBER);

        $this->assertSame(self::TEST_NUMBER, $base->number);
        $this->assertIsArray($base->location);
    }

    public function testGetCurrentYear(): void
    {
        $base = self::createTestBase(self::TEST_NUMBER);
        $currentYear = $base->getCurrentYear();

        $this->assertIsInt($currentYear);
        $this->assertGreaterThanOrEqual(20, $currentYear);
        $this->assertLessThanOrEqual(99, $currentYear);
    }

    public function testGetNikYear(): void
    {
        $base = self::createTestBase(self::TEST_NUMBER);
        $nikYear = $base->getNIKYear();

        $this->assertIsInt($nikYear);
        $this->assertSame(99, $nikYear); // From TEST_NUMBER: 3273012501990001
    }

    public function testGetNikDate(): void
    {
        $base = self::createTestBase(self::TEST_NUMBER);
        $nikDate = $base->getNIKDate();

        $this->assertIsInt($nikDate);
        $this->assertSame(25, $nikDate); // From TEST_NUMBER: 3273012501990001
    }

    public function testGetBornDate(): void
    {
        $base = self::createTestBase(self::TEST_NUMBER);
        $bornDate = $base->getBornDate();

        $this->assertIsObject($bornDate);
        $this->assertTrue(property_exists($bornDate, 'date'));
        $this->assertTrue(property_exists($bornDate, 'month'));
        $this->assertTrue(property_exists($bornDate, 'year'));
        $this->assertTrue(property_exists($bornDate, 'full'));

        // Check specific values for the test number
        $this->assertSame('25', $bornDate->date);
        $this->assertSame('01', $bornDate->month);
        $this->assertSame('1999', $bornDate->year);
        $this->assertSame('25-01-1999', $bornDate->full);
    }

    public function testGetBornDateForFemale(): void
    {
        // Create a test class that returns female gender
        $femaleBase = new class('3273016501990001') extends Base {
            public function parse(): object
            {
                return (object) ['valid' => true];
            }

            public function validate(): bool
            {
                return true;
            }

            public function getGender(): string
            {
                return 'PEREMPUAN';
            }
        };

        $bornDate = $femaleBase->getBornDate();

        // For female, the date should be adjusted (65 - 40 = 25)
        $this->assertSame('25', $bornDate->date);
        $this->assertSame('01', $bornDate->month);
        $this->assertSame('1999', $bornDate->year);
    }

    public function testGetAge(): void
    {
        $base = self::createTestBase(self::TEST_NUMBER);
        $age = $base->getAge();

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
        $base = self::createTestBase(self::TEST_NUMBER);
        $nextBirthday = $base->getNextBirthday();

        $this->assertIsObject($nextBirthday);
        $this->assertTrue(property_exists($nextBirthday, 'month'));
        $this->assertTrue(property_exists($nextBirthday, 'day'));

        // Values should be non-negative
        $this->assertGreaterThanOrEqual(0, $nextBirthday->month);
        $this->assertGreaterThanOrEqual(0, $nextBirthday->day);
    }

    public function testGetProvince(): void
    {
        $base = self::createTestBase(self::TEST_NUMBER);
        $province = $base->getProvince();

        $this->assertIsString($province);
        $this->assertNotEmpty($province);
    }

    public function testGetCity(): void
    {
        $base = self::createTestBase(self::TEST_NUMBER);
        $city = $base->getCity();

        $this->assertIsString($city);
        $this->assertNotEmpty($city);
    }

    public function testGetSubDistrict(): void
    {
        $base = self::createTestBase(self::TEST_NUMBER);
        $subDistrict = $base->getSubDistrict();

        $this->assertIsString($subDistrict);
        $this->assertNotEmpty($subDistrict);
    }

    public function testGetPostalCode(): void
    {
        $base = self::createTestBase(self::TEST_NUMBER);
        $postalCode = $base->getPostalCode();

        $this->assertIsString($postalCode);
        $this->assertNotEmpty($postalCode);
    }

    public function testReadonlyProperties(): void
    {
        $base = self::createTestBase(self::TEST_NUMBER);

        // Test that properties are readonly (should not be able to modify them)
        $this->expectException(\Error::class);
        $base->number = '1234567890123456';
    }

    public function testTypeSafety(): void
    {
        $base = self::createTestBase(self::TEST_NUMBER);

        $this->assertIsString($base->number);
        $this->assertIsArray($base->location);
    }

    public function testNullCoalescingOperator(): void
    {
        // Test that the null coalescing operator works correctly
        $base = self::createTestBase('9999999999999999');

        $this->assertNull($base->getProvince());
        $this->assertNull($base->getCity());
        $this->assertNull($base->getSubDistrict());
    }

    // Concrete implementation of Base for testing
    private static function createTestBase(string $number): Base
    {
        return new class($number) extends Base {
            public function parse(): object
            {
                return (object) ['valid' => true];
            }

            public function validate(): bool
            {
                return true;
            }
        };
    }
}
