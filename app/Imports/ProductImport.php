<?php

namespace App\Imports;

use App\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Product([
            //
            'brand_id'=>$row['marca'],
            'code'=>$row['codigo'],
            'name'=>$row['nombre'],
            'description'=>$row['descripcion'],
            'detail'=>$row['detalle'],
            'price'=>$row['precio'],
            'price'=>$row['precio_web'],
            'price_promo'=>$row['precio_web'],
            'state'=>1,
            'unity_id'=>$row['unidad'],
            'category_id'=>$row['categoria'],
        ]);
    }
}
