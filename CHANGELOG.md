# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Planned
- Additional validation rules
- More comprehensive error messages
- Performance monitoring tools
- Extended documentation

## [2.0.0] - 2026-05-24

### Added
- **Identity Generation**: Added `NIK::generate()` and `KK::generate()` for high-performance random identity number generation
- **Localized Generation**: Support for generating NIK/KK based on specific provinces and genders
- **Comprehensive Benchmarks**: Added performance demonstration for generation features

### Changed
- **Performance Optimizations**: Further refined string operations and caching for faster processing

### Removed
- **Zodiac Calculation**: Removed zodiac calculation from NIK results to focus on core identity data and performance

### Technical Enhancements
- **Static Methods**: Modernized helper methods for utility access
- **Memory Efficiency**: Reduced overhead during bulk generation operations

### Breaking Changes
- **Namespace Migration**: Migrated from `Turahe\Validator` to `Korfra\Identika` namespace
- **Zodiac Removal**: The `zodiac` property is no longer available in the NIK parse result

### Migration Guide
- Update your `use` statements from `Turahe\Validator\*` to `Korfra\Identika\*`
- Remove any logic that relies on the `zodiac` property from NIK parse results

## [1.0.0] - 2024-12-19

### Added
- **PHP 8.4 Support**: Full compatibility with PHP 8.4 features
- **NIK Validator**: Complete NIK (Nomor Induk Kependudukan) validation and parsing
- **KK Validator**: Complete KK (Kartu Keluarga) validation and parsing
- **Performance Optimizations**: Caching mechanisms for frequently accessed data
- **Comprehensive Testing**: 62 test cases with 176 assertions
- **Code Quality**: PHP CS Fixer integration with PSR-12 standards
- **CI/CD Pipeline**: GitHub Actions workflow with multiple checks
- **Documentation**: Complete README with badges and usage examples

### Features
- **Readonly Properties**: Immutable data structures for better type safety
- **Constructor Property Promotion**: Modern PHP 8.4 initialization
- **Union Types**: Support for both string and integer inputs
- **Null Coalescing Assignment**: Efficient default value handling
- **Direct String Access**: Optimized substring operations
- **Static Caching**: Performance improvements for repeated operations
- **Error Handling**: Comprehensive exception handling with detailed messages
- **Array Output**: Structured data output for easy integration

### Performance Improvements
- **Caching Benefits**: 13.5x faster on cache hits
- **String Operations**: 3.5x faster with direct access
- **Memory Efficiency**: Optimized object creation and memory usage
- **Early Returns**: Reduced unnecessary computations

### Technical Enhancements
- **Type Safety**: Strict type declarations throughout
- **Modern PHP Features**: Arrow functions, match expressions, improved error handling
- **Code Coverage**: Comprehensive test suite with high coverage
- **Security**: Input validation and sanitization
- **Compatibility**: PHP 8.4+ requirement with modern features

### Documentation
- **README.md**: Complete documentation with badges
- **Example Usage**: Comprehensive demonstration script
- **API Documentation**: Clear method documentation
- **Installation Guide**: Step-by-step setup instructions

### Development Tools
- **PHPUnit 12**: Latest testing framework
- **PHP CS Fixer**: Code style enforcement
- **GitHub Actions**: Automated CI/CD pipeline
- **Composer**: Modern dependency management

### Breaking Changes
- **PHP Version**: Requires PHP 8.4 or higher
- **API Changes**: Some method signatures updated for better type safety

### Migration Guide
- Update PHP to version 8.4 or higher
- Review method signatures for type changes
- Update any custom implementations to match new interfaces
