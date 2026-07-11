<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    /**
     * Update user's push subscription.
     */
    public function store(Request $request)
    {
        \Log::info('Push Subscription Payload:', $request->all());

        $request->validate([
            'endpoint' => 'required',
            'keys.auth' => 'required',
            'keys.p256dh' => 'required',
        ]);

        $endpoint = $request->endpoint;
        $key = $request->keys['p256dh'];
        $token = $request->keys['auth'];
        $contentEncoding = $request->contentEncoding ?? 'aesgcm';

        $request->user()->updatePushSubscription($endpoint, $key, $token, $contentEncoding);

        // Enable push notifications for the user
        $request->user()->update(['push_notifications' => true]);

        // Send a test notification
        $request->user()->notify(new \App\Notifications\PushSubscriptionSuccess());

        return response()->json(['success' => true]);
    }

    /**
     * Delete user's push subscription.
     */
    public function destroy(Request $request)
    {
        $request->validate(['endpoint' => 'required']);

        $request->user()->deletePushSubscription($request->endpoint);

        return response()->json(['success' => true]);
    }
}
