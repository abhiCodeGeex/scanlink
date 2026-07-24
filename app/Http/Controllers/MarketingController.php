<?php

namespace App\Http\Controllers;

use App\Enums\UserType;
use App\Mail\ContactUsMessage;
use App\Models\CodePrising;
use App\Models\Gallery;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\User;
use App\Services\ContactCaptchaService;
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
        return [
            ['title' => 'Create a ScanLink account', 'url' => 'https://www.youtube.com/embed/9aTjweHyAWw?rel=0'],
            ['title' => 'Getting Started', 'url' => 'https://www.youtube.com/embed/o6NxTt0CmYI?rel=0'],
            ['title' => 'Register a new code', 'url' => 'https://www.youtube.com/embed/GZ12nXTO7_w?rel=0'],
            ['title' => 'Upload a logo', 'url' => 'https://www.youtube.com/embed/hGrBYsys2Oo?rel=0'],
            ['title' => 'Upload a video', 'url' => 'https://www.youtube.com/embed/H33caspIlcc?rel=0'],
            ['title' => 'Add text and phone numbers', 'url' => 'https://www.youtube.com/embed/CZx8xplEfoU?rel=0'],
            ['title' => 'Upload pictures', 'url' => 'https://www.youtube.com/embed/GshHCp9F0wU?rel=0'],
            ['title' => 'Upload documents', 'url' => 'https://www.youtube.com/embed/ujiEr65yg30?rel=0'],
            ['title' => 'Add web link buttons', 'url' => 'https://www.youtube.com/embed/id0I8j8RTuY?rel=0'],
            ['title' => 'Add social media and email share buttons', 'url' => 'https://www.youtube.com/embed/qOi6tSBsII4?rel=0'],
            ['title' => 'Create pop up messages to collect data', 'url' => 'https://www.youtube.com/embed/C_vH14MFtXA?rel=0'],
            ['title' => 'Select a code type - QR or Data matrix', 'url' => 'https://www.youtube.com/embed/jCeyQOfm7uc?rel=0'],
            ['title' => 'Feature code profile number on mobile display', 'url' => 'https://www.youtube.com/embed/eJtzHbZoCPw?rel=0'],
            ['title' => 'Password protect a code profile', 'url' => 'https://www.youtube.com/embed/KcXJnxuMVyc?rel=0'],
            ['title' => 'Link a code to a URL', 'url' => 'https://www.youtube.com/embed/uEDTnBPUk28?rel=0'],
            ['title' => 'Delete a code profile', 'url' => 'https://www.youtube.com/embed/Gu12cnKn16s?rel=0'],
            ['title' => 'View and download scan activity', 'url' => 'https://www.youtube.com/embed/Y0bVkzDA5Rc?rel=0'],
            ['title' => 'Create a form', 'url' => 'https://www.youtube.com/embed/cYQnzxkp528?rel=0'],
        ];
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

    public function calculatePricing(Request $request): \Illuminate\Http\JsonResponse
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

        return redirect()
            ->route('marketing.contact')
            ->with('contact_submitted', true);
    }

    public function home(): View
    {
        return view('marketing.home', [
            'testimonials' => Testimonial::query()->latest('id')->limit(6)->get(),
            'gallery' => Gallery::query()->latest('id')->limit(8)->get(),
            'howToLinks' => [
                ['title' => 'Create a ScanLink account', 'url' => 'https://www.youtube.com/watch?v=9aTjweHyAWw'],
                ['title' => 'Getting Started', 'url' => 'https://www.youtube.com/watch?v=o6NxTt0CmYI'],
                ['title' => 'Register a new code', 'url' => 'https://www.youtube.com/watch?v=GZ12nXTO7_w'],
                ['title' => 'Upload a logo', 'url' => 'https://www.youtube.com/watch?v=hGrBYsys2Oo'],
                ['title' => 'Upload a video', 'url' => 'https://www.youtube.com/watch?v=H33caspIlcc'],
                ['title' => 'Create a form', 'url' => 'https://www.youtube.com/watch?v=cYQnzxkp528'],
            ],
        ]);
    }

    public function howTo(): View
    {
        return view('marketing.how-to', [
            'tutorials' => [
                ['title' => 'Create a ScanLink account', 'url' => 'https://www.youtube.com/watch?v=9aTjweHyAWw'],
                ['title' => 'Getting Started', 'url' => 'https://www.youtube.com/watch?v=o6NxTt0CmYI'],
                ['title' => 'Register a new code', 'url' => 'https://www.youtube.com/watch?v=GZ12nXTO7_w'],
                ['title' => 'Upload a logo', 'url' => 'https://www.youtube.com/watch?v=hGrBYsys2Oo'],
                ['title' => 'Upload a video', 'url' => 'https://www.youtube.com/watch?v=H33caspIlcc'],
                ['title' => 'Add text and phone numbers', 'url' => 'https://www.youtube.com/watch?v=CZx8xplEfoU'],
                ['title' => 'Upload pictures', 'url' => 'https://www.youtube.com/watch?v=GshHCp9F0wU'],
                ['title' => 'Upload documents', 'url' => 'https://www.youtube.com/watch?v=ujiEr65yg30'],
                ['title' => 'Add web link buttons', 'url' => 'https://www.youtube.com/watch?v=id0I8j8RTuY'],
                ['title' => 'Add social media and email share buttons', 'url' => 'https://www.youtube.com/watch?v=qOi6tSBsII4'],
                ['title' => 'Create pop up messages to collect data', 'url' => 'https://www.youtube.com/watch?v=C_vH14MFtXA'],
                ['title' => 'Select a code type - QR or Data matrix', 'url' => 'https://www.youtube.com/watch?v=jCeyQOfm7uc'],
                ['title' => 'Feature code profile number on mobile display', 'url' => 'https://www.youtube.com/watch?v=eJtzHbZoCPw'],
                ['title' => 'Password protect a code profile', 'url' => 'https://www.youtube.com/watch?v=KcXJnxuMVyc'],
                ['title' => 'Link a code to a URL', 'url' => 'https://www.youtube.com/watch?v=uEDTnBPUk28'],
                ['title' => 'Delete a code profile', 'url' => 'https://www.youtube.com/watch?v=Gu12cnKn16s'],
                ['title' => 'View and download scan activity', 'url' => 'https://www.youtube.com/watch?v=Y0bVkzDA5Rc'],
                ['title' => 'Create a form', 'url' => 'https://www.youtube.com/watch?v=cYQnzxkp528'],
            ],
        ]);
    }
}
