<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class BasePolicy
{
    protected $module;

    protected $restrict;

    public function __construct()
    {
        $this->module = request()->route()->getAction('name');
        $this->restrict = config('permision');
    }

    private function accessProtected($user, $permision)
    {
        $role = $user->role ?? 'guest';
        if (! isset($this->restrict[$role])) return false;

        // Support module prefix match: 'aset' matches 'aset.getTable', 'aset.getCreate', etc.
        foreach ($this->restrict[$role] as $modKey => $rule) {
            $isMatch = $this->module === $modKey
                || str_starts_with($this->module, $modKey . '.')
                || str_starts_with($this->module, $modKey . '/')
                || $modKey === $this->module;

            if (! $isMatch) continue;

            // false → deny all
            if ($rule === false) return true;

            // ['create'=>false] associative
            if (is_array($rule)) {
                // associative deny map
                if (array_key_exists($permision, $rule) && $rule[$permision] === false) return true;
                // numeric list deny
                if (in_array($permision, $rule, true)) return true;
                // also handle ['create'=>false] where permision is 'save' mapped from create/update
                // map save/create/update/delete/table/show to generic
                $mapped = $this->mapPermision($permision);
                foreach ($rule as $k => $v) {
                    if ($v === false && ($k === $permision || $k === $mapped)) return true;
                    if (is_int($k) && $v === $permision) return true;
                }
            }
        }

        return false;
    }

    private function mapPermision(string $p): string
    {
        return match($p) {
            'save' => 'create',
            'table' => 'view',
            default => $p,
        };
    }

    public function save(User $user): Response
    {
        return $this->accessProtected($user, __FUNCTION__) ? Response::deny() : Response::allow();
    }

    public function create(User $user): Response
    {
        return $this->accessProtected($user, __FUNCTION__) ? Response::deny() : Response::allow();
    }

    public function update(User $user): Response
    {
        return $this->accessProtected($user, __FUNCTION__) ? Response::deny() : Response::allow();
    }

    public function table(User $user): Response
    {
        return $this->accessProtected($user, __FUNCTION__) ? Response::deny() : Response::allow();
    }

    public function delete(User $user): Response
    {
        return $this->accessProtected($user, __FUNCTION__) ? Response::deny() : Response::allow();
    }

    public function show(User $user): Response
    {
        return $this->accessProtected($user, __FUNCTION__) ? Response::deny() : Response::allow();
    }

    public function prepare(User $user): Response
    {
        return $this->accessProtected($user, __FUNCTION__) ? Response::deny() : Response::allow();
    }

    public function prepareSo(User $user): Response
    {
        return $this->accessProtected($user, __FUNCTION__) ? Response::deny() : Response::allow();
    }

    public function storeship(User $user): Response
    {
        return $this->accessProtected($user, __FUNCTION__) ? Response::deny() : Response::allow();
    }

    public function recalc(User $user): Response
    {
        // samakan dengan update — jika boleh update aset boleh recalc
        return $this->update($user);
    }

    public function beritaAcara(User $user): Response
    {
        return $this->update($user);
    }
}
