<?php

namespace App\Exceptions;

use Exception;

class BussinessException extends Exception
{
    /**
     * Dilempar ketika ada pelanggaran aturan bisnis.
     * Contoh: approve request yang sudah APPROVED, delete user sendiri, dsb.
     */
    public function __construct(string $message, int $code = 422)
    {
        parent::__construct($message, $code);
    }
}
