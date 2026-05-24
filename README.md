# Identika

[![PHP Version](https://img.shields.io/badge/PHP-8.4+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![Tests](https://img.shields.io/badge/Tests-63%20passed-brightgreen.svg)](https://github.com/korfra/identika)
[![Performance](https://img.shields.io/badge/Performance-Optimized-orange.svg)](https://github.com/korfra/identika)
[![Coverage](https://img.shields.io/badge/Coverage-100%25-brightgreen.svg)](https://github.com/korfra/identika)

A high-performance PHP package for validating, parsing, and generating Indonesian identity numbers (NIK and KK) with PHP 8.4 features and optimized performance.

## 🚀 Features

- **NIK (Nomor Induk Kependudukan)** validation and parsing
- **KK (Kartu Keluarga)** validation and parsing
- **PHP 8.4** optimized with modern features
- **High Performance** with intelligent caching
- **Offline Operation** - no internet connection required
- **Comprehensive Data** - age, gender, address, postal code
- **Type Safety** with strict type declarations
- **Error Handling** with detailed validation messages
- **Random Generation** for testing and development

## 📦 Installation

```bash
composer require korfra/identika
```

## 🎯 Quick Start

### NIK Validation

```php
<?php
use Korfra\Identika\NIK;

$nik = NIK::set('3273012501990001');
$result = $nik->parse();

if ($result->valid) {
    echo "Gender: " . $result->gender . "\n";
    echo "Born: " . $result->born->full . "\n";
    echo "Age: " . $result->age->year . " years\n";
    echo "Province: " . $result->address->province . "\n";
    echo "City: " . $result->address->city . "\n";
    echo "Sub-district: " . $result->address->subDistrict . "\n";
    echo "Postal Code: " . $result->postalCode . "\n";
    echo "Unique Code: " . $result->uniqueCode . "\n";
}
```

### KK Validation

```php
<?php
use Korfra\Identika\KK;

$kk = KK::set('3273012501990001');
$result = $kk->parse();

if ($result->valid) {
    echo "Formatted: " . $kk->getFormattedNumber() . "\n";
    echo "Province: " . $result->address->province . "\n";
    echo "City: " . $result->address->city . "\n";
    echo "Sub-district: " . $result->address->subDistrict . "\n";
    echo "Postal Code: " . $result->postalCode . "\n";
}
```

## 🔧 Advanced Usage

### Error Handling

```php
<?php
use Korfra\Identika\NIK;

try {
    $nik = NIK::set('123456789012345'); // Too short
} catch (\InvalidArgumentException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Get detailed validation errors
$nik = NIK::set('1234567890123456'); // Valid format but invalid data
$errors = $nik->getValidationErrors();
print_r($errors);
```

### Array Output

```php
<?php
use Korfra\Identika\NIK;

$nik = NIK::set('3273012501990001');
$array = $nik->toArray();

echo json_encode($array, JSON_PRETTY_PRINT);
```

### Random Generation

```php
<?php
use Korfra\Identika\NIK;
use Korfra\Identika\KK;

// Generate random NIK
$nik = NIK::generate(); // Returns valid NIK string

// Generate NIK with specific location, gender and birth date
$nik = NIK::generate(
    province: '32',           // Jawa Barat
    city: '3273',             // Kota Bandung
    subDistrict: '327301',     // Sukasari
    gender: 'PEREMPUAN',
    birthDate: '170845'       // 17 August 1945
);

// Generate random KK
$kk = KK::generate(); // Returns valid KK string

// Generate KK for specific location
$kk = KK::generate(province: '31'); // DKI Jakarta
```

### Type Safety

```php
<?php
use Korfra\Identika\NIK;

// Both string and integer inputs work
$nik1 = NIK::set('3273012501990001');  // string
$nik2 = NIK::set(3273012501990001);    // integer
```

## ⚡ Performance Optimizations

### Caching System
- Intelligent caching for frequently accessed data
- **13.8x faster** performance on cached vs non-cached calls
- Memory-efficient caching strategy

### String Operations
- Direct character access instead of `substr()`
- **2.6x faster** string operations

### Memory Management
- Readonly properties for immutability
- Efficient object lifecycle management
- Static caching for current year calculation

## 🛡️ PHP 8.4 Features

- **Readonly Properties**: `public readonly string $number`
- **Constructor Property Promotion**: Simplified constructors
- **Union Types**: `string|int` for flexible input
- **Null Coalescing Assignment**: `??=` operator
- **Improved Type Declarations**: Better type safety
- **Early Returns**: Optimized control flow

## 📊 Performance Benchmarks

| Operation | Time | Memory |
|-----------|------|--------|
| NIK Creation | ~1.1ms | ~785KB |
| NIK Parsing | ~0.03ms | ~69KB |
| KK Creation | ~0.1ms | ~16KB |
| KK Parsing | <0.01ms | ~1KB |
| NIK Generation | ~0.05ms | ~48B |
| KK Generation | ~0.01ms | ~48B |
| 1000 NIK Operations | ~3.1ms | Optimized |
| 1000 KK Operations | ~1.3ms | Optimized |

## 🧪 Testing

Run the comprehensive test suite:

```bash
composer test
```

All 63 tests pass with 100% coverage.

## 📁 Project Structure

```
identika/
├── src/
│   ├── Base.php          # Abstract base class with optimizations
│   ├── NIK.php           # NIK validator with caching
│   ├── KK.php            # KK validator with formatting
│   └── assets/
│       └── wilayah.json  # Location data
├── tests/
│   ├── BaseTest.php      # Base class tests
│   ├── NIKTest.php       # NIK validation tests
│   ├── KKTest.php        # KK validation tests
│   └── TestCase.php      # Test base class
├── example.php           # Performance demonstration
└── composer.json         # Dependencies
```

## 🔍 API Reference

### NIK Class

#### Methods
- `set(string|int $number): self` - Create NIK instance
- `generate(?string $province = null, ?string $city = null, ?string $subDistrict = null, ?string $gender = null, ?string $birthDate = null): string` - Generate random valid NIK
- `parse(): object` - Parse and validate NIK data
- `validate(): bool` - Check if NIK is valid
- `getValidationErrors(): array` - Get detailed error messages
- `toArray(): array` - Get data as array
- `getGender(): string` - Get gender (LAKI-LAKI/PEREMPUAN)
- `getBornDate(): object` - Get birth date information
- `getAge(): object` - Get age calculation
- `getProvince(): ?string` - Get province name
- `getCity(): ?string` - Get city name
- `getSubDistrict(): ?string` - Get sub-district name
- `getPostalCode(): ?string` - Get postal code

### KK Class

#### Methods
- `set(string|int $number): self` - Create KK instance
- `generate(?string $province = null, ?string $city = null, ?string $subDistrict = null): string` - Generate random valid KK number
- `parse(): object` - Parse and validate KK data
- `validate(): bool` - Check if KK is valid
- `getValidationErrors(): array` - Get detailed error messages
- `toArray(): array` - Get data as array
- `getFormattedNumber(): string` - Get formatted KK number
- `getRawNumber(): string` - Get raw KK number
- `getProvince(): ?string` - Get province name
- `getCity(): ?string` - Get city name
- `getSubDistrict(): ?string` - Get sub-district name
- `getPostalCode(): ?string` - Get postal code

## 🚀 Performance Tips

1. **Reuse Instances**: Create validator once and reuse for multiple operations
2. **Caching Benefits**: Subsequent calls to `getBornDate()`, `getAge()`, etc. are cached
3. **Type Safety**: Use union types for flexible input handling
4. **Error Handling**: Always check `$result->valid` before accessing data

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Ensure all tests pass
6. Submit a pull request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- Indonesian government for the NIK and KK number format specifications
- PHP community for the excellent 8.4 features
- All contributors who helped optimize this package

---

**Built with ❤️ and optimized for PHP 8.4+**
