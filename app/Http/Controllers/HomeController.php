<?php

namespace App\Http\Controllers;

use App\Mail\ContactFormMail;
use App\Models\Banner;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Inqiry;
use App\Models\Product;
use App\Models\ProductInquiry;
use App\Models\ProductStock;
use App\Models\Review;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        $title = get_setting('home_meta_title');
        $description = get_setting('home_meta_description');
        SEOTools::setTitle($title);
        SEOTools::setDescription($description);
        SEOTools::setCanonical(url()->current());
        SEOTools::opengraph()->setUrl(url()->current());
        SEOTools::opengraph()->addProperty('type', 'website');
        SEOTools::opengraph()->addProperty('url', url()->current());


        // Featured Products
        $featuredProducts = Product::with('categories')->where('is_featured', true)->where('is_active', true)->orderBy('sort_order')->latest()->take(8)->get();

        // Popular Categories
        $popularCategories = Category::where('status', true)->where('featured', true)->whereNull('parent_id')->orderBy('created_at', 'asc')->take(4)->get();
        // Main Banner
        $mainBanner =  Banner::where('is_active', true)->orderBy('sort_order')->get();

        // Blog Posts
        $blogPosts = Blog::where('is_published', true)->orderBy('created_at', 'desc')->take(2)->get();

        // Testimonials
        $testimonials =  Review::where('is_approved', true)->where('show_in_home', true)->orderBy('created_at', 'desc')->take(8)->get();

        return view('frontend.home', compact('featuredProducts', 'popularCategories', 'mainBanner', 'blogPosts', 'testimonials'));
    }

    public function about()
    {
        $title = config('app.name') . ' | About Us';
        $description = "Learn about Nature's Virtue - your trusted source for pure dehydrated foods and herbal products. Discover our commitment to quality, sustainability, and bringing nature's goodness to your table.";
        SEOTools::setTitle($title);
        SEOTools::setDescription($description);
        SEOTools::setCanonical(url()->current());
        SEOTools::opengraph()->setUrl(url()->current());
        SEOTools::opengraph()->addProperty('type', 'website');
        SEOTools::opengraph()->addProperty('url', url()->current());
        SEOTools::opengraph()->setDescription($description);
        SEOTools::twitter()->setDescription($description);

        $breadcrumbs = [
            ['url' => route('home'), 'label' => 'Home'],
            ['url' => route('about'), 'label' => 'About Us'],
        ];
        return view('frontend.about', compact('title', 'description', 'breadcrumbs'));
    }

    public function contact()
    {

        $title = config('app.name') . ' | Contact Us';
        $description = "Get in touch with Nature's Virtue. We're here to help with your inquiries about our pure dehydrated foods and herbal products. Contact our team for product information, bulk orders, or any questions.";

        SEOTools::setTitle($title);
        SEOTools::setDescription($description);
        SEOTools::setCanonical(url()->current());
        SEOTools::opengraph()->setUrl(url()->current());
        SEOTools::opengraph()->addProperty('type', 'website');
        SEOTools::opengraph()->addProperty('url', url()->current());
        SEOTools::opengraph()->setDescription('Contact Us');

        return view('frontend.contact');
    }

    /**
     * Store Inquiry
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeInquiry(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|max:255',
            'additional_comments' => 'nullable|string',
            'products' => 'required|array',
            'products.*' => 'required|string|max:255|distinct',
            'variants' => 'required|array',
            'variants.*' => 'required|integer|exists:product_stocks,id',
            'quantities' => 'required|array',
            'quantities.*' => 'required|integer|min:1',
        ],
        [
            'quantities.*.required' => 'Please enter a quantity for each selected product.',
            'quantities.*.min' => 'Quantity must be at least 1.',
        ]);



        try {

            DB::beginTransaction();
            // Create Inquiry
            $inquiry = new Inqiry();
            $inquiry->company = $request->company_name;
            $inquiry->name = $request->contact_person;
            $inquiry->email = $request->contact_email;
            $inquiry->phone = $request->contact_phone;
            $inquiry->message = $request->additional_comments;
            $inquiry->save();

            $items = [];

            // Attach products with variants
            foreach ($request->products as $key => $productId) {
                $variantId = $request->variants[$key] ?? null;
                $quantity = $request->quantities[$key];

                // Validate that the variant belongs to the product
                if ($variantId) {
                    $variant = ProductStock::where('id', $variantId)
                        ->where('product_id', $productId)
                        ->first();

                    if (!$variant) {
                        throw new \Exception("Invalid variant selected for product.");
                    }
                }

                $items[] = [
                    'product_id' => $productId,
                    'variant_id' => $variantId,
                    'quantity' => $quantity,
                    'inqiry_id' => $inquiry->id,
                ];
            }

            if (!empty($items)) {
                $inquiry->products()->insert($items);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Inquiry submitted successfully.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Inquiry Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    public function terms()
    {
        $title = config('app.name') . ' | Terms & Conditions';
        $description = "Read our terms and conditions for Nature's Virtue. We provide clear guidelines for our products and services. If you have any questions, please contact us.";
        SEOTools::setTitle($title);
        SEOTools::setDescription($description);
        SEOTools::setCanonical(url()->current());
        SEOTools::opengraph()->setUrl(url()->current());

        $breadcrumbs = [
            ['url' => route('home'), 'label' => 'Home'],
            ['url' => route('terms'), 'label' => 'Terms & Conditions'],
        ];
        return view('frontend.terms', compact('title', 'description', 'breadcrumbs'));
    }

    public function returnPolicy()
    {
        $title = config('app.name') . ' | Return Policy';
        $description = "Learn about our return policy for Nature's Virtue. We offer a 30-day return policy for all products. If you're not satisfied, we'll help you find a solution.";
        SEOTools::setTitle($title);
        SEOTools::setDescription($description);
        SEOTools::setCanonical(url()->current());
        SEOTools::opengraph()->setUrl(url()->current());

        $breadcrumbs = [
            ['url' => route('home'), 'label' => 'Home'],
            ['url' => route('return-policy'), 'label' => 'Return Policy'],
        ];
        return view('frontend.return-policy', compact('title', 'description', 'breadcrumbs'));
    }

    public function privacyPolicy()
    {
        $title = config('app.name') . ' | Privacy Policy';
        $description = "Read our privacy policy for Nature's Virtue. We respect your privacy and protect your personal information. If you have any questions, please contact us.";
        SEOTools::setTitle($title);
        SEOTools::setDescription($description);
        SEOTools::setCanonical(url()->current());
        SEOTools::opengraph()->setUrl(url()->current());

        $breadcrumbs = [
            ['url' => route('home'), 'label' => 'Home'],
            ['url' => route('privacy-policy'), 'label' => 'Privacy Policy'],
        ];
        return view('frontend.privacy-policy', compact('title', 'description', 'breadcrumbs'));
    }

    public function blog($slug)
    {
        $blog = Blog::where('is_published', true)->where('slug', $slug)->first();
        if (!$blog) {
            toast()->error('Blog not found');
            return redirect()->route('home');
        }
        $title = $blog->meta_title;
        $description = $blog->meta_description;
        SEOTools::setTitle($title);
        SEOTools::setDescription($description);
        SEOTools::setCanonical(url()->current());
        SEOTools::opengraph()->setUrl(url()->current());
        SEOTools::opengraph()->addProperty('type', 'website');
        SEOTools::opengraph()->addProperty('url', url()->current());

        $breadcrumbs = [
            ['url' => route('home'), 'label' => 'Home'],
            ['url' => route('home'), 'label' => 'Blog'],
            ['url' => route('blog', $blog->id), 'label' => $blog->title],
        ];
        return view('frontend.blog', compact('blog', 'title', 'description', 'breadcrumbs'));
    }

    public function contactStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'subject' => 'required|string',
            'message' => 'required|string',
            'g-recaptcha-response' => 'required|captcha',
        ], [
            'g-recaptcha-response.required' => 'Please complete the reCAPTCHA verification.',
            'g-recaptcha-response.captcha' => 'Captcha verification failed, please try again.',
        ]);

        Mail::to(config('mail.from.address'))->send(new ContactFormMail($request->all()));

        return back()->with('success', 'Thank you! Your message has been sent successfully.');
    }
}
