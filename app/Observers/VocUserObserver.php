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
        // A new email may belong to a different login. The card password stays on the card, so a
        // password change alone has nothing to sync.
        if ($vocUser->wasChanged('email')) {
            $this->provisioner->provision($vocUser);
        }
    }
}
