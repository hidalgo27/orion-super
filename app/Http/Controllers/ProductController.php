<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Product;
use App\Category;
use App\Order;
use App\ProductPhoto;
use App\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use App\Imports\ProductImport;

class ProductController extends Controller
{
    //
    public function getProduct()
    {
        $brands_id = Brand::get()->first()->id;
        $category_id = Category::get()->first()->id;
        return $this->burcar_producto($category_id,$brands_id);
    }

    public function getProduct_buscar(Request $request)
    {
        $brands_id = $request->input('brands_id');
        $category_id = $request->input('category_id');
        return $this->burcar_producto($category_id,$brands_id);
    }
    function burcar_producto($category_id,$brands_id){
        $brands = Brand::get();
        $categories = Category::get();
        $products = Product::where('category_id',$category_id)->where('brand_id',$brands_id)->get();
        $unidades=Unit::get();
        return view('admin.product.lista', compact('products', 'brands', 'categories','category_id','brands_id','unidades'));
    }
    public function nuevo()
    {
        $categories = Category::get();
        $brands = Brand::get();
        $unidades=Unit::get();
        return view('admin.product.nuevo', compact(['categories', 'brands','unidades']));
    }
    public function nuevo_excel()
    {
        return view('admin.product.nuevo-excel');
    }

    public function nuevo_excel_store(Request $request)
    {
    //    return Excel::download(new UsersExport,'user-excel.xlsx');
        $excel=$request->file('excel');
        Excel::import(new ProductImport,$excel);
        return back()->with('success','Importacion de productos completado');

    }
    public function store(Request $request)
    {
        $marca = $request->input('marca');
        $categoria = $request->input('categoria');
        $unidad = $request->input('unidad');
        $codigo = $request->input('codigo');
        $nombre = $request->input('nombre');
        $descripcion = $request->input('descripcion');
        $detalle = $request->input('detalle');
        $precio = $request->input('precio');
        $precio_online = $request->input('precio_online');
        $portada = $request->file('portada');
        // $miniatura=$request->file('miniatura');
        $fotos = $request->file('foto');
        $existencias_codigo = Product::where('code', $codigo)->count();
        $existencias_nombre = Product::where('name', $nombre)->count();
        // if(trim($distrito_id)==''||trim($distrito_id)=='0'){
        //     return redirect()->back()->with('error','escoja un departamento, provincia y distrito')->withInput();
        // }
        if (strlen(trim($descripcion)) == 0) {
            return redirect()->back()->with('error', 'El producto no tiene una descripcion')->withInput();
        }
        if (strlen(trim($detalle)) == 0) {
            return redirect()->back()->with('error', 'El producto no tiene un detalle')->withInput();
        }
        if ($existencias_codigo > 0) {
            return redirect()->back()->with('error', 'El producto con codigo ' . $codigo . ' ya existe')->withInput();
        }
        if ($existencias_nombre > 0) {
            return redirect()->back()->with('error', 'El producto con nombre ' . $nombre . ' ya existe')->withInput();
        } else {
            $product = new Product();
            $product->brand_id = $marca;
            $product->code = $codigo;
            $product->name = $nombre;
            $product->description = $descripcion;
            $product->detail = $detalle;
            $product->price = $precio;
            $product->price_promo = $precio_online;
            $product->state = 1;
            $product->unity_id = $unidad;
            $product->category_id = $categoria;
            $product->save();
            if (!empty($portada)) {
                // foreach($fotos as $foto){
                $productfoto = new ProductPhoto();
                $productfoto->product_id = $product->id;
                $productfoto->photo = '';
                $productfoto->state = '1';
                $productfoto->save();

                $filename = 'foto-' . $productfoto->id . '.' . $portada->getClientOriginalExtension();
                $productfoto->photo = $filename;
                $productfoto->save();
                Storage::disk('product')->put($filename,  File::get($portada));
                // }
            }
            // if(!empty($miniatura)){
            //     // foreach($fotos as $foto){
            //         $comunidadfoto = new ComunidadFoto();
            //         $comunidadfoto->comunidad_id=$comunidad->id;
            //         $comunidadfoto->save();

            //         $filename ='foto-'.$comunidadfoto->id.'.'.$miniatura->getClientOriginalExtension();
            //         $comunidadfoto->imagen=$filename;
            //         $comunidadfoto->estado='2';
            //         $comunidadfoto->save();
            //         Storage::disk('comunidades')->put($filename,  File::get($miniatura));
            //     // }
            // }
            if (!empty($fotos)) {
                foreach ($fotos as $foto) {
                    $productfoto = new ProductPhoto();
                    $productfoto->product_id = $product->id;
                    $productfoto->photo = '';
                    $productfoto->state = '0';
                    $productfoto->save();

                    $filename = 'foto-' . $productfoto->id . '.' . $foto->getClientOriginalExtension();
                    $productfoto->photo = $filename;
                    $productfoto->save();
                    Storage::disk('product')->put($filename,  File::get($foto));
                }
            }
            // Alert()->success('Datos guardados.')->autoclose(3000);
            return redirect()->route('product_nuevo_path')->with('success', 'Datos guardados');
        }
    }
    public function mostrarProvincias(Request $request)
    {

        $categoria_id = $request->categoria_id;
        $producto_id = $request->producto_id;
        if ($request->ajax()) {
            $provincias = Provincia::where('departamento_id', $request->departamento_id)->get();
            $data = view('admin.comunidad.mostrar-provincias-ajax', compact('provincias', 'categoria_id', 'producto_id'))->render();
            return \Response::json(['options' => $data]);
        }
    }
    public function mostrarDistritos(Request $request)
    {
        if ($request->ajax()) {
            $distritos = Distrito::where('provincia_id', $request->provincia_id)->get();
            $data = view('admin.comunidad.mostrar-distritos-ajax', compact('distritos'))->render();
            return \Response::json(['options' => $data]);
        }
    }
    public function editar(Request $request)
    {
        $id = $request->input('id');
        $marca = $request->input('marca');
        $categoria = $request->input('categoria');
        $unidad = $request->input('unidad');
        $codigo = $request->input('codigo');
        $nombre = $request->input('nombre');
        $descripcion = $request->input('descripcion');
        $detalle = $request->input('detalle');
        $precio = $request->input('precio');
        $precio_online = $request->input('precio_online');
        $portada_f = $request->file('portada_f');
        $portada = $request->input('portada');
        // $miniatura=$request->input('miniatura');
        // $miniatura_f=$request->file('miniatura_f');
        $fotos = $request->file('foto');
        $fotosExistentes = $request->input('fotos_');
        // dd($fotosExistentes);
        // if(trim($distrito_id)==''||trim($distrito_id)=='0'){
        //     return redirect()->back()->with('error','escoja un departamento, provincia y distrito')->withInput();
        // }

        if (strlen(trim($descripcion)) > 0) {
            $descripcion='.';
        }
        if (strlen(trim($detalle)) > 0) {
           $detalle ='.';
        }

        $product = Product::find($id);
        $product->brand_id = $marca;
        $product->unity_id = $unidad;
        $product->code = $codigo;
        $product->name = $nombre;
        $product->description = $descripcion;
        $product->detail = $detalle;
        $product->price = $precio;
        $product->price_promo = $precio_online;
        $product->category_id = $categoria;
        $product->save();


        // borramos de la db la foto de portada que han sido eliminadas por el usuario
        if (isset($portada)) {
            $fotos_existentes = ProductPhoto::where('product_id', $product->id)->where('state', '1')->get();
            foreach ($fotos_existentes as $value) {
                # code...
                if ($value->id != $portada) {
                    ProductPhoto::find($value->id)->delete();
                }
            }
        } else {
            ProductPhoto::where('product_id', $product->id)->where('state', '1')->delete();
        }

        if (!empty($portada_f)) {
            ProductPhoto::where('product_id', $product->id)->where('state', '1')->delete();
            // foreach($portada_f as $foto){
            $comunidadfoto = new ProductPhoto();
            $comunidadfoto->product_id = $product->id;
            $comunidadfoto->photo = '';
            $comunidadfoto->state = '1';
            $comunidadfoto->save();

            $filename = 'foto-' . $comunidadfoto->id . '.' . $portada_f->getClientOriginalExtension();
            $comunidadfoto->photo = $filename;
            $comunidadfoto->save();
            Storage::disk('product')->put($filename,  File::get($portada_f));
            // }
        }
        // borramos de la db la foto de portada que han sido eliminadas por el usuario
        // if(isset($miniatura)){
        //     $fotos_existentes=ComunidadFoto::where('comunidad_id',$comunidad->id)->where('estado','2')->get();
        //     foreach ($fotos_existentes as $value) {
        //         # code...
        //         if($value->id!=$miniatura){
        //             ComunidadFoto::find($value->id)->delete();
        //         }
        //     }
        // }
        // else{
        //     ComunidadFoto::where('comunidad_id',$comunidad->id)->where('estado','2')->delete();
        // }

        // if(!empty($miniatura_f)){
        //     ComunidadFoto::where('comunidad_id',$comunidad->id)->where('estado','2')->delete();
        //     // foreach($miniatura_f as $foto){
        //         $comunidadfoto = new ComunidadFoto();
        //         $comunidadfoto->comunidad_id=$comunidad->id;
        //         $comunidadfoto->save();

        //         $filename ='foto-'.$comunidadfoto->id.'.'.$miniatura_f->getClientOriginalExtension();
        //         $comunidadfoto->imagen=$filename;
        //         $comunidadfoto->estado='2';
        //         $comunidadfoto->save();
        //         Storage::disk('comunidades')->put($filename,  File::get($miniatura_f));
        //     // }
        // }
        // borramos de la db las fotos que han sido eliminadas por el usuario
        // if(count((array)$fotosExistentes)>0){
        //     $fotos_existentes=ComunidadFoto::where('comunidad_id',$comunidad->id)->where('estado','0')->get();
        //     foreach ($fotos_existentes as $value) {
        //         # code...
        //         if(!in_array($value->id,$fotosExistentes)){
        //             ComunidadFoto::find($value->id)->delete();
        //         }
        //     }
        // }
        // else{
        //     ComunidadFoto::where('comunidad_id',$comunidad->id)->where('estado','0')->delete();
        //  }
        if (!empty($fotos)) {
            foreach ($fotos as $foto) {
                $comunidadfoto = new ProductPhoto();
                $comunidadfoto->product_id = $product->id;
                $comunidadfoto->photo = '';
                $comunidadfoto->state = '0';
                $comunidadfoto->save();

                $filename = 'foto-' . $comunidadfoto->id . '.' . $foto->getClientOriginalExtension();
                $comunidadfoto->photo = $filename;
                $comunidadfoto->save();
                Storage::disk('product')->put($filename,  File::get($foto));
            }
        }
         return redirect()->route('product_lista_path')->with('success', 'Datos editados');
    }
    public function getFoto($filename)
    {
        $file = Storage::disk('product')->get($filename);
        return response($file, 200);
    }
    public function getDelete($id)
    {
        if (Product::destroy($id))
            return 1;
        else
            return 1;
    }
    public function mostrar_pagina($grupo_id, $estado)
    {
        // try {
        //code...

        $temp = Product::find($grupo_id);
        $temp->state = $estado;
        $temp->save();

        if ($estado == 0) {
            $estado_rpt = 0;
            $clase_span = 'badge-success';
            $estado_span = 'Confirmado';
            $clase_confirmar = 'btn-danger';
            $estado_confirmar = '<i class="fas fa-eye-slash"></i>';
        } elseif ($estado == 1) {
            $estado_rpt = 1;
            $clase_span = 'badge-dark';
            $estado_span = 'Pendiente';
            $clase_confirmar = 'btn-success';
            $estado_confirmar = '<i class="fas fa-eye"></i>';
        }

        return response()->json([
            'rpt' => '1',
            'estado' => $estado,
            'clase_span' => $clase_span,
            'estado_span' => $estado_span,
            'clase_confirmar' => $clase_confirmar,
            'estado_confirmar' => $estado_confirmar
        ]);
    }

    public function lista_report($f1,$f2){
        return $this->listar_productos_vendidos($f1,$f2);
    }
    public function lista_report_post(Request $request)
    {
        $f1 = $request->input('desde');
        $f2 = $request->input('hasta');
        return $this->listar_productos_vendidos($f1,$f2);

    }
    function listar_productos_vendidos($f1,$f2){

        // $orders=Order::with(['order_products'],function($query){
        //     $query->select('product_id',DB::raw('COUNT(product_id) as total_ventas'));
        // })
        // ->whereBetween('processed_date',[$f1,$f2])->where('state','3')
        // ->get();
        $orders=Order::whereBetween('processed_date',[$f1.' 00:00:00',$f2.' 23:59:59'])->where('state','3')->get();
        $array=[];
        foreach($orders as $order){
            foreach($order->order_products as $order_product){
                if(!array_key_exists ($order_product->product_id,$array)){
                    $array[$order_product->product_id]=array('producto'=>Product::find($order_product->product_id)->name,
                                                            'cantidad'=>$order_product->quality,
                                                            'codigo'=>Product::find($order_product->product_id)->code);
                }
                else{
                    $array[$order_product->product_id]['cantidad']=+$order_product->quality;

                }
            }
        }
        // Obtener una lista de columnas
        $cantidad=[];
        foreach ($array as $clave => $fila) {
        $cantidad[$clave] = $fila['cantidad'];
        }

        // Ordenar los datos con volumen descendiente, edición ascendiente
        // Agregar $datos como el último parámetro, para ordenar por la clave común
         array_multisort($cantidad, SORT_DESC, $array);


        // $sorted = array_orderby($array, 'cantidad', SORT_DESC);
        //  dd($array);
        return view('admin.product.lista-report',compact('array','f1','f2'));
    }

}
