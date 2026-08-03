<?php

namespace App\Http\Controllers;

use App\Enums\UserType;
use App\Mail\ContactUsMessage;
use App\Models\CodePrising;
use App\Models\Gallery;
use App\Models\HowToTutorial;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\User;
use App\Services\ContactCaptchaService;
use App\Support\SystemNotifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class MarketingController extends Controller
{
    /**
     * @return list<array{title: string, url: string}>
     */
    protected function howToLinks(): array
    {
        return HowToTutorial::catalog();
    }

    /**
     * @return array<string, mixed>
     */
    protected function marketingLayoutData(): array
    {
        $user = Auth::user();
        $isPortalUser = $user instanceof User && $user->user_type === UserType::Portal;

        return [
            'howToLinks' => $this->howToLinks(),
            'isPortalUser' => $isPortalUser,
            'portalUserEmail' => $isPortalUser ? $user->email : null,
        ];
    }

    public function contact(): View|RedirectResponse
    {
        $user = Auth::user();

        // Logged-in portal users use the Filament contact page (sidebar/header theme).
        if ($user instanceof User && $user->user_type === UserType::Portal) {
            return redirect('/portal/contact');
        }

        return view('marketing.contact', [
            ...$this->marketingLayoutData(),
            'contactEmail' => Setting::valueFor('contact_email') ?? 'admin@scanlink.com',
        ]);
    }

    public function pricing(): View
    {
        return view('marketing.pricing', $this->marketingLayoutData());
    }

    public function calculatePricing(Request $request): JsonResponse
    {
        $raw = trim((string) $request->input('no_codes', ''));
        $errors = [];

        if ($raw === '') {
            $errors['no_codes'] = 'Enter a number of code required.';
        } elseif (! ctype_digit($raw)) {
            $errors['no_codes'] = 'Enter a number of code required.';
        } elseif ((int) $raw > 1000) {
            $errors['no_codes'] = 'Enter a number of code less than 1000.';
        }

        $perMonth = '0.00';
        $annual = '0.00';

        if ($errors === []) {
            $qty = (int) $raw;
            $tier = CodePrising::query()
                ->where('code_min_qty', '<=', $qty)
                ->where('code_max_qty', '>=', $qty)
                ->orderBy('code_min_qty')
                ->first();

            if ($tier) {
                $amount = (float) $tier->amount;
                $perMonth = number_format($amount, 2, '.', '');
                $annual = number_format($amount * $qty * 12, 2, '.', '');
            }
        }

        // Legacy register/getData response shape (totalsubscrption spelling preserved).
        return response()->json([
            'errors' => $errors,
            'amount' => $perMonth,
            'totalsubscrption' => $annual,
        ]);
    }

    public function faq(): View
    {
        return view('marketing.faq', $this->marketingLayoutData());
    }

    public function privacy(): View
    {
        return view('marketing.privacy', $this->marketingLayoutData());
    }

    public function terms(): View
    {
        return view('marketing.terms', $this->marketingLayoutData());
    }

    public function submitContact(Request $request, ContactCaptchaService $captcha): RedirectResponse
    {
        $name = trim((string) $request->input('name', ''));
        $email = trim((string) $request->input('email', ''));
        $comments = trim((string) ($request->input('comments') ?? $request->input('message', '')));
        $captchaAnswer = trim((string) $request->input('captcha', ''));

        if ($name === '' || $email === '' || $comments === '' || $captchaAnswer === '') {
            return redirect()
                ->route('marketing.contact')
                ->withInput($request->except('captcha'))
                ->withErrors(['form' => 'All fields are required...']);
        }

        if (! $captcha->valid($captchaAnswer)) {
            return redirect()
                ->route('marketing.contact')
                ->withInput($request->except('captcha'))
                ->withErrors(['captcha' => 'Invalid Verification Code...']);
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
            trim($name).' ('.$email.') sent a message via the website contact form.',
            'heroicon-o-envelope',
            'info',
        );

        return redirect()
            ->route('marketing.contact')
            ->with('contact_submitted', true);
    }

    public function home(): View
    {
        return view('marketing.home', [
            'testimonials' => Testimonial::query()->latest('id')->limit(6)->get(),
            'gallery' => Gallery::query()->latest('id')->limit(8)->get(),
            ...$this->marketingLayoutData(),
        ]);
    }

    public function howTo(): View
    {
        return view('marketing.how-to', [
            'tutorials' => $this->howToLinks(),
            ...$this->marketingLayoutData(),
        ]);
    }
}
