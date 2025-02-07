<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;

class ShoppingListItemController extends Controller
{
    public function store(Request $request, $id)
    {
        $list = ShoppingList::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'integer|min:1',
        ]);

        $item = $list->items()->create([
            'name' => $request->name,
            'quantity' => $request->quantity ?? 1,
        ]);

        return response()->json($item, 201);
    }

    public function update(Request $request, $id, $itemId)
    {
        $list = ShoppingList::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $item = $list->items()->where('id', $itemId)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'integer|min:1',
        ]);

        $item->update([
            'name' => $request->name,
            'quantity' => $request->quantity,
        ]);

        return response()->json($item);
    }

    public function destroy($id, $itemId)
    {
        $list = ShoppingList::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $item = $list->items()->where('id', $itemId)->firstOrFail();

        $item->delete();

        return response()->json(['message' => 'Item deleted successfully.']);
    }
}

