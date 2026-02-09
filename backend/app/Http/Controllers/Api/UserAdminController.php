<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserAdminController extends Controller
{
    /**
     * Mark a user as admin. Only callable by existing admins.
     */
    public function makeAdmin(Request $request, $id)
    {
        $actor = $request->user();
        if (! $actor || ! $actor->is_admin) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $user = User::find($id);
        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->is_admin = true;
        $user->save();

        return response()->json(['message' => 'User promoted to admin', 'user' => $user]);
    }
}
