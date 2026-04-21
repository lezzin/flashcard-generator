<?php

namespace App\Support;

class AnkiFieldNormalizer
{
    public static function normalizeExtra(array $fields): array
    {
        if (!array_key_exists('Extra', $fields)) {
            return $fields;
        }

        $extra = $fields['Extra'];

        if ($extra === "null" || $extra === '') {
            $extra = '';
        }

        $fields['Extra'] = $extra;

        return $fields;
    }

    public static function prepareForUpdate(array $fields): array
    {
        $fields = self::normalizeExtra($fields);
        $extra = $fields['Extra'] ?? null;

        $filtered = array_filter($fields, fn ($v) => $v !== null && $v !== '');

        if (array_key_exists('Extra', $fields)) {
            $filtered['Extra'] = $extra;
        }

        return $filtered;
    }
}
