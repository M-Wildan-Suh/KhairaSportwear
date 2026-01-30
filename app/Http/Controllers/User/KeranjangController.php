<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Keranjang;
use App\Models\Produk;
use Illuminate\Support\Facades\DB;

class KeranjangController extends Controller
{
    public function index()
    {
        $keranjangs = auth()->user()->keranjangs()
            ->with('produk.kategori')
            ->get();

        $subtotal = $keranjangs->sum('subtotal');
        $tax = $subtotal * 0.11; // 11% PPN
        $total = $subtotal + $tax;

        return view('user.keranjang.index', compact('keranjangs', 'subtotal', 'tax', 'total'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:produks,id',
            'type' => 'required|in:jual,sewa',
            'quantity' => 'required|integer|min:1',
            'options' => 'nullable|array'
        ]);

        $user = auth()->user();
        $product = Produk::findOrFail($request->product_id);

        // Check stock
        if ($product->stok_tersedia < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Stok tidak mencukupi'
            ], 400);
        }

        // Check if product is available for the requested type
        if ($request->type === 'jual' && !in_array($product->tipe, ['jual', 'both'])) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak tersedia untuk pembelian'
            ], 400);
        }

        if ($request->type === 'sewa' && !in_array($product->tipe, ['sewa', 'both'])) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak tersedia untuk penyewaan'
            ], 400);
        }

        // For sewa type, validate options
        if ($request->type === 'sewa') {
            $request->validate([
                'options.durasi' => 'required|in:harian,mingguan,bulanan',
                'options.jumlah_hari' => 'required|integer|min:1|max:30',
                'options.tanggal_mulai' => 'required|date|after:today'
            ]);
        }

        $defaultVarian = $product->varians()->first();

        if (!$defaultVarian) {
            return response()->json([
                'success' => false,
                'message' => 'Varian produk tidak ditemukan'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Check if item already exists in cart
            $existingItem = $user->keranjangs()
                ->where('produk_id', $product->id)
                ->where('tipe', $request->type)
                ->first();

            if ($existingItem) {
                // Update quantity
                $existingItem->quantity += $request->quantity;
                $existingItem->bundle_id = $existingItem->bundle_id ?? $defaultVarian->id;

                // Update sewa options if needed
                if ($request->type === 'sewa' && $request->has('options')) {
                    $existingItem->opsi_sewa = $request->options;
                }

                $existingItem->updateSubtotal();
                $existingItem->save();
            } else {
                // Create new cart item
                $keranjang = new Keranjang([
                    'user_id' => $user->id,
                    'produk_id' => $product->id,
                    'tipe' => $request->type,
                    'quantity' => $request->quantity,
                    'bundle_id' => $defaultVarian->id,
                    'opsi_sewa' => $request->type === 'sewa' ? $request->options : null
                ]);

                $keranjang->updateSubtotal();
                $keranjang->save();
            }

            DB::commit();

            $cartCount = $user->keranjangs()->count();

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan ke keranjang',
                'cart_count' => $cartCount
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $keranjang = auth()->user()->keranjangs()->findOrFail($id);
        $product = $keranjang->produk;

        // Check stock
        if ($product->stok_tersedia < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Stok tidak mencukupi'
            ], 400);
        }

        $keranjang->quantity = $request->quantity;
        $keranjang->updateSubtotal();
        $keranjang->save();

        // Recalculate totals
        $keranjangs = auth()->user()->keranjangs()->with('produk')->get();
        $subtotal = $keranjangs->sum('subtotal');
        $tax = $subtotal * 0.11;
        $total = $subtotal + $tax;

        return response()->json([
            'success' => true,
            'message' => 'Keranjang berhasil diperbarui',
            'subtotal' => number_format($subtotal, 0, ',', '.'),
            'tax' => number_format($tax, 0, ',', '.'),
            'total' => number_format($total, 0, ',', '.'),
            'item_subtotal' => number_format($keranjang->subtotal, 0, ',', '.')
        ]);
    }

    public function destroy($id)
    {
        $keranjang = auth()->user()->keranjangs()->findOrFail($id);
        $keranjang->delete();

        // Recalculate totals
        $keranjangs = auth()->user()->keranjangs()->with('produk')->get();
        $subtotal = $keranjangs->sum('subtotal');
        $tax = $subtotal * 0.11;
        $total = $subtotal + $tax;

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil dihapus dari keranjang',
            'cart_count' => auth()->user()->keranjangs()->count(),
            'subtotal' => number_format($subtotal, 0, ',', '.'),
            'tax' => number_format($tax, 0, ',', '.'),
            'total' => number_format($total, 0, ',', '.')
        ]);
    }

    public function clear()
    {
        auth()->user()->keranjangs()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Keranjang berhasil dikosongkan',
            'cart_count' => 0
        ]);
    }
}
