<?php

namespace App\Http\Controllers;

use App\Models\CodePrising;
use App\Models\Gallery;
use App\Models\Setting;
use App\Models\Testimonial;
use Illuminate\Http\Request;
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

    public function submitContact(Request $request): View
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        return view('marketing.contact', [
            'contactEmail' => Setting::valueFor('contact_email') ?? 'admin@scanlink.com',
            'submitted' => true,
        ]);
    }

    public function home(): View
    {
        return view('marketing.home', [
            'testimonials' => Testimonial::query()->latest('id')->limit(6)->get(),
            'gallery' => Gallery::query()->latest('id')->limit(8)->get(),
        ]);
    }
}
