<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Sushi\Sushi;

class Product extends Model
{
    use Sushi;
 
    /**
     * Model Rows
     *
     * @return void
     */
    public function getRows()
    {
        //API
        $products = Http::get('https://dummyjson.com/products')->json();
 
        //filtering some attributes dan memastikan semua field ada
        $products = Arr::map($products['products'], function ($item) {
            // Ekstrak field yang dibutuhkan
            $extractedItem = Arr::only($item,
                [
                    'id',
                    'title',
                    'description',
                    'price',
                    'rating',
                    'brand',
                    'category',
                    'thumbnail',
                ]
            );
            
            // Pastikan semua field memiliki nilai, tambahkan default jika tidak ada
            return [
                'id' => $extractedItem['id'] ?? null,
                'title' => $extractedItem['title'] ?? '',
                'description' => $extractedItem['description'] ?? '',
                'price' => $extractedItem['price'] ?? 0,
                'rating' => $extractedItem['rating'] ?? 0,
                'brand' => $extractedItem['brand'] ?? 'Generic', // Nilai default untuk brand
                'category' => $extractedItem['category'] ?? 'Uncategorized',
                'thumbnail' => $extractedItem['thumbnail'] ?? '',
            ];
        });
 
        return $products;
    }
}