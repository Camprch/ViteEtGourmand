<?php

// Fonctions principales :
// - fr_datetime(?string $s)         : Formate une date/heure au format français (d/m/Y H:i)
// - fr_date(?string $s)             : Formate une date au format français (d/m/Y)
// - fr_jour_depuis_date(string $d)  : Retourne le jour de la semaine (fr) à partir d'une date YYYY-MM-DD
// - hhmm_to_minutes(string $hhmm)   : Convertit HH:MM en minutes depuis minuit

declare(strict_types=1);

// Formate une date/heure au format français (d/m/Y H:i)
function fr_datetime(?string $s): string {
    if (!$s) return '';
    $ts = strtotime($s);
    return $ts ? date('d/m/Y H:i', $ts) : htmlspecialchars($s);
}

// Formate une date au format français (d/m/Y)
function fr_date(?string $s): string {
    if (!$s) return '';
    $ts = strtotime($s);
    return $ts ? date('d/m/Y', $ts) : htmlspecialchars($s);
}

// Retourne le jour de la semaine (fr) à partir d'une date YYYY-MM-DD
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

// Convertit HH:MM en minutes depuis minuit (ex: 13:30 => 810)
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

