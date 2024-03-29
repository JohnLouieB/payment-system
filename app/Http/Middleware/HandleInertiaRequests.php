<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\History;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
                'role' => [
                    'is_admin' => $user ? $request->user()->hasRole('admin') : null,
                    'is_student' => $user ? $request->user()->hasRole('student') : null,
                    'is_employee' => $user ? $request->user()->hasRole('employee') : null,
                ]
            ],
            'notificationCount' =>  History::query()
                ->where('status', 'pending')
                ->count(),
        ];
    }
}
