<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\User;

class NotificationController extends Controller
{
    // ================= LIST SEMUA NOTIF ADMIN =================
    public function index()
    {
        $notifications = Notification::whereNotNull('admin_id')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.notifications.index', compact('notifications'));
    }

    // ================= LIST REQUEST GANTI PASSWORD =================
    public function requestsPassword()
    {
        $requests = Notification::where('type', 'request_password_change')
            ->where('is_read', 0)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.requests.password', compact('requests'));
    }

    // ================= APPROVE PASSWORD TEKNISI =================
    public function approvePasswordChange($id)
    {
$notification = Notification::findOrFail($id);

$user = User::findOrFail($notification->user_id);
$user->password = $notification->pending_password;
$user->save();

$notification->is_read = 1;
$notification->pending_password = null;
$notification->save();

        // validasi tipe notif
        if ($notification->type !== 'request_password_change') {
            return back()->with('error', 'Notifikasi tidak valid.');
        }

        $userId = $notification->user_id;

        // ambil password dari session teknisi
        $hashedPassword = session('pending_password_' . $userId);

        if (!$hashedPassword) {
            return back()->with('error', 'Password tidak ditemukan atau session sudah habis.');
        }

        $user = User::findOrFail($userId);
        $user->password = $hashedPassword;
        $user->save();

        // tandai notif sudah diproses
        $notification->is_read = 1;
        $notification->save();

        // hapus session password
        session()->forget('pending_password_' . $userId);

        return back()->with('success', 'Password teknisi berhasil di-approve.');
    }

    // ================= HELPER BUAT NOTIF KE ADMIN =================
    public static function createAdminNotification($type, $message, $userId = null)
    {
        // ambil admin pertama (atau sesuaikan kalau multi-admin)
        $adminId = User::where('role', 'admin')->value('id');

   Notification::create([
    'user_id'  => $userId,
    'admin_id' => $adminId,  // kalau null -> notif gagal masuk
    'type'     => $type,
    'message'  => $message,
    'is_read'  => 0,
]);

    }

    public function markAsRead($id)
{
    $notification = Notification::findOrFail($id);
    $notification->is_read = 1;
    $notification->save();

    return back()->with('success', 'Notifikasi ditandai sebagai dibaca.');
}

}
