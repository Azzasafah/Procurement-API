<?php

namespace App\Services;

use App\Models\Stock;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function getAll(array $filters): LengthAwarePaginator
    {
        $query = Stock::query();

        if (!empty($filters['item_name'])) {
            $query->where('item_name', 'like', '%' . $filters['item_name'] . '%');
        }

        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        return $query->paginate($filters['limit'] ?? 10);
    }

    public function create(array $data): Stock
    {
        return Stock::create([
            'item_name'     => $data['item_name'],
            'category'      => $data['category'],
            'quantity'      => $data['quantity'],
            'unit'          => $data['unit'],
            'location'      => $data['location'] ?? null,
            'minimum_stock' => $data['minimum_stock'] ?? 0,
        ]);
    }

    public function update(Stock $stock, array $data): Stock
    {
        $stock->update($data);
        return $stock->fresh();
    }

    /*
     * Cek ketersediaan stok dan kurangi jika tersedia.
     * Menggunakan pessimistic locking (SELECT FOR UPDATE) untuk mencegah race condition.
     * Dua warehouse staff yang cek stok bersamaan tidak akan double-ambil.
     */
    public function checkAndDeduct(string $itemName, int $requiredQty): array
    {
        return DB::transaction(function () use ($itemName, $requiredQty) {
            $stock = Stock::where('item_name', 'like', '%' . $itemName . '%')
                ->lockForUpdate() 
                ->first();

            if (!$stock) {
                return ['available' => false, 'quantity' => 0, 'item_name' => $itemName];
            }

            $available = $stock->isAvailable($requiredQty);

            if ($available) {
                $stock->decrement('quantity', $requiredQty);
            }

            return [
                'available'         => $available,
                'quantity_in_stock' => $stock->fresh()->quantity,
                'quantity_requested'=> $requiredQty,
                'item_name'         => $stock->item_name,
            ];
        });
    }
}
