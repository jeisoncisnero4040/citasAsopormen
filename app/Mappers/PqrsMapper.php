<?php

namespace App\Mappers;

use Carbon\Carbon;

class PqrsMapper
{
    public static function mapDateRange(array $data): array
    {
        if (isset($data['from'])) {
            $data['from'] = Carbon::parse($data['from'])->format('Y-m-d H:i:s');
        }
        if (isset($data['to'])) {
            $data['to'] = Carbon::parse($data['to'])->format('Y-m-d H:i:s');
        }

        return $data;
    }
}