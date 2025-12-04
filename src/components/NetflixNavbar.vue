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
            placeholder="Tìm kiếm phim, diễn viên"
            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 pl-10 text-sm text-white placeholder-gray-400 focus:outline-none focus:border-yellow-400 focus:ring-1 focus:ring-yellow-400 transition"
            @input="onSearchInput"
            @keyup.enter="handleSearch"
            @blur="closeSuggestions"
            @focus="searchQuery.length >= 2 && fetchSuggestions()"
          />
          <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>

          <!-- Suggestions Dropdown -->
          <div 
            v-if="showSuggestions && searchSuggestions.length > 0"
            class="absolute top-full left-0 right-0 mt-2 bg-gray-800 rounded-lg shadow-2xl border border-gray-700 overflow-hidden z-50"
          >
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
        
        <!-- Desktop Menu -->
        <ul class="hidden lg:flex items-center gap-4 text-sm font-medium">
          <li>
            <router-link 
              to="/list/phim-le/page/1" 
              :class="scrolled 
                ? 'text-white hover:text-yellow-400' 
                : 'text-white/90 hover:text-white'"
              class="transition-colors px-2 py-1"
            >
              Phim Lẻ
            </router-link>
          </li>
          <li>
            <router-link 
              to="/list/phim-bo/page/1" 
              :class="scrolled 
                ? 'text-white hover:text-yellow-400' 
                : 'text-white/90 hover:text-white'"
              class="transition-colors px-2 py-1"
            >
              Phim Bộ
            </router-link>
          </li>
          <li class="relative group">
            <button 
              :class="scrolled 
                ? 'text-white hover:text-yellow-400' 
                : 'text-white/90 hover:text-white'"
              class="transition-colors flex items-center gap-1 px-2 py-1"
            >
              Thể loại
              <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
              </svg>
            </button>
            <div class="absolute top-full left-0 mt-2 w-48 bg-gray-800 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all max-h-96 overflow-y-auto border border-gray-700">
              <router-link
                v-for="cat in categories"
                :key="cat.slug"
                :to="`/category/${cat.slug}/page/1`"
                class="block px-4 py-2 text-white hover:bg-gray-700 hover:text-yellow-400 transition-colors text-sm"
              >
                {{ cat.name }}
              </router-link>
            </div>
          </li>
          <li class="relative group">
            <button 
              :class="scrolled 
                ? 'text-white hover:text-yellow-400' 
                : 'text-white/90 hover:text-white'"
              class="transition-colors flex items-center gap-1 px-2 py-1"
            >
              Quốc gia
              <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
              </svg>
            </button>
            <div class="absolute top-full left-0 mt-2 w-48 bg-gray-800 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all max-h-96 overflow-y-auto border border-gray-700">
              <a href="#" class="block px-4 py-2 text-white hover:bg-gray-700 hover:text-yellow-400 transition-colors text-sm">Việt Nam</a>
              <a href="#" class="block px-4 py-2 text-white hover:bg-gray-700 hover:text-yellow-400 transition-colors text-sm">Hàn Quốc</a>
              <a href="#" class="block px-4 py-2 text-white hover:bg-gray-700 hover:text-yellow-400 transition-colors text-sm">Trung Quốc</a>
              <a href="#" class="block px-4 py-2 text-white hover:bg-gray-700 hover:text-yellow-400 transition-colors text-sm">Mỹ</a>
              <a href="#" class="block px-4 py-2 text-white hover:bg-gray-700 hover:text-yellow-400 transition-colors text-sm">Thái Lan</a>
            </div>
          </li>
          <li>
            <a 
              href="#" 
              :class="scrolled 
                ? 'text-white hover:text-yellow-400' 
                : 'text-white/90 hover:text-white'"
              class="transition-colors px-2 py-1"
            >
              Xem Chung
            </a>
          </li>
          <li class="relative group">
            <button 
              :class="scrolled 
                ? 'text-white hover:text-yellow-400' 
                : 'text-white/90 hover:text-white'"
              class="transition-colors flex items-center gap-1 px-2 py-1"
            >
              Thêm
              <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
              </svg>
            </button>
            <div class="absolute top-full left-0 mt-2 w-48 bg-gray-800 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all border border-gray-700">
              <a href="#" class="block px-4 py-2 text-white hover:bg-gray-700 hover:text-yellow-400 transition-colors text-sm">Phim yêu thích</a>
              <a href="#" class="block px-4 py-2 text-white hover:bg-gray-700 hover:text-yellow-400 transition-colors text-sm">Lịch sử xem</a>
            </div>
          </li>
        </ul>
      </div>

      <!-- Right Side -->
      <div class="flex items-center gap-3 flex-shrink-0">
        <!-- Pricing -->
        <router-link
          to="/pricing"
          class="hidden md:flex items-center gap-2 bg-gradient-to-r from-yellow-400 to-yellow-500 text-black px-4 py-2 rounded-lg font-semibold text-sm hover:from-yellow-500 hover:to-yellow-600 transition-all hover:scale-105 shadow-lg"
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
          title="Admin Panel"
        >
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
        </router-link>

        <!-- Cart -->
        <router-link
          to="/cart"
          class="relative p-2 bg-gray-800 rounded-lg hover:bg-gray-700 transition-colors"
        >
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
          <span
            v-if="cartStore.count > 0"
            class="absolute -top-1 -right-1 bg-red-600 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center"
          >
            {{ cartStore.count }}
          </span>
        </router-link>

        <!-- User -->
        <div class="relative group">
          <button class="flex items-center gap-2 bg-gray-800 px-3 py-2 rounded-lg hover:bg-gray-700 hover:scale-105 transition-all duration-300">
            <!-- User Avatar -->
            <img 
              v-if="user"
              :src="user?.avatar || user?.photoURL || `https://ui-avatars.com/api/?name=${encodeURIComponent(user?.full_name || user?.displayName || user?.username || 'User')}&background=f59e0b&color=000`"
              class="w-7 h-7 rounded-full border-2 border-yellow-400 shadow-lg"
              :alt="user?.full_name || user?.displayName || user?.username || 'User'"
            />
            <!-- Default Icon if not logged in -->
            <svg v-else class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span class="text-sm font-medium text-white hidden md:block">{{ user ? (user.displayName || user.username || 'Thành viên') : 'Thành viên' }}</span>
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
                  <div class="flex items-center gap-1.5">
                    <p class="text-white font-bold truncate text-sm">
                      {{ user?.full_name || user?.displayName || user?.username }}
                    </p>
                    <svg class="w-3.5 h-3.5 text-yellow-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                  </div>
                  <p class="text-gray-400 text-[10px] truncate">
                    {{ user?.email }}
                  </p>
                </div>
              </div>
              
              <!-- Upgrade Button -->
              <button class="w-full bg-gradient-to-r from-yellow-400 to-yellow-500 text-black px-3 py-2 rounded-lg font-bold text-xs hover:from-yellow-500 hover:to-yellow-600 hover:scale-105 hover:shadow-xl hover:shadow-yellow-400/50 transition-all duration-300 shadow-lg flex items-center justify-center gap-1.5 group/upgrade">
                <svg class="w-3.5 h-3.5 group-hover/upgrade:rotate-12 transition-transform duration-300" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <span class="group-hover/upgrade:tracking-wider transition-all duration-300">Nâng cấp ngay</span>
              </button>
            </div>

            <!-- Stats -->
            <div v-if="user" class="px-4 py-2.5 bg-gray-800/50 border-b border-gray-700/50">
              <div class="flex items-center justify-between text-xs">
                <div class="flex items-center gap-1.5">
                  <svg class="w-3.5 h-3.5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"/>
                    <path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/>
                  </svg>
                  <span class="text-gray-300">Số dư</span>
                </div>
                <div class="flex items-center gap-1.5">
                  <span class="text-white font-bold">0</span>
                  <span class="text-yellow-400 font-bold text-xs">Ⓡ</span>
                  <button class="bg-gray-700 hover:bg-gray-600 hover:scale-110 text-white px-2 py-0.5 rounded text-[10px] font-medium transition-all duration-300">
                    + Nạp
                  </button>
                </div>
              </div>
            </div>

            <!-- Menu Items -->
            <div class="py-1.5">
              <a 
                v-if="user"
                href="#" 
                class="flex items-center gap-2.5 px-4 py-2.5 text-white hover:bg-gray-800 hover:pl-5 transition-all duration-300 group/item"
              >
                <svg class="w-4 h-4 text-gray-400 group-hover/item:text-yellow-400 transition-all duration-300 group-hover/item:scale-110" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                </svg>
                <span class="font-medium text-sm">Yêu thích</span>
              </a>

              <a 
                v-if="user"
                href="#" 
                class="flex items-center gap-2.5 px-4 py-2.5 text-white hover:bg-gray-800 hover:pl-5 transition-all duration-300 group/item"
              >
                <svg class="w-4 h-4 text-gray-400 group-hover/item:text-yellow-400 transition-all duration-300 group-hover/item:scale-110" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/>
                </svg>
                <span class="font-medium text-sm">Danh sách</span>
              </a>

              <a 
                v-if="user"
                href="#" 
                class="flex items-center gap-2.5 px-4 py-2.5 text-white hover:bg-gray-800 hover:pl-5 transition-all duration-300 group/item"
              >
                <svg class="w-4 h-4 text-gray-400 group-hover/item:text-yellow-400 transition-all duration-300 group-hover/item:scale-110" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                </svg>
                <span class="font-medium text-sm">Xem tiếp</span>
              </a>

              <router-link 
                v-if="user"
                to="/account" 
                class="flex items-center gap-2.5 px-4 py-2.5 text-white hover:bg-gray-800 hover:pl-5 transition-all duration-300 group/item"
              >
                <svg class="w-4 h-4 text-gray-400 group-hover/item:text-yellow-400 transition-all duration-300 group-hover/item:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
          @click="mobileMenuOpen = !mobileMenuOpen"
          class="lg:hidden p-2 rounded-lg hover:bg-gray-800 transition-colors"
        >
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
      </div>
    </div>

    <!-- Mobile Menu -->
    <div 
      v-if="mobileMenuOpen"
      class="lg:hidden bg-gray-900/98 backdrop-blur-lg border-t border-gray-800 shadow-2xl"
    >
      <div class="px-4 py-6 space-y-2">
        <router-link 
          to="/home" 
          @click="mobileMenuOpen = false"
          class="block px-4 py-3 text-white hover:bg-gray-800 hover:text-yellow-400 rounded-lg transition-all font-medium"
        >
          <div class="flex items-center gap-3">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
            </svg>
            Trang chủ
          </div>
        </router-link>
        
        <router-link 
          to="/list/phim-le/page/1" 
          @click="mobileMenuOpen = false"
          class="block px-4 py-3 text-white hover:bg-gray-800 hover:text-yellow-400 rounded-lg transition-all font-medium"
        >
          <div class="flex items-center gap-3">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
            </svg>
            Phim Lẻ
          </div>
        </router-link>
        
        <router-link 
          to="/list/phim-bo/page/1" 
          @click="mobileMenuOpen = false"
          class="block px-4 py-3 text-white hover:bg-gray-800 hover:text-yellow-400 rounded-lg transition-all font-medium"
        >
          <div class="flex items-center gap-3">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
            </svg>
            Phim Bộ
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
  </nav>

  <!-- Auth Modal -->
  <AuthModal v-model="showAuthModal" @success="loadUser" />
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import { getAuth, signOut, onAuthStateChanged } from 'firebase/auth';
import { useCategoryStore } from '@/stores/Category/category.js';
import { useCartStore } from '@/stores/cartStore';
import authService from '@/services/authService';
import AuthModal from './AuthModal.vue';
import { useToast } from '@/composables/useToast';

const router = useRouter();
const categoryStore = useCategoryStore();
const cartStore = useCartStore();
const auth = getAuth();
const toast = useToast();

const scrolled = ref(false);
const searchQuery = ref('');
const user = ref(null);
const mobileMenuOpen = ref(false);
const searchSuggestions = ref([]);
const showSuggestions = ref(false);
const isSearching = ref(false);
const showAuthModal = ref(false);

// Load categories from store
categoryStore.getCategory();

// Load user from both Firebase and PHP
const loadUser = () => {
  // Check Firebase first
  const firebaseUser = auth.currentUser;
  if (firebaseUser) {
    user.value = firebaseUser;
    console.log('🔐 Navbar - Firebase user:', firebaseUser.email);
    return;
  }
  
  // Check PHP localStorage as fallback
  const phpUser = authService.getCurrentUser();
  if (phpUser) {
    user.value = phpUser;
    console.log('🔐 Navbar - PHP user:', phpUser.email);
  }
  
  // Listen for Firebase auth changes
  onAuthStateChanged(auth, (currentUser) => {
    if (currentUser) {
      user.value = currentUser;
      console.log('🔐 Navbar - Firebase auth changed:', currentUser.email);
    } else {
      // If Firebase logout, check PHP
      const phpUser = authService.getCurrentUser();
      if (phpUser) {
        user.value = phpUser;
      } else {
        user.value = null;
      }
    }
  });
};

const categories = computed(() => {
  return categoryStore.getAllCategories?.slice(0, 10) || [];
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
  router.push(`/film/${movie.slug}`);
};

const handleSearch = () => {
  console.log('🔍 handleSearch called, searchQuery:', searchQuery.value);
  showSuggestions.value = false;
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.trim();
    console.log('🔍 Navigating to /search?q=' + query);
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
  try {
    // Logout Firebase (nếu đăng nhập bằng Google)
    await signOut(auth);
    
    // Logout PHP (xóa localStorage)
    authService.logout();
    
    // Clear user
    user.value = null;
    
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

  // Load user from localStorage
  loadUser();
  
  // Load cart
  await cartStore.fetchCart();
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
});
</script>
