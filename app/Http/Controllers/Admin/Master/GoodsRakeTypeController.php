<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\GoodsRakeType;
use Illuminate\Http\Request;

class GoodsRakeTypeController extends Controller
{
    public function index()
    {
        $records = GoodsRakeType::get();

        return view('admin.master.goods_rake_type.index', compact('records'));
    }

    public function create()
    {
        return view('admin.master.goods_rake_type.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:goods_rake_types,name',
            'status' => 'required|in:1,0',
        ]);

        GoodsRakeType::create([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('goods-rake-type.index')
            ->with('success', 'Goods Rake Type created successfully.');
    }

    public function edit($id)
    {
        $record = GoodsRakeType::findOrFail(decrypt($id));

        return view('admin.master.goods_rake_type.edit', compact('record'));
    }

    public function update(Request $request, $id)
    {
        $record = GoodsRakeType::findOrFail(decrypt($id));

        $request->validate([
            'name' => 'required|string|max:255|unique:goods_rake_types,name,' . $record->id,
            'status' => 'required|in:1,0',
        ]);

        $record->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('goods-rake-type.index')
            ->with('success', 'Goods Rake Type updated successfully.');
    }

    public function destroy($id)
    {
        $record = GoodsRakeType::findOrFail(decrypt($id));
        $record->delete();

        return redirect()
            ->route('goods-rake-type.index')
            ->with('success', 'Goods Rake Type deleted successfully.');
    }
}
