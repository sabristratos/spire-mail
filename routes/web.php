<?php

use Illuminate\Support\Facades\Route;
use SpireMail\Http\Controllers\AssetController;
use SpireMail\Http\Controllers\PreviewController;
use SpireMail\Http\Controllers\TagController;
use SpireMail\Http\Controllers\TemplateController;

Route::get('/', [TemplateController::class, 'index'])->name('templates.index');
Route::redirect('/templates', '/')->name('templates.redirect');
Route::get('/templates/create', [TemplateController::class, 'create'])->name('templates.create');
Route::delete('/templates/bulk', [TemplateController::class, 'bulkDestroy'])->name('templates.bulk-destroy');
Route::post('/templates', [TemplateController::class, 'store'])->name('templates.store');
Route::get('/templates/{template}', [TemplateController::class, 'edit'])->name('templates.edit');
Route::put('/templates/{template}', [TemplateController::class, 'update'])->name('templates.update');
Route::delete('/templates/{template}', [TemplateController::class, 'destroy'])->name('templates.destroy');
Route::patch('/templates/{template}/toggle-status', [TemplateController::class, 'toggleStatus'])->name('templates.toggle-status');

Route::post('/templates/{template}/duplicate', [TemplateController::class, 'duplicate'])->name('templates.duplicate');
Route::post('/templates/{template}/preview', [PreviewController::class, 'show'])->name('templates.preview');
Route::post('/templates/{template}/send-test', [PreviewController::class, 'sendTest'])->name('templates.send-test');

Route::post('/assets/upload', [AssetController::class, 'store'])->name('assets.store');
Route::delete('/assets/{filename}', [AssetController::class, 'destroy'])->name('assets.destroy');

Route::get('/tags', [TagController::class, 'index'])->name('tags.index');
Route::get('/tags/formatters', [TagController::class, 'formatters'])->name('tags.formatters');
Route::get('/templates/{template}/tags', [TagController::class, 'show'])->name('templates.tags.show');
Route::put('/templates/{template}/tags', [TagController::class, 'update'])->name('templates.tags.update');
