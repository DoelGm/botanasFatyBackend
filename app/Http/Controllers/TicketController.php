<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $ticket = Ticket::create(['subtotal' => 0]);
            $subtotal = 0;

            foreach ($request->products as $item) {
                $product = Product::findOrFail($item['id']);

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("No hay suficiente stock para el producto: {$product->name}");
                }

                $product->stock -= $item['quantity'];
                $product->save();

                // Verifica si se envió opción de mayoreo
                $useWholesale = isset($item['useWholesalePrice']) && $item['useWholesalePrice'];
                $unitPrice = $useWholesale && isset($item['wholesalePrice']) 
                    ? floatval($item['wholesalePrice']) 
                    : $product->price;

                $lineSubtotal = $unitPrice * $item['quantity'];
                $subtotal += $lineSubtotal;

                $ticket->products()->attach($product->id, [
                    'quantity' => $item['quantity'],
                    'price_applied' => $unitPrice, // Opcional: para guardar el precio que se aplicó
                ]);
            }

            $ticket->subtotal = $subtotal;
            $ticket->save();

            DB::commit();

            return response()->json([
                'message' => 'Ticket creado correctamente y stock actualizado.',
                'ticket' => $ticket,
                'subtotal' => $subtotal
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
