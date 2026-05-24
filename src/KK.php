<?php

declare(strict_types = 1);

namespace Korfra\Identika;

/**
 * KK (Kartu Keluarga) validator class with optimized performance.
 */
class KK extends Base
{
    /**
     * Generate a random valid KK number.
     *
     * @param string|null $province 2-digit province code
     * @param string|null $city 4-digit city code (must start with province code)
     * @param string|null $subDistrict 6-digit sub-district code (must start with city code)
     */
    public static function generate(
        ?string $province = null,
        ?string $city = null,
        ?string $subDistrict = null,
    ): string {
        $prefix = $subDistrict ?? $city ?? $province;
        $locationCode = self::getRandomLocationCode($prefix);

        // KK usually has a date part representing when it was issued/created
        // For generation, we use a random date within the last 20 years
        $start = strtotime('-20 years');
        $end = time();
        $timestamp = random_int($start, $end);
        $datePart = date('dmy', $timestamp);

        $uniqueCode = str_pad((string) random_int(1, 999), 4, '0', STR_PAD_LEFT);

        return $locationCode . $datePart . $uniqueCode;
    }

    /**
     * Create a new KK instance with optimized type handling.
     */
    public static function set(string | int $number): self
    {
        $numberString = (string) $number;

        // Pre-validate number format for better performance
        if (strlen($numberString) !== 16 || ! ctype_digit($numberString)) {
            throw new \InvalidArgumentException('KK number must be exactly 16 digits');
        }

        return new self($numberString);
    }

    /**
     * Parse and validate KK data with optimized structure.
     */
    public function parse(): object
    {
        if (! $this->validate()) {
            return (object) ['valid' => false];
        }

        // Cache frequently accessed data
        $address = (object) [
            'province' => $this->getProvince(),
            'city' => $this->getCity(),
            'subDistrict' => $this->getSubDistrict(),
        ];

        return (object) [
            'number' => $this->number,
            'address' => $address,
            'postalCode' => $this->getPostalCode(),
            'valid' => true,
        ];
    }

    /**
     * Validate KK format and data with optimized checks.
     */
    public function validate(): bool
    {
        // Quick length check first
        if (strlen($this->number) !== 16) {
            return false;
        }

        // Check if all characters are digits
        if (! ctype_digit($this->number)) {
            return false;
        }

        // Validate location data exists
        $province = $this->getProvince();
        $city = $this->getCity();
        $subDistrict = $this->getSubDistrict();

        return null !== $province && null !== $city && null !== $subDistrict;
    }

    /**
     * Get detailed validation errors for debugging.
     */
    public function getValidationErrors(): array
    {
        $errors = [];

        if (strlen($this->number) !== 16) {
            $errors[] = 'KK number must be exactly 16 digits';
        }

        if (! ctype_digit($this->number)) {
            $errors[] = 'KK number must contain only digits';
        }

        if ($this->getProvince() === null) {
            $errors[] = 'Invalid province code';
        }

        if ($this->getCity() === null) {
            $errors[] = 'Invalid city code';
        }

        if ($this->getSubDistrict() === null) {
            $errors[] = 'Invalid sub-district code';
        }

        return $errors;
    }

    /**
     * Get KK information as array (alternative to object).
     */
    public function toArray(): array
    {
        $result = $this->parse();

        if (! $result->valid) {
            return ['valid' => false];
        }

        return [
            'number' => $result->number,
            'address' => [
                'province' => $result->address->province,
                'city' => $result->address->city,
                'subDistrict' => $result->address->subDistrict,
            ],
            'postalCode' => $result->postalCode,
            'valid' => true,
        ];
    }

    /**
     * Get KK number with formatting (e.g., "1234-5678-9012-3456").
     */
    public function getFormattedNumber(): string
    {
        return implode('-', str_split($this->number, 4));
    }

    /**
     * Get KK number without formatting.
     */
    public function getRawNumber(): string
    {
        return $this->number;
    }
}
