<?php

namespace App\Helpers;

use App\Models\ProcurementRequest;

class GenerateReqNumber
{
    public static function generate(): string
    {
        $prefix = 'REQ-' . date('Ymd') . '-';
        $lastRequest = ProcurementRequest::where('request_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastRequest) {
            $lastSeq = (int) substr($lastRequest->request_number, -4);
            return $prefix . str_pad($lastSeq + 1, 4, '0', STR_PAD_LEFT);
        }

        return $prefix . '0001';
    }
}
