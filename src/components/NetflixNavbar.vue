<template>
  <nav 
    class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
    :class="scrolled 
      ? 'bg-gray-900/95 backdrop-blur-sm shadow-lg' 
      : 'bg-black/30 backdrop-blur-sm'"
  >
    <div class="flex items-center justify-between px-4 md:px-8 py-3 max-w-[1920px] mx-auto">
      <!-- Logo -->
      <router-link to="/home" class="flex items-center gap-2 flex-shrink-0">
        <svg class="w-8 h-8 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
          <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
        </svg>
        <span class="text-yellow-400 text-xl font-bold">HTHREE</span>
      </router-link>

      <!-- Center: Search + Menu -->
      <div class="flex items-center gap-6 flex-1 max-w-4xl mx-6">
        <!-- Search Bar -->
        <div class="relative flex-1 max-w-md">
          <input 
            v-model="searchQuery"
            type="text"
            :placeholder="$t('nav.search')"
            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 pl-10 text-sm text-white placeholder-gray-400 focus:outline-none focus:border-yellow-400 focus:ring-1 focus:ring-yellow-400 transition"
            :title="$t('nav.searchTitle')"
            :aria-label="$t('nav.search')"
            @input="onSearchInput"
            @keyup.enter="handleSearch"
            @blur="closeSuggestions"
            @focus="searchQuery.length >= 2 ? fetchSuggestions() : (showSuggestions = true)"
          />
          <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>

          <!-- Loading Spinner -->
          <div 
            v-if="isSearching"
            class="absolute right-10 top-1/2 -translate-y-1/2"
          >
            <svg class="animate-spin h-4 w-4 text-yellow-400" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          </div>

          <!-- Suggestions Dropdown -->
          <div 
            v-if="showSuggestions && (searchSuggestions.length > 0 || (searchQuery.length === 0 && recentSearches.length > 0))"
            class="absolute top-full left-0 right-0 mt-2 bg-gray-800 rounded-lg shadow-2xl border border-gray-700 overflow-hidden z-50"
          >
            <!-- Recent Searches (when no query) -->
            <div v-if="searchQuery.length === 0 && recentSearches.length > 0">
              <div class="px-3 py-2 bg-gray-900 border-b border-gray-700 flex items-center justify-between">
                <span class="text-gray-400 text-xs font-medium">Tìm kiếm gần đây</span>
                <button 
                  @mousedown="clearSearches"
                  class="text-gray-500 hover:text-red-400 text-xs transition-colors"
                >
                  Xóa tất cả
                </button>
              </div>
              <div
                v-for="(search, index) in recentSearches"
                :key="index"
                @mousedown="searchQuery = search; handleSearch()"
                class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-700 cursor-pointer transition-colors border-b border-gray-700 last:border-b-0"
              >
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-white text-sm">{{ search }}</span>
              </div>
            </div>
            
            <!-- Search Results -->
            <div v-if="searchQuery.length > 0 && searchSuggestions.length > 0">
              <div
                v-for="movie in searchSuggestions"
                :key="movie.slug"
                @mousedown="selectSuggestion(movie)"
                class="flex items-center gap-3 p-3 hover:bg-gray-700 cursor-pointer transition-colors border-b border-gray-700 last:border-b-0"
              >
                <img
                  :src="getImageUrl(movie.poster_url)"
                  :alt="movie.name"
                  class="w-12 h-16 object-cover rounded flex-shrink-0"
                  @error="(e) => e.target.src = 'https://placehold.co/50x75/1a1a1a/fff?text=Error'"
                />
                <div class="flex-1 min-w-0">
                  <p class="text-white font-medium truncate">{{ movie.name }}</p>
                  <p class="text-gray-400 text-xs truncate mb-1">{{ movie.origin_name }}</p>
                  <div class="flex gap-2 flex-wrap">
                    <span v-if="movie.year" class="text-xs bg-gray-700 px-2 py-0.5 rounded">{{ movie.year }}</span>
                    <span v-if="movie.quality" class="text-xs bg-yellow-600 text-black px-2 py-0.5 rounded font-semibold">{{ movie.quality }}</span>
                    <span v-if="movie.lang" class="text-xs bg-blue-600 text-white px-2 py-0.5 rounded">{{ movie.lang }}</span>
                  </div>
                </div>
              </div>
              <div class="p-2 bg-gray-900 text-center border-t border-gray-700">
                <button 
                  @mousedown="handleSearch" 
                  class="text-yellow-400 text-sm hover:text-yellow-300 font-medium transition-colors"
                >
                  Xem tất cả kết quả cho "{{ searchQuery }}" →
                </button>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Desktop Menu -->
        <ul class="hidden lg:flex items-center gap-4 text-sm font-medium">
          <li class="relative group">
            <router-link 
              to="/list/phim-le/page/1" 
              :class="[
                scrolled ? 'text-white hover:text-yellow-400' : 'text-white/90 hover:text-white',
                isActiveRoute('/list/phim-le') ? 'text-yellow-400' : ''
              ]"
              class="transition-all duration-300 px-2 py-1 block relative no-underline"
              style="text-decoration: none !important;"
            >
              {{ $t('nav.single') }}
              <!-- Underline animation -->
              <span 
                class="absolute bottom-0 left-0 h-0.5 bg-yellow-400 transition-all duration-300"
                :class="isActiveRoute('/list/phim-le') ? 'w-full' : 'w-0 group-hover:w-full'"
              ></span>
            </router-link>
          </li>
          <li class="relative group">
            <router-link 
              to="/list/phim-bo/page/1" 
              :class="[
                scrolled ? 'text-white hover:text-yellow-400' : 'text-white/90 hover:text-white',
                isActiveRoute('/list/phim-bo') ? 'text-yellow-400' : ''
              ]"
              class="transition-all duration-300 px-2 py-1 block relative no-underline"
              style="text-decoration: none !important;"
            >
              {{ $t('nav.series') }}
              <!-- Underline animation -->
              <span 
                class="absolute bottom-0 left-0 h-0.5 bg-yellow-400 transition-all duration-300"
                :class="isActiveRoute('/list/phim-bo') ? 'w-full' : 'w-0 group-hover:w-full'"
              ></span>
            </router-link>
          </li>
          <li class="relative group">
            <button 
              :class="scrolled 
                ? 'text-white hover:text-yellow-400' 
                : 'text-white/90 hover:text-white'"
              class="transition-all duration-300 flex items-center gap-1 px-2 py-1 relative"
            >
              {{ $t('nav.genres') }}
              <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
              </svg>
              <!-- Underline animation -->
              <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-yellow-400 transition-all duration-300 group-hover:w-full pointer-events-none"></span>
            </button>
            <!-- Dropdown Menu -->
            <div class="hidden group-hover:block absolute top-full left-0 pt-2 w-48 z-50">
              <div class="bg-gray-800 rounded-lg shadow-xl max-h-96 overflow-y-auto border border-gray-700">
                <router-link
                  v-for="cat in categories"
                  :key="cat.slug"
                  :to="`/category/${cat.slug}/page/1`"
                  class="block px-4 py-2 text-white hover:bg-gray-700 hover:text-yellow-400 transition-colors text-sm"
                >
                  {{ cat.name }}
                </router-link>
                <!-- Show more link if there are more categories -->
                <router-link
                  v-if="hasMoreCategories"
                  to="/categories"
                  class="block px-4 py-2 text-yellow-400 hover:bg-gray-700 hover:text-yellow-300 transition-colors text-sm font-medium border-t border-gray-700"
                >
                  {{ $t('nav.viewAllGenres') }} →
                </router-link>
              </div>
            </div>
          </li>
          <li class="relative group">
            <button 
              :class="scrolled 
                ? 'text-white hover:text-yellow-400' 
                : 'text-white/90 hover:text-white'"
              class="transition-all duration-300 flex items-center gap-1 px-2 py-1 relative"
            >
              {{ $t('nav.countries') }}
              <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
              </svg>
              <!-- Underline animation -->
              <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-yellow-400 transition-all duration-300 group-hover:w-full pointer-events-none"></span>
            </button>
            <!-- Dropdown Menu -->
            <div class="hidden group-hover:block absolute top-full left-0 pt-2 w-48 z-50">
              <div class="bg-gray-800 rounded-lg shadow-xl max-h-96 overflow-y-auto border border-gray-700">
                <a href="#" class="block px-4 py-2 text-white hover:bg-gray-700 hover:text-yellow-400 transition-colors text-sm">Việt Nam</a>
                <a href="#" class="block px-4 py-2 text-white hover:bg-gray-700 hover:text-yellow-400 transition-colors text-sm">Hàn Quốc</a>
                <a href="#" class="block px-4 py-2 text-white hover:bg-gray-700 hover:text-yellow-400 transition-colors text-sm">Trung Quốc</a>
                <a href="#" class="block px-4 py-2 text-white hover:bg-gray-700 hover:text-yellow-400 transition-colors text-sm">Mỹ</a>
                <a href="#" class="block px-4 py-2 text-white hover:bg-gray-700 hover:text-yellow-400 transition-colors text-sm">Thái Lan</a>
              </div>
            </div>
          </li>

        </ul>
      </div>

      <!-- Right Side -->
      <div class="flex items-center gap-2 flex-shrink-0">
        <!-- Language Switcher - Compact & Beautiful -->
        <button
          @click="toggleLanguage"
          class="hidden md:flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-800/50 hover:bg-gray-700/70 border border-gray-700 hover:border-gray-600 transition-all duration-200 group"
          :title="currentLanguage === 'vi' ? 'Switch to English' : 'Chuyển sang Tiếng Việt'"
        >
          <svg class="w-4 h-4 text-gray-400 group-hover:text-yellow-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
          </svg>
          <span class="text-xs font-bold text-white uppercase tracking-wider">
            {{ currentLanguage === 'vi' ? 'VI' : 'EN' }}
          </span>
        </button>
        
        <!-- Pricing (Ẩn với admin và khi chưa đăng nhập) -->
        <router-link
          v-if="user && !isAdmin"
          to="/pricing"
          :class="[
            'hidden md:flex items-center gap-2 px-4 py-2 rounded-lg font-semibold text-sm transition-all hover:scale-105 shadow-lg',
            isActiveRoute('/pricing') 
              ? 'bg-gradient-to-r from-yellow-500 to-yellow-600 text-black ring-2 ring-yellow-400' 
              : 'bg-gradient-to-r from-yellow-400 to-yellow-500 text-black hover:from-yellow-500 hover:to-yellow-600'
          ]"
          title="Nâng cấp tài khoản VIP"
        >
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
          </svg>
          Nâng cấp VIP
        </router-link>

        <!-- Admin (only for admin users) -->
        <router-link
          v-if="user && (user.email === 'hient7182@gmail.com' || user.email === 'admin@hthree.com' || user.role === 'admin')"
          to="/admin"
          class="p-2 bg-purple-600 rounded-lg hover:bg-purple-700 transition-colors"
          title="Quản trị hệ thống"
          aria-label="Admin Panel"
        >
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
        </router-link>

        <!-- User -->
        <div class="relative group">
          <button 
            class="flex items-center gap-2 bg-gray-800 px-3 py-2 rounded-lg hover:bg-gray-700 hover:scale-105 transition-all duration-300"
            :title="authStore.loading ? 'Đang tải...' : (user ? `Tài khoản: ${user.displayName || user.username || user.email}` : 'Đăng nhập / Đăng ký')"
            aria-label="Menu tài khoản"
          >
            <!-- Loading Spinner -->
            <div v-if="authStore.loading" class="w-7 h-7 flex items-center justify-center">
              <svg class="animate-spin h-5 w-5 text-yellow-400" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </div>
            <!-- User Avatar with Badge -->
            <div v-else-if="user" class="relative">
              <img 
                :src="user?.avatar || user?.photoURL || `https://ui-avatars.com/api/?name=${encodeURIComponent(user?.full_name || user?.displayName || user?.username || 'User')}&background=f59e0b&color=000`"
                class="w-7 h-7 rounded-full border-2 border-yellow-400 shadow-lg"
                :alt="user?.full_name || user?.displayName || user?.username || 'User'"
              />
              <!-- Subscription Badge Icon (Small) -->
              <div 
                v-if="subscriptionBadge"
                :class="subscriptionBadge.color"
                class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full flex items-center justify-center text-[9px] font-bold border-2 border-gray-900 shadow-lg"
              >
                {{ subscriptionBadge.icon }}
              </div>
            </div>
            <!-- Default Icon if not logged in -->
            <svg v-else class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span class="text-sm font-medium text-white hidden md:block">
              {{ authStore.loading ? '...' : (user ? (user.full_name || user.displayName || user.username || user.email?.split('@')[0] || 'Thành viên') : 'Thành viên') }}
            </span>
          </button>
          
          <!-- User Dropdown -->
          <div class="absolute top-full right-0 mt-3 w-64 bg-gray-900 rounded-xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-500 ease-out transform group-hover:translate-y-0 -translate-y-4 group-hover:scale-100 scale-95 border border-yellow-400/30 overflow-hidden backdrop-blur-xl">
            <!-- User Info Header -->
            <div v-if="user" class="bg-gradient-to-br from-gray-800 to-gray-900 px-4 py-3 border-b border-yellow-400/20">
              <div class="flex items-center gap-2.5 mb-2.5">
                <img 
                  :src="user?.avatar || user?.photoURL || `https://ui-avatars.com/api/?name=${encodeURIComponent(user?.full_name || user?.displayName || user?.username || 'User')}&background=f59e0b&color=000`"
                  class="w-11 h-11 rounded-full border-2 border-yellow-400 shadow-lg"
                  :alt="user?.full_name || user?.displayName || user?.username || 'User'"
                />
                <div class="flex-1 min-w-0">
                  <div class="flex items-center gap-1.5 mb-0.5">
                    <p class="text-white font-bold truncate text-sm">
                      {{ user?.full_name || user?.displayName || user?.username }}
                    </p>
                    <!-- Subscription Badge (inline with name) -->
                    <span 
                      v-if="subscriptionBadge"
                      :class="subscriptionBadge.color"
                      class="px-1.5 py-0.5 rounded text-[9px] font-bold flex items-center gap-0.5 flex-shrink-0"
                    >
                      <span>{{ subscriptionBadge.icon }}</span>
                      <span>{{ subscriptionBadge.label }}</span>
                    </span>
                  </div>
                  <p class="text-gray-400 text-[10px] truncate mb-0.5">
                    {{ user?.email }}
                  </p>
                  <!-- Subscription expiry info -->
                  <p v-if="subscriptionBadge" class="text-gray-500 text-[9px] truncate">
                    {{ subscriptionTooltip }}
                  </p>
                </div>
              </div>
              
              <!-- Upgrade Button (Ẩn với admin) -->
              <router-link
                v-if="!isAdmin"
                to="/pricing"
                class="w-full bg-gradient-to-r from-yellow-400 to-yellow-500 text-black px-3 py-2 rounded-lg font-bold text-xs hover:from-yellow-500 hover:to-yellow-600 hover:scale-105 hover:shadow-xl hover:shadow-yellow-400/50 transition-all duration-300 shadow-lg flex items-center justify-center gap-1.5 group/upgrade"
              >
                <svg class="w-3.5 h-3.5 group-hover/upgrade:rotate-12 transition-transform duration-300" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <span class="group-hover/upgrade:tracking-wider transition-all duration-300">Nâng cấp ngay</span>
              </router-link>
              
              <!-- Admin Badge -->
              <div v-else class="w-full bg-gradient-to-r from-purple-600 to-purple-700 text-white px-3 py-2 rounded-lg font-bold text-xs flex items-center justify-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>Quản trị viên</span>
              </div>
            </div>



            <!-- Menu Items -->
            <div class="py-1.5">
              <!-- Grouped: Library (Yêu thích + Danh sách + Xem tiếp) -->
              <router-link 
                v-if="user"
                to="/library"
                :class="[
                  'flex items-center gap-2.5 px-4 py-2.5 text-white hover:bg-gray-800 hover:pl-5 transition-all duration-300 group/item',
                  isActiveRoute('/library') ? 'bg-gray-800 border-l-2 border-yellow-400' : ''
                ]"
                title="Yêu thích, Danh sách, Xem tiếp"
              >
                <svg 
                  class="w-4 h-4 transition-all duration-300 group-hover/item:scale-110" 
                  :class="isActiveRoute('/library') ? 'text-yellow-400' : 'text-gray-400 group-hover/item:text-yellow-400'"
                  fill="currentColor" 
                  viewBox="0 0 20 20"
                >
                  <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                </svg>
                <span class="font-medium text-sm">Thư viện của tôi</span>
              </router-link>

              <router-link 
                v-if="user"
                to="/account" 
                :class="[
                  'flex items-center gap-2.5 px-4 py-2.5 text-white hover:bg-gray-800 hover:pl-5 transition-all duration-300 group/item',
                  isActiveRoute('/account') ? 'bg-gray-800 border-l-2 border-yellow-400' : ''
                ]"
              >
                <svg 
                  class="w-4 h-4 transition-all duration-300 group-hover/item:scale-110" 
                  :class="isActiveRoute('/account') ? 'text-yellow-400' : 'text-gray-400 group-hover/item:text-yellow-400'"
                  fill="none" 
                  stroke="currentColor" 
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="font-medium text-sm">Tài khoản</span>
              </router-link>

              <div v-if="user" class="border-t border-gray-700/50 my-1.5"></div>

              <button 
                v-if="user"
                @click="handleLogout"
                class="flex items-center gap-2.5 w-full px-4 py-2.5 text-white hover:bg-red-500/20 hover:text-red-400 hover:pl-5 transition-all duration-300 group/item"
              >
                <svg class="w-4 h-4 text-gray-400 group-hover/item:text-red-400 transition-all duration-300 group-hover/item:scale-110 group-hover/item:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span class="font-medium text-sm">Thoát</span>
              </button>

              <button
                v-else
                @click="showAuthModal = true"
                class="flex items-center gap-2.5 w-full px-4 py-2.5 text-white hover:bg-gray-800 hover:text-yellow-400 hover:pl-5 transition-all duration-300 group/item"
              >
                <svg class="w-4 h-4 text-gray-400 group-hover/item:text-yellow-400 transition-all duration-300 group-hover/item:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                <span class="font-medium text-sm">Đăng nhập</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Mobile Menu Toggle -->
        <button 
          @click="toggleMobileMenu"
          class="lg:hidden p-2 rounded-lg hover:bg-gray-800 transition-colors relative z-50"
          aria-label="Menu điều hướng"
          :aria-expanded="mobileMenuOpen"
        >
          <svg 
            v-if="!mobileMenuOpen"
            class="w-6 h-6 text-white" 
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
          <svg 
            v-else
            class="w-6 h-6 text-white" 
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
    </div>

    <!-- Mobile Menu Backdrop -->
    <Transition name="fade">
      <div 
        v-if="mobileMenuOpen"
        @click="mobileMenuOpen = false"
        class="lg:hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-30 top-[64px]"
      ></div>
    </Transition>

    <!-- Mobile Menu -->
    <Transition name="slide-down">
      <div 
        v-if="mobileMenuOpen"
        class="lg:hidden bg-gray-900/98 backdrop-blur-lg border-t border-gray-800 shadow-2xl fixed left-0 right-0 top-[64px] z-40 max-h-[calc(100vh-64px)] overflow-y-auto"
      >
      <div class="px-4 py-6 space-y-2">
        <!-- Trang chủ -->
        <router-link 
          to="/home" 
          @click="mobileMenuOpen = false"
          :class="[
            'block px-4 py-3 rounded-lg transition-all font-medium',
            isActiveRoute('/home') ? 'bg-gray-800 text-yellow-400' : 'text-white hover:bg-gray-800 hover:text-yellow-400'
          ]"
        >
          <div class="flex items-center gap-3">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
            </svg>
            Trang chủ
          </div>
        </router-link>
        
        <!-- Phim Lẻ -->
        <router-link 
          to="/list/phim-le/page/1" 
          @click="mobileMenuOpen = false"
          :class="[
            'block px-4 py-3 rounded-lg transition-all font-medium',
            isActiveRoute('/list/phim-le') ? 'bg-gray-800 text-yellow-400' : 'text-white hover:bg-gray-800 hover:text-yellow-400'
          ]"
        >
          <div class="flex items-center gap-3">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
            </svg>
            Phim Lẻ
          </div>
        </router-link>
        
        <!-- Phim Bộ -->
        <router-link 
          to="/list/phim-bo/page/1" 
          @click="mobileMenuOpen = false"
          :class="[
            'block px-4 py-3 rounded-lg transition-all font-medium',
            isActiveRoute('/list/phim-bo') ? 'bg-gray-800 text-yellow-400' : 'text-white hover:bg-gray-800 hover:text-yellow-400'
          ]"
        >
          <div class="flex items-center gap-3">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
            </svg>
            Phim Bộ
          </div>
        </router-link>

        <!-- Thể loại Accordion -->
        <div class="space-y-1">
          <button
            @click="mobileCategoriesOpen = !mobileCategoriesOpen"
            class="w-full flex items-center justify-between px-4 py-3 text-white hover:bg-gray-800 hover:text-yellow-400 rounded-lg transition-all font-medium"
          >
            <div class="flex items-center gap-3">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/>
              </svg>
              Thể loại
            </div>
            <svg 
              class="w-4 h-4 transition-transform duration-300" 
              :class="mobileCategoriesOpen ? 'rotate-180' : ''"
              fill="currentColor" 
              viewBox="0 0 20 20"
            >
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
          </button>
          
          <!-- Categories List -->
          <Transition name="accordion">
            <div v-if="mobileCategoriesOpen" class="pl-4 space-y-1 max-h-64 overflow-y-auto">
              <router-link
                v-for="cat in categories"
                :key="cat.slug"
                :to="`/category/${cat.slug}/page/1`"
                @click="mobileMenuOpen = false"
                class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 hover:text-yellow-400 rounded-lg transition-all"
              >
                {{ cat.name }}
              </router-link>
              <router-link
                v-if="hasMoreCategories"
                to="/categories"
                @click="mobileMenuOpen = false"
                class="block px-4 py-2 text-sm text-yellow-400 hover:bg-gray-800 rounded-lg transition-all font-medium"
              >
                Xem tất cả thể loại →
              </router-link>
            </div>
          </Transition>
        </div>

        <!-- Quốc gia Accordion -->
        <div class="space-y-1">
          <button
            @click="mobileCountriesOpen = !mobileCountriesOpen"
            class="w-full flex items-center justify-between px-4 py-3 text-white hover:bg-gray-800 hover:text-yellow-400 rounded-lg transition-all font-medium"
          >
            <div class="flex items-center gap-3">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0 2 2 0 011.523-1.943A5.977 5.977 0 0116 10c0 .34-.028.675-.083 1H15a2 2 0 00-2 2v2.197A5.973 5.973 0 0110 16v-2a2 2 0 00-2-2 2 2 0 01-2-2 2 2 0 00-1.668-1.973z" clip-rule="evenodd"/>
              </svg>
              Quốc gia
            </div>
            <svg 
              class="w-4 h-4 transition-transform duration-300" 
              :class="mobileCountriesOpen ? 'rotate-180' : ''"
              fill="currentColor" 
              viewBox="0 0 20 20"
            >
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
          </button>
          
          <!-- Countries List -->
          <Transition name="accordion">
            <div v-if="mobileCountriesOpen" class="pl-4 space-y-1">
              <router-link
                v-for="country in countries"
                :key="country.slug"
                :to="`/country/${country.slug}/page/1`"
                @click="mobileMenuOpen = false"
                class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 hover:text-yellow-400 rounded-lg transition-all"
              >
                {{ country.name }}
              </router-link>
            </div>
          </Transition>
        </div>

        <!-- Library (chỉ hiện khi đã đăng nhập) -->
        <router-link 
          v-if="user"
          to="/library" 
          @click="mobileMenuOpen = false"
          :class="[
            'block px-4 py-3 rounded-lg transition-all font-medium',
            isActiveRoute('/library') ? 'bg-gray-800 text-yellow-400' : 'text-white hover:bg-gray-800 hover:text-yellow-400'
          ]"
        >
          <div class="flex items-center gap-3">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
            </svg>
            Thư viện của tôi
          </div>
        </router-link>

        <!-- Pricing (chỉ hiện khi đã đăng nhập và không phải admin) -->
        <router-link 
          v-if="user && !isAdmin"
          to="/pricing" 
          @click="mobileMenuOpen = false"
          :class="[
            'block px-4 py-3 rounded-lg transition-all font-medium',
            isActiveRoute('/pricing') ? 'bg-gray-800 text-yellow-400' : 'text-white hover:bg-gray-800 hover:text-yellow-400'
          ]"
        >
          <div class="flex items-center gap-3">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
            Nâng cấp VIP
          </div>
        </router-link>

        <!-- Divider -->
        <div class="border-t border-gray-800 my-4"></div>

        <!-- User Section on Mobile -->
        <div v-if="!user" class="px-4 py-3">
          <button 
            @click="showAuthModal = true; mobileMenuOpen = false"
            class="w-full bg-yellow-400 text-black px-4 py-3 rounded-lg font-bold hover:bg-yellow-500 transition-all flex items-center justify-center gap-2"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
            Đăng nhập
          </button>
        </div>
        <div v-else class="px-4 py-3 space-y-2">
          <div class="flex items-center gap-3 mb-3">
            <img 
              :src="user?.avatar || user?.photoURL || `https://ui-avatars.com/api/?name=${encodeURIComponent(user?.full_name || user?.displayName || user?.username || 'User')}&background=f59e0b&color=000`"
              class="w-12 h-12 rounded-full border-2 border-yellow-400"
              :alt="user?.full_name || user?.displayName || user?.username || 'User'"
            />
            <div>
              <p class="text-white font-bold">{{ user?.full_name || user?.displayName || user?.username }}</p>
              <p class="text-gray-400 text-sm">{{ user?.email }}</p>
            </div>
          </div>
          <button 
            @click="handleLogout(); mobileMenuOpen = false"
            class="w-full bg-gray-800 text-white px-4 py-3 rounded-lg font-medium hover:bg-gray-700 transition-all flex items-center justify-center gap-2"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            Đăng xuất
          </button>
        </div>
      </div>
    </div>
    </Transition>

    <!-- Backdrop for mobile menu -->
    <Transition name="fade">
      <div
        v-if="mobileMenuOpen"
        @click="toggleMobileMenu"
        class="lg:hidden fixed inset-0 bg-black/50 z-30 top-[64px]"
        aria-hidden="true"
      ></div>
    </Transition>
  </nav>

  <!-- Auth Modal -->
  <AuthModal v-model="showAuthModal" @success="loadUser" />
  
  <!-- Keyboard Shortcuts Help Modal -->
  <KeyboardShortcutsHelp :show="showShortcutsHelp" @close="showShortcutsHelp = false" />
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { getAuth, signOut, onAuthStateChanged } from 'firebase/auth';
import { useAuthStore } from '@/stores/authStore';
import { useCategoryStore } from '@/stores/Category/category.js';
import authService from '@/services/authService';
import AuthModal from './AuthModal.vue';
import KeyboardShortcutsHelp from './KeyboardShortcutsHelp.vue';
import { useToast } from '@/composables/useToast';
import { useRecentSearches } from '@/composables/useRecentSearches';
import { useI18n } from 'vue-i18n';
import { setLocale } from '@/i18n';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();
const categoryStore = useCategoryStore();
const auth = getAuth();
const toast = useToast();
const { recentSearches, addSearch, clearSearches } = useRecentSearches();
const { locale } = useI18n();

// Language state
const currentLanguage = computed(() => locale.value);

// Toggle language
const toggleLanguage = () => {
  console.log('🌐 Current language:', locale.value);
  const newLocale = locale.value === 'vi' ? 'en' : 'vi';
  console.log('🌐 Switching to:', newLocale);
  
  // Use setLocale helper function
  setLocale(newLocale);
  
  console.log('✅ Language switched to:', locale.value);
  toast.success(`Switched to ${newLocale === 'vi' ? 'Tiếng Việt' : 'English'}`);
};

const scrolled = ref(false);
const searchQuery = ref('');
const user = computed(() => authStore.user); // Get user from authStore
const mobileMenuOpen = ref(false);
const searchSuggestions = ref([]);
const showSuggestions = ref(false);
const isSearching = ref(false);
const showAuthModal = ref(false);
const mobileCategoriesOpen = ref(false);
const mobileCountriesOpen = ref(false);
const showShortcutsHelp = ref(false);

// Subscription state
const userSubscription = ref(null);
const loadingSubscription = ref(false);

// Fetch user subscription
const fetchUserSubscription = async () => {
  if (!user.value?.id) return;
  
  try {
    loadingSubscription.value = true;
    const API_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost/HTHREE_film/backend/api';
    const response = await fetch(`${API_URL}/user_subscription.php?user_id=${user.value.id}`);
    const data = await response.json();
    
    if (data.success && data.data && data.data.length > 0) {
      // Get active subscription with highest priority
      const active = data.data.filter(sub => sub.status === 'active');
      if (active.length > 0) {
        const priority = { vip: 4, premium: 3, basic: 2, free: 1 };
        userSubscription.value = active.sort((a, b) => {
          return (priority[b.plan_slug] || 0) - (priority[a.plan_slug] || 0);
        })[0];
      }
    }
  } catch (error) {
    console.error('Failed to fetch subscription:', error);
  } finally {
    loadingSubscription.value = false;
  }
};

// Computed: Subscription badge info
const subscriptionBadge = computed(() => {
  if (!userSubscription.value) return null;
  
  const badges = {
    vip: { label: 'VIP', color: 'bg-purple-600 text-white', icon: '👑' },
    premium: { label: 'PREMIUM', color: 'bg-red-600 text-white', icon: '⭐' },
    basic: { label: 'BASIC', color: 'bg-blue-600 text-white', icon: '✓' },
    free: { label: 'FREE', color: 'bg-gray-600 text-white', icon: '○' }
  };
  
  return badges[userSubscription.value.plan_slug] || null;
});

// Computed: Subscription tooltip
const subscriptionTooltip = computed(() => {
  if (!userSubscription.value) return '';
  
  const daysRemaining = userSubscription.value.days_remaining;
  const endDate = userSubscription.value.end_date_formatted;
  
  if (daysRemaining <= 0) {
    return `Đã hết hạn: ${endDate}`;
  } else if (daysRemaining <= 7) {
    return `⚠️ Sắp hết hạn: ${endDate} (còn ${daysRemaining} ngày)`;
  } else {
    return `Hết hạn: ${endDate} (còn ${daysRemaining} ngày)`;
  }
});

// Countries list
const countries = ref([
  { name: '🇻🇳 Việt Nam', slug: 'viet-nam' },
  { name: '🇰🇷 Hàn Quốc', slug: 'han-quoc' },
  { name: '🇨🇳 Trung Quốc', slug: 'trung-quoc' },
  { name: '🇺🇸 Mỹ', slug: 'au-my' },
  { name: '🇹🇭 Thái Lan', slug: 'thai-lan' },
  { name: '🇯🇵 Nhật Bản', slug: 'nhat-ban' },
  { name: '🇮🇳 Ấn Độ', slug: 'an-do' },
  { name: '🇬🇧 Anh', slug: 'anh' },
  { name: '🇫🇷 Pháp', slug: 'phap' },
  { name: '🇩🇪 Đức', slug: 'duc' }
]);

// Check if current route is active
const isActiveRoute = (path) => {
  return route.path.startsWith(path);
};

// Toggle mobile menu with debug
const toggleMobileMenu = () => {
  mobileMenuOpen.value = !mobileMenuOpen.value;
  console.log('🍔 Mobile menu toggled:', mobileMenuOpen.value);
  
  // Prevent body scroll when menu is open
  if (mobileMenuOpen.value) {
    document.body.style.overflow = 'hidden';
  } else {
    document.body.style.overflow = '';
  }
};

// Load categories from store
categoryStore.getCategory();

// User is now managed by authStore - no need for separate loadUser function

// Limit categories to 8 for better UX (Hick's Law)
const categories = computed(() => {
  return categoryStore.getAllCategories?.slice(0, 8) || [];
});

const hasMoreCategories = computed(() => {
  return (categoryStore.getAllCategories?.length || 0) > 8;
});

// Kiểm tra xem user có phải admin không
const isAdmin = computed(() => {
  if (!user.value) return false;
  
  // Kiểm tra role từ database
  if (user.value.role === 'admin') return true;
  
  // Kiểm tra email admin cố định
  const adminEmails = ['hient7182@gmail.com', 'admin@hthree.com'];
  return adminEmails.includes(user.value.email);
});

const handleScroll = () => {
  scrolled.value = window.scrollY > 50;
};

const getImageUrl = (url) => {
  if (!url) return 'https://placehold.co/50x75/1a1a1a/fff?text=No+Image';
  return url.startsWith('http') ? url : `https://phimimg.com/${url}`;
};

// Debounce function
let searchTimeout = null;
const fetchSuggestions = async () => {
  const query = searchQuery.value.trim();
  
  if (query.length < 2) {
    searchSuggestions.value = [];
    showSuggestions.value = false;
    return;
  }

  isSearching.value = true;
  
  try {
    const { searchMovies } = await import('@/services/movieApi');
    const response = await searchMovies(query, { limit: 5 });
    
    if ((response.status === 'success' || response.status === true) && response.data?.items) {
      searchSuggestions.value = response.data.items.slice(0, 5);
      showSuggestions.value = true;
    } else {
      searchSuggestions.value = [];
      showSuggestions.value = false;
    }
  } catch (error) {
    console.error('Error fetching suggestions:', error);
    searchSuggestions.value = [];
  } finally {
    isSearching.value = false;
  }
};

const onSearchInput = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    fetchSuggestions();
  }, 300); // Debounce 300ms
};

const selectSuggestion = (movie) => {
  showSuggestions.value = false;
  
  // Add movie name to recent searches
  addSearch(movie.name);
  
  router.push(`/film/${movie.slug}`);
};

const handleSearch = () => {
  console.log('🔍 handleSearch called, searchQuery:', searchQuery.value);
  showSuggestions.value = false;
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.trim();
    console.log('🔍 Navigating to /search?q=' + query);
    
    // Add to recent searches
    addSearch(query);
    
    router.push(`/search?q=${encodeURIComponent(query)}`);
  } else {
    console.warn('⚠️ Search query is empty');
  }
};

const closeSuggestions = () => {
  setTimeout(() => {
    showSuggestions.value = false;
  }, 200);
};

const handleLogout = async () => {
  // Import useConfirm dynamically
  const { useConfirm } = await import('@/composables/useConfirm');
  const { confirm } = useConfirm();
  
  // Show confirmation dialog
  const confirmed = await confirm({
    title: 'Đăng xuất tài khoản?',
    message: 'Bạn có chắc chắn muốn đăng xuất khỏi tài khoản?',
    type: 'warning',
    confirmText: 'Đăng xuất',
    cancelText: 'Hủy'
  });
  
  if (!confirmed) {
    return; // User cancelled
  }
  
  try {
    // Use authStore.logout() which handles both Firebase and PHP
    await authStore.logout();
    
    // Show toast
    toast.success('👋 Đã đăng xuất thành công!');
    
    // Reload page sau 1s để user thấy toast
    setTimeout(() => {
      window.location.reload();
    }, 1000);
  } catch (error) {
    console.error('Logout error:', error);
    toast.error('❌ Lỗi khi đăng xuất. Vui lòng thử lại!');
  }
};

onMounted(async () => {
  window.addEventListener('scroll', handleScroll);
  
  // Load categories
  if (!categoryStore.getAllCategories?.length) {
    await categoryStore.getCategory();
  }

  // User is loaded by authStore.initAuth() in main.js
  
  // Fetch subscription if user is logged in
  if (user.value) {
    await fetchUserSubscription();
  }
  
  // Watch for user changes to refetch subscription
  onAuthStateChanged(auth, async (currentUser) => {
    if (currentUser) {
      await fetchUserSubscription();
    } else {
      userSubscription.value = null;
    }
  });
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
  // Restore body scroll when component unmounts
  document.body.style.overflow = '';
});
</script>

<style scoped>
/* Remove default underline from links */
a {
  text-decoration: none !important;
}

.no-underline {
  text-decoration: none !important;
}

/* Remove underline on hover too */
a:hover {
  text-decoration: none !important;
}

/* Slide down animation for mobile menu */
.slide-down-enter-active {
  animation: slideDown 0.3s ease-out;
}

.slide-down-leave-active {
  animation: slideDown 0.3s ease-out reverse;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Accordion animation */
.accordion-enter-active,
.accordion-leave-active {
  transition: all 0.3s ease;
  max-height: 300px;
  overflow: hidden;
}

.accordion-enter-from,
.accordion-leave-to {
  opacity: 0;
  max-height: 0;
}

/* Fade animation for backdrop */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Ensure mobile menu button is hidden on desktop */
@media (min-width: 1024px) {
  .lg\:hidden {
    display: none !important;
  }
}
</style>
