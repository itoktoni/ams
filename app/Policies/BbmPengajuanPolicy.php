<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class BbmPengajuanPolicy extends BasePolicy
{
    public function approve(User $user): Response
    {
        if (in_array($user->role, ['developer', 'admin'], true)) {
            return Response::allow();
        }

        return Response::deny('Hanya admin/developer yang bisa menyetujui pengajuan BBM.');
    }

    public function reject(User $user): Response
    {
        return $this->approve($user);
    }
}
