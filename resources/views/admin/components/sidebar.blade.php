<aside 
    id="sidebar" 
    class="fixed hidden z-20 h-full top-0 left-0 pt-16 flex lg:flex flex-shrink-0 flex-col w-64 transition-width duration-75" 
    aria-label="Sidebar"
    @if(isset($isOpen) && $isOpen) style="display: flex;" @endif
>
    <div class="relative flex-1 flex flex-col min-h-0 border-r border-gray-200 bg-white pt-0">
        <!-- Logo Section -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-white">
            <div class="flex items-center space-x-3">
                <!-- Logo Icon -->
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-600 to-blue-400 flex items-center justify-center shadow-sm">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                </div>
                
                <!-- Logo Text -->
                <div class="flex-1 min-w-0">
                    <h2 class="text-lg font-bold text-gray-900 truncate">AdminPanel</h2>
                    <p class="text-xs text-gray-500 truncate">Management System</p>
                </div>
            </div>
        </div>
        
        <div class="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
            <!-- User Profile Mini (Optional) -->
            @auth
            <div class="px-6 mb-6">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-cyan-400 flex items-center justify-center text-white font-semibold text-sm">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
            </div>
            @endauth
            
            <div class="flex-1 px-3 bg-white divide-y space-y-1">
                <!-- Search Section -->
                @if($showMobileSearch ?? true)
                <div class="pb-4">
                    <form action="{{ $searchAction ?? '#' }}" method="GET" class="lg:hidden">
                        <label for="mobile-search" class="sr-only">Search</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                name="q" 
                                id="mobile-search" 
                                class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 transition-all duration-200" 
                                placeholder="Search..."
                                value="{{ request('q') }}"
                            >
                        </div>
                    </form>
                </div>
                @endif
                
                <!-- Main Navigation -->
                <div class="pb-6">
                    <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">MAIN MENU</h3>
                    <ul class="space-y-1">
                        @foreach($navigationItems ?? [] as $item)
                            <li>
                                <a 
                                    href="{{ $item['url'] ?? '#' }}" 
                                    class="flex items-center p-3 text-sm font-medium rounded-lg transition-all duration-200 group
                                        {{ (request()->url() === url($item['url']) || ($item['active'] ?? false)) 
                                            ? 'bg-gradient-to-r from-blue-50 to-blue-100 text-blue-700 border-l-4 border-blue-500' 
                                            : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900 hover:border-l-4 hover:border-gray-200' }}"
                                >
                                    @if(isset($item['icon']))
                                        <span class="flex-shrink-0 w-5 h-5 mr-3 {{ (request()->url() === url($item['url']) || ($item['active'] ?? false)) ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-600' }}">
                                            {!! $item['icon'] !!}
                                        </span>
                                    @elseif(isset($item['icon_svg']))
                                        <span class="flex-shrink-0 w-5 h-5 mr-3 {{ (request()->url() === url($item['url']) || ($item['active'] ?? false)) ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-600' }}">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                {!! $item['icon_svg'] !!}
                                            </svg>
                                        </span>
                                    @endif
                                    <span class="flex-1">{{ $item['label'] }}</span>
                                    @if(isset($item['badge']))
                                        <span class="ml-auto inline-flex items-center justify-center px-2 py-0.5 text-xs font-medium rounded-full 
                                            {{ (request()->url() === url($item['url']) || ($item['active'] ?? false)) 
                                                ? 'bg-blue-100 text-blue-800' 
                                                : 'bg-gray-100 text-gray-800' }}">
                                            {{ $item['badge'] }}
                                        </span>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                        
                        <!-- Default Navigation Items -->
                        @if(!isset($navigationItems))
                            <li>
                                <a href="{{ route('dashboard') }}" 
                                   class="flex items-center p-3 text-sm font-medium rounded-lg transition-all duration-200 group
                                        {{ request()->routeIs('dashboard') 
                                            ? 'bg-gradient-to-r from-blue-50 to-blue-100 text-blue-700 border-l-4 border-blue-500' 
                                            : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900 hover:border-l-4 hover:border-gray-200' }}">
                                    <span class="flex-shrink-0 w-5 h-5 mr-3 {{ request()->routeIs('dashboard') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-600' }}">
                                        <svg fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
                                            <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path>
                                        </svg>
                                    </span>
                                    <span class="flex-1">Dashboard</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.transaksi.index') }}" 
                                   class="flex items-center p-3 text-sm font-medium rounded-lg transition-all duration-200 group
                                        {{ request()->routeIs('admin.transaksi.*') 
                                            ? 'bg-gradient-to-r from-blue-50 to-blue-100 text-blue-700 border-l-4 border-blue-500' 
                                            : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900 hover:border-l-4 hover:border-gray-200' }}">
                                    <span class="flex-shrink-0 w-5 h-5 mr-3 {{ request()->routeIs('admin.transaksi.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-600' }}">
                                        <svg fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"></path>
                                        </svg>
                                    </span>
                                    <span class="flex-1">Transaksi</span>
                                    <span class="ml-auto inline-flex items-center justify-center px-2 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                        {{ \App\Models\Transaksi::count() }}
                                    </span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.produk.index') }}" 
                                   class="flex items-center p-3 text-sm font-medium rounded-lg transition-all duration-200 group
                                        {{ request()->routeIs('admin.produk.*') 
                                            ? 'bg-gradient-to-r from-blue-50 to-blue-100 text-blue-700 border-l-4 border-blue-500' 
                                            : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900 hover:border-l-4 hover:border-gray-200' }}">
                                    <span class="flex-shrink-0 w-5 h-5 mr-3 {{ request()->routeIs('admin.produk.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-600' }}">
                                        <svg fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path>
                                        </svg>
                                    </span>
                                    <span class="flex-1">Produk</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.users.index') }}" 
                                   class="flex items-center p-3 text-sm font-medium rounded-lg transition-all duration-200 group
                                        {{ request()->routeIs('admin.users.*') 
                                            ? 'bg-gradient-to-r from-blue-50 to-blue-100 text-blue-700 border-l-4 border-blue-500' 
                                            : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900 hover:border-l-4 hover:border-gray-200' }}">
                                    <span class="flex-shrink-0 w-5 h-5 mr-3 {{ request()->routeIs('admin.users.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-600' }}">
                                        <svg fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path>
                                        </svg>
                                    </span>
                                    <span class="flex-1">Users</span>
                                </a>
                            </li>
                        @endif
                        
                        @if(isset($navigation))
                            {{ $navigation }}
                        @endif
                    </ul>
                </div>
                
                <!-- Bottom Section -->
                <div class="pt-4 border-t border-gray-200">
                    <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">SUPPORT</h3>
                    <ul class="space-y-1">
                        @foreach($bottomItems ?? [] as $item)
                            <li>
                                <a 
                                    href="{{ $item['url'] ?? '#' }}" 
                                    target="{{ $item['target'] ?? '_self' }}"
                                    class="flex items-center p-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group"
                                >
                                    @if(isset($item['icon']))
                                        <span class="flex-shrink-0 w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-600">
                                            {!! $item['icon'] !!}
                                        </span>
                                    @endif
                                    <span class="flex-1">{{ $item['label'] }}</span>
                                    @if(isset($item['target']) && $item['target'] === '_blank')
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                        
                        <!-- Default Bottom Items -->
                        @if(!isset($bottomItems))
                            <li>
                                <a href="{{ route('admin.settings') }}" 
                                   class="flex items-center p-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group
                                        {{ request()->routeIs('admin.settings') ? 'bg-gray-50 text-gray-900' : '' }}">
                                    <span class="flex-shrink-0 w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-600">
                                        <svg fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
                                        </svg>
                                    </span>
                                    <span class="flex-1">Settings</span>
                                </a>
                            </li>
                            <li>
                                <a href="https://laravel.com/docs" target="_blank" 
                                   class="flex items-center p-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group">
                                    <span class="flex-shrink-0 w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-600">
                                        <svg fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                                        </svg>
                                    </span>
                                    <span class="flex-1">Documentation</span>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                </a>
                            </li>
                        @endif
                        
                        @if(isset($bottom))
                            {{ $bottom }}
                        @endif
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Footer Mini -->
        <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <div class="text-xs text-gray-500">
                    v1.0.0
                </div>
                <div class="text-xs text-gray-500">
                    © {{ date('Y') }}
                </div>
            </div>
        </div>
    </div>
</aside>

<!-- Overlay untuk mobile -->
@if($showOverlay ?? false)
<div id="sidebar-overlay" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-10 lg:hidden hidden"></div>
@endif