<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Product;
use Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(['products' => Product::all()]);
    }


    /**
     * List all of the products attached to the requesting user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function userIndex(Request $request)
    {
        $user = Auth::user();

        return response()->json(['products' => $user->products]);
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

        // If an image file is present, upload it and get the file path.
        if ($request->has('image')) {
            $data['image'] = $request->file('image')->store('images');
        }

        // Add the product to the database
        $product = Product::create($data);

        return response()->json(['message' => 'Product was successfully created!', 'product' => $product]);
    }


    /**
     * Show the specified product.
     *
     * @param  App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return response()->json(['product' => $product]);
    }


    /**
     * Update the specified product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        // Validate the data
        $data = $this->validateProductRequest($request);

        // If an image file is present, upload it and get the file path.
        if ($request->has('image')) {
            $data['image'] = $request->file('image')->store('images');
        }

        // Update the product
        $product->update($data);

        return response()->json(['message' => 'Product #' . $product->id . ' was successfully updated!']);
    }


    /**
     * Delete the specified product.
     *
     * @param  App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function delete(Product $product)
    {
        // Delete the product
        $product->delete();

        return response()->json(['message' => 'Product successfully deleted!']);
    }


    /**
     * Attach the specified product to the requesting user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function attach(Request $request, Product $product)
    {
        $user = Auth::user();

        // Only allow product to be attached to user if the user has an active subscription.
        if (!$user->subscriptions->count()) {
            return response()->json(['message' => 'Product could not be attached.', 'error' => 'User does not have an active subscription.'], 403);
        }

        // Attach this product to the user if it's not already (without detaching any other products)
        $user->products()->syncWithoutDetaching($product->id);

        return response()->json(['message' => 'Product #' . $product->id . ' was successfully added to User #' . $user->id . '!']);
    }


    /**
     * Detach the specified product to the requesting user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function detach(Request $request, Product $product)
    {
        $user = Auth::user();

        // Detach the product from the user if the relationship exists.
        $user->products()->detach($product->id);

        return response()->json(['message' => 'Product #' . $product->id . ' was successfully removed from User #' . $user->id . '!']);
    }



    /**
     * Upload an image to the specified product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function uploadImage(Request $request, Product $product)
    {
        // Validate the image
        $data = $request->validate([
            'image' => 'required|mimes:jpg,jpeg,png,svg,gif'
        ]);

        // Store the image file and update the image path in the DB
        $image_path = $request->file('image')->store('images');
        $product->update(['image' => $image_path]);

        return response()->json(['message' => 'Product image successfully uploaded!']);
    }


    /**
     * Validate a product request and return the validated data.
     */
    public function validateProductRequest($request)
    {
        // Validate the data
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|integer',
            'image' => 'sometimes|mimes:jpg,jpeg,png,svg,gif', // only validate when present
        ]);

        return $data;
    }



}
