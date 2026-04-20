<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class QrTokenSvg
{
    public static function svg(string $payload, int $modules = 29, int $size = 320): string
    {
        $grid = self::buildGrid($payload, $modules);
        $cell = max(4, (int) floor($size / $modules));
        $actualSize = $cell * $modules;
        $rects = [];

        foreach ($grid as $rowIndex => $row) {
            foreach ($row as $colIndex => $filled) {
                if (! $filled) {
                    continue;
                }

                $rects[] = sprintf(
                    '<rect x="%d" y="%d" width="%d" height="%d" fill="#111827"/>',
                    $colIndex * $cell,
                    $rowIndex * $cell,
                    $cell,
                    $cell
                );
            }
        }

        return sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 %1$d %1$d" width="%1$d" height="%1$d" role="img" aria-label="QR tiket"><rect width="%1$d" height="%1$d" fill="#ffffff"/>%2$s</svg>',
            $actualSize,
            implode('', $rects)
        );
    }

    public static function publicUrl(string $payload, string $path, int $size = 320): string
    {
        Storage::disk('public')->put($path, self::svg($payload, 29, $size));

        return $path;
    }

    protected static function buildGrid(string $payload, int $modules): array
    {
        $grid = array_fill(0, $modules, array_fill(0, $modules, false));
        $reserved = array_fill(0, $modules, array_fill(0, $modules, false));

        self::placeFinder($grid, $reserved, 0, 0);
        self::placeFinder($grid, $reserved, 0, $modules - 7);
        self::placeFinder($grid, $reserved, $modules - 7, 0);
        self::placeTiming($grid, $reserved, $modules);

        $stream = self::bitStream($payload, $modules * $modules);
        $index = 0;

        for ($row = 0; $row < $modules; $row++) {
            for ($col = 0; $col < $modules; $col++) {
                if ($reserved[$row][$col]) {
                    continue;
                }

                $mask = (($row + $col) % 2) === 0;
                $bit = (int) ($stream[$index] ?? 0);
                $grid[$row][$col] = (bool) ($bit ^ (int) $mask);
                $index++;
            }
        }

        return $grid;
    }

    protected static function placeFinder(array &$grid, array &$reserved, int $top, int $left): void
    {
        for ($row = 0; $row < 7; $row++) {
            for ($col = 0; $col < 7; $col++) {
                $isOuter = $row === 0 || $row === 6 || $col === 0 || $col === 6;
                $isInner = $row >= 2 && $row <= 4 && $col >= 2 && $col <= 4;

                $grid[$top + $row][$left + $col] = $isOuter || $isInner;
                $reserved[$top + $row][$left + $col] = true;
            }
        }

        for ($row = max(0, $top - 1); $row <= min(count($grid) - 1, $top + 7); $row++) {
            for ($col = max(0, $left - 1); $col <= min(count($grid) - 1, $left + 7); $col++) {
                $reserved[$row][$col] = true;
            }
        }
    }

    protected static function placeTiming(array &$grid, array &$reserved, int $modules): void
    {
        for ($i = 8; $i < $modules - 8; $i++) {
            $grid[6][$i] = $i % 2 === 0;
            $grid[$i][6] = $i % 2 === 0;
            $reserved[6][$i] = true;
            $reserved[$i][6] = true;
        }
    }

    protected static function bitStream(string $payload, int $length): string
    {
        $stream = '';
        $seed = hash('sha256', $payload, true);
        $counter = 0;

        while (strlen($stream) < $length) {
            $chunk = hash('sha256', $seed.pack('N', $counter), true);

            foreach (str_split($chunk) as $char) {
                $stream .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
            }

            $counter++;
        }

        return substr($stream, 0, $length);
    }
}
