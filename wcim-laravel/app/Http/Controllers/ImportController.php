<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ImportController extends Controller
{
    public function index()
    {
        return view('import');
    }

    public function store(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        if (!$handle) {
            return back()->with('error', 'Could not read the uploaded file.');
        }

        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            return back()->with('error', 'CSV file appears to be empty.');
        }

        $headers = array_map('trim', $headers);

        $requiredColumns = ['category', 'type', 'product', 'brand', 'size', 'unit', 'code'];
        $missing = array_diff($requiredColumns, array_map('strtolower', $headers));
        if (!empty($missing)) {
            fclose($handle);
            return back()->with('error', 'Missing required columns: ' . implode(', ', $missing)
                . '. Required: category, type, product, brand, size, unit, code.');
        }

        $headerMap = [];
        foreach ($headers as $i => $h) {
            $headerMap[strtolower($h)] = $i;
        }

        $allowedFields = ['category', 'type', 'product', 'brand', 'size', 'price', 'unit',
            'cost_per_unit', 'product_id', 'code', 'requirement', 'stock', 'expiry'];

        $imported = 0;
        $skipped = 0;
        $errors = [];

        $products = [];

        while (($row = fgetcsv($handle)) !== false) {
            $data = [];
            foreach ($allowedFields as $field) {
                if (isset($headerMap[$field]) && isset($row[$headerMap[$field]])) {
                    $value = trim($row[$headerMap[$field]]);
                    if ($value !== '') {
                        $data[$field] = $value;
                    }
                }
            }

            $missingRequired = array_diff($requiredColumns, array_keys($data));
            if (!empty($missingRequired)) {
                $skipped++;
                $errors[] = 'Row ' . ($imported + $skipped + 1) . ': missing ' . implode(', ', $missingRequired);
                continue;
            }

            $productData = [
                'category' => $data['category'],
                'type' => $data['type'],
                'product' => $data['product'],
                'brand' => $data['brand'],
                'size' => $data['size'],
                'unit' => $data['unit'],
                'code' => $data['code'],
                'price' => isset($data['price']) && is_numeric($data['price']) ? $data['price'] : 0,
                'cost_per_unit' => isset($data['cost_per_unit']) && is_numeric($data['cost_per_unit']) ? $data['cost_per_unit'] : 0,
                'product_id' => $data['product_id'] ?? null,
                'requirement' => isset($data['requirement']) && is_numeric($data['requirement']) ? (int) $data['requirement'] : 0,
                'stock' => isset($data['stock']) && is_numeric($data['stock']) ? (int) $data['stock'] : 0,
                'expiry' => $data['expiry'] ?? null,
                'input_mode' => 'unit',
            ];

            $products[] = $productData;
            $imported++;
        }

        fclose($handle);

        if (!empty($products)) {
            Product::insert($products);
        }

        $message = "Successfully imported {$imported} product(s).";
        if ($skipped > 0) {
            $message .= " {$skipped} row(s) skipped due to errors.";
        }

        if (!empty($errors)) {
            session()->flash('import_errors', array_slice($errors, 0, 20));
        }

        return redirect()->route('import.index')->with('success', $message);
    }

    public function sample()
    {
        $headers = ['category', 'type', 'product', 'brand', 'size', 'unit', 'code',
            'price', 'cost_per_unit', 'product_id', 'requirement', 'stock', 'expiry'];

        $rows = [
            ['Cleansing', 'HOCl wound wash', 'Hydrocyn Aqua', 'Hydrocyn', '500ml', '12/box', 'N09.3604.01', '165.73', '13.75', 'Super Oxidised Hypochlorous Acid (HOCI) Solution 500ml Bottle', '24', '6', ''],
            ['Cleansing', 'HOCl spray', 'Hydrocyn Aqua Spray', 'Hydrocyn', '100-150ml', '50/box', 'N09.3605.01', '206.00', '4.12', 'Super Oxidised Hypochlorous Acid (HOCI) Solution 100ml - 150ml Spray Mist', '24', '1', ''],
            ['Cleansing', 'Antiseptic', 'Octenilin Solution', 'Octenilin', '350ml', 'Unit', 'N1360670043.01', '0', '0', 'Wound Irrigation Solution, Octenidine', '1', '1', ''],
            ['Cleansing', 'Antiseptic', 'Nano Silver Spray', 'Dr. Wound', '50ml', '12/box', 'F1950010008.01', '45.00', '3.75', 'Antiseptic Wound Spray, Colloidal Silver', '2', '0', ''],
            ['Foam', 'Foam dressing', 'Allevyn Gentle Border', 'Allevyn', '12.5 x 12.5 cm', '10/box', 'N09.2600.01', '152.77', '15.27', 'Non Adhesive Conformable Foam Dressing (12.5cm x 12.5cm)', '2', '3', ''],
            ['Foam', 'Foam dressing', 'Allevyn Adhesive', 'Allevyn', '17.5 x 17.5 cm', '10/box', 'N09.2600.02', '292.77', '19.27', 'Non Adhesive Conformable Foam Dressing (17.5cm x 17.5cm)', '2', '2', ''],
            ['Foam', 'Foam dressing', 'Aquacel Foam', 'Aquacel', '15 x 15 cm', '5/box', 'N1360220010.01', '193.85', '38.70', 'Hydrofiber Foam Dressing, Non-Adhesive, Size: 15cm x 15cm', '0', '0', ''],
            ['Foam', 'Sacral dressing', 'Aquacel Foam Sacral', 'Aquacel', '20 x 16.9 cm', '5/box', 'N1360220016.00', '167.25', '33.45', 'Hydrofiber Foam Dressing For Sacral, Adhesive Sz: 20cm x 16.9cm', '1', '0', ''],
            ['Hydrofiber', 'Exudate control', 'CMC', 'Almedico', '10 x 10 cm', '10/box', 'N09.1401.01', '122.70', '12.27', 'Hydrofiber Dressing (10cm x 10cm)', '2', '3', ''],
            ['Hydrofiber Ag', 'Antimicrobial', 'CMC Silver', 'Almedico', '15 x 15 cm', '10/box', 'N09.1400.02', '152.77', '15.27', 'Hydrofiber Impregnated With Silver (Ag) Dressing 15cm x 15cm', '1', '8', ''],
            ['Hydrofiber Ag', 'Cavity wound', 'Aquacel Ag+ Ribbon', 'Aquacel', '2 x 45 cm', '5/box', 'N1360220020.00', '91.60', '18.32', 'Hydrofiber Wound Dressing - Ribbon, Sz 2cm x 45cm', '1', '0', ''],
            ['Hydrofiber Ag', 'Antimicrobial', 'Aquacel Ag+ Extra', 'Aquacel', '10 x 10 cm', '10/box', 'N09.1400.01', '167.00', '16.70', 'Hydrofiber Impregnated With Silver (Ag) Dressing 10cm x 10cm', '0', '0', ''],
            ['Hydrofiber Ag', 'Antimicrobial', 'Aquacel Ag+ Extra', 'Aquacel', '20 x 30 cm', '5/box', 'N1360220034.01', '666.40', '133.28', 'Hydrofiber Wound Dressing, Size: 20cm x 30cm', '0', '0', ''],
            ['Gauze', 'Non-medicated', 'Paraffin Gauze', 'Choice', '10 x 10 cm', '10/box', 'N09.1205.07', '10.47', '1.05', 'Paraffin Gauze Dressing, Sterile BP 10cm x 10cm', '10', '16', ''],
            ['Gauze', 'Bactigras Chlorhexidine Acetate Tulle Gras Dressing', 'Bactigras roll', 'Smith & Nephew', '15 cm x 1 m', 'Unit', 'N09.2403.01', '83.70', '83.70', 'Paraffin Gauze Dressing With Chlorhexidine B.P. 0.5%, Sterile 15cm x 1m', '0', '0', ''],
            ['Charcoal', 'Odor / absorbent', 'Winner Charcoal Dressing', 'Winner', '10 x 10 cm', 'Unit', 'N1360550137.01', '131.60', '0', 'Super Absorbent, Activated Charcoal, Sz 10cm x 10cm', '1', '0', ''],
            ['Charcoal', 'Odor control', 'Zorflex Carbon Dressing', 'Zorflex', '10 x 10 cm', '10/box', 'N1360100064.01', '200.00', '20.00', 'Cloth Dressing, Activated Carbon, Sz 10cm x 10cm', '1', '2', ''],
            ['Debridement', 'Debridement', 'Intrasite Gel', 'Smith & Nephew', '15g', 'Unit', 'N1360010004', '25.00', '25.00', 'Amorphous Hydrogel Dressing 15g/tube', '5', '0', ''],
            ['Debridement', 'Debridement', 'Cavidagel', 'Cavidagel', '30g', 'Unit', 'N1360230008.01', '50.00', '50.00', 'Amorphous Hydrogel Dressing 30g/tube', '1', '0', ''],
            ['Hydrogel', 'Hydrogel', 'Dermacyn Hydrogel', 'Dermacyn', '50 g', 'Unit', 'F1950670012.01', '28.00', '28.00', 'Super Oxidised Wound Wash, Irrigation and Debridement Hydrogel', '10', '38', ''],
            ['Advanced Gel', 'Moderate exudate', 'Flaminal Hydro', 'Flaminal', '50 g', 'Unit', 'N1360160111.01', '78.00', '78.00', 'Enzyme Alginogel (3.5%) 50g', '2', '0', ''],
            ['Advanced Gel', 'High exudate', 'Flaminal Forte', 'Flaminal', '50 g', 'Unit', 'N1360160109.01', '78.00', '78.00', 'Enzyme Alginogel (5.5%) 50g', '1', '0', ''],
            ['Bioactive', 'Healing gel', 'Bioheal Chitosan', 'Dr Wound', '60 ml', '12/Box', 'N1360070048.00', '68.00', '5.70', 'Chitosan Gel 60ml', '12', '3', ''],
            ['Bioactive', 'Healing gel', 'Bioheal Chitosan', 'Dr Wound', '20 ml', '20/Box', 'N1360070048.01', '30.00', '1.50', 'Chitosan Gel 20ml', '0', '0', ''],
            ['Moisturiser', 'Barrier cream', 'Remoise Cream', 'Remoise', '20 ml', 'Unit', 'F1950370004.03', '25.00', '25.00', 'Moisturising Cream, 20ml', '5', '0', ''],
        ];

        $output = fopen('php://temp', 'w');
        fputcsv($output, $headers);
        foreach ($rows as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="wcim-import-sample.csv"',
        ]);
    }
}
