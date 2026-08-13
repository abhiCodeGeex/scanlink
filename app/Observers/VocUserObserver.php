<?php

namespace App\Observers;

use App\Models\VocUser;
use App\Services\VocUserProvisioner;

class VocUserObserver
{
    public function __construct(protected VocUserProvisioner $provisioner) {}

    public function created(VocUser $vocUser): void
    {
        $this->provisioner->provision($vocUser);
    }

    public function updated(VocUser $vocUser): void
    {
        // Re-provision only when the credentials changed, keeping the linked login in sync.
        if ($vocUser->wasChanged(['email', 'password'])) {
            $this->provisioner->provision($vocUser);
        }
    }
}
