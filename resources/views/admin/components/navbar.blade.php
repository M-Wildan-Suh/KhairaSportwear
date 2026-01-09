<!-- resources/views/components/navbar.blade.php -->
<nav class="navbar-admin-sport fixed z-30 w-full">
  <div class="px-3 py-3 lg:px-5 lg:pl-3">
    <div class="flex items-center justify-between">
      <div class="flex items-center justify-start">
        <!-- Mobile Menu Toggle -->
        <button 
          id="toggleSidebarMobile" 
          aria-expanded="true" 
          aria-controls="sidebar" 
          class="lg:hidden mr-2 text-gray-300 hover:text-white cursor-pointer p-2 hover:bg-white/10 focus:bg-white/10 rounded-lg transition-colors"
        >
          <!-- Hamburger Icon -->
          <svg id="toggleSidebarMobileHamburger" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
          </svg>
          <!-- Close Icon -->
          <svg id="toggleSidebarMobileClose" class="w-6 h-6 hidden" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
          </svg>
        </button>
        
        <!-- Logo & Brand -->
        <a href="{{ $logoUrl ?? route('admin.dashboard') }}" class="text-xl font-bold flex items-center lg:ml-2.5">
          @if(isset($logo))
            <img src="{{ $logo }}" class="h-6 mr-2" alt="{{ $brandName }} Logo">
          @else
            <div class="w-8 h-8 rounded-lg bg-gradient-to-r from-accent to-yellow-500 flex items-center justify-center mr-2">
              <i class="fas fa-dumbbell text-white text-sm"></i>
            </div>
          @endif
          <span class="self-center whitespace-nowrap text-white">
            {{ $brandName ?? 'SportWear Admin' }}
          </span>
        </a>
        
        <!-- Search Form (Desktop) -->
        @if($showSearch ?? true)
        <form action="{{ $searchAction ?? route('admin.search') }}" method="GET" class="hidden lg:block lg:pl-8">
          <label for="topbar-search" class="sr-only">Search</label>
          <div class="mt-1 relative lg:w-64">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
              </svg>
            </div>
            <input 
              type="text" 
              name="q" 
              id="topbar-search" 
              class="bg-white/10 border border-white/20 text-white placeholder-gray-300 sm:text-sm rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent block w-full pl-10 p-2.5" 
              placeholder="Cari..."
              value="{{ request('q') }}"
            >
          </div>
        </form>
        @endif
      </div>
      
      <!-- Right Side -->
      <div class="flex items-center">
        <!-- Mobile Search Toggle -->
        @if($showSearch ?? true)
        <button 
          id="toggleSidebarMobileSearch" 
          type="button" 
          class="lg:hidden text-gray-300 hover:text-white hover:bg-white/10 p-2 rounded-lg transition-colors"
        >
          <span class="sr-only">Search</span>
          <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
          </svg>
        </button>
        @endif
        
        <!-- GitHub Star Button (Optional) -->
        @if($showGithub ?? false)
        <div class="hidden lg:flex items-center">
          <span class="text-base font-normal text-gray-300 mr-5">Open source ❤️</span>
          <div class="-mb-1">
            <a class="github-button" href="{{ $githubUrl }}" data-color-scheme="no-preference: dark; light: light; dark: light;" data-icon="octicon-star" data-size="large" data-show-count="true" aria-label="Star themesberg/windster-tailwind-css-dashboard on GitHub">Star</a>
          </div>
        </div>
        @endif
        
        <!-- Notifications -->
        <div class="relative ml-2">
          <button class="p-2 text-gray-300 hover:text-white hover:bg-white/10 rounded-lg transition-colors relative">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
            </svg>
            @if(auth()->check() && auth()->user()->getUnreadNotificationsCount() > 0)
            <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">
              {{ auth()->user()->getUnreadNotificationsCount() }}
            </span>
            @endif
          </button>
        </div>
        
        <!-- User Dropdown -->
        <div class="relative user-dropdown-admin ml-2">
          <button class="flex items-center text-sm rounded-full focus:ring-2 focus:ring-accent">
            <div class="w-8 h-8 rounded-full bg-gradient-to-r from-accent to-yellow-500 flex items-center justify-center text-white font-semibold">
              {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="hidden lg:block ml-2 text-left">
              <div class="text-sm font-medium text-white">{{ auth()->user()->name }}</div>
              <div class="text-xs text-gray-300">Administrator</div>
            </div>
            <svg class="w-4 h-4 ml-1 text-gray-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
            </svg>
          </button>
          
          <!-- Dropdown Menu -->
          <div class="user-dropdown-menu hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-40 border border-gray-200">
            <div class="px-4 py-3 border-b border-gray-100">
              <div class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</div>
              <div class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</div>
            </div>
            
            <a href="{{ route('user.dashboard') }}" class="dropdown-item-admin">
              <i class="fas fa-user mr-3 text-gray-400"></i>
              <span>User Dashboard</span>
            </a>
            
            <a href="{{ route('user.profil.edit') }}" class="dropdown-item-admin">
              <i class="fas fa-cog mr-3 text-gray-400"></i>
              <span>Profile Settings</span>
            </a>
            
            <a href="{{ route('home') }}" target="_blank" class="dropdown-item-admin">
              <i class="fas fa-external-link-alt mr-3 text-gray-400"></i>
              <span>View Website</span>
            </a>
            
            <div class="border-t border-gray-100 my-1"></div>
            
            <form method="POST" action="{{ route('logout') }}" class="w-full">
              @csrf
              <button type="submit" class="dropdown-item-admin text-red-600 hover:text-red-700 hover:bg-red-50 w-full text-left">
                <i class="fas fa-sign-out-alt mr-3"></i>
                <span>Logout</span>
              </button>
            </form>
          </div>
        </div>
        
        <!-- Slot untuk item tambahan di kanan -->
        @if(isset($rightContent))
          <div class="ml-4">
            {{ $rightContent }}
          </div>
        @endif
      </div>
    </div>
  </div>
</nav>

<!-- Mobile Search Form (Hidden by default) -->
@if($showSearch ?? true)
<div id="mobile-search" class="lg:hidden bg-gray-800 border-b border-gray-700 px-3 py-2 hidden">
  <form action="{{ $searchAction ?? route('admin.search') }}" method="GET" class="w-full">
    <div class="relative">
      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
          <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
        </svg>
      </div>
      <input 
        type="text" 
        name="q" 
        class="bg-gray-700 border border-gray-600 text-white placeholder-gray-400 sm:text-sm rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent block w-full pl-10 p-2.5" 
        placeholder="Cari..."
        value="{{ request('q') }}"
      >
    </div>
  </form>
</div>
@endif

<!-- JavaScript untuk toggle -->
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Toggle Mobile Menu
  const toggleSidebarMobile = document.getElementById('toggleSidebarMobile');
  const toggleSidebarMobileHamburger = document.getElementById('toggleSidebarMobileHamburger');
  const toggleSidebarMobileClose = document.getElementById('toggleSidebarMobileClose');
  const sidebar = document.getElementById('sidebar');
  
  if (toggleSidebarMobile && sidebar) {
    toggleSidebarMobile.addEventListener('click', function() {
      sidebar.classList.toggle('hidden');
      toggleSidebarMobileHamburger.classList.toggle('hidden');
      toggleSidebarMobileClose.classList.toggle('hidden');
    });
  }
  
  // Toggle Mobile Search
  const toggleSidebarMobileSearch = document.getElementById('toggleSidebarMobileSearch');
  const mobileSearch = document.getElementById('mobile-search');
  
  if (toggleSidebarMobileSearch && mobileSearch) {
    toggleSidebarMobileSearch.addEventListener('click', function() {
      mobileSearch.classList.toggle('hidden');
    });
  }
  
  // User Dropdown Toggle
  const userDropdownBtn = document.querySelector('.user-dropdown-admin button');
  const userDropdownMenu = document.querySelector('.user-dropdown-menu');
  
  if (userDropdownBtn && userDropdownMenu) {
    userDropdownBtn.addEventListener('click', function(e) {
      e.stopPropagation();
      userDropdownMenu.classList.toggle('hidden');
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
      if (!userDropdownBtn.contains(event.target) && !userDropdownMenu.contains(event.target)) {
        userDropdownMenu.classList.add('hidden');
      }
    });
  }
  
  // Close dropdown when clicking on item
  const dropdownItems = document.querySelectorAll('.dropdown-item-admin');
  dropdownItems.forEach(item => {
    item.addEventListener('click', function() {
      userDropdownMenu.classList.add('hidden');
    });
  });
});
</script>
@endpush

<style>
.navbar-admin-sport {
  background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
  border-bottom: 3px solid var(--accent);
  box-shadow: 0 4px 20px rgba(26, 54, 93, 0.15);
}

.user-dropdown-menu {
  min-width: 200px;
}

.dropdown-item-admin {
  display: flex;
  align-items: center;
  padding: 10px 16px;
  color: #4B5563;
  text-decoration: none;
  transition: all 0.2s ease;
  font-size: 14px;
}

.dropdown-item-admin:hover {
  background-color: #F3F4F6;
  color: #1F2937;
}

.dropdown-item-admin i {
  width: 16px;
  text-align: center;
}

/* Responsive */
@media (max-width: 640px) {
  .navbar-admin-sport .text-xl {
    font-size: 1rem;
  }
}
</style>