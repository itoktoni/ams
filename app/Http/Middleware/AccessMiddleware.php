<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AccessMiddleware
{
    protected array $abilityMap = [
        'getTable' => 'view',
        'getCreate' => 'save',
        'postCreate' => 'save',
        'getUpdate' => 'save',
        'postUpdate' => 'save',
        'postDelete' => 'delete',
        'postDeleteBulk' => 'delete',
    ];

    /**
     * Handle an incoming request.
     * Implement role-based access control based on RoleEnum.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Customer hanya boleh halaman publik + lelang (web login, anti-bot injection)
        if (($user->role ?? null) === 'customer') {
            $routeName = $request->route()?->getName() ?? '';
            $path = $request->path();

            $isPublic = $routeName && (
                in_array($routeName, ['lelang.index','lelang.show','lelang.bid','login','register','password.request','password.email','password.reset','verification.notice','verification.verify','home','blog','page','post','category','tag','services','contact','search','api.content','captchaImage'])
                || str_starts_with($routeName, 'lelang.')
                || $routeName === 'home'
            );

            // path fallback untuk PublicController (/, /page/*, /blog*, /lelang*, /api/*, /captcha/*)
            if (! $isPublic) {
                $isPublic = $path === '/' 
                    || str_starts_with($path, 'lelang')
                    || str_starts_with($path, 'api')
                    || str_starts_with($path, 'captcha')
                    || str_starts_with($path, 'blog')
                    || str_starts_with($path, 'page')
                    || $path === 'contact'
                    || $path === 'services'
                    || $path === 'search';
            }

            if (! $isPublic) {
                return redirect()->route('lelang.index')->with('error', 'Customer hanya boleh akses halaman publik dan lelang. Silakan kembali ke lelang.');
            }
        }

        $method = $request->route()->getActionMethod();
        $routeName = $request->route()->getName();

        // Route-based access control
        // Editors can only access content-entry routes
        // Admins and developers have full access
        if ($this->isBlueprintRoute($routeName)) {
            // Blueprint routes: content-type, custom-field, field-group
            if (in_array($user->role, ['user', 'editor'])) {
                abort(403, 'Unauthorized action. Admin access required for blueprint management.');
            }
        }

        return $next($request);
    }

    /**
     * Check if the route is a blueprint management route.
     */
    protected function isBlueprintRoute(?string $routeName): bool
    {
        if (! $routeName) {
            return false;
        }

        $blueprintRoutes = [
            'cms-type',
            'field',
            'section',
        ];

        foreach ($blueprintRoutes as $blueprint) {
            if (str_contains($routeName, $blueprint)) {
                return true;
            }
        }

        return false;
    }
}
