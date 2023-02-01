@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card mt-5">
            <div class="card-header">
                <h3>List Product</h3>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                <p>
                    <a class="btn btn-primary" href="{{ route('products.create') }}">New Product</a>
                </p>
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>SKU</th>
                            <th>Name</th>
                            <th>Brand</th>
                            <th>Categories</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                <td>{{ $product->id }}</td>
                                <td><img src="{{ asset($product->image) }}" class="img-thumbnail" style="width:200px" /></td>
                                <td>{{ $product->sku }}</td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->brand != null ? $product->brand->name : '' }}</td>
                                <td>{{ implode(', ', $product->categories->pluck('name')->toArray()) }}</td>
                                <td>{{ $product->price }}</td>
                                <td>{{ $product->stock }}</td>
                                <td>
                                    <a href="{{ route('products.edit', ['id' => $product->id]) }}"
                                        class="btn btn-secondary btn-sm">edit</a>
                                    <a href="#" class="btn btn-sm btn-danger"
                                        onclick="
                            event.preventDefault();
                            if (confirm('Do you want to remove this?')) {
                              document.getElementById('delete-row-{{ $product->id }}').submit();
                            }">
                                        delete
                                    </a>
                                    <form id="delete-row-{{ $product->id }}"
                                        action="{{ route('products.destroy', ['id' => $product->id]) }}" method="POST">
                                        <input type="hidden" name="_method" value="DELETE">
                                        @csrf
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    No record found!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
