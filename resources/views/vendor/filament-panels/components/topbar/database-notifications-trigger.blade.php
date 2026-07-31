{{--
    Override of Filament's topbar bell trigger: use the SOLID bell when there are
    unread notifications, and the outlined bell when the list is empty.
--}}
<x-filament::icon-button
    :badge="$unreadNotificationsCount ?: null"
    color="gray"
    :icon="
        ($unreadNotificationsCount ?? 0)
            ? \Filament\Support\Icons\Heroicon::Bell
            : \Filament\Support\Icons\Heroicon::OutlinedBell
    "
    :icon-alias="\Filament\View\PanelsIconAlias::TOPBAR_OPEN_DATABASE_NOTIFICATIONS_BUTTON"
    icon-size="lg"
    :label="__('filament-panels::layout.actions.open_database_notifications.label')"
    class="fi-topbar-database-notifications-btn"
/>
