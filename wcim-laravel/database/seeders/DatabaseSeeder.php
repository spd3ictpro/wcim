<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (Product::count() > 0) {
            return;
        }

        $products = [
            ['category' => 'Cleansing', 'type' => 'HOCl wound wash', 'product' => 'Hydrocyn Aqua', 'brand' => 'Hydrocyn', 'size' => '500ml', 'price' => 165.73, 'unit' => '12/box', 'cost_per_unit' => 13.75, 'product_id' => 'Super Oxidised Hypochlorous Acid (HOCI) Solution 500ml Bottle', 'code' => 'N09.3604.01', 'requirement' => 0, 'stock' => 0],
            ['category' => 'Cleansing', 'type' => 'HOCl spray', 'product' => 'Hydrocyn Aqua Spray', 'brand' => 'Hydrocyn', 'size' => '100-150ml', 'price' => 206, 'unit' => '50/box', 'cost_per_unit' => 4.12, 'product_id' => 'Super Oxidised Hypochlorous Acid (HOCI) Solution 100ml - 150ml Spray Mist', 'code' => 'N09.3605.01', 'requirement' => 0, 'stock' => 0],
            ['category' => 'Cleansing', 'type' => 'Antiseptic', 'product' => 'Octenilin Solution', 'brand' => 'Octenilin', 'size' => '350ml', 'price' => 0, 'unit' => 'Unit', 'cost_per_unit' => 0, 'product_id' => 'Wound Irrigation Solution, Octenidine', 'code' => 'N1360670043.01', 'requirement' => 0, 'stock' => 0],
            ['category' => 'Cleansing', 'type' => 'Antiseptic', 'product' => 'Nano Silver Spray', 'brand' => 'Dr. Wound', 'size' => '50ml', 'price' => 45, 'unit' => '12/box', 'cost_per_unit' => 3.75, 'product_id' => 'Antiseptic Wound Spray, Colloidal Silver', 'code' => 'F1950010008.01', 'requirement' => 0, 'stock' => 0],
            ['category' => 'Foam', 'type' => 'Foam dressing', 'product' => 'Allevyn Gentle Border', 'brand' => 'Allevyn', 'size' => '12.5x12.5cm', 'price' => 152.77, 'unit' => '10/box', 'cost_per_unit' => 15.27, 'product_id' => 'Non Adhesive Conformable Foam Dressing (12.5cm x 12.5cm)', 'code' => 'N09.2600.01', 'requirement' => 0, 'stock' => 0],
            ['category' => 'Foam', 'type' => 'Foam dressing', 'product' => 'Allevyn Adhesive', 'brand' => 'Allevyn', 'size' => '17.5x17.5cm', 'price' => 292.77, 'unit' => '10/box', 'cost_per_unit' => 19.27, 'product_id' => 'Non Adhesive Conformable Foam Dressing (17.5cm x 17.5cm)', 'code' => 'N09.2600.02', 'requirement' => 0, 'stock' => 0],
            ['category' => 'Foam', 'type' => 'Foam dressing', 'product' => 'Aquacel Foam', 'brand' => 'Aquacel', 'size' => '15x15cm', 'price' => 193.85, 'unit' => '5/box', 'cost_per_unit' => 38.7, 'product_id' => 'Hydrofiber Foam Dressing, Non-Adhesive, Size: 15cm x 15cm', 'code' => 'N1360220010.01', 'requirement' => 0, 'stock' => 0],
            ['category' => 'Foam', 'type' => 'Sacral dressing', 'product' => 'Aquacel Foam Sacral', 'brand' => 'Aquacel', 'size' => '20x16.9cm', 'price' => 167.25, 'unit' => '5/box', 'cost_per_unit' => 33.45, 'product_id' => 'Hydrofiber Foam Dressing For Sacral, Adhesive Sz: 20cm x 16.9cm', 'code' => 'N1360220016.00', 'requirement' => 0, 'stock' => 0],
            ['category' => 'Hydrofiber', 'type' => 'Exudate control', 'product' => 'CMC', 'brand' => 'Almedico', 'size' => '10x10cm', 'price' => 122.7, 'unit' => '10/box', 'cost_per_unit' => 12.27, 'product_id' => 'Hydrofiber Dressing (10cm x 10cm)', 'code' => 'N09.1401.01', 'requirement' => 0, 'stock' => 0],
            ['category' => 'Hydrofiber Ag', 'type' => 'Antimicrobial', 'product' => 'CMC Silver', 'brand' => 'Almedico', 'size' => '15x15cm', 'price' => 152.77, 'unit' => '10/box', 'cost_per_unit' => 15.27, 'product_id' => 'Hydrofiber Impregnated With Silver (Ag) Dressing 15cm x 15cm', 'code' => 'N09.1400.02', 'requirement' => 0, 'stock' => 0],
            ['category' => 'Hydrofiber Ag', 'type' => 'Cavity wound', 'product' => 'Aquacel Ag+ Ribbon', 'brand' => 'Aquacel', 'size' => '2x45cm', 'price' => 91.6, 'unit' => '5/box', 'cost_per_unit' => 18.32, 'product_id' => 'Hydrofiber Wound Dressing - Ribbon, Sz 2cm x 45cm', 'code' => 'N1360220020.00', 'requirement' => 0, 'stock' => 0],
            ['category' => 'Hydrofiber Ag', 'type' => 'Antimicrobial', 'product' => 'Aquacel Ag+ Extra', 'brand' => 'Aquacel', 'size' => '10x10cm', 'price' => 167, 'unit' => '10/box', 'cost_per_unit' => 16.7, 'product_id' => 'Hydrofiber Impregnated With Silver (Ag) Dressing 10cm x 10cm', 'code' => 'N09.1400.01', 'requirement' => 0, 'stock' => 0],
            ['category' => 'Hydrofiber Ag', 'type' => 'Antimicrobial', 'product' => 'Aquacel Ag+ Extra', 'brand' => 'Aquacel', 'size' => '20x30cm', 'price' => 666.4, 'unit' => '5/box', 'cost_per_unit' => 133.28, 'product_id' => 'Hydrofiber Wound Dressing, Size: 20cm x 30cm', 'code' => 'N1360220034.01', 'requirement' => 0, 'stock' => 0],
            ['category' => 'Gauze', 'type' => 'Non-medicated', 'product' => 'Paraffin Gauze', 'brand' => 'Choice', 'size' => '10x10cm', 'price' => 10.47, 'unit' => '10/box', 'cost_per_unit' => 1.05, 'product_id' => 'Paraffin Gauze Dressing, Sterile BP 10cm x 10cm', 'code' => 'N09.1205.07', 'requirement' => 0, 'stock' => 0],
            ['category' => 'Gauze', 'type' => 'Bactigras', 'product' => 'Bactigras roll', 'brand' => 'Smith & Nephew', 'size' => '15cm x 1m', 'price' => 83.7, 'unit' => 'Unit', 'cost_per_unit' => 83.7, 'product_id' => 'Paraffin Gauze Dressing With Chlorhexidine B.P. 0.5%, Sterile 15cm x 1m', 'code' => 'N09.2403.01', 'requirement' => 0, 'stock' => 0],
            ['category' => 'Charcoal', 'type' => 'Odor / absorbent', 'product' => 'Winner Charcoal Dressing', 'brand' => 'Winner', 'size' => '10x10cm', 'price' => 131.6, 'unit' => 'Unit', 'cost_per_unit' => 0, 'product_id' => 'Super Absorbent, Activated Charcoal, Sz 10cm x 10cm', 'code' => 'N1360550137.01', 'requirement' => 0, 'stock' => 0],
            ['category' => 'Charcoal', 'type' => 'Odor control', 'product' => 'Zorflex Carbon Dressing', 'brand' => 'Zorflex', 'size' => '10x10cm', 'price' => 200, 'unit' => '10/box', 'cost_per_unit' => 20, 'product_id' => 'Cloth Dressing, Activated Carbon, Sz 10cm x 10cm', 'code' => 'N1360100064.01', 'requirement' => 0, 'stock' => 0],
            ['category' => 'Debridement', 'type' => 'Debridement', 'product' => 'Intrasite Gel', 'brand' => 'Smith & Nephew', 'size' => '15g', 'price' => 25, 'unit' => 'Unit', 'cost_per_unit' => 25, 'product_id' => 'Amorphous Hydrogel Dressing 15g/tube', 'code' => 'N1360010004', 'requirement' => 0, 'stock' => 0],
            ['category' => 'Debridement', 'type' => 'Debridement', 'product' => 'Cavidagel', 'brand' => 'Cavidagel', 'size' => '30g', 'price' => 50, 'unit' => 'Unit', 'cost_per_unit' => 50, 'product_id' => 'Amorphous Hydrogel Dressing 30g/tube', 'code' => 'N1360230008.01', 'requirement' => 0, 'stock' => 0],
            ['category' => 'Hydrogel', 'type' => 'Hydrogel', 'product' => 'Dermacyn Hydrogel', 'brand' => 'Dermacyn', 'size' => '50g', 'price' => 28, 'unit' => 'Unit', 'cost_per_unit' => 28, 'product_id' => 'Super Oxidised Wound Wash, Irrigation and Debridement Hydrogel', 'code' => 'F1950670012.01', 'requirement' => 0, 'stock' => 0],
            ['category' => 'Advanced Gel', 'type' => 'Enzyme alginogel', 'product' => 'Flaminal Hydro', 'brand' => 'Flaminal', 'size' => '50g', 'price' => 78, 'unit' => 'Unit', 'cost_per_unit' => 78, 'product_id' => 'Enzyme Alginogel (3.5%) 50g', 'code' => 'N1360160111.01', 'requirement' => 0, 'stock' => 0],
            ['category' => 'Advanced Gel', 'type' => 'High exudate enzyme', 'product' => 'Flaminal Forte', 'brand' => 'Flaminal', 'size' => '50g', 'price' => 78, 'unit' => 'Unit', 'cost_per_unit' => 78, 'product_id' => 'Enzyme Alginogel (5.5%) 50g', 'code' => 'N1360160109.01', 'requirement' => 0, 'stock' => 0],
            ['category' => 'Bioactive', 'type' => 'Healing gel', 'product' => 'Bioheal Chitosan', 'brand' => 'Dr Wound', 'size' => '60ml', 'price' => 68, 'unit' => '12/box', 'cost_per_unit' => 5.7, 'product_id' => 'Chitosan Gel 60ml', 'code' => 'N1360070048.00', 'requirement' => 0, 'stock' => 0],
            ['category' => 'Bioactive', 'type' => 'Healing gel', 'product' => 'Bioheal Chitosan', 'brand' => 'Dr Wound', 'size' => '20ml', 'price' => 30, 'unit' => '20/box', 'cost_per_unit' => 1.5, 'product_id' => 'Chitosan Gel 20ml', 'code' => 'N1360070048.01', 'requirement' => 0, 'stock' => 0],
            ['category' => 'Moisturiser', 'type' => 'Barrier cream', 'product' => 'Remoise Cream', 'brand' => 'Remoise', 'size' => '20ml', 'price' => 25, 'unit' => 'Unit', 'cost_per_unit' => 0, 'product_id' => 'Moisturising Cream, 20ml', 'code' => 'F1950370004.03', 'requirement' => 0, 'stock' => 0],
        ];

        foreach ($products as $data) {
            Product::create($data);
        }
    }
}
