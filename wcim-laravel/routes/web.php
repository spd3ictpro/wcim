<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\IndentController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ImportController;

Route::get('/', [InventoryController::class, 'index'])->name('dashboard');
Route::get('/products/{product}', [InventoryController::class, 'show'])->name('products.show');
Route::post('/products', [InventoryController::class, 'store'])->name('products.store');
Route::patch('/products/{product}', [InventoryController::class, 'update'])->name('products.update');
Route::post('/products/{product}/update-details', [InventoryController::class, 'updateDetails'])->name('products.update-details');
Route::patch('/products/{product}/input-mode', [InventoryController::class, 'updateInputMode'])->name('products.input-mode');
Route::post('/products/batch-input-mode', [InventoryController::class, 'batchUpdateInputMode'])->name('products.batch-input-mode');
Route::delete('/products/{product}', [InventoryController::class, 'destroy'])->name('products.destroy');
Route::get('/products/{product}/history', [InventoryController::class, 'history'])->name('products.history');
Route::get('/indent/preview', [InventoryController::class, 'previewIndent'])->name('indent.preview');
Route::get('/indent/pdf', [InventoryController::class, 'generatePdf'])->name('indent.pdf');
Route::post('/indent/save-and-download', [IndentController::class, 'saveAndDownload'])->name('indent.save-and-download');
Route::get('/indent/check-month', [IndentController::class, 'checkCurrentMonth'])->name('indent.check-month');

Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
Route::get('/analytics/consumption', [AnalyticsController::class, 'consumption'])->name('analytics.consumption');
Route::post('/analytics/apply-forecast/{product}', [AnalyticsController::class, 'applyForecast'])->name('analytics.apply-forecast');
Route::get('/analytics/{indent}', [AnalyticsController::class, 'show'])->name('analytics.show');
Route::patch('/analytics/{indent}/notes', [AnalyticsController::class, 'updateNotes'])->name('analytics.update-notes');
Route::get('/analytics/{indent}/pdf', [AnalyticsController::class, 'regeneratePdf'])->name('analytics.pdf');

Route::get('/stock/receive', [StockController::class, 'index'])->name('stock.receive');
Route::post('/stock/receive', [StockController::class, 'store'])->name('stock.receive.store');

Route::get('/import', [ImportController::class, 'index'])->name('import.index');
Route::post('/import', [ImportController::class, 'store'])->name('import.store');
Route::get('/import/sample', [ImportController::class, 'sample'])->name('import.sample');

Route::delete('/products/{product}/image', [InventoryController::class, 'deleteImage'])->name('products.image.destroy');

Route::get('/backup', [BackupController::class, 'index'])->name('backup.index');
Route::post('/backup/authenticate', [BackupController::class, 'authenticate'])->name('backup.authenticate');
Route::post('/backup/logout', [BackupController::class, 'logout'])->name('backup.logout');
Route::get('/backup/export', [BackupController::class, 'export'])->name('backup.export');
Route::post('/backup/import', [BackupController::class, 'import'])->name('backup.import');
Route::get('/backup/download/{filename}', [BackupController::class, 'download'])->name('backup.download');
Route::post('/backup/delete/{filename}', [BackupController::class, 'delete'])->name('backup.delete');
Route::post('/backup/password', [BackupController::class, 'updatePassword'])->name('backup.password');
