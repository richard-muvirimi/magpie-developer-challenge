<?php

declare(strict_types=1);

/**
 * Custom storage converter file
 *
 * @package App
 * @subpackage App\Helper
 * @since 1.0.0
 * @version 1.0.0
 */

namespace App\Helpers;

use Byte\ByteConverter as ByteByteConverter;

/**
 * Storage unit  conversion class
 *
 * Simply overrides the one provided by conversion library
 *
 * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since 1.0.0
 * @version 1.0.0
 */
class ByteConverter extends ByteByteConverter
{

    /**
     * Conversion units
     *
     * @var array
     *
     * @inheritDoc
     * @since 1.0.0
     * @version 1.0.0
     */
    protected static $units = array (
        self::BYTE_STRING => 1,
        self::K_BYTE_STRING => 1000,
        self::M_BYTE_STRING => 1000000,
        self::G_BYTE_STRING => 1000000000
    );

    /**
     * Convert to B
     *
     * @param string $input
     *
     * @inheritDoc
     * @since 1.0.0
     * @version 1.0.0
     *
     * @return float
     */
    public function getBytes($input)
    {
        return $this->compute($input, self::BYTE_STRING);
    }
    
    /**
     * Convert to KB
     *
     * @param string $input
     *
     * @inheritDoc
     * @since 1.0.0
     * @version 1.0.0
     *
     * @return float
     */
    public function getKBytes($input)
    {
        return $this->compute($input, self::K_BYTE_STRING);
    }
    
    /**
     * Convert to MB
     *
     * @param string $input
     *
     * @inheritDoc
     * @since 1.0.0
     * @version 1.0.0
     *
     * @return float
     */
    public function getMBytes($input)
    {
        return $this->compute($input, self::M_BYTE_STRING);
    }
    
    /**
     * Convert to GB
     *
     * @param string $input
     *
     * @inheritDoc
     * @since 1.0.0
     * @version 1.0.0
     *
     * @return float
     */
    public function getGBytes($input)
    {
        return $this->compute($input, self::G_BYTE_STRING);
    }
    
    /**
     * Compute size
     *
     * @param string $input
     * @param string $unitOut
     *
     * @inheritDoc
     * @since 1.0.0
     * @version 1.0.0
     *
     * @return float
     */
    private function compute($input, $unitOut)
    {
        $value = ( int ) substr($input, 0, - 1);
        $unitIn = strtolower(substr($input, - 1));
        return (isset(self::$units [$unitIn])) ?
            (($value * self::$units [$unitIn]) / self::$units [$unitOut]) : 0;
    }
}
