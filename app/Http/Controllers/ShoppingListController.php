<?php

namespace App\Http\Controllers;

use App\Models\ShoppingListItem;
use Illuminate\Http\Request;
use App\Models\ShoppingList;

class ShoppingListController extends Controller
{
    public function index()
    {
        $lists = auth()->user()->shoppingLists()->with('items')->get();
        return response()->json($lists);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'items' => 'required'
        ]);

        $list = ShoppingList::create([
            'user_id' => auth()->id(),
            'name' => $request->list_name,
        ]);

        $items = $request->input('items');
        foreach ($items as $item){
            ShoppingListItem::create([
                'shopping_list_id' => $list->id,
                'name' => $item->name,
                'quantity' => $item->quantity
            ]);
        }
        return response()->json($list, 201);
    }

    public function update(Request $request, $id)
    {
        $list = ShoppingList::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $list->update(['name' => $request->name]);

        return response()->json($list);
    }

    public function destroy($id)
    {
        $list = ShoppingList::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $list->delete();

        return response()->json(['message' => 'Shopping list deleted successfully.']);
    }
}
