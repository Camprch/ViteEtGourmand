<?php
declare(strict_types=1);

function fr_datetime(?string $s): string {
    if (!$s) return '';
    $ts = strtotime($s);
    return $ts ? date('d/m/Y H:i', $ts) : htmlspecialchars($s);
}

function fr_date(?string $s): string {
    if (!$s) return '';
    $ts = strtotime($s);
    return $ts ? date('d/m/Y', $ts) : htmlspecialchars($s);
}

function fr_jour_depuis_date(string $dateYmd): ?string {
    // Attend YYYY-MM-DD
    $dt = DateTime::createFromFormat('Y-m-d', $dateYmd);
    if (!$dt) {
        return null;
    }

    return match ((int)$dt->format('N')) {
        1 => 'Lundi',
        2 => 'Mardi',
        3 => 'Mercredi',
        4 => 'Jeudi',
        5 => 'Vendredi',
        6 => 'Samedi',
        7 => 'Dimanche',
        default => null,
    };
}

function hhmm_to_minutes(string $hhmm): ?int {
    $hhmm = substr(trim($hhmm), 0, 5); // sécurise HH:MM
    if (!preg_match('/^\d{2}:\d{2}$/', $hhmm)) {
        return null;
    }

    [$h, $m] = array_map('intval', explode(':', $hhmm));
    if ($h < 0 || $h > 23 || $m < 0 || $m > 59) {
        return null;
    }

    return $h * 60 + $m;
}

