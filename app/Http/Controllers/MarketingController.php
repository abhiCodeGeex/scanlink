<?php

namespace App\Http\Controllers;

use App\Models\CodePrising;
use App\Models\Gallery;
use App\Models\Setting;
use App\Models\Testimonial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class MarketingController extends Controller
{
    public function contact(): View
    {
        return view('marketing.contact', [
            'contactEmail' => Setting::valueFor('contact_email') ?? 'admin@scanlink.com',
        ]);
    }

    public function pricing(): View
    {
        return view('marketing.pricing', [
            'tiers' => CodePrising::query()->orderBy('code_min_qty')->get(),
        ]);
    }

    public function faq(): View
    {
        return view('marketing.faq');
    }

    public function privacy(): View
    {
        return view('marketing.privacy');
    }

    public function terms(): View
    {
        return view('marketing.terms');
    }

    public function submitContact(Request $request): RedirectResponse|View
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $contactEmail = Setting::valueFor('contact_email') ?? 'admin@scanlink.com';

        try {
            $body = "Contact form submission\n\n"
                ."Name: {$validated['name']}\n"
                ."Email: {$validated['email']}\n\n"
                .$validated['message'];

            Mail::raw($body, function ($message) use ($contactEmail, $validated): void {
                $message->to($contactEmail)
                    ->replyTo($validated['email'], $validated['name'])
                    ->subject('ScanLink contact form: '.$validated['name']);
            });
        } catch (\Throwable $exception) {
            Log::warning('Contact form mail failed', [
                'email' => $validated['email'],
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
