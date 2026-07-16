<?php

namespace App\Filament\Concerns;

use App\Enums\AdminRole;
use App\Models\User;

trait AuthorizesAdminRole
{
  protected static function adminUser(): ?User
  {
    $user = auth()->user();

    return $user instanceof User ? $user : null;
  }

  protected static function adminRole(): ?AdminRole
  {
    return static::adminUser()?->admin_role;
  }

  protected static function canWriteAdmin(): bool
  {
    return static::adminRole()?->canWrite() ?? false;
  }

  public static function canViewAny(): bool
  {
    return static::adminRole() !== null;
  }

  public static function canView($record): bool
  {
    return static::canViewAny();
  }

  public static function canCreate(): bool
  {
    return static::canWriteAdmin();
  }

  public static function canEdit($record): bool
  {
    return static::canWriteAdmin();
  }

  public static function canDelete($record): bool
  {
    return static::canWriteAdmin();
  }
}
