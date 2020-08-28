<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Create a new product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // Validate the data
        $data = $this->validateProductRequest($request);

        // Add the product to the database
        $product = Product::create($data);

        return response()->json(['message' => 'Product was successfully created!', 'product' => $product]);
    }

    /**
     * Show the specified product.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return response()->json(['message' => 'Product found!', 'product' => $product]);
    }

    /**
     * Update the specified product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        // Validate the data
        $data = $this->validateProductRequest($request);

        // Update the product
        $product->update($data);

        return response()->json(['message' => 'Product #' . $product->id . ' was successfully updated!']);
    }

    /**
     * Delete the specified product.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Product $product)
    {
        // Delete the product
        $product->delete();

        return response()->json(['message' => 'Product successfully deleted!']);
    }


    /**
     * Validate a product request and return the validated data.
     */
    public function validateProductRequest($request)
    {
        // Validate the data
        $data = $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required|integer',
            'image' => 'sometimes|max:255', // only validate when present
        ]);

        return $data;
    }
}
