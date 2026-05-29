<?php

namespace App\Support;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DemoMode
{
    public static function isDemoRequest(Request $request): bool
    {
        if (! (bool) config('domain_context.demo.enabled', true)) {
            return false;
        }

        return in_array(self::normalizeHost($request->getHost()), self::demoHosts(), true);
    }

    public static function demoHosts(): array
    {
        return array_values(array_unique(array_filter(array_map(
            [self::class, 'normalizeHost'],
            (array) config('domain_context.demo.hosts', [])
        ))));
    }

    public static function ensureDemoUser(): User
    {
        $username = (string) config('domain_context.demo.user.username', 'demo');
        $email = (string) config('domain_context.demo.user.email', 'demo@ceralith.local');

        $query = User::query();
        if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive(User::class), true)) {
            $query->withTrashed();
        }

        if (Schema::hasColumn('users', 'username')) {
            $query->where('username', $username);
        }

        if (Schema::hasColumn('users', 'email')) {
            Schema::hasColumn('users', 'username')
                ? $query->orWhere('email', $email)
                : $query->where('email', $email);
        }

        $user = $query->first();

        if (! $user) {
            $user = new User();
        }

        $attributes = self::demoUserAttributes($username, $email);
        foreach ($attributes as $column => $value) {
            if (Schema::hasColumn('users', $column)) {
                $user->{$column} = $value;
            }
        }

        if (method_exists($user, 'trashed') && $user->trashed()) {
            $user->restore();
        }

        $user->save();

        return $user;
    }

    private static function demoUserAttributes(string $username, string $email): array
    {
        return [
            'first_name' => (string) config('domain_context.demo.user.first_name', 'Demo'),
            'last_name' => (string) config('domain_context.demo.user.last_name', 'User'),
            'name_initials' => (string) config('domain_context.demo.user.initials', 'DU'),
            'name' => (string) config('domain_context.demo.user.name', 'Demo User'),
            'username' => $username,
            'email' => $email,
            'phone' => (string) config('domain_context.demo.user.phone', '0000000000'),
            'password' => Hash::make((string) config('domain_context.demo.user.password', 'demo')),
            'is_admin' => 1,
            'status' => 1,
            'included_in_reports' => 0,
            'is_developer' => 0,
            'has_photo' => 0,
            'email_verified_at' => now(),
        ];
    }

    private static function normalizeHost(?string $host): string
    {
        $normalized = strtolower(trim((string) $host));

        return strpos($normalized, 'www.') === 0 ? substr($normalized, 4) : $normalized;
    }
}
