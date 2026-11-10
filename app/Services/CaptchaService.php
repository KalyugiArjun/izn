<?php

namespace App\Services;

class CaptchaService
{
    private const CHARS = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
    private const LENGTH = 5;

    /**
     * Generate a new captcha code, store it in the session, and return the SVG markup.
     */
    public static function generate(): string
    {
        $code = '';
        $max = strlen(self::CHARS) - 1;
        for ($i = 0; $i < self::LENGTH; $i++) {
            $code .= self::CHARS[random_int(0, $max)];
        }

        // Store lowercase in session for case-insensitive verification
        session(['login_captcha' => strtolower($code)]);

        $svg = self::buildSvg($code);
        session(['login_captcha_svg' => $svg]);

        return $svg;
    }

    /**
     * Render the current session's captcha SVG or generate a new one if not present.
     */
    public static function renderSvg(): string
    {
        if (session()->has('login_captcha') && session()->has('login_captcha_svg')) {
            return session('login_captcha_svg');
        }

        return self::generate();
    }

    /**
     * Validate the provided captcha string against the session value.
     */
    public static function validate(?string $input): bool
    {
        if (empty($input)) {
            return false;
        }

        $sessionCode = session('login_captcha');
        if (empty($sessionCode)) {
            return false;
        }

        $isValid = hash_equals(strtolower($sessionCode), strtolower(trim($input)));

        // Once validated, reset captcha to prevent replay
        if ($isValid) {
            session()->forget(['login_captcha', 'login_captcha_svg']);
        }

        return $isValid;
    }

    /**
     * Build SVG string for the given captcha code.
     * Compact: 120px x 38px
     */
    private static function buildSvg(string $code): string
    {
        $width = 120;
        $height = 38;
        $letters = str_split($code);

        // Noise lines
        $noise = '';
        for ($i = 0; $i < 3; $i++) {
            $x1 = random_int(0, $width);
            $y1 = random_int(0, $height);
            $x2 = random_int(0, $width);
            $y2 = random_int(0, $height);
            $stroke = '#cbd5e1';
            $noise .= "<line x1='{$x1}' y1='{$y1}' x2='{$x2}' y2='{$y2}' stroke='{$stroke}' stroke-width='1.5' stroke-dasharray='4,4'/>";
        }

        // Letters
        $colors = ['#0f172a', '#1e40af', '#0f766e', '#6d28d9', '#c2410c', '#0369a1'];
        $textElements = '';
        $xOffset = 10;

        foreach ($letters as $char) {
            $rot = random_int(-12, 12);
            $color = $colors[array_rand($colors)];
            $y = random_int(24, 27);
            $textElements .= "<text x='{$xOffset}' y='{$y}' font-family='Arial, Helvetica, sans-serif' font-size='19' font-weight='800' fill='{$color}' transform='rotate({$rot}, {$xOffset}, {$y})'>{$char}</text>";
            $xOffset += 21;
        }

        return "<svg xmlns='http://www.w3.org/2000/svg' width='{$width}' height='{$height}' viewBox='0 0 {$width} {$height}' style='background: #f8fafc; border-radius: 8px; border: 1.5px solid #e3e9f5; user-select: none; display: block;'>
            {$noise}
            {$textElements}
        </svg>";
    }
}
