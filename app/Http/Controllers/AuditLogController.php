<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogController extends Controller
{
    /**
     * Tampilkan audit log (khusus Guru BK).
     */
    public function index(Request $request)
    {
        if (Auth::user()->role !== 'Guru BK') {
            abort(403, 'Hanya Guru BK yang dapat melihat audit log.');
        }

        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        // Filter berdasarkan action
        if ($action = $request->query('action')) {
            $query->where('action', $action);
        }

        // Filter berdasarkan model_type
        if ($modelType = $request->query('model_type')) {
            $query->where('model_type', $modelType);
        }

        // Filter berdasarkan user pelaku
        if ($userId = $request->query('id_user')) {
            $query->where('id_user', $userId);
        }

        $logs = $query->paginate(20)->withQueryString();

        // Untuk filter dropdown
        $actions = ['create' => 'Tambah', 'update' => 'Ubah', 'delete' => 'Hapus'];
        $modelTypes = AuditLog::select('model_type')->distinct()->pluck('model_type');

        return view('audit.index', compact('logs', 'actions', 'modelTypes'));
    }
}
