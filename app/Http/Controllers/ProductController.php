<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use AuthorizesRequests;

    public function update(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:180',
            'description' => 'sometimes|string|max:5000',
            'price' => 'sometimes|numeric|min:0.01',
            'stock' => 'sometimes|integer|min:0',
        ]);

        $product->update($validated);

        return back()->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        $product->delete();

        return back()->with('success', 'Product deleted.');
    }
}
