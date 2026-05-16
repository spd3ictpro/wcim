<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ExcelDataSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['code' => 'N09.3604.01', 'req_raw' => '24 Unit', 'stock_raw' => '6 BOTTLE'],
            ['code' => 'N09.3605.01', 'req_raw' => '24 Unit', 'stock_raw' => '1 BOTTLE'],
            ['code' => 'N1360670043.01', 'req_raw' => '1 Unit', 'stock_raw' => '1 BOTTLE'],
            ['code' => 'F1950010008.01', 'req_raw' => '2', 'stock_raw' => '0'],
            ['code' => 'N09.2600.01', 'req_raw' => '2 Box', 'stock_raw' => '3 BOX'],
            ['code' => 'N09.2600.02', 'req_raw' => '2 Box', 'stock_raw' => '2 BOX'],
            ['code' => 'N1360220010.01', 'req_raw' => 'PRN', 'stock_raw' => '0'],
            ['code' => 'N1360220016.00', 'req_raw' => '1 Box', 'stock_raw' => '0'],
            ['code' => 'N09.1401.01', 'req_raw' => '2 Box', 'stock_raw' => '3'],
            ['code' => 'N09.1400.02', 'req_raw' => '1 Box', 'stock_raw' => '8'],
            ['code' => 'N1360220020.00', 'req_raw' => '1 Box', 'stock_raw' => '0'],
            ['code' => 'N09.1400.01', 'req_raw' => 'PRN', 'stock_raw' => '0'],
            ['code' => 'N1360220034.01', 'req_raw' => 'PRN', 'stock_raw' => '0'],
            ['code' => 'N09.1205.07', 'req_raw' => '10 Box', 'stock_raw' => '16 KOTAK'],
            ['code' => 'N09.2403.01', 'req_raw' => 'PRN', 'stock_raw' => '0'],
            ['code' => 'N1360550137.01', 'req_raw' => '1 Box', 'stock_raw' => '0'],
            ['code' => 'N1360100064.01', 'req_raw' => '1 Box', 'stock_raw' => '2 keping'],
            ['code' => 'N1360010004', 'req_raw' => '5 Unit', 'stock_raw' => '0'],
            ['code' => 'N1360230008.01', 'req_raw' => '1 Unit', 'stock_raw' => '0'],
            ['code' => 'F1950670012.01', 'req_raw' => '10 Unit', 'stock_raw' => '38'],
            ['code' => 'N1360160111.01', 'req_raw' => '2 Unit', 'stock_raw' => '0'],
            ['code' => 'N1360160109.01', 'req_raw' => '1 Unit', 'stock_raw' => '0'],
            ['code' => 'N1360070048.00', 'req_raw' => '10 Unit', 'stock_raw' => '3'],
            ['code' => 'N1360070048.01', 'req_raw' => 'PRN', 'stock_raw' => '0'],
            ['code' => 'F1950370004.03', 'req_raw' => '5 Unit', 'stock_raw' => '0'],
        ];

        foreach ($data as $row) {
            $product = Product::where('code', $row['code'])->first();
            if (!$product) continue;

            $perBox = $product->per_box;

            $req = $this->parseValue($row['req_raw'], $perBox);
            $stock = $this->parseValue($row['stock_raw'], $perBox);

            $product->requirement = $req;
            $product->stock = $stock;
            $product->save();

            echo "Updated {$product->product} ({$product->code}): req={$req}, stock={$stock}\n";
        }
    }

    private function parseValue(string $raw, ?int $perBox): int
    {
        $raw = trim($raw);

        if (stripos($raw, 'prn') !== false || $raw === '' || $raw === '-') {
            return 0;
        }

        preg_match('/(\d+)/', $raw, $matches);
        if (!$matches) return 0;

        $num = (int) $matches[1];
        $lower = strtolower($raw);

        if (str_contains($lower, 'box') || str_contains($lower, 'kotak') || str_contains($lower, 'bottle')) {
            if ($perBox) {
                return $num * $perBox;
            }
            return $num;
        }

        return $num;
    }
}
