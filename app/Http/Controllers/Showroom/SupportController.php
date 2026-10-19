<?php

namespace App\Http\Controllers\Showroom;

use App\Http\Controllers\Controller;
use App\Models\Showroom;
use Illuminate\Contracts\View\View;

class SupportController extends Controller
{
    /**
     * Display the comprehensive dealership support & help center.
     */
    public function index(Showroom $showroom): View
    {
        $showroom->load(['subscription.plan']);

        $faqs = [
            [
                'category' => 'Storefront & Website Builder',
                'question' => 'How do I toggle sections on and off on my public homepage?',
                'answer' => 'Navigate to "Website Builder" in your left back-office menu. Under the "Full Homepage Sections & Layout Management" card, you can toggle any section (Hero Banners, Category Strip, Featured Inventory, Workshop & Services, Promo Banner, Trust Pillars, Customer Stories, Dealership Stats, and Journalism) on or off with instant preview.',
            ],
            [
                'category' => 'Storefront & Website Builder',
                'question' => 'How can I take my public website offline or put it in maintenance mode?',
                'answer' => 'Open "Website Builder" and scroll down to the "Publish Storefront Online" card. Turning this toggle off immediately switches your public customer website to an offline maintenance landing page. Dealership managers can still preview the site while logged in.',
            ],
            [
                'category' => 'Inventory & 360 Studio',
                'question' => 'How does the interactive 360° Motorcycle Viewer work?',
                'answer' => 'When creating or editing a motorcycle in "Motorcycles", you can upload multi-angle sequence images (up to 36 frames). Our turntable renderer will automatically generate an interactive 360° rotatable viewer with zoom capabilities for buyers.',
            ],
            [
                'category' => 'Payments & Subscriptions',
                'question' => 'How do online booking token payments and Razorpay work?',
                'answer' => 'Go to "Settings" and enter your Razorpay Key ID and Secret Key. Once enabled, customers can pay token amounts to reserve motorcycles or book certified service slots online. All transactions appear in your "Payments" ledger.',
            ],
            [
                'category' => 'Workshop & Service Bay',
                'question' => 'How can customers book and track motorcycle services?',
                'answer' => 'Customers can visit your public service page to choose maintenance packages, pick available time slots, and submit their vehicle details. You can track progress and update job cards in "Workshop & Service".',
            ],
            [
                'category' => 'Security & Team Access',
                'question' => 'How do I invite additional staff members or service advisors?',
                'answer' => 'Go to "Settings" and scroll to the "Authorized Showroom Personnel" section. You can invite staff members by email and assign them specific roles (Manager, Sales Executive, Service Advisor).',
            ],
        ];

        return view('showrooms.manage.support', compact('showroom', 'faqs'));
    }
}
