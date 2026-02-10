<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Liste des notifications de l'utilisateur
     */
    public function index(Request $request)
    {
        $notifications = Notification::where('user_id', $request->user()->user_id)
            ->orderBy('date_envoi', 'desc')
            ->limit(50)
            ->get();

        return response()->json(['notifications' => $notifications]);
    }

    /**
     * Notifications non lues
     */
    public function nonLues(Request $request)
    {
        $count = Notification::where('user_id', $request->user()->user_id)
            ->where('lue', false)
            ->count();

        return response()->json(['non_lues' => $count]);
    }

    /**
     * Marquer comme lue
     */
    public function marquerLue(Request $request, $id)
    {
        $notif = Notification::where('user_id', $request->user()->user_id)
            ->where('notification_id', $id)
            ->first();

        if ($notif) {
            $notif->update(['lue' => true]);
        }

        return response()->json(['message' => 'Marquée comme lue']);
    }

    /**
     * Marquer toutes comme lues
     */
    public function marquerToutesLues(Request $request)
    {
        Notification::where('user_id', $request->user()->user_id)
            ->where('lue', false)
            ->update(['lue' => true]);

        return response()->json(['message' => 'Toutes marquées comme lues']);
    }
}
