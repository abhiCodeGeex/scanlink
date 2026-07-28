<?php

namespace App\Filament\Portal\Auth;

use App\Enums\ClientUserRole;
use App\Enums\UserType;
use App\Mail\RegistrationWelcomeMail;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\CodePrising;
use App\Models\CodePurchase;
use App\Models\User;
use App\Services\ContactCaptchaService;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Auth\Events\Registered;
use Filament\Auth\Http\Responses\Contracts\RegistrationResponse;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Facades\Filament;
use Filament\Schemas\Components\View as SchemaView;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use SensitiveParameter;

/**
 * Legacy ScanLink registration wizard (steps 1–2 on one page).
 * Step 1: details → Step 2: quantity UI → complete: email + nearly-done popup → portal login.
 */
class Register extends BaseRegister
{
    public int $captchaNonce = 0;

    public int $wizardStep = 1;

    public bool $showNearlyDoneModal = false;

    public string $perCodeAmount = '0.00';

    public string $subscriptionAmount = '0.00';

    public function mount(): void
    {
        parent::mount();

        $this->refreshCaptcha();
        data_set($this->data, 'no_codes', '');
    }

    public function getMaxWidth(): Width|string|null
    {
        // Legacy register is ~930px wide (3 × 310px breadcrumbs).
        return Width::Full;
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return 'Register with ScanLink';
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return null;
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            SchemaView::make('filament.portal.auth.register-form'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function refreshCaptcha(): void
    {
        $this->captchaNonce = time();
        data_set($this->data, 'captcha', '');
    }

    /**
     * Step 1 NEXT → validate and stay on page at step 2.
     * Step 2 NEXT → create account, email, popup, then portal login on dismiss.
     */
    public function register(): ?RegistrationResponse
    {
        if ($this->wizardStep === 1) {
            $this->resetErrorBag();

            $data = $this->normalizedData();
            $this->data = array_merge(is_array($this->data) ? $this->data : [], $data);

            // Validate only — do not rate-limit here (step 1 + step 2 would trip Filament's limiter).
            $this->validateRegistration($data);
            app(ContactCaptchaService::class)->consume();

            $this->wizardStep = 2;
            $this->resetErrorBag();

            return null;
        }

        return $this->completeRegistration();
    }

    public function calculateCodes(): void
    {
        $qty = (int) trim((string) data_get($this->data, 'no_codes', ''));

        if ($qty < 1) {
            throw ValidationException::withMessages([
                'data.no_codes' => 'Enter a number of codes required.',
            ]);
        }

        if ($qty > 1000) {
            throw ValidationException::withMessages([
                'data.no_codes' => 'Quantity must be 1000 or less.',
            ]);
        }

        $tier = CodePrising::query()
            ->where('code_min_qty', '<=', $qty)
            ->where('code_max_qty', '>=', $qty)
            ->orderBy('code_min_qty')
            ->first();

        if (! $tier) {
            // Registration includes 1 free code; show zeros when no paid tier matches.
            $this->perCodeAmount = '0.00';
            $this->subscriptionAmount = '0.00';

            return;
        }

        $per = (float) $tier->amount;
        $this->perCodeAmount = number_format($per, 2, '.', '');
        $this->subscriptionAmount = number_format($per * $qty * 12, 2, '.', '');
    }

    public function dismissNearlyDone(): void
    {
        $this->showNearlyDoneModal = false;

        $this->redirectIntended(Filament::getUrl());
    }

    /**
     * Filament default is max 2 attempts per email — that blocks normal 2-step wizard use
     * (and leftover hits from earlier testing). Only enforce on final account create.
     */
    protected function isRegisterRateLimited(string $email): bool
    {
        if (blank($email)) {
            return false;
        }

        $rateLimitingKey = 'filament-register:'.sha1($email);

        if (RateLimiter::tooManyAttempts($rateLimitingKey, maxAttempts: 10)) {
            $this->getRateLimitedNotification(new TooManyRequestsException(
                static::class,
                'register',
                request()->ip(),
                RateLimiter::availableIn($rateLimitingKey),
            ))?->send();

            return true;
        }

        RateLimiter::hit($rateLimitingKey);

        return false;
    }

    protected function completeRegistration(): ?RegistrationResponse
    {
        $this->resetErrorBag();

        $data = $this->normalizedData();
        $this->data = array_merge(is_array($this->data) ? $this->data : [], $data);

        // Re-validate in case Livewire state was tampered between steps.
        $this->validateRegistration($data, skipCaptcha: true);

        // Rate-limit only the final account-create (not step navigation).
        try {
            $this->rateLimit(10);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        if ($this->isRegisterRateLimited($data['email'] ?? '')) {
            return null;
        }

        $user = $this->wrapInDatabaseTransaction(function () use ($data): Model {
            return $this->handleRegistration($data);
        });

        Mail::to($data['email'])->send(new RegistrationWelcomeMail(
            $data['first_name'],
            $data['last_name'],
            url('/portal/login'),
        ));

        event(new Registered($user));

        Filament::auth()->login($user);
        session()->regenerate();

        $this->showNearlyDoneModal = true;

        // Stay on page to show popup; dismissNearlyDone() redirects into the portal.
        return null;
    }

    /**
     * @return array<string, mixed>
     */
    protected function normalizedData(): array
    {
        $data = is_array($this->data) ? $this->data : [];

        foreach ([
            'first_name',
            'last_name',
            'company_name',
            'billing_address',
            'email',
            'town',
            'phone',
            'postal_code',
            'password',
            'cpassword',
            'client_reseller_code',
            'captcha',
            'no_codes',
        ] as $key) {
            $data[$key] = trim((string) ($data[$key] ?? ''));
        }

        $data['email'] = Str::lower($data['email']);

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function validateRegistration(array $data, bool $skipCaptcha = false): void
    {
        $errors = [];

        if ($data['first_name'] === '') {
            $errors['data.first_name'] = 'Enter a first name.';
        }
        if ($data['last_name'] === '') {
            $errors['data.last_name'] = 'Enter a last name.';
        }
        if ($data['company_name'] === '') {
            $errors['data.company_name'] = 'Enter a company name.';
        }
        if ($data['billing_address'] === '') {
            $errors['data.billing_address'] = 'Enter a address.';
        }
        if ($data['email'] === '') {
            $errors['data.email'] = 'Enter a email.';
        } elseif (! preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/', $data['email'])) {
            $errors['data.email'] = 'Enter a valid email.';
        } elseif (User::query()->where('email', $data['email'])->exists()) {
            $errors['data.email'] = 'This email is already exists.';
        }
        if ($data['town'] === '') {
            $errors['data.town'] = 'Enter a town.';
        }
        if ($data['phone'] === '') {
            $errors['data.phone'] = 'Enter a phone.';
        } elseif (! ctype_digit($data['phone'])) {
            $errors['data.phone'] = 'Enter a valid telephone number.';
        }
        if ($data['postal_code'] === '') {
            $errors['data.postal_code'] = 'Enter a postal code.';
        } elseif (! ctype_digit($data['postal_code'])) {
            $errors['data.postal_code'] = 'Enter a valid postal code.';
        }
        if ($data['password'] === '') {
            $errors['data.password'] = 'Enter a password.';
        } elseif (strlen($data['password']) < 5 || strlen($data['password']) > 12) {
            $errors['data.password'] = 'Password must between 5-12 character.';
        }
        if ($data['cpassword'] === '') {
            $errors['data.cpassword'] = 'Enter a confirm password.';
        } elseif ($data['password'] !== '' && $data['password'] !== $data['cpassword']) {
            $errors['data.cpassword'] = 'Password and confirm password does not match.';
        }

        $resellerCode = $data['client_reseller_code'];
        if ($resellerCode !== '') {
            $resellerExists = Client::findByResellerCode($resellerCode) !== null;

            if (! $resellerExists) {
                $errors['data.client_reseller_code'] = 'Enter a valid Reseller Code.';
            }
        }

        if (! $skipCaptcha) {
            if ($data['captcha'] === '') {
                $errors['data.captcha'] = 'Enter a verification code.';
            } elseif (! app(ContactCaptchaService::class)->matches($data['captcha'])) {
                $errors['data.captcha'] = 'Enter a valid captcha.';
                // Refresh image only — do not remount the whole form (that wiped errors/fields).
                $this->captchaNonce = time();
            }
        }

        if ($errors !== []) {
            // Keep wizard on the failing step.
            if (! $skipCaptcha) {
                $this->wizardStep = 1;
            }

            throw ValidationException::withMessages($errors);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRegistration(#[SensitiveParameter] array $data): Model
    {
        return DB::transaction(function () use ($data): User {
            $baseUrl = Str::slug((string) $data['company_name']) ?: 'client';
            $url = $baseUrl;
            $suffix = 1;

            while (Client::query()->where('url', $url)->exists()) {
                $url = $baseUrl.'-'.$suffix;
                $suffix++;
            }

            $fullName = trim($data['first_name'].' '.$data['last_name']);

            $client = Client::query()->create([
                'client_name' => $data['company_name'],
                'contact_person' => $fullName,
                'email' => $data['email'],
                'telephone' => $data['phone'],
                'address' => $data['billing_address'],
                'password' => '',
                'url' => $url,
                'approve' => true,
                'regi_date' => now(),
            ]);

            $user = User::query()->create([
                'name' => $fullName,
                'email' => $data['email'],
                'password' => $data['password'],
                'user_type' => UserType::Portal,
                'admin_role' => null,
            ]);

            ClientUser::query()->create([
                'client_id' => $client->id,
                'auth_user_id' => $user->id,
                'email' => $data['email'],
                'password' => '',
                'role' => ClientUserRole::Primary,
                'status' => true,
                'is_sub_user' => false,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'company_name' => $data['company_name'],
                'billing_address' => $data['billing_address'],
                'town' => $data['town'],
                'phone' => $data['phone'],
                'postal_code' => $data['postal_code'],
                'client_reseller_code' => $data['client_reseller_code'] !== '' ? $data['client_reseller_code'] : null,
                'is_password_change' => true,
                'expire_at' => now()->addYear(),
            ]);

            // Legacy bypass: 1 free code for the year.
            CodePurchase::query()->create([
                'client_id' => $client->id,
                'email' => $data['email'],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'company_name' => $data['company_name'],
                'billing_address' => $data['billing_address'],
                'town' => $data['town'],
                'phone' => $data['phone'],
                'postal_code' => $data['postal_code'],
                'no_of_codes' => 1,
                'per_code_amount' => 0,
                'total_amount' => 0,
                'status' => \App\Enums\CodeOrderStatus::New,
                'enable' => true,
                'free_code' => true,
                'ordered_on' => now(),
            ]);

            return $user->fresh();
        });
    }
}
