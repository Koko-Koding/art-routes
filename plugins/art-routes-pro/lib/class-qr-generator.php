<?php
/**
 * Self-contained QR Code SVG generator.
 *
 * Supports byte mode encoding, EC level M, versions 1-10.
 * Handles URLs up to ~200 characters — sufficient for any WordPress permalink.
 *
 * No external dependencies. Generates clean SVG output ideal for print.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Art_Routes_QR_Generator {

    // GF(256) lookup tables.
    private static $exp = array();
    private static $log = array();
    private static $initialized = false;

    /**
     * Version EC info for EC Level M.
     * Format: version => [ec_per_block, [[group1_blocks, group1_data_cw], [group2_blocks, group2_data_cw]]]
     */
    private static $versions = array(
        1  => array( 10, array( array( 1, 16 ) ) ),
        2  => array( 16, array( array( 1, 28 ) ) ),
        3  => array( 26, array( array( 1, 44 ) ) ),
        4  => array( 18, array( array( 2, 32 ) ) ),
        5  => array( 24, array( array( 2, 43 ) ) ),
        6  => array( 16, array( array( 4, 27 ) ) ),
        7  => array( 18, array( array( 4, 31 ) ) ),
        8  => array( 22, array( array( 2, 38 ), array( 2, 39 ) ) ),
        9  => array( 22, array( array( 3, 36 ), array( 2, 37 ) ) ),
        10 => array( 26, array( array( 4, 43 ), array( 1, 44 ) ) ),
    );

    /** Byte mode capacity per version at EC level M. */
    private static $capacity = array( 0, 14, 26, 42, 62, 84, 106, 122, 152, 180, 213 );

    /** Alignment pattern center positions per version. */
    private static $alignment = array(
        1  => array(),
        2  => array( 6, 18 ),
        3  => array( 6, 22 ),
        4  => array( 6, 26 ),
        5  => array( 6, 30 ),
        6  => array( 6, 34 ),
        7  => array( 6, 22, 38 ),
        8  => array( 6, 24, 42 ),
        9  => array( 6, 26, 46 ),
        10 => array( 6, 28, 50 ),
    );

    // =========================================================================
    // Public API
    // =========================================================================

    /**
     * Generate a QR code as an SVG string.
     *
     * @param string $text        The text to encode (typically a URL).
     * @param int    $module_size  Size of each module in SVG units (default 10).
     * @param int    $quiet_zone   Number of quiet zone modules (default 4, per spec).
     * @return string|false SVG string, or false if text is too long.
     */
    public static function svg( $text, $module_size = 10, $quiet_zone = 4 ) {
        $matrix = self::encode( $text );
        if ( false === $matrix ) {
            return false;
        }
        return self::render_svg( $matrix, $module_size, $quiet_zone );
    }

    /**
     * Generate a QR code matrix (2D array of 0/1).
     *
     * @param string $text The text to encode.
     * @return array|false 2D array of module values, or false if text is too long.
     */
    public static function matrix( $text ) {
        return self::encode( $text );
    }

    // =========================================================================
    // Encoding pipeline
    // =========================================================================

    private static function encode( $text ) {
        self::init();

        $bytes = array_values( unpack( 'C*', $text ) );
        $length = count( $bytes );

        $version = self::get_version( $length );
        if ( false === $version ) {
            return false;
        }

        // Build data codewords.
        $data_cw = self::encode_data( $bytes, $version );

        // EC encode and interleave.
        $final_cw = self::interleave( $data_cw, $version );

        // Convert codewords to bit stream.
        $bits = array();
        foreach ( $final_cw as $cw ) {
            for ( $i = 7; $i >= 0; $i-- ) {
                $bits[] = ( $cw >> $i ) & 1;
            }
        }

        $dim = 4 * $version + 17;

        // Create matrix and function pattern mask.
        $matrix = array_fill( 0, $dim, array_fill( 0, $dim, 0 ) );
        $is_func = array_fill( 0, $dim, array_fill( 0, $dim, false ) );

        self::place_finder_patterns( $matrix, $is_func, $dim );
        self::place_alignment_patterns( $matrix, $is_func, $version, $dim );
        self::place_timing_patterns( $matrix, $is_func, $dim );
        self::place_dark_module( $matrix, $is_func, $version );
        self::reserve_format_area( $is_func, $dim );

        // Place data bits.
        self::place_data( $matrix, $is_func, $bits, $dim );

        // Find best mask.
        $best_mask   = 0;
        $best_penalty = PHP_INT_MAX;

        for ( $mask = 0; $mask < 8; $mask++ ) {
            $test = self::apply_mask( $matrix, $is_func, $mask, $dim );
            self::write_format_info( $test, $mask, $dim );
            $penalty = self::evaluate_penalty( $test, $dim );
            if ( $penalty < $best_penalty ) {
                $best_penalty = $penalty;
                $best_mask    = $mask;
            }
        }

        // Apply best mask to the real matrix.
        $matrix = self::apply_mask( $matrix, $is_func, $best_mask, $dim );
        self::write_format_info( $matrix, $best_mask, $dim );

        return $matrix;
    }

    private static function get_version( $byte_count ) {
        for ( $v = 1; $v <= 10; $v++ ) {
            if ( $byte_count <= self::$capacity[ $v ] ) {
                return $v;
            }
        }
        return false;
    }

    // =========================================================================
    // Data encoding (byte mode)
    // =========================================================================

    private static function encode_data( $bytes, $version ) {
        $length    = count( $bytes );
        $count_bits = $version <= 9 ? 8 : 16;
        $ver_info  = self::$versions[ $version ];
        $total_data_cw = 0;
        foreach ( $ver_info[1] as $group ) {
            $total_data_cw += $group[0] * $group[1];
        }

        // Build bit stream.
        $bit_stream = array();

        // Mode indicator: 0100 (byte mode).
        $bit_stream = array_merge( $bit_stream, array( 0, 1, 0, 0 ) );

        // Character count.
        for ( $i = $count_bits - 1; $i >= 0; $i-- ) {
            $bit_stream[] = ( $length >> $i ) & 1;
        }

        // Data bytes.
        foreach ( $bytes as $byte ) {
            for ( $i = 7; $i >= 0; $i-- ) {
                $bit_stream[] = ( $byte >> $i ) & 1;
            }
        }

        // Terminator (up to 4 zero bits).
        $total_bits = $total_data_cw * 8;
        $terminator = min( 4, $total_bits - count( $bit_stream ) );
        for ( $i = 0; $i < $terminator; $i++ ) {
            $bit_stream[] = 0;
        }

        // Pad to byte boundary.
        while ( count( $bit_stream ) % 8 !== 0 ) {
            $bit_stream[] = 0;
        }

        // Pad with alternating 0xEC, 0x11.
        $pad_bytes = array( 0xEC, 0x11 );
        $pad_idx   = 0;
        while ( count( $bit_stream ) < $total_bits ) {
            $byte = $pad_bytes[ $pad_idx % 2 ];
            for ( $i = 7; $i >= 0; $i-- ) {
                $bit_stream[] = ( $byte >> $i ) & 1;
            }
            $pad_idx++;
        }

        // Convert bit stream to codewords.
        $codewords = array();
        for ( $i = 0; $i < $total_data_cw; $i++ ) {
            $val = 0;
            for ( $b = 0; $b < 8; $b++ ) {
                $val = ( $val << 1 ) | ( $bit_stream[ $i * 8 + $b ] ?? 0 );
            }
            $codewords[] = $val;
        }

        return $codewords;
    }

    // =========================================================================
    // Reed-Solomon error correction
    // =========================================================================

    private static function interleave( $data_cw, $version ) {
        $ver_info    = self::$versions[ $version ];
        $ec_per_block = $ver_info[0];
        $groups       = $ver_info[1];

        $blocks = array();
        $offset = 0;

        foreach ( $groups as $group ) {
            $num_blocks    = $group[0];
            $data_per_block = $group[1];
            for ( $b = 0; $b < $num_blocks; $b++ ) {
                $block_data = array_slice( $data_cw, $offset, $data_per_block );
                $block_ec   = self::rs_encode( $block_data, $ec_per_block );
                $blocks[]   = array( 'data' => $block_data, 'ec' => $block_ec );
                $offset    += $data_per_block;
            }
        }

        // Interleave data codewords.
        $result   = array();
        $max_data = 0;
        foreach ( $blocks as $block ) {
            $max_data = max( $max_data, count( $block['data'] ) );
        }
        for ( $i = 0; $i < $max_data; $i++ ) {
            foreach ( $blocks as $block ) {
                if ( $i < count( $block['data'] ) ) {
                    $result[] = $block['data'][ $i ];
                }
            }
        }

        // Interleave EC codewords.
        for ( $i = 0; $i < $ec_per_block; $i++ ) {
            foreach ( $blocks as $block ) {
                $result[] = $block['ec'][ $i ];
            }
        }

        return $result;
    }

    private static function rs_encode( $data, $num_ec ) {
        $gen = self::generator_poly( $num_ec );

        $result = array_merge( $data, array_fill( 0, $num_ec, 0 ) );

        for ( $i = 0, $len = count( $data ); $i < $len; $i++ ) {
            $lead = $result[ $i ];
            if ( 0 !== $lead ) {
                for ( $j = 1; $j <= $num_ec; $j++ ) {
                    $result[ $i + $j ] ^= self::gf_mul( $gen[ $j ], $lead );
                }
            }
        }

        return array_slice( $result, count( $data ) );
    }

    /**
     * Compute RS generator polynomial coefficients (descending power order).
     * g(x) = (x + α^0)(x + α^1)...(x + α^(n-1))
     */
    private static function generator_poly( $n ) {
        // Build in ascending power order, then reverse.
        $gen = array( 1 );
        for ( $i = 0; $i < $n; $i++ ) {
            $new_gen = array_fill( 0, count( $gen ) + 1, 0 );
            for ( $j = 0, $len = count( $gen ); $j < $len; $j++ ) {
                $new_gen[ $j ]     ^= self::gf_mul( $gen[ $j ], self::$exp[ $i ] );
                $new_gen[ $j + 1 ] ^= $gen[ $j ];
            }
            $gen = $new_gen;
        }
        return array_reverse( $gen );
    }

    // =========================================================================
    // GF(256) arithmetic
    // =========================================================================

    private static function init() {
        if ( self::$initialized ) {
            return;
        }

        self::$exp = array_fill( 0, 512, 0 );
        self::$log = array_fill( 0, 256, 0 );

        $x = 1;
        for ( $i = 0; $i < 255; $i++ ) {
            self::$exp[ $i ] = $x;
            self::$log[ $x ] = $i;
            $x <<= 1;
            if ( $x & 256 ) {
                $x ^= 0x11D; // Primitive polynomial: x^8 + x^4 + x^3 + x^2 + 1
            }
        }
        // Extend exp table for easy modular access.
        for ( $i = 255; $i < 512; $i++ ) {
            self::$exp[ $i ] = self::$exp[ $i - 255 ];
        }

        self::$initialized = true;
    }

    private static function gf_mul( $a, $b ) {
        if ( 0 === $a || 0 === $b ) {
            return 0;
        }
        return self::$exp[ self::$log[ $a ] + self::$log[ $b ] ];
    }

    // =========================================================================
    // Matrix construction — function patterns
    // =========================================================================

    private static function place_finder_patterns( &$matrix, &$is_func, $dim ) {
        $positions = array(
            array( 0, 0 ),
            array( 0, $dim - 7 ),
            array( $dim - 7, 0 ),
        );

        foreach ( $positions as $pos ) {
            $r0 = $pos[0];
            $c0 = $pos[1];

            for ( $r = -1; $r <= 7; $r++ ) {
                for ( $c = -1; $c <= 7; $c++ ) {
                    $row = $r0 + $r;
                    $col = $c0 + $c;
                    if ( $row < 0 || $row >= $dim || $col < 0 || $col >= $dim ) {
                        continue;
                    }

                    // Finder pattern: 7x7 with specific pattern; separator ring is white.
                    if ( $r === -1 || $r === 7 || $c === -1 || $c === 7 ) {
                        $val = 0; // Separator / outside.
                    } elseif ( $r === 0 || $r === 6 || $c === 0 || $c === 6 ) {
                        $val = 1; // Outer ring.
                    } elseif ( $r === 1 || $r === 5 || $c === 1 || $c === 5 ) {
                        $val = 0; // Inner white ring.
                    } else {
                        $val = 1; // Center 3x3.
                    }

                    $matrix[ $row ][ $col ] = $val;
                    $is_func[ $row ][ $col ] = true;
                }
            }
        }
    }

    private static function place_alignment_patterns( &$matrix, &$is_func, $version, $dim ) {
        $positions = self::$alignment[ $version ];
        if ( empty( $positions ) ) {
            return;
        }

        $centers = array();
        foreach ( $positions as $r ) {
            foreach ( $positions as $c ) {
                $centers[] = array( $r, $c );
            }
        }

        foreach ( $centers as $center ) {
            $cr = $center[0];
            $cc = $center[1];

            // Skip if overlapping with finder patterns.
            if ( $cr <= 8 && $cc <= 8 ) continue;             // Top-left.
            if ( $cr <= 8 && $cc >= $dim - 8 ) continue;      // Top-right.
            if ( $cr >= $dim - 8 && $cc <= 8 ) continue;      // Bottom-left.

            for ( $r = -2; $r <= 2; $r++ ) {
                for ( $c = -2; $c <= 2; $c++ ) {
                    $row = $cr + $r;
                    $col = $cc + $c;
                    if ( abs( $r ) === 2 || abs( $c ) === 2 ) {
                        $val = 1;
                    } elseif ( $r === 0 && $c === 0 ) {
                        $val = 1;
                    } else {
                        $val = 0;
                    }
                    $matrix[ $row ][ $col ] = $val;
                    $is_func[ $row ][ $col ] = true;
                }
            }
        }
    }

    private static function place_timing_patterns( &$matrix, &$is_func, $dim ) {
        for ( $i = 8; $i < $dim - 8; $i++ ) {
            $val = ( $i % 2 === 0 ) ? 1 : 0;

            // Horizontal timing (row 6).
            if ( ! $is_func[6][ $i ] ) {
                $matrix[6][ $i ] = $val;
                $is_func[6][ $i ] = true;
            }

            // Vertical timing (column 6).
            if ( ! $is_func[ $i ][6] ) {
                $matrix[ $i ][6] = $val;
                $is_func[ $i ][6] = true;
            }
        }
    }

    private static function place_dark_module( &$matrix, &$is_func, $version ) {
        $row = 4 * $version + 9;
        $matrix[ $row ][8] = 1;
        $is_func[ $row ][8] = true;
    }

    private static function reserve_format_area( &$is_func, $dim ) {
        // Around top-left finder: row 8 (cols 0-8) and column 8 (rows 0-8).
        for ( $i = 0; $i <= 8; $i++ ) {
            $is_func[8][ $i ] = true;
            $is_func[ $i ][8] = true;
        }

        // Near bottom-left finder: column 8, rows (dim-7) to (dim-1).
        for ( $i = $dim - 7; $i < $dim; $i++ ) {
            $is_func[ $i ][8] = true;
        }

        // Near top-right finder: row 8, cols (dim-8) to (dim-1).
        for ( $i = $dim - 8; $i < $dim; $i++ ) {
            $is_func[8][ $i ] = true;
        }
    }

    // =========================================================================
    // Data placement (zigzag)
    // =========================================================================

    private static function place_data( &$matrix, &$is_func, $bits, $dim ) {
        $bit_idx = 0;
        $bit_len = count( $bits );
        $right   = $dim - 1;
        $upward  = true;

        while ( $right >= 0 ) {
            if ( 6 === $right ) {
                $right--;
            }

            $left = $right - 1;

            if ( $upward ) {
                for ( $row = $dim - 1; $row >= 0; $row-- ) {
                    foreach ( array( $right, $left ) as $col ) {
                        if ( $col >= 0 && ! $is_func[ $row ][ $col ] ) {
                            $matrix[ $row ][ $col ] = ( $bit_idx < $bit_len ) ? $bits[ $bit_idx ] : 0;
                            $bit_idx++;
                        }
                    }
                }
            } else {
                for ( $row = 0; $row < $dim; $row++ ) {
                    foreach ( array( $right, $left ) as $col ) {
                        if ( $col >= 0 && ! $is_func[ $row ][ $col ] ) {
                            $matrix[ $row ][ $col ] = ( $bit_idx < $bit_len ) ? $bits[ $bit_idx ] : 0;
                            $bit_idx++;
                        }
                    }
                }
            }

            $upward = ! $upward;
            $right -= 2;
        }
    }

    // =========================================================================
    // Masking
    // =========================================================================

    private static function apply_mask( $matrix, $is_func, $mask, $dim ) {
        $result = $matrix;
        for ( $r = 0; $r < $dim; $r++ ) {
            for ( $c = 0; $c < $dim; $c++ ) {
                if ( $is_func[ $r ][ $c ] ) {
                    continue;
                }
                if ( self::mask_condition( $mask, $r, $c ) ) {
                    $result[ $r ][ $c ] ^= 1;
                }
            }
        }
        return $result;
    }

    private static function mask_condition( $mask, $r, $c ) {
        switch ( $mask ) {
            case 0: return ( ( $r + $c ) % 2 ) === 0;
            case 1: return ( $r % 2 ) === 0;
            case 2: return ( $c % 3 ) === 0;
            case 3: return ( ( $r + $c ) % 3 ) === 0;
            case 4: return ( ( (int) ( $r / 2 ) + (int) ( $c / 3 ) ) % 2 ) === 0;
            case 5: return ( ( $r * $c ) % 2 + ( $r * $c ) % 3 ) === 0;
            case 6: return ( ( ( $r * $c ) % 2 + ( $r * $c ) % 3 ) % 2 ) === 0;
            case 7: return ( ( ( $r + $c ) % 2 + ( $r * $c ) % 3 ) % 2 ) === 0;
        }
        return false;
    }

    /**
     * Evaluate mask penalty score (4 rules from QR spec).
     */
    private static function evaluate_penalty( $matrix, $dim ) {
        $penalty = 0;

        // Rule 1: Runs of same color in rows and columns.
        for ( $r = 0; $r < $dim; $r++ ) {
            $run = 1;
            for ( $c = 1; $c < $dim; $c++ ) {
                if ( $matrix[ $r ][ $c ] === $matrix[ $r ][ $c - 1 ] ) {
                    $run++;
                } else {
                    if ( $run >= 5 ) {
                        $penalty += $run - 2;
                    }
                    $run = 1;
                }
            }
            if ( $run >= 5 ) {
                $penalty += $run - 2;
            }
        }
        for ( $c = 0; $c < $dim; $c++ ) {
            $run = 1;
            for ( $r = 1; $r < $dim; $r++ ) {
                if ( $matrix[ $r ][ $c ] === $matrix[ $r - 1 ][ $c ] ) {
                    $run++;
                } else {
                    if ( $run >= 5 ) {
                        $penalty += $run - 2;
                    }
                    $run = 1;
                }
            }
            if ( $run >= 5 ) {
                $penalty += $run - 2;
            }
        }

        // Rule 2: 2x2 blocks of same color.
        for ( $r = 0; $r < $dim - 1; $r++ ) {
            for ( $c = 0; $c < $dim - 1; $c++ ) {
                $val = $matrix[ $r ][ $c ];
                if ( $val === $matrix[ $r ][ $c + 1 ]
                    && $val === $matrix[ $r + 1 ][ $c ]
                    && $val === $matrix[ $r + 1 ][ $c + 1 ] ) {
                    $penalty += 3;
                }
            }
        }

        // Rule 3: Finder-like patterns (1011101 0000 or 0000 1011101).
        $pattern_a = array( 1, 0, 1, 1, 1, 0, 1, 0, 0, 0, 0 );
        $pattern_b = array( 0, 0, 0, 0, 1, 0, 1, 1, 1, 0, 1 );

        for ( $r = 0; $r < $dim; $r++ ) {
            for ( $c = 0; $c <= $dim - 11; $c++ ) {
                $match_a = true;
                $match_b = true;
                for ( $k = 0; $k < 11; $k++ ) {
                    if ( $matrix[ $r ][ $c + $k ] !== $pattern_a[ $k ] ) $match_a = false;
                    if ( $matrix[ $r ][ $c + $k ] !== $pattern_b[ $k ] ) $match_b = false;
                    if ( ! $match_a && ! $match_b ) break;
                }
                if ( $match_a ) $penalty += 40;
                if ( $match_b ) $penalty += 40;
            }
        }
        for ( $c = 0; $c < $dim; $c++ ) {
            for ( $r = 0; $r <= $dim - 11; $r++ ) {
                $match_a = true;
                $match_b = true;
                for ( $k = 0; $k < 11; $k++ ) {
                    if ( $matrix[ $r + $k ][ $c ] !== $pattern_a[ $k ] ) $match_a = false;
                    if ( $matrix[ $r + $k ][ $c ] !== $pattern_b[ $k ] ) $match_b = false;
                    if ( ! $match_a && ! $match_b ) break;
                }
                if ( $match_a ) $penalty += 40;
                if ( $match_b ) $penalty += 40;
            }
        }

        // Rule 4: Dark module ratio.
        $dark = 0;
        for ( $r = 0; $r < $dim; $r++ ) {
            for ( $c = 0; $c < $dim; $c++ ) {
                if ( $matrix[ $r ][ $c ] ) {
                    $dark++;
                }
            }
        }
        $total      = $dim * $dim;
        $percentage = ( $dark * 100 ) / $total;
        $prev5      = (int) ( $percentage / 5 ) * 5;
        $next5      = $prev5 + 5;
        $penalty   += min( abs( $prev5 - 50 ), abs( $next5 - 50 ) ) * 2;

        return $penalty;
    }

    // =========================================================================
    // Format information
    // =========================================================================

    private static function write_format_info( &$matrix, $mask, $dim ) {
        $bits = self::format_info_bits( $mask );

        // First copy: around top-left finder.
        // Bits along row 8 (columns 0-5, 7, 8) and column 8 (rows 7, 5-0).
        $pos1 = array(
            array( 8, 0 ), array( 8, 1 ), array( 8, 2 ), array( 8, 3 ),
            array( 8, 4 ), array( 8, 5 ), array( 8, 7 ), array( 8, 8 ),
            array( 7, 8 ), array( 5, 8 ), array( 4, 8 ), array( 3, 8 ),
            array( 2, 8 ), array( 1, 8 ), array( 0, 8 ),
        );

        // Second copy: bottom-left (column 8) and top-right (row 8).
        $pos2 = array(
            array( $dim - 1, 8 ), array( $dim - 2, 8 ), array( $dim - 3, 8 ),
            array( $dim - 4, 8 ), array( $dim - 5, 8 ), array( $dim - 6, 8 ),
            array( $dim - 7, 8 ),
            array( 8, $dim - 8 ), array( 8, $dim - 7 ), array( 8, $dim - 6 ),
            array( 8, $dim - 5 ), array( 8, $dim - 4 ), array( 8, $dim - 3 ),
            array( 8, $dim - 2 ), array( 8, $dim - 1 ),
        );

        for ( $i = 0; $i < 15; $i++ ) {
            $val = ( $bits >> ( 14 - $i ) ) & 1;
            $matrix[ $pos1[ $i ][0] ][ $pos1[ $i ][1] ] = $val;
            $matrix[ $pos2[ $i ][0] ][ $pos2[ $i ][1] ] = $val;
        }
    }

    /**
     * Compute BCH(15,5) encoded format info for EC Level M.
     */
    private static function format_info_bits( $mask ) {
        $data = $mask; // EC Level M = 00, so data = 00xxx = mask pattern.

        // BCH(15,5) encoding.
        $remainder = $data << 10;
        $generator = 0x537; // x^10 + x^8 + x^5 + x^4 + x^2 + x + 1

        for ( $i = 4; $i >= 0; $i-- ) {
            if ( $remainder & ( 1 << ( $i + 10 ) ) ) {
                $remainder ^= ( $generator << $i );
            }
        }

        $format = ( $data << 10 ) | ( $remainder & 0x3FF );
        $format ^= 0x5412; // XOR mask: 101010000010010

        return $format;
    }

    // =========================================================================
    // SVG rendering
    // =========================================================================

    private static function render_svg( $matrix, $module_size, $quiet_zone ) {
        $dim       = count( $matrix );
        $total_dim = $dim + 2 * $quiet_zone;
        $svg_size  = $total_dim * $module_size;

        $svg  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $svg .= '<svg xmlns="http://www.w3.org/2000/svg" version="1.1"';
        $svg .= ' width="' . $svg_size . '" height="' . $svg_size . '"';
        $svg .= ' viewBox="0 0 ' . $total_dim . ' ' . $total_dim . '"';
        $svg .= ' shape-rendering="crispEdges">' . "\n";

        // White background.
        $svg .= '<rect width="' . $total_dim . '" height="' . $total_dim . '" fill="#FFFFFF"/>' . "\n";

        // Dark modules as a single path for efficiency.
        $path = '';
        for ( $r = 0; $r < $dim; $r++ ) {
            for ( $c = 0; $c < $dim; $c++ ) {
                if ( $matrix[ $r ][ $c ] ) {
                    $x = $c + $quiet_zone;
                    $y = $r + $quiet_zone;
                    $path .= 'M' . $x . ',' . $y . 'h1v1h-1z';
                }
            }
        }

        if ( $path ) {
            $svg .= '<path d="' . $path . '" fill="#000000"/>' . "\n";
        }

        $svg .= '</svg>';

        return $svg;
    }
}
