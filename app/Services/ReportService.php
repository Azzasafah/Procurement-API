<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Top 5 department dengan permintaan terbanyak dalam 3 bulan terakhir.
     * MySQL: DATE_SUB(NOW(), INTERVAL 3 MONTH)
     */
    public function topDepartments(): array
    {
        return DB::select("
            SELECT
                d.id,
                d.name      AS nama_department,
                d.code      AS kode_department,
                COUNT(r.id) AS total_request
            FROM departments d
            JOIN users    u ON u.department_id = d.id
            JOIN requests r ON r.requester_id  = u.id
            WHERE r.created_at >= DATE_SUB(NOW(), INTERVAL 3 MONTH)
              AND r.deleted_at IS NULL
              AND u.deleted_at IS NULL
              AND d.deleted_at IS NULL
            GROUP BY d.id, d.name, d.code
            ORDER BY total_request DESC
            LIMIT 5
        ");
    }

    /**
     * Kategori barang paling banyak diminta per bulan.
     * MySQL: DATE_FORMAT(created_at, '%Y-%m')
     */
    public function categoryPerMonth(): array
    {
        return DB::select("
            SELECT
                DATE_FORMAT(r.created_at, '%Y-%m') AS bulan,
                ri.category                         AS kategori,
                SUM(ri.quantity)                    AS total_quantity,
                COUNT(ri.id)                        AS total_item
            FROM request_items ri
            JOIN requests r ON r.id = ri.request_id
            WHERE ri.deleted_at IS NULL
              AND r.deleted_at  IS NULL
            GROUP BY DATE_FORMAT(r.created_at, '%Y-%m'), ri.category
            ORDER BY bulan DESC, total_quantity DESC
        ");
    }

    /**
     * Rata-rata lead time dari SUBMITTED hingga COMPLETED (dalam hari).
     * MySQL: TIMESTAMPDIFF(DAY, waktu_awal, waktu_akhir)
     */
    public function averageLeadTime(): object|null
    {
        $result = DB::select("
            SELECT
                COUNT(*)                                                              AS total_selesai,
                ROUND(AVG(TIMESTAMPDIFF(DAY, st.submitted_at, ct.completed_at)), 2) AS rata_rata_hari,
                ROUND(MIN(TIMESTAMPDIFF(DAY, st.submitted_at, ct.completed_at)), 2) AS tercepat_hari,
                ROUND(MAX(TIMESTAMPDIFF(DAY, st.submitted_at, ct.completed_at)), 2) AS terlama_hari
            FROM (
                SELECT request_id, MIN(created_at) AS submitted_at
                FROM   status_histories
                WHERE  to_status = 'SUBMITTED'
                GROUP  BY request_id
            ) AS st
            JOIN (
                SELECT request_id, MIN(created_at) AS completed_at
                FROM   status_histories
                WHERE  to_status = 'COMPLETED'
                GROUP  BY request_id
            ) AS ct ON ct.request_id = st.request_id
        ");

        return $result[0] ?? null;
    }

    /**
     * Ringkasan dashboard.
     */
    public function summary(): array
    {
        return [
            'total_request'     => DB::table('requests')->whereNull('deleted_at')->count(),
            'per_status'        => DB::table('requests')
                ->whereNull('deleted_at')
                ->select('status', DB::raw('COUNT(*) as jumlah'))
                ->groupBy('status')
                ->get(),
            'menunggu_approval' => DB::table('requests')
                ->whereNull('deleted_at')
                ->where('status', 'SUBMITTED')
                ->count(),
            'vendor_aktif'      => DB::table('vendors')
                ->whereNull('deleted_at')
                ->where('is_active', true)
                ->count(),
        ];
    }
}
