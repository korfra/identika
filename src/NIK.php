<?php

declare(strict_types = 1);

namespace Korfra\Identika;

/**
 * NIK (Nomor Induk Kependudukan) validator class with optimized performance.
 */
class NIK extends Base
{
    /**
     * Generate a random valid NIK.
     *
     * @param string|null $province 2-digit province code
     * @param string|null $city 4-digit city code (must start with province code)
     * @param string|null $subDistrict 6-digit sub-district code (must start with city code)
     * @param string|null $gender 'LAKI-LAKI' or 'PEREMPUAN'
     * @param string|null $birthDate Format: 'dmy' (e.g., '250199')
     */
    public static function generate(
        ?string $province = null,
        ?string $city = null,
        ?string $subDistrict = null,
        ?string $gender = null,
        ?string $birthDate = null,
    ): string {
        $prefix = $subDistrict ?? $city ?? $province;
        $locationCode = self::getRandomLocationCode($prefix);

        if (null === $birthDate) {
            $start = strtotime('-70 years');
            $end = time();
            $timestamp = random_int($start, $end);
            $birthDate = date('dmy', $timestamp);
        }

        $day = (int) substr($birthDate, 0, 2);
        $month = substr($birthDate, 2, 2);
        $year = substr($birthDate, 4, 2);

        $gender ??= (random_int(0, 1) === 0 ? 'LAKI-LAKI' : 'PEREMPUAN');

        if ('PEREMPUAN' === $gender) {
            $day += 40;
        }

        $datePart = (10 <= $day ? (string) $day : "0$day") . $month . $year;
        $uniqueCode = str_pad((string) random_int(1, 999), 4, '0', STR_PAD_LEFT);

        return $locationCode . $datePart . $uniqueCode;
    }

    /**
     * Create a new NIK instance with optimized type handling.
     */
    public static function set(string | int $number): self
    {
        $numberString = (string) $number;

        // Pre-validate number format for better performance
        if (strlen($numberString) !== 16 || ! ctype_digit($numberString)) {
            throw new \InvalidArgumentException('NIK must be exactly 16 digits');
        }

        return new self($numberString);
    }

    /**
     * Parse and validate NIK data with optimized structure.
     */
    public function parse(): object
    {
        if (! $this->validate()) {
            return (object) ['valid' => false];
        }

        // Cache frequently accessed data
        $born = $this->getBornDate();
        $address = (object) [
            'province' => $this->getProvince(),
            'city' => $this->getCity(),
            'subDistrict' => $this->getSubDistrict(),
        ];

        return (object) [
            'number' => $this->number,
            'uniqueCode' => $this->getUniqueCode(),
            'gender' => $this->getGender(),
            'born' => $born,
            'age' => $this->getAge(),
            'nextBirthday' => $this->getNextBirthday(),
            'address' => $address,
            'postalCode' => $this->getPostalCode(),
            'valid' => true,
        ];
    }

    /**
     * Validate NIK format and data with optimized checks.
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
            $errors[] = 'NIK must be exactly 16 digits';
        }

        if (! ctype_digit($this->number)) {
            $errors[] = 'NIK must contain only digits';
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
     * Get NIK information as array (alternative to object).
     */
    public function toArray(): array
    {
        $result = $this->parse();

        if (! $result->valid) {
            return ['valid' => false];
        }

        return [
            'number' => $result->number,
            'uniqueCode' => $result->uniqueCode,
            'gender' => $result->gender,
            'born' => [
                'date' => $result->born->date,
                'month' => $result->born->month,
                'year' => $result->born->year,
                'full' => $result->born->full,
            ],
            'age' => [
                'year' => $result->age->year,
                'month' => $result->age->month,
                'day' => $result->age->day,
            ],
            'nextBirthday' => [
                'month' => $result->nextBirthday->month,
                'day' => $result->nextBirthday->day,
            ],
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
     * Get unique code from NIK (optimized with direct access).
     */
    private function getUniqueCode(): string
    {
        return $this->number[12] . $this->number[13] . $this->number[14] . $this->number[15];
    }
}
