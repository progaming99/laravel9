<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Exception;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::all();

        return view('products.index', ['products' => $products]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $brands = Brand::orderBy('name', 'asc')->get()->pluck('name', 'id');
        $categories = Category::orderBy('name', 'asc')->get()->pluck('name', 'id');

        return view('products.create', ['brands' => $brands, 'categories' => $categories]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductRequest $request)
    {
        // $request->validate([
        //     'sku' => ['required', 'unique:products', 'max:100'],
        //     'name' => ['required', 'max:100'],
        //     'price' => ['required', 'numeric', 'min;1'],
        //     'stock' => ['required', 'numeric', 'min:0'],
        // ]);
        // return 'Good!';

        // atau

        // $validator = Validator::make($request->all(), [
        //     'sku' => ['required', 'unique:products', 'max:100'],
        //     'name' => ['required', 'max:100'],
        //     'price' => ['required', 'numeric', 'min:1'],
        //     'stock' => ['required', 'numeric', 'min:0'],
        // ]);

        // if ($validator->fails()) {
        //     return redirect('products/create')
        //         ->withErrors($validator)
        //         ->withInput();
        // }

        // // Retrieve the validated input...
        // $validated = $validator->validated();

        // return $validated;

        // ---stdar
        // $params = $request->validated();
        // if ($product = Product::create($params)) {
        //     $product->categories()->sync($params['category_ids']);

        //     return redirect(route('products.index'))->with('success', 'Added!');
        // }

        // ---Automatic Database Transaction
        // $params = $request->validated();

        // $saveProduct = false;
        // $saveProduct = DB::transaction(function () use ($params) {
        //     $product = Product::create($params);
        //     $product->categories()->sync($params['category_ids']);

        //     return true;
        // });
        // if ($saveProduct) {
        //     return redirect(route('products.index'))->with('success', 'Added!');
        // }
        // return redirect(route('products.index'))->with('error', 'Failed!');

        // ---Manual Transaction Database
        // $params = $request->validated();

        // DB::beginTransaction();
        // try {
        //     $product = Product::create($params);
        //     $product->categories()->sync($params['category_ids']);
        //     // Commit Transaction : semua proses berjalan success
        //     DB::commit();

        //     return redirect(route('products.index'))->with('success', 'Added!');
        // } catch (Exception $e) {
        //     // Rollback: ada proses yang error
        //     DB::rollBack();
        //     return redirect(route('products.index'))->with('error', 'Failed!');
        // }

        // -----upload gambar
        $imageName = time() . '.' . $request->image->extension();
        // upload ke public
        $uploadedImage = $request->image->move(public_path('images'), $imageName);
        $imagePath = 'images/' . $imageName;

        // upload ke storage        
        // $uploadedImage = $request->image->storeAs('images', $imageName);
        // harus membuat perintah php artisan storage:link

        // upload ke S3
        // $uploadedImage = $request->image->storeAs('images', $imageName, 's3');

        $params = $request->validated();

        if ($product = Product::create($params)) {
            $product->image = $imagePath;
            $product->save();

            return redirect(route('products.index'))->with('success', 'Added!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        echo 'show product';
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $brands = Brand::orderBy('name', 'asc')->get()->pluck('name', 'id');
        $categories = Category::orderBy('name', 'asc')->get()->pluck('name', 'id');

        return view('products.edit', ['product' => $product, 'brands' => $brands, 'categories' => $categories]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductRequest $request, $id)
    {
        $product = Product::findOrFail($id);
        $params = $request->validated();

        if ($product->update($params)) {
            $product->categories()->sync($params['category_ids']);

            return redirect(route('products.index'))->with('success', 'Updated!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->categories()->detach();

        if ($product->delete()) {
            return redirect(route('products.index'))->with('success', 'Deleted!');
        }

        return redirect(route('products.index'))->with('error', 'Sorry, unable to delete this!');
    }
}
