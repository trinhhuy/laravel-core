<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Combo;
use App\Models\ProductCombo;

class ComboController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('productCombo.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $combo = (new Combo)->forceFill([
            'status' => true,
        ]);

        return view('productCombo.create', compact('combo'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request  $request)
    {
        $rules = [
            'name' => 'required|max:255|unique:combos',
            'price' => 'required|numeric',
        ];
        $messages = [
            'name.unique' => 'Tên combo bị trùng.',
            'name.required' => 'Hãy nhập tên combo.',
            'price.required' => 'Hãy nhập giá combo.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response['mess'] = 'fails';
            $response['errors'] = $errors;
        } else {
            $combo = Combo::forceCreate([
                'name' => request('name'),
                'price' => request('price', 0),
                'status' => !! request('status'),
            ]);
            $combo->forceFill([
                'code' => $combo->name . '-' . $combo->id
            ])->save();

            if (request()->has('productIds')) {
                $productIds = request('productIds');

                $quantity = request('quantity');

                foreach ($productIds as $key => $productId) {
                    $bundleProduct = ProductCombo::forceCreate([
                        'combo_id' => $combo->id,
                        'product_id' => $productIds[$key],
                        'quantity' =>  $quantity[$key] ? $quantity[$key] : 1,
                    ]);
                }
            }

            flash()->success('Success!', 'Combo successfully created.');
            $response['mess'] = 'success';
        }

        return response()->json($response);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Combo $combo)
    {
       $productsCombo = $combo->products()->get();
       $productIds = $combo->products()->pluck('products.id');

       return view('productCombo.edit', compact('productsCombo', 'productIds', 'combo'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Combo $combo)
    {
        $this->validate(request(), [
            'price' => 'required|numeric',
        ], [
            'price.required' => 'Hãy nhập giá combo.',
        ]);

        $combo->forceFill([
            'price' => request('price'),
            'status' => !! request('status'),
        ])->save();

        if (request()->has('productIds')) {
            $productIds = request('productIds');
            $quantity = request('quantity');

            foreach ($productIds as $key => $productId) {
                $productCombo = ProductCombo::where('product_id',$productId)
                    ->where('combo_id',$combo->id)
                    ->first();

                if (! $productCombo) {
                    $productCombo = (new ProductCombo)->forceFill([
                        'combo_id' => $combo->id,
                        'product_id' => $productId,
                    ]);
                }

                $productCombo->forceFill([
                    'quantity' => isset($quantity[$key]) ? $quantity[$key] : 1
                ])->save();
            }
        }

        flash()->success('Success!', 'Combo successfully updated.');

        return redirect()->route('combo.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function destroyproduct()
    {
        ProductCombo::where('product_id',request('productId'))->where('combo_id',request('comboId'))->delete();

        return response()->json([
            'message' => 'success',
        ]);
    }

    public function getDatatables()
    {
        return Combo::getDatatables();
    }
}
