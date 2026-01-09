@extends('user.layouts.app')

@section('title', 'SportWear - Platform Alat Olahraga Premium')

@section('content')
    <!-- Hero Section - Elegant Design -->
    <section class="relative min-h-screen flex items-center overflow-hidden bg-gradient-to-br from-gray-50 via-white to-blue-50 py-12">
        <!-- Elegant Background Pattern -->
        <div class="absolute inset-0">
            <div class="absolute top-0 left-0 w-full h-64 bg-gradient-to-b from-primary/5 to-transparent"></div>
            <div class="absolute bottom-0 left-0 w-full h-64 bg-gradient-to-t from-accent/5 to-transparent"></div>
            
            <!-- Geometric Patterns -->
            <div class="absolute inset-0 opacity-5">
                <div class="absolute inset-0" style="background-image: 
                    linear-gradient(30deg, var(--primary) 2px, transparent 2px),
                    linear-gradient(60deg, var(--accent) 1px, transparent 1px);
                    background-size: 60px 60px;"></div>
            </div>
            
            <!-- Floating Elegant Elements -->
            <div class="absolute top-1/4 right-1/4">
                <div class="w-32 h-32 bg-gradient-to-br from-primary/10 to-accent/10 rounded-full blur-3xl"></div>
            </div>
            <div class="absolute bottom-1/3 left-1/4">
                <div class="w-40 h-40 bg-gradient-to-br from-accent/10 to-transparent rounded-full blur-3xl"></div>
            </div>
        </div>
        
        <div class="relative z-10 container mx-auto px-4 lg:px-8">
            <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
                <!-- Left Content -->
                <div class="lg:w-1/2 text-center lg:text-left" data-aos="fade-right">
                    <!-- Elegant Badge -->
                    <div class="inline-flex items-center gap-3 px-6 py-3 bg-white border border-gray-200 rounded-full mb-8 shadow-sm hover:shadow-md transition-shadow duration-300">
                        <span class="w-2 h-2 bg-accent rounded-full"></span>
                        <span class="text-sm font-semibold text-primary uppercase tracking-wider">Premium Platform</span>
                        <i class="fas fa-gem text-accent ml-2"></i>
                    </div>
                    
                    <!-- Main Headline -->
                    <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold leading-tight mb-6">
                        <span class="text-gray-900">Elevate Your</span>
                        <span class="block mt-2">
                            <span class="bg-gradient-to-r from-primary via-primary to-accent bg-clip-text text-transparent">
                                Sports Experience
                            </span>
                        </span>
                    </h1>
                    
                    <!-- Subtitle -->
                    <p class="text-lg md:text-xl text-gray-600 mb-10 max-w-xl mx-auto lg:mx-0 leading-relaxed">
                        Discover premium sports equipment and professional gear. 
                        Rent or purchase with confidence.
                    </p>
                    
                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 mb-16 justify-center lg:justify-start">
                        <a href="{{ route('user.produk.index') }}" 
                           class="group relative overflow-hidden bg-gradient-to-r from-primary to-primary-dark text-white font-semibold py-4 px-8 rounded-xl transition-all duration-300 hover:shadow-lg hover:shadow-primary/20 flex items-center justify-center gap-3">
                            <i class="fas fa-shopping-bag"></i>
                            <span>Browse Products</span>
                            <i class="fas fa-arrow-right transform group-hover:translate-x-2 transition-transform duration-300"></i>
                        </a>
                        
                        <a href="{{ route('user.sewa.index') }}" 
                           class="group bg-white border-2 border-primary text-primary font-semibold py-4 px-8 rounded-xl transition-all duration-300 hover:bg-primary hover:text-white hover:shadow-lg flex items-center justify-center gap-3">
                            <i class="fas fa-calendar-check"></i>
                            <span>Rent Equipment</span>
                            <i class="fas fa-external-link-alt transform group-hover:rotate-12 transition-transform"></i>
                        </a>
                    </div>
                    
                    <!-- Stats Grid -->
                    <div class="grid grid-cols-3 gap-6 pt-8 border-t border-gray-200">
                        @foreach([
                            ['value' => '500+', 'label' => 'Premium Items', 'icon' => 'shapes', 'color' => 'text-primary'],
                            ['value' => '98%', 'label' => 'Satisfaction', 'icon' => 'smile', 'color' => 'text-green-500'],
                            ['value' => '24/7', 'label' => 'Support', 'icon' => 'headset', 'color' => 'text-accent']
                        ] as $stat)
                        <div class="text-center" data-aos="fade-up" data-aos-delay="{{ $loop->index * 150 }}">
                            <div class="flex flex-col items-center">
                                <div class="w-12 h-12 rounded-xl bg-white border border-gray-200 flex items-center justify-center mb-3 shadow-sm">
                                    <i class="fas fa-{{ $stat['icon'] }} {{ $stat['color'] }} text-lg"></i>
                                </div>
                                <h3 class="text-gray-900 font-bold text-2xl mb-1">{{ $stat['value'] }}</h3>
                                <p class="text-gray-600 text-sm">{{ $stat['label'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Right Image -->
                <div class="lg:w-1/2" data-aos="fade-left" data-aos-delay="300">
                    <div class="relative">
                        <!-- Main Image Card -->
                        <div class="relative rounded-2xl overflow-hidden shadow-xl border border-gray-200">
                            <img src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80" 
                                 alt="Premium Sports Equipment" 
                                 class="w-full h-[500px] object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-gray-900/30 via-transparent to-transparent"></div>
                            
                            <!-- Image Badge -->
                            <div class="absolute top-6 left-6">
                                <span class="px-4 py-2 bg-white text-primary font-semibold rounded-lg text-sm shadow-sm">
                                    <i class="fas fa-award text-accent mr-2"></i> Premium Selection
                                </span>
                            </div>
                        </div>
                        
                        <!-- Floating Info Cards -->
                        <div class="absolute -bottom-4 -left-4 bg-white rounded-xl p-5 shadow-lg border border-gray-200" data-aos="fade-up" data-aos-delay="500">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-shipping-fast text-primary text-lg"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900">Fast Delivery</p>
                                    <p class="text-sm text-gray-600">Same day service</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="absolute -top-4 -right-4 bg-white rounded-xl p-5 shadow-lg border border-gray-200" data-aos="fade-up" data-aos-delay="700">
                            <div class="text-center">
                                <div class="flex items-center justify-center gap-1 mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star text-yellow-400"></i>
                                    @endfor
                                </div>
                                <p class="font-bold text-gray-900">4.9/5</p>
                                <p class="text-sm text-gray-600">Rated Excellent</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2">
            <div class="flex flex-col items-center">
                <span class="text-gray-500 text-sm mb-2">Scroll to explore</span>
                <div class="animate-bounce w-6 h-10 border-2 border-gray-300 rounded-full flex justify-center">
                    <div class="w-1 h-3 bg-primary rounded-full mt-2"></div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Categories Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                    Explore <span class="text-primary">Categories</span>
                </h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                    Browse our curated collection of premium sports equipment
                </p>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach([
                    ['name' => 'Fitness', 'icon' => 'dumbbell', 'items' => '68'],
                    ['name' => 'Cycling', 'icon' => 'bicycle', 'items' => '42'],
                    ['name' => 'Soccer', 'icon' => 'futbol', 'items' => '35'],
                    ['name' => 'Tennis', 'icon' => 'table-tennis', 'items' => '28'],
                    ['name' => 'Swimming', 'icon' => 'swimmer', 'items' => '31'],
                    ['name' => 'Running', 'icon' => 'running', 'items' => '56']
                ] as $category)
                <div data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    <a href="#" class="group block">
                        <div class="bg-white rounded-xl p-6 text-center border border-gray-200 hover:border-primary hover:shadow-lg transition-all duration-300">
                            <div class="w-16 h-16 mx-auto bg-primary/10 rounded-xl flex items-center justify-center mb-4 group-hover:bg-primary/20 transition-colors">
                                <i class="fas fa-{{ $category['icon'] }} text-primary text-xl"></i>
                            </div>
                            <h3 class="font-semibold text-gray-900 mb-2">{{ $category['name'] }}</h3>
                            <p class="text-gray-600 text-sm">{{ $category['items'] }} items</p>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    
    <!-- Featured Products -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-12" data-aos="fade-up">
                <div>
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                        Featured <span class="text-primary">Products</span>
                    </h2>
                    <p class="text-gray-600 text-lg">Premium selection for serious athletes</p>
                </div>
                
                <a href="{{ route('user.produk.index') }}" 
                   class="mt-6 lg:mt-0 flex items-center gap-3 text-primary font-semibold hover:text-primary-dark transition-colors">
                    <span>View all products</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($featuredProducts as $product)
                <div data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="bg-white rounded-xl overflow-hidden border border-gray-200 hover:shadow-lg transition-all duration-300">
                        <!-- Product Image -->
                        <div class="relative h-48 overflow-hidden">
                            <img src="{{ $product->image_url }}" 
                                 alt="{{ $product->name }}"
                                 class="w-full h-full object-cover hover:scale-110 transition-transform duration-500">
                            <div class="absolute top-4 left-4">
                                <span class="px-3 py-1 bg-white text-primary text-xs font-semibold rounded-lg shadow-sm">
                                    Featured
                                </span>
                            </div>
                        </div>
                        
                        <!-- Product Info -->
                        <div class="p-6">
                            <h3 class="font-semibold text-gray-900 mb-2">{{ $product->name }}</h3>
                            <p class="text-gray-600 text-sm mb-4">{{ Str::limit($product->description, 60) }}</p>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-bold text-primary">{{$product->price }}</span>
                                <button class="px-4 py-2 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary-dark transition-colors">
                                    <i class="fas fa-shopping-cart mr-2"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    
    <!-- How It Works -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                    How It <span class="text-primary">Works</span>
                </h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                    Simple process to get your sports equipment
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach([
                    ['step' => '01', 'title' => 'Browse Catalog', 'desc' => 'Explore our premium collection', 'icon' => 'search'],
                    ['step' => '02', 'title' => 'Select Items', 'desc' => 'Choose rent or purchase option', 'icon' => 'check-circle'],
                    ['step' => '03', 'title' => 'Complete Order', 'desc' => 'Secure checkout process', 'icon' => 'credit-card'],
                    ['step' => '04', 'title' => 'Receive Gear', 'desc' => 'Fast delivery to your doorstep', 'icon' => 'shipping-fast']
                ] as $step)
                <div class="relative" data-aos="fade-up" data-aos-delay="{{ $loop->index * 150 }}">
                    <div class="bg-white rounded-xl p-8 border border-gray-200 hover:border-primary hover:shadow-lg transition-all duration-300">
                        <!-- Step Number -->
                        <div class="w-16 h-16 bg-primary/10 rounded-xl flex items-center justify-center mb-6">
                            <span class="text-primary font-bold text-xl">{{ $step['step'] }}</span>
                        </div>
                        
                        <!-- Content -->
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-{{ $step['icon'] }} text-primary text-lg"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 text-lg mb-2">{{ $step['title'] }}</h3>
                                <p class="text-gray-600">{{ $step['desc'] }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Arrow for Desktop -->
                    @if(!$loop->last)
                    <div class="hidden lg:block absolute top-1/2 -right-4 transform -translate-y-1/2">
                        <i class="fas fa-arrow-right text-gray-300 text-xl"></i>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </section>
    
    <!-- Testimonials -->
    <section class="py-20 bg-primary text-white">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-4xl md:text-5xl font-bold mb-4">
                    What Our <span class="text-accent">Clients Say</span>
                </h2>
                <p class="text-primary-light text-lg max-w-2xl mx-auto">
                    Trusted by athletes and professionals
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach([
                    ['name' => 'Andi Pratama', 'role' => 'Professional Athlete', 'text' => 'Exceptional quality and service. Highly recommended!'],
                    ['name' => 'Sari Dewi', 'role' => 'Fitness Trainer', 'text' => 'Premium equipment that exceeds expectations.'],
                    ['name' => 'Budi Santoso', 'role' => 'Sports Coach', 'text' => 'Reliable service and top-notch gear quality.']
                ] as $testimonial)
                <div data-aos="fade-up" data-aos-delay="{{ $loop->index * 150 }}">
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-8 border border-white/20">
                        <!-- Rating -->
                        <div class="flex text-accent mb-6">
                            @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star"></i>
                            @endfor
                        </div>
                        
                        <!-- Testimonial Text -->
                        <p class="text-primary-light mb-8 italic">"{{ $testimonial['text'] }}"</p>
                        
                        <!-- Author -->
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                                <span class="font-bold">{{ substr($testimonial['name'], 0, 1) }}</span>
                            </div>
                            <div>
                                <h6 class="font-semibold">{{ $testimonial['name'] }}</h6>
                                <p class="text-primary-light text-sm">{{ $testimonial['role'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    
    <!-- CTA Section -->
    <section class="py-20">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="bg-gradient-to-r from-primary to-primary-dark rounded-2xl p-12 text-center" data-aos="zoom-in">
                <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">
                    Ready to Elevate Your Game?
                </h2>
                <p class="text-primary-light text-xl mb-12 max-w-2xl mx-auto">
                    Join thousands of satisfied customers using our premium sports equipment
                </p>
                
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    @auth
                    <a href="{{ route('user.produk.index') }}" 
                       class="bg-white text-primary font-semibold py-4 px-8 rounded-xl hover:bg-gray-50 transition-colors shadow-lg">
                       <i class="fas fa-store mr-3"></i>Start Shopping
                    </a>
                    @else
                    <a href="{{ route('register') }}" 
                       class="bg-white text-primary font-semibold py-4 px-8 rounded-xl hover:bg-gray-50 transition-colors shadow-lg">
                       <i class="fas fa-user-plus mr-3"></i>Create Account
                    </a>
                    <a href="{{ route('login') }}" 
                       class="bg-transparent border-2 border-white text-white font-semibold py-4 px-8 rounded-xl hover:bg-white/10 transition-colors">
                       <i class="fas fa-sign-in-alt mr-3"></i>Sign In
                    </a>
                    @endauth
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
<style>
:root {
    --primary: #1A365D;
    --primary-dark: #153255;
    --primary-light: rgba(26, 54, 93, 0.1);
    --accent: #D69E2E;
}

.bg-primary {
    background-color: var(--primary);
}

.bg-primary-dark {
    background-color: var(--primary-dark);
}

.bg-primary-light {
    background-color: var(--primary-light);
}

.text-primary {
    color: var(--primary);
}

.text-primary-dark {
    color: var(--primary-dark);
}

.text-accent {
    color: var(--accent);
}

/* Elegant animations */
@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}

/* Smooth transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}

/* Card hover effects */
.hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

/* Elegant borders */
.border-elegant {
    border: 1px solid rgba(0, 0, 0, 0.08);
}

/* Gradient text */
.gradient-text {
    background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
</style>
@endpush

@push('scripts')
<script>
// Smooth scroll animation
document.addEventListener('DOMContentLoaded', function() {
    // Initialize AOS
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            once: true,
            offset: 100,
            easing: 'ease-out-cubic'
        });
    }

    // Counter animation
    function animateCounter(element, target, duration) {
        let start = 0;
        const increment = target / (duration / 16);
        let current = 0;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                element.textContent = target;
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current);
            }
        }, 16);
    }

    // Intersection Observer for counters
    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px 0px -100px 0px'
    };

    const counters = document.querySelectorAll('.counter');
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = parseInt(entry.target.textContent);
                animateCounter(entry.target, target, 2000);
                counterObserver.unobserve(entry.target);
            }
        });
    }, observerOptions);

    counters.forEach(counter => counterObserver.observe(counter));

    // Add hover effects to cards
    const cards = document.querySelectorAll('.hover-lift');
    cards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.classList.add('hover:shadow-lg');
        });
        
        card.addEventListener('mouseleave', () => {
            card.classList.remove('hover:shadow-lg');
        });
    });
});
</script>
@endpush