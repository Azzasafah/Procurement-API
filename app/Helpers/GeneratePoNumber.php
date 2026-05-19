<?php

namespace App\Helpers;

use App\Models\ProcurementOrder;

class GeneratePoNumber
{
    public static function generate(): string
    {
        $prefix = 'PO-' . date('Ymd') . '-';
        $last   = ProcurementOrder::where('po_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($last) {
            $lastSeq = (int) substr($last->po_number, -4);
            return $prefix . str_pad($lastSeq + 1, 4, '0', STR_PAD_LEFT);
        }

        return $prefix . '0001';
    }
}
