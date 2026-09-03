<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class PermintaanSukuCadangPolicy extends BasePolicy
{
    public function approve(User $user): Response
    {
        if (in_array($user->role, ['developer', 'admin', 'supervisor'], true)) return Response::allow();
        return Response::deny('Hanya admin/supervisor yang bisa approve.');
    }

    public function reject(User $user): Response
    {
        return $this->approve($user);
    }
}
