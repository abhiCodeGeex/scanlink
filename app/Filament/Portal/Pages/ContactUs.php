<?php

namespace App\Filament\Portal\Pages;

use App\Mail\ContactUsMessage;
use App\Models\Setting;
use App\Models\User;
use App\Services\ContactCaptchaService;
use App\Support\SystemNotifier;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactUs extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?string $navigationLabel = 'Contact us';

    protected static ?string $title = 'Contact us';

    protected static ?string $slug = 'contact';

    /** Top-level: Contact us | Dashboard | My Account | How to */
    protected static ?int $navigationSort = -40;

    public static function getNavigationGroup(): ?string
    {
        return null;
    }

    protected string $view = 'filament.portal.pages.contact-us';

    public string $name = '';

    public string $email = '';

    public string $comments = '';

    public string $captcha = '';

    public ?string $formError = null;

    public bool $submitted = false;

    public int $captchaNonce = 0;

    public static function canAccess(): bool
    {
        // Panel auth middleware already gates portal access.
        return Auth::check();
    }

    public function getHeading(): string|Htmlable|null
    {
        return '';
    }

    public function mount(): void
    {
        $user = Auth::user();

        if ($user instanceof User) {
            $this->email = (string) $user->email;
            $this->name = trim((string) $user->name) ?: '';
        }

        $this->captchaNonce = time();
    }

    public function submit(ContactCaptchaService $captcha): void
    {
        $this->formError = null;
        $this->submitted = false;

        $name = trim($this->name);
        $email = trim($this->email);
        $comments = trim($this->comments);
        $captchaAnswer = trim($this->captcha);

        if ($name === '' || $email === '' || $comments === '' || $captchaAnswer === '') {
            $this->formError = 'All fields are required...';
            $this->refreshCaptcha();

            return;
        }

        if (! $captcha->valid($captchaAnswer)) {
            $this->formError = 'Invalid Verification Code...';
            $this->refreshCaptcha();

            return;
        }

        $contactEmail = Setting::valueFor('contact_email') ?? 'admin@scanlink.com';

        try {
            Mail::to($contactEmail)->send(new ContactUsMessage($name, $email, $comments));
        } catch (\Throwable $exception) {
            Log::warning('Contact form mail failed', [
                'email' => $email,
                'message' => $exception->getMessage(),
            ]);
        }

        SystemNotifier::toAdmins(
            'New contact message',
            trim($name).' ('.$email.') sent a message via the portal contact form.',
            'heroicon-o-envelope',
            'info',
        );

        $this->comments = '';
        $this->captcha = '';
        $this->submitted = true;
        $this->refreshCaptcha();

        Notification::make()
            ->title('Thanks — your enquiry has been sent.')
            ->success()
            ->send();
    }

    protected function refreshCaptcha(): void
    {
        $this->captcha = '';
        $this->captchaNonce = time();
    }
}
