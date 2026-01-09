<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notifikasi;

class NotifikasiController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = $user->notifikasis()->latest();
        
        // Filter by type
        if ($request->has('type')) {
            $query->where('tipe', $request->type);
        }
        
        // Filter by read status
        if ($request->has('read')) {
            $query->where('dibaca', $request->read === 'true');
        }
        
        $notifikasis = $query->paginate(20);
        
        // Mark as read when viewing
        if ($request->has('mark_as_read') && $request->mark_as_read === 'true') {
            $user->notifikasis()->belumDibaca()->update(['dibaca' => true]);
        }
        
        // Get notification counts
        $totalCount = $user->notifikasis()->count();
        $unreadCount = $user->notifikasis()->belumDibaca()->count();
        
        // Group by type for filtering
        $typeCounts = [
            'all' => $totalCount,
            'unread' => $unreadCount,
            'transaksi' => $user->notifikasis()->where('tipe', 'transaksi')->count(),
            'sewa' => $user->notifikasis()->where('tipe', 'sewa')->count(),
            'denda' => $user->notifikasis()->where('tipe', 'denda')->count(),
            'info' => $user->notifikasis()->where('tipe', 'info')->count(),
            'success' => $user->notifikasis()->where('tipe', 'success')->count(),
            'warning' => $user->notifikasis()->where('tipe', 'warning')->count(),
            'danger' => $user->notifikasis()->where('tipe', 'danger')->count(),
        ];
        
        return view('user.notifikasi.index', compact(
            'notifikasis', 
            'totalCount', 
            'unreadCount', 
            'typeCounts'
        ));
    }
    
    public function markAsRead($id)
    {
        $notifikasi = auth()->user()->notifikasis()->findOrFail($id);
        
        $notifikasi->markAsRead();
        
        return response()->json([
            'success' => true,
            'message' => 'Notifikasi ditandai sebagai telah dibaca'
        ]);
    }
    
    public function markAllAsRead()
    {
        auth()->user()->notifikasis()->belumDibaca()->update(['dibaca' => true]);
        
        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi ditandai sebagai telah dibaca',
            'unread_count' => 0
        ]);
    }
    
    public function destroy($id)
    {
        $notifikasi = auth()->user()->notifikasis()->findOrFail($id);
        $notifikasi->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Notifikasi berhasil dihapus'
        ]);
    }
    
    public function clearAll()
    {
        auth()->user()->notifikasis()->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi berhasil dihapus',
            'total_count' => 0,
            'unread_count' => 0
        ]);
    }
    
    public function getUnreadCount()
    {
        $unreadCount = auth()->user()->notifikasis()->belumDibaca()->count();
        
        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount
        ]);
    }
    
    public function getLatest()
    {
        $notifikasis = auth()->user()->notifikasis()
            ->terbaru()
            ->belumDibaca()
            ->limit(5)
            ->get();
        
        return response()->json([
            'success' => true,
            'notifications' => $notifikasis,
            'count' => $notifikasis->count()
        ]);
    }
}