<?php

require_once 'vendor/autoload.php';

use Korfra\Identika\KK;
use Korfra\Identika\NIK;

echo "=== Optimized Number Validator with PHP 8.4 Features ===\n\n";

// Performance measurement function
function measurePerformance(callable $callback, string $name)
{
    $start = microtime(true);
    $memoryStart = memory_get_usage();

    $result = $callback();

    $end = microtime(true);
    $memoryEnd = memory_get_usage();

    $time = ($end - $start) * 1000; // Convert to milliseconds
    $memory = $memoryEnd - $memoryStart;

    echo "Timer $name: ".number_format($time, 2).'ms, Memory: '.number_format($memory)." bytes\n";

    return $result;
}

// Example 1: NIK Validation with Performance Measurement
echo "1. NIK Validation Performance Test:\n";
$nik = measurePerformance(
    function () {
        return NIK::set('3273012501990001');
    },
    'NIK Creation'
);

$result = measurePerformance(
    function () use ($nik) {
        return $nik->parse();
    },
    'NIK Parsing'
);

if ($result->valid) {
    echo "OK Valid NIK: {$result->number}\n";
    echo "   Gender: {$result->gender}\n";
    echo "   Born: {$result->born->full}\n";
    echo "   Age: {$result->age->year} years, {$result->age->month} months, {$result->age->day} days\n";
    echo "   Province: {$result->address->province}\n";
    echo "   City: {$result->address->city}\n";
    echo "   Sub-district: {$result->address->subDistrict}\n";
    echo "   Postal Code: {$result->postalCode}\n";
    echo "   Unique Code: {$result->uniqueCode}\n";
} else {
    echo "ERROR Invalid NIK\n";
}

echo "\n";

// Example 2: KK Validation with Performance Measurement
echo "2. KK Validation Performance Test:\n";
$kk = measurePerformance(
    function () {
        return KK::set('3273012501990001');
    },
    'KK Creation'
);

$kkResult = measurePerformance(
    function () use ($kk) {
        return $kk->parse();
    },
    'KK Parsing'
);

if ($kkResult->valid) {
    echo "OK Valid KK: {$kkResult->number}\n";
    echo "   Formatted: {$kk->getFormattedNumber()}\n";
    echo "   Province: {$kkResult->address->province}\n";
    echo "   City: {$kkResult->address->city}\n";
    echo "   Sub-district: {$kkResult->address->subDistrict}\n";
    echo "   Postal Code: {$kkResult->postalCode}\n";
} else {
    echo "ERROR Invalid KK\n";
}

echo "\n";

// Example 3: Performance Comparison - Multiple Operations
echo "3. Performance Comparison (1000 operations):\n";

measurePerformance(function () {
    for ($i = 0; $i < 1000; $i++) {
        $nik = NIK::set('3273012501990001');
        $result = $nik->parse();
        $gender = $nik->getGender();
    }
}, '1000 NIK Operations');

measurePerformance(function () {
    for ($i = 0; $i < 1000; $i++) {
        $kk = KK::set('3273012501990001');
        $result = $kk->parse();
        $formatted = $kk->getFormattedNumber();
    }
}, '1000 KK Operations');

echo "\n";

// Example 4: Error Handling and Validation
echo "4. Error Handling and Validation:\n";

try {
    $invalidNik = NIK::set('123456789012345'); // Too short
    echo "ERROR Should have thrown exception\n";
} catch (InvalidArgumentException $e) {
    echo 'OK Caught exception: '.$e->getMessage()."\n";
}

try {
    $invalidNik = NIK::set('123456789012345a'); // Non-numeric
    echo "ERROR Should have thrown exception\n";
} catch (InvalidArgumentException $e) {
    echo 'OK Caught exception: '.$e->getMessage()."\n";
}

// Test validation errors
$nik = NIK::set('1234567890123456'); // Valid format but invalid data
$errors = $nik->getValidationErrors();
echo 'OK Validation errors: '.implode(', ', $errors)."\n";

echo "\n";

// Example 5: Array Output Format
echo "5. Array Output Format:\n";
$nik = NIK::set('3273012501990001');
$array = $nik->toArray();

echo 'OK Array format: '.json_encode($array, JSON_PRETTY_PRINT)."\n";

echo "\n";

// Example 6: PHP 8.4 Features Demonstration
echo "6. PHP 8.4 Features Used:\n";
echo "   OK Readonly properties (number, location)\n";
echo "   OK Constructor property promotion\n";
echo "   OK Union types (string|int)\n";
echo "   OK Null coalescing assignment operator (??=)\n";
echo "   OK Early returns for better performance\n";
echo "   OK Direct string access for optimization\n";
echo "   OK Caching for frequently accessed data\n";
echo "   OK Improved error handling with JSON_THROW_ON_ERROR\n";
echo "   OK Static caching for current year\n";
echo "   OK Better validation with ctype_digit()\n";
echo "   OK Memory-efficient string operations\n";

echo "\n";

// Example 7: Memory Usage Comparison
echo "7. Memory Usage Comparison:\n";
$memoryBefore = memory_get_usage();

$niks = [];
for ($i = 0; $i < 100; $i++) {
    $niks[] = NIK::set('3273012501990001');
}

$memoryAfter = memory_get_usage();
$memoryUsed = $memoryAfter - $memoryBefore;

echo 'OK Memory used for 100 NIK objects: '.number_format($memoryUsed)." bytes\n";
echo 'OK Average memory per NIK object: '.number_format($memoryUsed / 100)." bytes\n";

echo "\n";

// Example 8: Caching Benefits
echo "8. Caching Benefits Demonstration:\n";
$nik = NIK::set('3273012501990001');

// First call (cache miss)
$time1 = microtime(true);
$born1 = $nik->getBornDate();
$time1 = (microtime(true) - $time1) * 1000;

// Second call (cache hit)
$time2 = microtime(true);
$born2 = $nik->getBornDate();
$time2 = (microtime(true) - $time2) * 1000;

echo 'OK First call (cache miss): '.number_format($time1, 4)."ms\n";
echo 'OK Second call (cache hit): '.number_format($time2, 4)."ms\n";
echo 'OK Performance improvement: '.($time2 > 0 ? round($time1 / $time2, 1) : 'infinite')."x faster\n";

echo "\n";

// Example 9: Type Safety and Union Types
echo "9. Type Safety and Union Types:\n";
try {
    // This will work with PHP 8.4 union types
    $nik1 = NIK::set('3273012501990001');  // string
    $nik2 = NIK::set(3273012501990001);    // integer
    echo "OK Both string and integer inputs work correctly\n";
} catch (TypeError $e) {
    echo 'ERROR Type error: '.$e->getMessage()."\n";
}

echo "\n";

// Example 10: Readonly Properties
echo "10. Readonly Properties Test:\n";
try {
    $nik = NIK::set('3273012501990001');
    $nik->number = '1234567890123456'; // This should fail
    echo "ERROR Should have thrown exception\n";
} catch (Error $e) {
    echo 'OK Readonly property protection works: '.$e->getMessage()."\n";
}

echo "\n";

// Example 11: Optimized String Operations
echo "11. Optimized String Operations:\n";
$nik = NIK::set('3273012501990001');

// Direct string access vs substr performance
$start = microtime(true);
for ($i = 0; $i < 10000; $i++) {
    $year = (int) ($nik->number[10].$nik->number[11]); // Optimized
}
$optimizedTime = (microtime(true) - $start) * 1000;

$start = microtime(true);
for ($i = 0; $i < 10000; $i++) {
    $year = (int) substr($nik->number, 10, 2); // Traditional
}
$traditionalTime = (microtime(true) - $start) * 1000;

echo 'OK Optimized string access: '.number_format($optimizedTime, 2)."ms\n";
echo 'OK Traditional substr: '.number_format($traditionalTime, 2)."ms\n";
echo 'OK Performance improvement: '.round($traditionalTime / $optimizedTime, 1)."x faster\n";

echo "\n";

// Example 12: New KK Features
echo "12. New KK Features:\n";
$kk = KK::set('3273012501990001');
echo "OK Raw number: {$kk->getRawNumber()}\n";
echo "OK Formatted number: {$kk->getFormattedNumber()}\n";
echo 'OK Array format: '.json_encode($kk->toArray(), JSON_PRETTY_PRINT)."\n";

echo "\n";

// Example 13: Random NIK and KK Generation
echo "13. Random Generation Performance Test:\n";

$generatedNik = measurePerformance(
    function () {
        return NIK::generate();
    },
    'NIK Random Generation'
);
echo "   Generated NIK: $generatedNik\n";

$generatedKk = measurePerformance(
    function () {
        return KK::generate();
    },
    'KK Random Generation'
);
echo "   Generated KK: $generatedKk\n";

echo "\nPerformance comparison for 1000 generations:\n";

measurePerformance(function () {
    for ($i = 0; $i < 1000; $i++) {
        NIK::generate();
    }
}, '1000 NIK Generations');

measurePerformance(function () {
    for ($i = 0; $i < 1000; $i++) {
        KK::generate();
    }
}, '1000 KK Generations');

echo "\n";

// Example 14: Localized Generation
echo "14. Localized Generation:\n";
$localizedNik = NIK::generate(province: '31', gender: 'PEREMPUAN');
echo "   Generated NIK (DKI Jakarta, Female): $localizedNik\n";
$nikObj = NIK::set($localizedNik);
echo "   Verified Province: {$nikObj->getProvince()}\n";
echo "   Verified Gender: {$nikObj->getGender()}\n";

echo "\n=== End of Optimized Demonstration ===\n";
