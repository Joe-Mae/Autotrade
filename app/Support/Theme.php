<?php

namespace App\Support;

/**
 * Calcule les variables CSS de la palette à partir des trois couleurs
 * configurables dans l'administration :
 *   - principale (accent : boutons, titres, libellés) ;
 *   - secondaire (contours, survols, halos) ;
 *   - fond sombre (arrière-plan du mode sombre).
 *
 * Les valeurs par défaut sont celles du site d'origine (citron, olive, noir).
 */
class Theme
{
    public const DEFAULTS = [
        'primary' => '#cfff04',
        'secondary' => '#536900',
        'dark' => '#0a0a0a',
    ];

    /**
     * Variables CSS pour le mode sombre et le mode clair.
     *
     * @return array{dark: array<string, string>, light: array<string, string>}
     */
    public static function variables(array $site): array
    {
        $primary = self::accent($site['color_primary'] ?? null);
        $secondary = self::color($site['color_secondary'] ?? null, self::DEFAULTS['secondary']);
        $dark = self::color($site['color_dark'] ?? null, self::DEFAULTS['dark']);
        $lightSecondary = self::darken($secondary, 0.88);

        return [
            'dark' => [
                '--ink' => self::contrastText($primary),
                '--page-bg' => $dark,
                '--bone' => '#f5f5f0',
                '--volt' => $primary,
                '--volt-text' => $primary,
                '--violet' => $secondary,
                '--violet-text' => $secondary,
                '--violet-soft' => self::rgba($secondary, 0.32),
                '--violet-glow' => self::rgba($secondary, 0.18),
                '--violet-halo' => self::rgba($secondary, 0.22),
                '--panel' => self::mixWithWhite($dark, 0.03),
                '--panel2' => self::mixWithWhite($dark, 0.065),
                '--border' => 'rgba(255, 255, 255, 0.1)',
                '--muted' => self::mixWithWhite($dark, 0.5),
            ],
            'light' => [
                '--ink' => '#000000',
                '--page-bg' => '#f5f5f0',
                '--bone' => '#111111',
                '--volt' => $primary,
                '--volt-text' => self::darkenForLight($primary),
                '--violet' => $lightSecondary,
                '--violet-text' => $lightSecondary,
                '--violet-soft' => self::rgba($lightSecondary, 0.2),
                '--violet-glow' => self::rgba($lightSecondary, 0.14),
                '--violet-halo' => self::rgba($lightSecondary, 0.16),
                '--panel' => '#ffffff',
                '--panel2' => '#eae8e0',
                '--border' => 'rgba(0, 0, 0, 0.14)',
                '--muted' => '#6b6a62',
            ],
        ];
    }

    /**
     * Couleur d'accent validée (repli sur le citron d'origine).
     */
    public static function accent(?string $value): string
    {
        return self::color($value, self::DEFAULTS['primary']);
    }

    /**
     * Couleur de texte lisible sur un aplat de cette couleur (#000 ou #fff).
     */
    public static function contrastText(?string $value): string
    {
        [$r, $g, $b] = self::rgb(self::accent($value));

        $luminance = 0.2126 * self::channel($r)
            + 0.7152 * self::channel($g)
            + 0.0722 * self::channel($b);

        return $luminance > 0.45 ? '#000000' : '#ffffff';
    }

    private static function color(?string $value, string $fallback): string
    {
        $value = strtolower(trim((string) $value));

        return preg_match('/^#[0-9a-f]{6}$/', $value) ? $value : $fallback;
    }

    /**
     * @return array{0: int, 1: int, 2: int}
     */
    private static function rgb(string $hex): array
    {
        return [
            (int) hexdec(substr($hex, 1, 2)),
            (int) hexdec(substr($hex, 3, 2)),
            (int) hexdec(substr($hex, 5, 2)),
        ];
    }

    /**
     * @param  array{0: float, 1: float, 2: float}  $rgb
     */
    private static function fromRgb(array $rgb): string
    {
        $channels = array_map(
            static fn ($channel) => max(0, min(255, (int) round($channel))),
            $rgb
        );

        return sprintf('#%02x%02x%02x', ...$channels);
    }

    private static function rgba(string $hex, float $alpha): string
    {
        [$r, $g, $b] = self::rgb($hex);
        $alpha = rtrim(rtrim(number_format($alpha, 2, '.', ''), '0'), '.');

        return sprintf('rgba(%d, %d, %d, %s)', $r, $g, $b, $alpha);
    }

    private static function mixWithWhite(string $hex, float $ratio): string
    {
        [$r, $g, $b] = self::rgb($hex);

        return self::fromRgb([
            $r + (255 - $r) * $ratio,
            $g + (255 - $g) * $ratio,
            $b + (255 - $b) * $ratio,
        ]);
    }

    private static function darken(string $hex, float $factor): string
    {
        [$r, $g, $b] = self::rgb($hex);

        return self::fromRgb([$r * $factor, $g * $factor, $b * $factor]);
    }

    /**
     * Accent lisible sur fond clair : on abaisse la luminosité sans toucher
     * à la teinte (le citron #cfff04 devient l'olive #536900 d'origine).
     */
    private static function darkenForLight(string $hex): string
    {
        // Palette d'origine : le citron retrouve exactement l'olive historique.
        if (strtolower($hex) === self::DEFAULTS['primary']) {
            return self::DEFAULTS['secondary'];
        }

        [$h, $s, $l] = self::toHsl($hex);

        return self::fromHsl($h, $s, min($l, 0.205));
    }

    private static function channel(int $value): float
    {
        $value /= 255;

        return $value <= 0.03928 ? $value / 12.92 : (($value + 0.055) / 1.055) ** 2.4;
    }

    /**
     * @return array{0: float, 1: float, 2: float}
     */
    private static function toHsl(string $hex): array
    {
        [$r, $g, $b] = self::rgb($hex);
        $r /= 255;
        $g /= 255;
        $b /= 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $lightness = ($max + $min) / 2;
        $delta = $max - $min;

        if ($delta === 0.0) {
            return [0.0, 0.0, $lightness];
        }

        $saturation = $lightness > 0.5
            ? $delta / (2 - $max - $min)
            : $delta / ($max + $min);

        if ($max === $r) {
            $hue = fmod(($g - $b) / $delta, 6);
        } elseif ($max === $g) {
            $hue = ($b - $r) / $delta + 2;
        } else {
            $hue = ($r - $g) / $delta + 4;
        }

        $hue *= 60;

        if ($hue < 0) {
            $hue += 360;
        }

        return [$hue, $saturation, $lightness];
    }

    private static function fromHsl(float $hue, float $saturation, float $lightness): string
    {
        $c = (1 - abs(2 * $lightness - 1)) * $saturation;
        $x = $c * (1 - abs(fmod($hue / 60, 2) - 1));
        $m = $lightness - $c / 2;

        if ($hue < 60) {
            [$r, $g, $b] = [$c, $x, 0];
        } elseif ($hue < 120) {
            [$r, $g, $b] = [$x, $c, 0];
        } elseif ($hue < 180) {
            [$r, $g, $b] = [0, $c, $x];
        } elseif ($hue < 240) {
            [$r, $g, $b] = [0, $x, $c];
        } elseif ($hue < 300) {
            [$r, $g, $b] = [$x, 0, $c];
        } else {
            [$r, $g, $b] = [$c, 0, $x];
        }

        return self::fromRgb([($r + $m) * 255, ($g + $m) * 255, ($b + $m) * 255]);
    }
}
