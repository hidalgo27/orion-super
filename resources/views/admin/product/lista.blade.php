@extends('layouts.app-admin')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="#">BASE DE DATOS</a></li>
<li class="breadcrumb-item active" aria-current="page">PRODUCTOS</li>
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-8">
                                <b class="text-danger text-15">LISTA DE PRODUCTOS</b>
                            </div>
                            <div class="col-2 text-right">
                                <a href="{{ route('product_nuevo_excel_path') }}" class="btn btn-success text-white"><i class="fas fa-file-excel"></i> IMPORTAR PRODUCTOS</a>
                            </div>
                            <div class="col-2 text-right">
                                <a href="{{ route('product_nuevo_path') }}" class="btn btn-info text-white"><i class="fas fa-plus-circle"></i> AGREGAR PRODUCTO</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mt-2">
                        <div class="row">
                            <div class="form-group col-3">
                                <label for="categoria">Categoria</label>
                                <select class="form-control" name="category_id" id="categoria" form="form_buscar_producto">
                                    <option value="0">Ingrese una categoria</option>
                                    @foreach ($categories as $categoria)
                                        <option value="{{ $categoria->id }}" @if($category_id==$categoria->id) selected @endif>{{ $categoria->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-3">
                                <label for="marca">Marca</label>
                                <select class="form-control" name="brands_id" id="marca" form="form_buscar_producto">
                                    <option value="0">Ingrese una marca</option>
                                    @foreach ($brands as $marca)
                                        <option value="{{ $marca->id }}" @if($brands_id==$marca->id) selected @endif>{{ $marca->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-3 mt-4">
                                <form id="form_buscar_producto" action="{{ route('product_buscar_path') }}" method="post">
                                    @csrf
                                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Buscar</button>
                                </form>
                            </div>
                        </div>
                        <table class="table table-bordered table-hover table-striped table-sm text-12">
                            <thead >
                                <tr>
                                    <th>#</th>
                                    <th>CATEGORIA</th>
                                    <th>MARCA</th>
                                    <th>CODIGO</th>
                                    <th>NOMBRE</th>
                                    <th>PRECIO REGULAR</th>
                                    <th>PRECIO ONLINE</th>
                                    <th>MOSTRAR EN PAGINA</th>
                                    <th>OPERACIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i=1;
                                @endphp
                                @foreach ($products as $item)
                                    <tr id="row_lista_productos_{{ $item->id }}">
                                        <td>{{ $i }}</td>
                                        <td>{{ $item->category->name }}</td>
                                        <td>{{ $item->brand->name }}</td>
                                        <td>{{ $item->code }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td class="text-right"><sup>S/.</sup>{{ $item->price }}</td>
                                        <td class="text-right"><sup>S/.</sup>{{ $item->price_promo }}</td>
                                        <td>
                                            <input type="hidden" id="mostrar_en_pagina_{{ $item->id }}" value="{{ $item->mostrar_en_pagina }}">
                                            @if ($item->state==1)
                                                <button class="btn btn-success btn-sm" id="confirmar_{{ $item->id }}" onclick="mostrar_pagina('{{ $item->id }}',$('#mostrar_en_pagina_{{ $item->id }}').val())"><i class="fas fa-eye"></i></button>
                                            @elseif($item->state==0)
                                                <button class="btn btn-danger btn-sm" id="confirmar_{{ $item->id }}" onclick="mostrar_pagina('{{ $item->id }}',$('#mostrar_en_pagina_{{ $item->id }}').val())"><i class="fas fa-eye-slash"></i></button>
                                            @endif
                                        </td>
                                        <td>
                                            <!-- Button trigger modal -->
                                            <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#comunidadModal_{{ $item->id }}">
                                                    <i class="fas fa-edit"></i>
                                            </a>

                                            <!-- Modal -->
                                            <div class="modal fade" id="comunidadModal_{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <form action="{{ route('product_editar_path') }}" method="POST" method="POST" enctype="multipart/form-data">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLabel">Editar datos</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="form-group col-6">
                                                                    <label for="categoria">Categoria</label>
                                                                    <select class="form-control" id="categoria" name="categoria">
                                                                        @foreach ($categories->sortBy('name')->sortBy('name') as $item_)
                                                                            <option value="{{ $item_->id }}" @if($item->category_id== $item_->id ){{ 'selected' }}@endif >{{ $item_->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="form-group col-6">
                                                                    <label for="marca">Marca</label>
                                                                    <select class="form-control" id="marca" name="marca">
                                                                        @foreach ($brands->sortBy('name')->sortBy('name') as $item_)
                                                                            <option value="{{ $item_->id }}" @if($item->brand_id== $item_->id){{ 'selected' }}@endif >{{ $item_->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="form-group col-3">
                                                                    <label for="unidad">Unidad</label>
                                                                    <select class="form-control" id="unidad" name="unidad">
                                                                        @foreach ($unidades->sortBy('name')->sortBy('name') as $item_)
                                                                            <option value="{{ $item_->id }}" @if($item->unity_id== $item_->id){{ 'selected' }}@endif >{{ $item_->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="form-group col-3">
                                                                    <label for="codigo">Codigo</label>
                                                                    <input type="text" class="form-control" id="codigo" name="codigo" value="{{ $item->code }}" aria-describedby="codigo" placeholder="Codigo" required>
                                                                </div>
                                                                <div class="form-group col-6">
                                                                    <label for="nombre">Nombre</label>
                                                                    <input type="text" class="form-control" id="nombre" name="nombre" aria-describedby="nombre" placeholder="Nombre" value="{{ $item->name }}">
                                                                </div>
                                                                <div class="form-group col-12">
                                                                    <label for="descripcion">Descripcion</label>
                                                                    <textarea class="form-control descripcion" name="descripcion" id="descripcion" cols="30" rows="10">{{ $item->description }}</textarea>
                                                                </div>
                                                                <div class="form-group col-12">
                                                                    <label for="detalle">Detalle</label>
                                                                    <textarea class="form-control descripcion" name="detalle" id="detalle" cols="30" rows="10">{{ $item->detail }}</textarea>
                                                                </div>
                                                                <div class="form-group col-6">
                                                                    <label for="precio">Precio regular</label>
                                                                    <input type="number" step="0.01" min="0" class="form-control" id="precio" name="precio" value="{{ $item->price }}" aria-describedby="precio" required>
                                                                </div>
                                                                <div class="form-group col-6">
                                                                    <label for="precio_online">Precio online</label>
                                                                    <input type="number" step="0.01" min="0" class="form-control" id="precio_online" name="precio_online" value="{{ $item->price_promo }}" aria-describedby="precio online" required>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-6">
                                                                        <div class="form-group col-12 text-left">
                                                                            <p><b>Portada</b></p>
                                                                            @foreach ($item->photos->where('state','1') as $foto)
                                                                                @if (Storage::disk('product')->has($foto->photo))
                                                                                    <figure class="figure m-3" id="{{ $item->id.'_'.$foto->id }}">
                                                                                        <img src="{{ route('product_editar_imagen_path',$foto->photo) }}" class="figure-img rounded" alt="{{ $foto->photo}}" width="150px" height="150px">
                                                                                        <figcaption class="figure-caption text-right mt-0">
                                                                                            <a href="#!" class="btn btn-danger btn btn-block" onclick="borrar_foto_cliente('{{ $item->id.'_'.$foto->id }}')">
                                                                                                <i class="fas fa-trash-alt"></i>
                                                                                            </a>
                                                                                        </figcaption>
                                                                                        <input type="hidden" name="portada" value="{{ $foto->id }}">
                                                                                    </figure>
                                                                                @endif
                                                                            @endforeach
                                                                        </div>
                                                                        <div class="form-group col-12">
                                                                            <label for="foto">Foto <span class="text-primary">(150x150px)</span></label>
                                                                            <input type="file" name="portada_f" class="form-control">
                                                                        </div>
                                                                    </div>
                                                                    {{-- <div class="col-6">
                                                                        <div class="form-group col-12 text-left">
                                                                            <p><b>Miniatura</b></p>
                                                                            @foreach ($item->fotos->where('estado','2') as $foto)
                                                                                @if (Storage::disk('comunidades')->has($foto->imagen))
                                                                                    <figure class="figure m-3" id="{{ $item->id.'_'.$foto->id }}">
                                                                                        <img src="{{ route('comunidad_editar_imagen_path',$foto->imagen) }}" class="figure-img rounded" alt="A generic" width="200px" height="200px">
                                                                                        <figcaption class="figure-caption text-right mt-0">
                                                                                            <a href="#!" class="btn btn-danger btn btn-block" onclick="borrar_foto_cliente('{{ $item->id.'_'.$foto->id }}')">
                                                                                                <i class="fas fa-trash-alt"></i>
                                                                                            </a>
                                                                                        </figcaption>
                                                                                        <input type="hidden" name="miniatura" value="{{ $foto->id }}">
                                                                                    </figure>
                                                                                @endif
                                                                            @endforeach
                                                                        </div>
                                                                        <div class="form-group col-12">
                                                                            <label for="foto">Foto</label>
                                                                            <input type="file" name="miniatura_f" class="form-control">
                                                                        </div>
                                                                    </div> --}}
                                                                    <div class="col-12">
                                                                        <div class="form-group col-12 text-left">
                                                                            <p><b>Galeria de fotos</b></p>
                                                                            @foreach ($item->photos->where('state','0') as $foto)
                                                                                @if (Storage::disk('product')->has($foto->photo))
                                                                                    <figure class="figure m-3" id="{{ $item->id.'_'.$foto->id }}">
                                                                                        <img src="{{ route('product_editar_imagen_path',$foto->photo) }}" class="figure-img rounded" alt="{{ $foto->photo }}" width="300px" height="300px">
                                                                                        <figcaption class="figure-caption text-right mt-0">
                                                                                            <a href="#!" class="btn btn-danger btn btn-block" onclick="borrar_foto_cliente('{{ $item->id.'_'.$foto->id }}')">
                                                                                                <i class="fas fa-trash-alt"></i>
                                                                                            </a>
                                                                                        </figcaption>
                                                                                        <input type="hidden" name="fotos_[]" value="{{ $foto->id }}">
                                                                                    </figure>
                                                                                @endif
                                                                            @endforeach
                                                                        </div>
                                                                        <div class="form-group col-12">
                                                                            <label for="foto">Foto <span class="text-primary">(300x300px)</span></label>
                                                                            <input type="file" name="foto[]" multiple class="form-control">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                        <div class="modal-footer text-right">
                                                            {{ csrf_field() }}
                                                            <input type="hidden" name="id" value="{{ $item->id }}">
                                                            <button type="submit" class="btn btn-primary">Guardar cambios</button>
                                                            <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Cerrar</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            </div>
                                            <a href="#" class="btn btn-danger" onclick="eliminar_producto_('{{ $item->id }}')"><i class="fas fa-trash-alt"></i></a>
                                        </td>
                                    </tr>
                                    @php
                                        $i++;
                                    @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
