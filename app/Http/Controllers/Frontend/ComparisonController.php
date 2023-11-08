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
        if (request()->hasCookie('comparison_token')) {
            $comparisonToken = request()->cookie('comparison_token');
        }
        dd($comparisonToken);

        $productComparison = ProductComparison::with('product')->where('token_id', request()->cookie('comparison_token'))->get();
        dd($productComparison);
        return view('comparison.index', compact('productComparison'));
    }

    public function addToCompareList(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id']
        ]);

        // $token = $request->comparison_token;
        $token = session()->get('_token');
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

        $count = ProductComparison::where('token_id', $token)->count();
        // put ProductComparison data in session
        if ($count > 0) {
            $productComparison = ProductComparison::with('product')->where('token_id', $token)->get();
            session()->put('productComparison', $productComparison);
        }



        return response()->json([
            'status' => 'success',
            'message' => 'Product added to the comparison list',
            'count' => $count
        ]);

    }

    public function getComparisonList(Request $request)
    {
        try {
            // get data from session
            $productComparison = session()->get('productComparison');
            if ($productComparison) {
                return response()->json([
                    'status' => 'success',
                    'count' => count($productComparison),
                    'data' => $productComparison
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No product found'
                ]);
            }
        }
        catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong'
            ]);
        }

    }

    public function destory($id)
    {
        try {
            $token = session()->get('_token');
            $productComparison = ProductComparison::where('token_id', $token)->where('product_id', $id)->first();

            if ($productComparison) {
                $productComparison->delete();
                $count = ProductComparison::where('token_id', $token)->count();
                // put ProductComparison data in session
                if ($count > 0) {
                    $productComparison = ProductComparison::with('product')->where('token_id', $token)->get();
                    session()->put('productComparison', $productComparison);
                } else {
                    session()->forget('productComparison');
                }
                return response()->json([
                    'status' => 'success',
                    'message' => 'Product removed',
                    'count' => $count
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Product not found'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong'
            ]);
        }


    }
    public function compare()
    {
        $token = session()->get('_token');
        $comparisonList = ProductComparison::where('token_id', $token)->get();
        // Retrieve product details for the comparisonList
        // Perform the comparison logic
        return view('comparison.compare', compact('comparisonList'));
    }

    public function getComparisonCount(Request $request)
    {
        // $token = $request->comparison_token;
        $token = session()->get('_token');
        $count = ProductComparison::where('token_id', $token)->count();
        return response()->json([
            'status' => 'success',
            'count' => $count
        ]);
    }
}
