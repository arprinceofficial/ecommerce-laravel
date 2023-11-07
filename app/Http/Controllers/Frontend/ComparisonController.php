<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ProductComparison;
use App\Models\Product;

use Illuminate\Http\Request;

class ComparisonController extends Controller
{
    public function index()
    {
        $productComparison = ProductComparison::with('product')->where('token_id', request()->cookie('comparison_token'))->get();
        dd($productComparison);
        return view('comparison.index', compact('productComparison'));
    }

    public function addToCompareList(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id']
        ]);

        $token = $request->comparison_token;
        $productComparison = ProductComparison::where('token_id', $token)->where('product_id', $request->product_id)->first();
        if ($productComparison) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product already exists in the comparison list'
            ]);
        }

        ProductComparison::create([
            'token_id' => $token,
            'product_id' => $request->product_id
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Product added to the comparison list'
        ]);

    }

    public function compare()
    {
        $token = request()->cookie('comparison_token');
        $comparisonList = ProductComparison::where('token_id', $token)->get();
        // Retrieve product details for the comparisonList
        // Perform the comparison logic
        return view('comparison.compare', compact('comparisonList'));
    }

}
