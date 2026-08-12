<?php

namespace App\Http\Controllers;

use App\Enums\UserType;
use App\Mail\ContactUsMessage;
use App\Mail\EnquiryMessage;
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
            'register' => url('/portal/register'),
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

    public function packaging(): View
    {
        return view('marketing.packaging', $this->marketingLayoutData());
    }

    public function forYou(): View
    {
        return view('marketing.for-you', $this->marketingLayoutData());
    }

    public function workplace(): View
    {
        return view('marketing.workplace', $this->marketingLayoutData());
    }

    public function forms(): View
    {
        return view('marketing.forms', $this->marketingLayoutData());
    }

    public function mobileVideo(): View
    {
        return view('marketing.mobile-video', $this->marketingLayoutData());
    }

    public function enquiry(): View
    {
        return view('marketing.enquiry', $this->marketingLayoutData());
    }

    public function submitEnquiry(Request $request): RedirectResponse
    {
        $companyName = trim((string) $request->input('companyName', ''));
        $contactName = trim((string) $request->input('contactName', ''));
        $email = trim((string) $request->input('email', ''));

        if ($companyName === '' || $contactName === '' || $email === '') {
            return redirect()
                ->route('marketing.enquiry')
                ->withInput()
                ->withErrors(['form' => 'Company name, contact name and email are required.']);
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()
                ->route('marketing.enquiry')
                ->withInput()
                ->withErrors(['email' => 'Please enter a valid email address.']);
        }

        $interestLabels = [
            'facilityAsset' => 'Facility Asset Management',
            'plantEquipment' => 'Plant & Equipment Document Management',
            'productProcedure' => 'Product & Procedure Management',
            'communicationTraining' => 'Communication Training',
            'videoProduction' => 'Video Production, or Re-Editing for Smartphone use',
            'QRCodeVisual' => 'QR Code Visual Communication',
            'allOfAbove' => 'All of the above',
        ];

        $interests = [];
        foreach ($interestLabels as $field => $label) {
            if ($request->boolean($field)) {
                $interests[] = $label;
            }
        }

        $enquiryEmail = Setting::valueFor('enquiry_email')
            ?? Setting::valueFor('contact_email')
            ?? 'admin@scanlink.com';

        try {
            Mail::to($enquiryEmail)->send(new EnquiryMessage(
                companyName: $companyName,
                contactName: $contactName,
                email: $email,
                tel: trim((string) $request->input('tel', '')),
                address: trim((string) $request->input('address', '')),
                industryType: trim((string) $request->input('industryType', '')),
                companySize: trim((string) $request->input('companySize', '')),
                briefDescription: trim((string) $request->input('briefDescription', '')),
                interests: $interests,
                comments: trim((string) $request->input('comments', '')),
            ));
        } catch (\Throwable $exception) {
            Log::warning('Enquiry form mail failed', [
                'email' => $email,
                'message' => $exception->getMessage(),
            ]);
        }

        SystemNotifier::toAdmins(
            'New solutions enquiry',
            trim($contactName).' ('.$email.') submitted a solutions enquiry via the website.',
            'heroicon-o-inbox-arrow-down',
            'info',
        );

        return redirect()
            ->route('marketing.enquiry')
            ->with('enquiry_submitted', true);
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
