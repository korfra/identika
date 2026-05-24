<?php

declare(strict_types = 1);

namespace Korfra\Identika;

/**
 * Base class for number validation with optimized performance.
 */
abstract class Base
{
    /**
     * Location data from JSON file.
     */
    public readonly array $location;

    /**
     * The number to validate.
     */
    public readonly string $number;

    /**
     * Static cache for location data to optimize static methods.
     */
    protected static ?array $cachedLocationData = null;

    /**
     * Cached values for performance.
     */
    private ?object $cachedBornDate = null;

    private ?object $cachedAge = null;

    private ?object $cachedNextBirthday = null;

    private ?string $cachedGender = null;

    /**
     * Constructor with property promotion and optimized file loading.
     */
    public function __construct(
        string $number,
        ?string $wilayahPath = null,
    ) {
        $this->number = $number;

        // Optimized file loading with error handling
        $wilayahPath ??= dirname(__FILE__) . '/assets/wilayah.json';

        if (null !== self::$cachedLocationData) {
            $this->location = self::$cachedLocationData;

            return;
        }

        if (! file_exists($wilayahPath)) {
            throw new \InvalidArgumentException("Wilayah file not found: $wilayahPath");
        }

        $jsonContent = file_get_contents($wilayahPath);

        if (false === $jsonContent) {
            throw new \RuntimeException("Failed to read wilayah file: $wilayahPath");
        }

        $this->location = self::$cachedLocationData = json_decode($jsonContent, true, 512, JSON_THROW_ON_ERROR) ?? [];
    }

    /**
     * Get last 2 digits number at the current year (cached).
     */
    public function getCurrentYear(): int
    {
        static $currentYear = null;

        if (null === $currentYear) {
            $currentYear = (int) substr(date('Y'), -2);
        }

        return $currentYear;
    }

    /**
     * Get year in NIK (optimized with direct access).
     */
    public function getNIKYear(): int
    {
        return (int) ($this->number[10] . $this->number[11]);
    }

    /**
     * Get date in number (optimized with direct access).
     */
    public function getNIKDate(): int
    {
        return (int) ($this->number[6] . $this->number[7]);
    }

    /**
     * Get born date from NIK (cached for performance).
     */
    public function getBornDate(): object
    {
        if (null !== $this->cachedBornDate) {
            return $this->cachedBornDate;
        }

        $nikDate = $this->getNIKDate();
        $nikYear = $this->getNIKYear();
        $currYear = $this->getCurrentYear();
        $isFemale = $this->getGender() === 'PEREMPUAN';

        // Optimized date calculation
        if ($isFemale) {
            $nikDate -= 40;
        }

        $date = 10 <= $nikDate ? (string) $nikDate : "0$nikDate";
        $month = $this->number[8] . $this->number[9];
        $year = (string) ($nikYear < $currYear ? 2000 + $nikYear : 1900 + $nikYear);
        $full = "$date-$month-$year";

        return $this->cachedBornDate = (object) compact('date', 'month', 'year', 'full');
    }

    /**
     * Get age data from born date (cached for performance).
     */
    public function getAge(): object
    {
        if (null !== $this->cachedAge) {
            return $this->cachedAge;
        }

        $bornDate = $this->getBornDate()->full;
        $bornTimestamp = strtotime($bornDate);

        if (false === $bornTimestamp) {
            throw new \RuntimeException("Invalid birth date format: $bornDate");
        }

        $ageDate = time() - $bornTimestamp;

        $year = abs((int) gmdate('Y', $ageDate) - 1970);
        $month = abs((int) gmdate('m', $ageDate));
        $day = abs((int) gmdate('d', $ageDate) - 1);

        return $this->cachedAge = (object) compact('year', 'month', 'day');
    }

    /**
     * Get next birthday from born date (cached for performance).
     */
    public function getNextBirthday(): object
    {
        if (null !== $this->cachedNextBirthday) {
            return $this->cachedNextBirthday;
        }

        $bornDate = $this->getBornDate()->full;
        $bornTimestamp = strtotime($bornDate);

        if (false === $bornTimestamp) {
            throw new \RuntimeException("Invalid birth date format: $bornDate");
        }

        $diff = $bornTimestamp - time();

        $month = abs((int) gmdate('m', $diff));
        $day = abs((int) gmdate('d', $diff) - 1);

        return $this->cachedNextBirthday = (object) compact('month', 'day');
    }

    /**
     * Get the province from NIK (optimized with direct access).
     */
    public function getProvince(): ?string
    {
        $provinceCode = $this->number[0] . $this->number[1];

        return $this->location['provinsi'][$provinceCode] ?? null;
    }

    /**
     * Get the city from NIK (optimized with direct access).
     */
    public function getCity(): ?string
    {
        $cityCode = $this->number[0] . $this->number[1] . $this->number[2] . $this->number[3];

        return $this->location['kabkot'][$cityCode] ?? null;
    }

    /**
     * Get the sub-district from NIK (optimized with direct access).
     */
    public function getSubDistrict(): ?string
    {
        $subDistrictCode = $this->number[0] . $this->number[1] . $this->number[2] .
            $this->number[3] . $this->number[4] . $this->number[5];

        $result = $this->location['kecamatan'][$subDistrictCode] ?? null;

        if (null === $result) {
            return null;
        }

        $parts = explode('--', $result, 2);

        return trim($parts[0]);
    }

    /**
     * Get postal code (optimized with direct access).
     */
    public function getPostalCode(): ?string
    {
        $subDistrictCode = $this->number[0] . $this->number[1] . $this->number[2] .
            $this->number[3] . $this->number[4] . $this->number[5];

        $result = $this->location['kecamatan'][$subDistrictCode] ?? null;

        if (null === $result) {
            return null;
        }

        $parts = explode('--', $result, 2);

        return isset($parts[1]) ? trim($parts[1]) : null;
    }

    /**
     * Get gender (cached for performance).
     */
    public function getGender(): string
    {
        if (null !== $this->cachedGender) {
            return $this->cachedGender;
        }

        $date = $this->getNIKDate();

        return $this->cachedGender = (40 < $date) ? 'PEREMPUAN' : 'LAKI-LAKI';
    }

    /**
     * Clear cached values (useful for testing or when data changes).
     */
    protected function clearCache(): void
    {
        $this->cachedBornDate = null;
        $this->cachedAge = null;
        $this->cachedNextBirthday = null;
        $this->cachedGender = null;
    }

    /**
     * Get a random location code from the available kecamatan data.
     */
    protected static function getRandomLocationCode(?string $prefix = null, ?string $wilayahPath = null): string
    {
        $kecamatan = self::getKecamatanCodes($prefix, $wilayahPath);

        return (string) $kecamatan[array_rand($kecamatan)];
    }

    /**
     * Get all kecamatan codes, optionally filtered by prefix.
     */
    protected static function getKecamatanCodes(?string $prefix = null, ?string $wilayahPath = null): array
    {
        if (null === self::$cachedLocationData) {
            $wilayahPath ??= dirname(__FILE__) . '/assets/wilayah.json';

            if (! file_exists($wilayahPath)) {
                throw new \InvalidArgumentException("Wilayah file not found: $wilayahPath");
            }

            $jsonContent = file_get_contents($wilayahPath);

            if (false === $jsonContent) {
                throw new \RuntimeException("Failed to read wilayah file: $wilayahPath");
            }

            self::$cachedLocationData = json_decode($jsonContent, true, 512, JSON_THROW_ON_ERROR);
        }

        $kecamatan = array_keys(self::$cachedLocationData['kecamatan'] ?? []);

        if (null !== $prefix) {
            $kecamatan = array_filter($kecamatan, static fn ($code) => str_starts_with((string) $code, $prefix));
        }

        if (empty($kecamatan)) {
            throw new \RuntimeException('No kecamatan data found for the given criteria');
        }

        return array_values($kecamatan);
    }
}
