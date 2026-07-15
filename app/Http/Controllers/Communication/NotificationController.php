<?php

namespace App\Http\Controllers\Communication;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display all notifications for the authenticated user.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        $query = $user->notifications()->latest();

        // Filtre par type de lecture
        if ($request->filled('filter')) {
            match ($request->input('filter')) {
                'unread' => $query->whereNull('read_at'),
                'read'   => $query->whereNotNull('read_at'),
                default  => null,
            };
        }

        // Filtre par type de notification
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        $notifications = $query->paginate(20)->withQueryString();

        $stats = [
            'total'  => $user->notifications()->count(),
            'unread' => $user->unreadNotifications()->count(),
            'read'   => $user->readNotifications()->count(),
        ];

        return view('notifications.index', compact('notifications', 'stats'));
    }

    /**
     * Marquer une notification comme lue.
     */
    public function markAsRead(string $id): RedirectResponse
    {
        $notification = Auth::user()
            ->notifications()
            ->findOrFail($id);

        $notification->markAsRead();

        // Rediriger vers l'URL cible si disponible
        $url = $notification->data['url'] ?? route('notifications.index');

        return redirect($url);
    }

    /**
     * Marquer toutes les notifications comme lues (AJAX).
     */
    public function markAllRead(Request $request): JsonResponse
    {
        $request->validate([
            '_token' => ['required'],
        ]);

        Auth::user()->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Toutes les notifications ont été marquées comme lues.',
            'count'   => 0,
        ]);
    }

    /**
     * Supprimer une notification.
     */
    public function destroy(string $id): RedirectResponse
    {
        Auth::user()
            ->notifications()
            ->findOrFail($id)
            ->delete();

        return redirect()
            ->route('notifications.index')
            ->with('success', 'La notification a été supprimée.');
    }

    /**
     * Supprimer toutes les notifications lues.
     */
    public function destroyRead(Request $request): JsonResponse
    {
        Auth::user()->readNotifications()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Toutes les notifications lues ont été supprimées.',
        ]);
    }

    /**
     * Retourner le nombre de notifications non lues (polling AJAX).
     */
    public function unreadCount(): JsonResponse
    {
        return response()->json([
            'count' => Auth::user()->unreadNotifications()->count(),
        ]);
    }

    /**
     * Retourner les 8 dernières notifications non lues (polling dropdown).
     */
    public function latest(): JsonResponse
    {
        $user          = Auth::user();
        $notifications = $user->unreadNotifications()
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn($n) => [
                'id'             => $n->id,
                'data'           => $n->data,
                'read_at'        => $n->read_at,
                'created_at_human' => $n->created_at->diffForHumans(),
            ]);

        return response()->json([
            'notifications' => $notifications,
            'unread_count'  => $user->unreadNotifications()->count(),
        ]);
    }
}