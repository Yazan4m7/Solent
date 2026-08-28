<?php

namespace App\Listeners;

use App\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class RecordUserLoginAudit
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle(Login $event): void
    {
        if ($event->guard !== 'web' || ! $event->user instanceof User || ! $this->isEnabled($event->user)) {
            return;
        }

        $username = (string) $event->user->username;

        try {
            DB::table('user_login_audits')->insert([
                'user_id' => $event->user->getKey(),
                'username' => $username,
                'ip_address' => $this->request->ip(),
                'user_agent' => mb_substr((string) $this->request->userAgent(), 0, 500),
                'logged_in_at' => now(),
            ]);
        } catch (Throwable $exception) {
            Log::warning('Unable to record user login audit.', [
                'user_id' => $event->user->getKey(),
                'username' => $username,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function isEnabled(User $user): bool
    {
        $attributes = $user->getAttributes();

        if (array_key_exists('status', $attributes) && ! (bool) $user->status) {
            return false;
        }

        if (array_key_exists('active', $attributes) && ! (bool) $user->active) {
            return false;
        }

        return true;
    }
}
