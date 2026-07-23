<?php

namespace App\Services;

use App\Enums\ClientUserRole;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\Profile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ClientSubdivisionService
{
    /**
     * @param  array<int>  $profileIds
     * @param  array{
     *     client_name: string,
     *     address: string,
     *     telephone: string,
     *     contact_person: string,
     *     regi_date: string,
     *     email: string,
     *     password: string,
     *     url: string,
     * }  $newClientData
     * @param  array<int>  $userIdsToTransfer
     */
    public function subdivide(
        Client $sourceClient,
        array $profileIds,
        array $newClientData,
        array $userIdsToTransfer = [],
    ): Client {
        if (! $this->emailIsAvailable($newClientData['email'])) {
            throw ValidationException::withMessages([
                'email' => 'Oops... Email you provided is already used. Please try another...',
            ]);
        }

        return DB::transaction(function () use ($sourceClient, $profileIds, $newClientData, $userIdsToTransfer): Client {
            $sourcePrimaryUser = $sourceClient->primaryUser;
            $checklistOption = $sourcePrimaryUser?->checklist_option ?? false;
            $customqrOption = $sourcePrimaryUser?->customqr_option ?? false;
            $sourcePrimaryUserId = $sourcePrimaryUser?->id;

            if ($userIdsToTransfer !== [] && $sourcePrimaryUserId) {
                $oldConflictProfileIds = Profile::query()
                    ->where('client_id', $sourceClient->id)
                    ->whereIn('user_id', $userIdsToTransfer)
                    ->whereNotIn('id', $profileIds)
                    ->pluck('id');

                if ($oldConflictProfileIds->isNotEmpty()) {
                    Profile::query()
                        ->whereIn('id', $oldConflictProfileIds)
                        ->update(['user_id' => $sourcePrimaryUserId]);
                }
            }

            $newClient = Client::query()->create([
                'client_name' => $newClientData['client_name'],
                'address' => $newClientData['address'],
                'telephone' => $newClientData['telephone'],
                'contact_person' => $newClientData['contact_person'],
                'regi_date' => $newClientData['regi_date'],
                'email' => $newClientData['email'],
                'password' => $newClientData['password'],
                'url' => $this->resolveAvailableUrl($newClientData['url']),
                'approve' => true,
                'is_password_change' => false,
            ]);

            $newPrimaryUser = $newClient->users()->create([
                'email' => $newClientData['email'],
                'password' => $newClientData['password'],
                'role' => ClientUserRole::Primary,
                'status' => true,
                'video_upload' => true,
                'checklist_option' => $checklistOption,
                'customqr_option' => $customqrOption,
                'is_password_change' => false,
                'expire_at' => now()->addYear(),
            ]);

            $newClientConflictProfileIds = collect();

            if ($userIdsToTransfer !== []) {
                $newClientConflictProfileIds = Profile::query()
                    ->where('client_id', $sourceClient->id)
                    ->whereNotIn('user_id', $userIdsToTransfer)
                    ->whereIn('id', $profileIds)
                    ->pluck('id');

                ClientUser::query()
                    ->whereIn('id', $userIdsToTransfer)
                    ->update(['client_id' => $newClient->id]);
            }

            if ($profileIds !== []) {
                Profile::query()
                    ->whereIn('id', $profileIds)
                    ->update(['client_id' => $newClient->id]);
            }

            if ($newClientConflictProfileIds->isNotEmpty()) {
                Profile::query()
                    ->whereIn('id', $newClientConflictProfileIds)
                    ->update(['user_id' => $newPrimaryUser->id]);
            }

            return $newClient->fresh();
        });
    }

    public function emailIsAvailable(string $email): bool
    {
        if (Client::query()->where('email', $email)->exists()) {
            return false;
        }

        return ! ClientUser::query()->where('email', $email)->exists();
    }

    protected function resolveAvailableUrl(string $url): string
    {
        if (! Client::query()->where('url', $url)->exists()) {
            return $url;
        }

        $suffix = 2;

        while (Client::query()->where('url', "{$url}-{$suffix}")->exists()) {
            $suffix++;
        }

        return "{$url}-{$suffix}";
    }
}
