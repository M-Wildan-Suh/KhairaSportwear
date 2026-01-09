{{-- components/product-card-sporty.blade.php --}}
<div class="group relative bg-white rounded-3xl p-6 transition-all duration-500 hover:-translate-y-3 hover:shadow-2xl border-2 border-gray-100 hover:border-transparent overflow-hidden">
    <!-- Sport Badge -->
    @if($product->is_featured)
    <div class="absolute top-4 right-4 z-10">
        <span class="px-3 py-1 bg-gradient-to-r from-red-500 to-orange-500 text-white text-xs font-bold rounded-full uppercase tracking-wider">
            <i class="fas fa-bolt mr-1"></i> Hot
        </span>
    </div>
    @endif
    
    <!-- Product Image -->
    <div class="relative h-56 mb-6 rounded-2xl overflow-hidden">
        <img src="{{ $product->image_url ?: 'https://images.unsplash.com/photo-1534367507867-0edd93bd013b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80' }}" 
             alt="{{ $product->name }}" 
             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
        <div class="absolute inset-0 bg-gradient-to-t from-gray-900/30 to-transparent"></div>
        
        <!-- Quick Action Overlay -->
        <div class="absolute inset-0 bg-gradient-to-br from-red-500/90 to-orange-500/90 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
            <button class="bg-white text-red-600 font-bold py-3 px-6 rounded-xl transform -translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                <i class="fas fa-shopping-cart mr-2"></i> Quick Add
            </button>
        </div>
    </div>
    
    <!-- Product Info -->
    <div class="mb-4">
        <h4 class="font-bold text-gray-900 text-lg mb-2 line-clamp-1">{{ $product->name }}</h4>
        <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ Str::limit($product->description, 80) }}</p>
        
        <!-- Sport Category -->
        <div class="inline-flex items-center gap-2 mb-4">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center">
                <i class="fas fa-dumbbell text-white text-xs"></i>
            </div>
            <span class="text-sm text-gray-700 font-medium">{{ $product->category->name ?? 'Fitness' }}</span>
        </div>
        
        <!-- Price & Rating -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <span class="text-2xl font-black text-gray-900">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                @if($product->discount_price)
                <span class="text-sm text-red-500 line-through ml-2">Rp {{ number_format($product->discount_price, 0, ',', '.') }}</span>
                @endif
            </div>
            <div class="flex items-center gap-1">
                <i class="fas fa-star text-yellow-400"></i>
                <span class="font-bold text-gray-900">4.8</span>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="grid grid-cols-2 gap-3">
            <button class="group/btn bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white font-bold py-3 rounded-xl transition-all duration-300 transform hover:-translate-y-1">
                <span class="flex items-center justify-center gap-2">
                    <i class="fas fa-shopping-cart"></i>
                    Buy
                </span>
            </button>
            <button class="group/btn border-2 border-blue-500 text-blue-600 hover:bg-blue-500 hover:text-white font-bold py-3 rounded-xl transition-all duration-300 transform hover:-translate-y-1">
                <span class="flex items-center justify-center gap-2">
                    <i class="fas fa-calendar-alt"></i>
                    Rent
                </span>
            </button>
        </div>
    </div>
    
    <!-- Hover Gradient Effect -->
    <div class="absolute inset-0 bg-gradient-to-br from-red-500 to-orange-500 opacity-0 group-hover:opacity-5 rounded-3xl transition-opacity duration-500 -z-10"></div>
</div>