<template>
  <div class="min-h-screen bg-gradient-to-b from-black via-gray-900 to-black">
    <!-- Offline Banner -->
    <OfflineBanner :show="showOfflineBanner" :show-retry="true" @retry="handleRetry" />
    
    <!-- Progress Steps -->
    <div class="bg-gray-900/50 border-b border-gray-800 sticky top-0 z-40 backdrop-blur-sm">
      <div class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-center max-w-3xl mx-auto">
          <!-- Step 1: Cart -->
          <div class="flex items-center">
            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-green-600 text-white">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
              </svg>
            </div>
            <span class="ml-2 text-sm text-gray-400 hidden sm:inline">Giỏ hàng</span>
          </div>

          <div class="w-16 sm:w-24 h-0.5 bg-red-600 mx-2"></div>

          <!-- Step 2: Checkout -->
          <div class="flex items-center">
            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-r from-red-600 to-yellow-500 text-white font-bold">
              2
            </div>
            <span class="ml-2 text-sm text-white font-medium hidden sm:inline">Thanh toán</span>
          </div>

          <div class="w-16 sm:w-24 h-0.5 bg-gray-700 mx-2"></div>

          <!-- Step 3: Complete -->
          <div class="flex items-center">
            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-700 text-gray-400 font-bold">
              3
            </div>
            <span class="ml-2 text-sm text-gray-400 hidden sm:inline">Hoàn tất</span>
          </div>
        </div>
      </div>
    </div>

    <div class="container mx-auto px-4 py-8 max-w-7xl">
      <!-- Loading -->
      <div v-if="cartStore.loading" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <div class="lg:col-span-2 space-y-6">
            <LoadingSkeleton type="list" :count="2" />
          </div>
          <div class="lg:col-span-1">
            <LoadingSkeleton type="cart" />
          </div>
        </div>
      </div>

      <!-- Empty Cart -->
      <div v-else-if="cartStore.isEmpty" class="max-w-md mx-auto text-center py-20">
        <div class="w-24 h-24 mx-auto mb-6 bg-gray-800 rounded-full flex items-center justify-center">
          <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
        </div>
        <h2 class="text-2xl font-bold text-white mb-4">Giỏ hàng trống</h2>
        <p class="text-gray-400 mb-6">Vui lòng thêm gói vào giỏ hàng trước khi thanh toán</p>
        <router-link
          to="/pricing"
          class="inline-flex items-center gap-2 bg-gradient-to-r from-red-600 to-yellow-500 text-white px-6 py-3 rounded-xl font-bold hover:from-red-700 hover:to-yellow-600 transition-all"
        >
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
          </svg>
          Xem các gói
        </router-link>
      </div>

      <!-- Checkout Content -->
      <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left: Form -->
        <div class="lg:col-span-2 space-y-6">
          <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Customer Info -->
            <div class="bg-gradient-to-br from-gray-900/90 to-gray-800/90 backdrop-blur-sm rounded-2xl p-6 border border-gray-700/50">
              <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-600 to-blue-700 flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                  </svg>
                </div>
                <h2 class="text-2xl font-bold text-white">Thông tin khách hàng</h2>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                  <label class="flex items-center gap-2 text-gray-400 text-sm font-medium mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Họ và tên
                    <span class="text-red-500">*</span>
                  </label>
                  <input
                    v-model="form.customer_name"
                    type="text"
                    required
                    class="w-full bg-gray-800/80 backdrop-blur-sm text-white px-4 py-3 rounded-xl border border-gray-700 focus:border-red-600 focus:outline-none focus:ring-2 focus:ring-red-600/20 transition-all placeholder:text-gray-500"
                    placeholder="Nguyễn Văn A"
                    @blur="onFieldBlur('customer_name')"
                  />
                  <p v-if="validationErrors.customer_name" class="text-red-400 text-sm mt-1">{{ validationErrors.customer_name }}</p>
                </div>

                <div>
                  <label class="flex items-center gap-2 text-gray-400 text-sm font-medium mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Email
                    <span class="text-red-500">*</span>
                  </label>
                  <input
                    v-model="form.customer_email"
                    type="email"
                    required
                    class="w-full bg-gray-800/80 backdrop-blur-sm text-white px-4 py-3 rounded-xl border border-gray-700 focus:border-red-600 focus:outline-none focus:ring-2 focus:ring-red-600/20 transition-all placeholder:text-gray-500"
                    placeholder="email@example.com"
                    @blur="onFieldBlur('customer_email')"
                  />
                  <p v-if="validationErrors.customer_email" class="text-red-400 text-sm mt-1">{{ validationErrors.customer_email }}</p>
                </div>

                <div>
                  <label class="flex items-center gap-2 text-gray-400 text-sm font-medium mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    Số điện thoại
                  </label>
                  <input
                    v-model="form.customer_phone"
                    type="tel"
                    class="w-full bg-gray-800/80 backdrop-blur-sm text-white px-4 py-3 rounded-xl border border-gray-700 focus:border-red-600 focus:outline-none focus:ring-2 focus:ring-red-600/20 transition-all placeholder:text-gray-500"
                    placeholder="0901234567"
                    @blur="onFieldBlur('customer_phone')"
                  />
                  <p v-if="validationErrors.customer_phone" class="text-red-400 text-sm mt-1">{{ validationErrors.customer_phone }}</p>
                </div>

                <div class="md:col-span-2">
                  <label class="flex items-center gap-2 text-gray-400 text-sm font-medium mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                    </svg>
                    Ghi chú
                  </label>
                  <textarea
                    v-model="form.note"
                    rows="3"
                    class="w-full bg-gray-800/80 backdrop-blur-sm text-white px-4 py-3 rounded-xl border border-gray-700 focus:border-red-600 focus:outline-none focus:ring-2 focus:ring-red-600/20 transition-all placeholder:text-gray-500 resize-none"
                    placeholder="Ghi chú thêm cho đơn hàng (nếu có)"
                  ></textarea>
                </div>
              </div>
            </div>

            <!-- Payment Method -->
            <div class="bg-gradient-to-br from-gray-900/90 to-gray-800/90 backdrop-blur-sm rounded-2xl p-6 border border-gray-700/50">
              <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-600 to-green-700 flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                  </svg>
                </div>
                <h2 class="text-2xl font-bold text-white">Phương thức thanh toán</h2>
              </div>
              
              <div class="space-y-4">
                <label
                  v-for="method in paymentMethods"
                  :key="method.value"
                  class="relative group cursor-pointer"
                >
                  <input
                    v-model="form.payment_method"
                    type="radio"
                    :value="method.value"
                    class="peer sr-only"
                  />
                  
                  <!-- Modern Payment Card -->
                  <div 
                    class="relative overflow-hidden p-5 bg-gradient-to-br from-gray-800/80 to-gray-900/80 backdrop-blur-sm rounded-2xl border-2 transition-all duration-300 hover:border-gray-600 group-hover:scale-[1.02] group-hover:shadow-xl"
                    :class="form.payment_method === method.value ? 'border-red-500 shadow-lg shadow-red-500/20' : 'border-gray-700/50'"
                  >
                    <!-- Selected Indicator -->
                    <div class="absolute top-3 right-3">
                      <div 
                        class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all duration-300 group-hover:scale-110"
                        :class="form.payment_method === method.value ? 'border-red-500 bg-red-500' : 'border-gray-600'"
                      >
                        <svg 
                          class="w-4 h-4 text-white transition-opacity duration-300" 
                          :class="form.payment_method === method.value ? 'opacity-100' : 'opacity-0'"
                          fill="currentColor" 
                          viewBox="0 0 20 20"
                        >
                          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                      </div>
                    </div>

                    <!-- Gradient Accent (when selected) -->
                    <div 
                      class="absolute inset-0 bg-gradient-to-br transition-opacity duration-300" 
                      :class="[method.color, form.payment_method === method.value ? 'opacity-10' : 'opacity-0']"
                    ></div>

                    <div class="relative flex items-start gap-4">
                      <!-- Icon -->
                      <div class="flex-shrink-0">
                        <div class="w-14 h-14 rounded-xl bg-gradient-to-br flex items-center justify-center shadow-lg transition-transform group-hover:scale-110" :class="[method.color, method.iconClass]">
                          <!-- Image Icon -->
                          <img v-if="method.icon" :src="method.icon" :alt="method.label" class="w-10 h-10 object-contain" />
                          
                          <!-- SVG Icon for Bank Transfer -->
                          <svg v-else-if="method.svgIcon" class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                          </svg>
                        </div>
                      </div>

                      <!-- Content -->
                      <div class="flex-1 min-w-0 pt-1">
                        <h3 class="text-white font-bold text-base mb-1.5 group-hover:text-red-400 transition-colors">
                          {{ method.label }}
                        </h3>
                        <p class="text-gray-400 text-xs leading-relaxed">
                          {{ method.description }}
                        </p>
                      </div>
                    </div>

                    <!-- Bottom Accent Line (when selected) -->
                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r opacity-0 peer-checked:opacity-100 transition-opacity duration-300" :class="method.color"></div>
                  </div>
                </label>
              </div>
            </div>

            <!-- Auto-save indicator -->
            <div v-if="lastSaved" class="flex items-center justify-center gap-2 text-xs text-gray-500">
              <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
              <span>Đã lưu tự động lúc {{ lastSaved.toLocaleTimeString('vi-VN') }}</span>
            </div>

            <!-- Submit Button -->
            <button
              type="submit"
              :disabled="submitting || !isFormValid || !isOnline"
              class="w-full bg-gradient-to-r from-red-600 to-yellow-500 text-white py-4 rounded-xl font-bold text-lg hover:from-red-700 hover:to-yellow-600 transition-all disabled:opacity-50 disabled:cursor-not-allowed hover:scale-105 active:scale-95 shadow-lg shadow-red-600/30 flex items-center justify-center gap-2"
            >
              <svg v-if="submitting" class="animate-spin h-5 w-5" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
              </svg>
              <svg v-else-if="!isOnline" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3m8.293 8.293l1.414 1.414"/>
              </svg>
              <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <span>{{ submitting ? 'Đang xử lý...' : !isOnline ? 'Không có kết nối' : 'Xác nhận đặt hàng' }}</span>
            </button>

            <!-- Security Note -->
            <div class="flex items-center justify-center gap-6 text-gray-400 text-xs">
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                </svg>
                <span>Bảo mật SSL</span>
              </div>
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>Thanh toán an toàn</span>
              </div>
            </div>
          </form>
        </div>

        <!-- Right: Order Summary -->
        <div class="lg:col-span-1">
          <div class="sticky top-24 space-y-4">
            <!-- Order Summary Card -->
            <div class="bg-gradient-to-br from-gray-900/95 to-gray-800/95 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-2xl">
              <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-600 to-purple-700 flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                  </svg>
                </div>
                <h2 class="text-xl font-bold text-white">Đơn hàng của bạn</h2>
              </div>

              <!-- Items List -->
              <div class="space-y-3 mb-4 pb-4 border-b border-gray-700/50">
                <div
                  v-for="item in cartStore.items"
                  :key="item.id"
                  class="group p-3 bg-gray-800/50 rounded-xl hover:bg-gray-800 transition-all"
                >
                  <div class="flex items-start gap-3">
                    <div 
                      class="w-12 h-12 rounded-lg flex items-center justify-center text-xl flex-shrink-0"
                      :class="getIconBgClass(item.plan_slug)"
                    >
                      {{ getIcon(item.plan_slug) }}
                    </div>
                    <div class="flex-1 min-w-0">
                      <div class="flex items-start justify-between mb-1">
                        <h3 class="text-white font-bold text-sm">{{ item.plan_name }}</h3>
                        <span class="text-white font-bold text-sm">{{ item.subtotal_formatted }}</span>
                      </div>
                      <p class="text-gray-400 text-xs">
                        {{ item.quantity }} tháng × {{ item.price_formatted }}
                      </p>
                      <div class="flex flex-wrap gap-1 mt-2">
                        <span class="bg-gray-700 text-white px-2 py-0.5 rounded text-xs">{{ item.quality }}</span>
                        <span class="bg-blue-600 text-white px-2 py-0.5 rounded text-xs">{{ item.max_devices }} thiết bị</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Coupon Code -->
              <div class="mb-4">
                <label class="flex items-center gap-2 text-gray-400 text-sm font-medium mb-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                  </svg>
                  Mã giảm giá
                </label>
                <div class="flex gap-2">
                  <input
                    v-model="couponCode"
                    type="text"
                    placeholder="Nhập mã (VD: SUMMER2024)"
                    class="flex-1 bg-gray-800/80 backdrop-blur-sm text-white px-3 py-2 rounded-lg border border-gray-700 focus:border-red-600 focus:outline-none focus:ring-2 focus:ring-red-600/20 transition-all placeholder:text-gray-500 text-sm uppercase"
                    @keyup.enter="applyCoupon"
                    maxlength="20"
                  />
                  <button
                    @click="applyCoupon"
                    :disabled="applyingCoupon || !couponCode.trim()"
                    class="bg-gradient-to-r from-red-600 to-red-700 text-white px-4 py-2 rounded-lg hover:from-red-700 hover:to-red-800 disabled:opacity-50 disabled:cursor-not-allowed transition-all font-bold text-sm hover:scale-105 active:scale-95"
                  >
                    {{ applyingCoupon ? '...' : 'Áp dụng' }}
                  </button>
                </div>
                
                <Transition name="fade">
                  <p v-if="couponError" class="flex items-center gap-2 text-red-400 text-xs mt-2 bg-red-500/10 px-2 py-1.5 rounded border border-red-500/20">
                    <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    {{ couponError }}
                  </p>
                </Transition>
                
                <Transition name="fade">
                  <p v-if="appliedCoupon" class="flex items-center gap-2 text-green-400 text-xs mt-2 bg-green-500/10 px-2 py-1.5 rounded border border-green-500/20">
                    <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Đã áp dụng mã <span class="font-bold">{{ appliedCoupon.code }}</span>
                  </p>
                </Transition>
              </div>

              <!-- Price Breakdown -->
              <div class="space-y-2 mb-4 pb-4 border-b border-gray-700/50">
                <div class="flex justify-between text-gray-400 text-sm">
                  <span>Tạm tính</span>
                  <span class="font-semibold text-white">{{ cartStore.cartTotalFormatted }}</span>
                </div>
                <Transition name="fade">
                  <div v-if="appliedCoupon" class="flex justify-between text-green-400 font-bold text-sm">
                    <span>Giảm giá</span>
                    <span>-{{ appliedCoupon.discount_amount_formatted }}</span>
                  </div>
                </Transition>
              </div>

              <!-- Total -->
              <div class="flex justify-between items-center mb-4 p-3 bg-gradient-to-r from-red-600/10 to-yellow-500/10 rounded-xl border border-red-600/20">
                <span class="text-gray-300 font-semibold">Tổng cộng</span>
                <span class="text-2xl font-black bg-gradient-to-r from-red-500 to-yellow-500 bg-clip-text text-transparent">
                  {{ finalTotal }}
                </span>
              </div>

              <!-- Back to Cart -->
              <router-link
                to="/cart"
                class="flex items-center justify-center gap-2 w-full text-gray-400 hover:text-white transition-all text-sm py-2 hover:bg-gray-800/50 rounded-lg"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Quay lại giỏ hàng
              </router-link>
            </div>

            <!-- Trust Badges -->
            <div class="bg-gray-900/50 backdrop-blur-sm rounded-xl p-4 border border-gray-800">
              <div class="space-y-3">
                <div class="flex items-center gap-3 text-gray-400 text-xs">
                  <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                  </svg>
                  <div>
                    <p class="text-white font-medium">Thanh toán an toàn 100%</p>
                    <p class="text-gray-500">Mã hóa SSL 256-bit</p>
                  </div>
                </div>
                <div class="flex items-center gap-3 text-gray-400 text-xs">
                  <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                  </svg>
                  <div>
                    <p class="text-white font-medium">Hủy bất kỳ lúc nào</p>
                    <p class="text-gray-500">Không ràng buộc dài hạn</p>
                  </div>
                </div>
                <div class="flex items-center gap-3 text-gray-400 text-xs">
                  <svg class="w-5 h-5 text-yellow-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                  </svg>
                  <div>
                    <p class="text-white font-medium">Hỗ trợ 24/7</p>
                    <p class="text-gray-500">Luôn sẵn sàng giúp đỡ</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useCartStore } from '@/stores/cartStore';
import { useAuthStore } from '@/stores/authStore';
import { createOrder, validateCoupon } from '@/services/ecommerceApi';
import { useToast } from '@/composables/useToast';
import { useFormValidation } from '@/composables/useFormValidation';
import { useFormAutoSave } from '@/composables/useFormAutoSave';
import { useNetworkStatus } from '@/composables/useNetworkStatus';
import paymentService from '@/services/paymentService';
import axios from 'axios';
import LoadingSkeleton from '@/components/LoadingSkeleton.vue';
import ErrorBoundary from '@/components/ErrorBoundary.vue';
import OfflineBanner from '@/components/OfflineBanner.vue';

const router = useRouter();
const cartStore = useCartStore();
const authStore = useAuthStore();
const toast = useToast();
const { isOnline, showOfflineBanner } = useNetworkStatus();

const submitting = ref(false);
const couponCode = ref('');
const appliedCoupon = ref(null);
const applyingCoupon = ref(false);
const couponError = ref(null);

const form = reactive({
  customer_name: '',
  customer_email: '',
  customer_phone: '',
  payment_method: 'bank_transfer',
  note: ''
});

// Use form validation composable
const { errors: validationErrors, rules, validateField, validateForm, isFormValid: formIsValid } = useFormValidation();

// Auto-save form data
const { lastSaved, loadFromStorage, clearStorage } = useFormAutoSave('checkout', form, {
  debounceMs: 3000,
  enabled: true
});

const saveInfo = ref(false);

const paymentMethods = [
  {
    value: 'vnpay',
    label: 'VNPay',
    description: 'Thanh toán qua VNPay (ATM, Visa, MasterCard)',
    icon: 'https://cdn.haitrieu.com/wp-content/uploads/2022/10/Logo-VNPAY-QR.png',
    color: 'from-white to-gray-100',
    iconClass: 'p-2'
  },
  {
    value: 'momo',
    label: 'MoMo',
    description: 'Thanh toán qua ví điện tử MoMo',
    icon: 'https://cdn.haitrieu.com/wp-content/uploads/2022/10/Logo-MoMo-Square.png',
    color: 'from-pink-500 to-pink-600'
  },
  {
    value: 'bank_transfer',
    label: 'Chuyển khoản ngân hàng',
    description: 'Chuyển khoản trực tiếp qua ngân hàng',
    svgIcon: true,
    color: 'from-green-500 to-green-600'
  }
];

const finalTotal = computed(() => {
  if (appliedCoupon.value) {
    return appliedCoupon.value.final_total_formatted;
  }
  return cartStore.cartTotalFormatted;
});

const isFormValid = computed(() => {
  return form.customer_name.trim() !== '' && 
         form.customer_email.trim() !== '' &&
         /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.customer_email) &&
         formIsValid.value;
});

onMounted(async () => {
  await cartStore.fetchCart();

  // Try to restore form draft first
  const savedDraft = loadFromStorage();
  if (savedDraft) {
    Object.assign(form, savedDraft);
    toast.info('📝 Đã khôi phục thông tin đã nhập trước đó');
    return;
  }

  // Pre-fill user info from localStorage (updated from profile page)
  if (authStore.user) {
    // Get fresh user data from localStorage
    const storedUser = localStorage.getItem('user');
    const userData = storedUser ? JSON.parse(storedUser) : authStore.user;
    
    form.customer_name = userData.full_name || userData.displayName || userData.username || '';
    form.customer_email = userData.email || '';
    form.customer_phone = userData.phone || userData.phone_number || '';
    
    console.log('📝 Checkout - Auto-filled:', { name: form.customer_name, email: form.customer_email, phone: form.customer_phone });
  }
});

const onFieldBlur = (fieldName) => {
  const validationSchema = {
    customer_name: {
      value: form.customer_name,
      rules: [rules.required, rules.minLength(2)],
      label: 'Họ tên'
    },
    customer_email: {
      value: form.customer_email,
      rules: [rules.required, rules.email],
      label: 'Email'
    },
    customer_phone: {
      value: form.customer_phone,
      rules: [rules.phone],
      label: 'Số điện thoại'
    }
  };
  
  if (validationSchema[fieldName]) {
    validateField(
      fieldName, 
      validationSchema[fieldName].value, 
      validationSchema[fieldName].rules, 
      validationSchema[fieldName].label
    );
  }
};

const validateAllFields = () => {
  const validationSchema = {
    customer_name: {
      value: form.customer_name,
      rules: [rules.required, rules.minLength(2)],
      label: 'Họ tên'
    },
    customer_email: {
      value: form.customer_email,
      rules: [rules.required, rules.email],
      label: 'Email'
    },
    customer_phone: {
      value: form.customer_phone,
      rules: [rules.phone],
      label: 'Số điện thoại'
    }
  };
  
  return validateForm(form, validationSchema);
};

const oldValidateField = (field) => {
  if (field === 'customer_name') {
    validationErrors.customer_name = form.customer_name.trim() === '' ? 'Vui lòng nhập họ tên' : '';
  } else if (field === 'customer_email') {
    if (form.customer_email.trim() === '') {
      validationErrors.customer_email = 'Vui lòng nhập email';
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.customer_email)) {
      errors.customer_email = 'Email không hợp lệ';
    } else {
      errors.customer_email = '';
    }
  }
};

const applyCoupon = async () => {
  // Sanitize input
  const sanitizedCode = couponCode.value.trim().toUpperCase();
  if (!sanitizedCode) return;

  applyingCoupon.value = true;
  couponError.value = null;

  try {
    const response = await validateCoupon(
      sanitizedCode,
      cartStore.total,
      authStore.user?.id
    );

    if (response.success) {
      appliedCoupon.value = response.data;
      toast.success(response.message);
    }
  } catch (error) {
    const errorMsg = error.response?.data?.message || 'Mã giảm giá không hợp lệ hoặc đã hết hạn';
    couponError.value = errorMsg + '. Vui lòng kiểm tra lại mã hoặc liên hệ hỗ trợ.';
    appliedCoupon.value = null;
    toast.error(errorMsg);
  } finally {
    applyingCoupon.value = false;
  }
};

const handleSubmit = async () => {
  // Validate all fields
  validateField('customer_name');
  validateField('customer_email');

  if (!isFormValid.value) {
    toast.error('Vui lòng điền đầy đủ thông tin');
    return;
  }

  if (cartStore.isEmpty) {
    toast.error('Giỏ hàng trống');
    return;
  }

  if (!authStore.user) {
    toast.error('Vui lòng đăng nhập');
    router.push('/account');
    return;
  }

  // Import useConfirm dynamically
  const { useConfirm } = await import('@/composables/useConfirm');
  const { confirm } = useConfirm();
  
  // Show final payment confirmation
  const paymentMethodName = paymentMethods.find(m => m.value === form.payment_method)?.label || 'Không xác định';
  const confirmed = await confirm({
    title: 'Xác nhận thanh toán',
    message: `Bạn có chắc chắn muốn thanh toán ${finalTotal.value} qua ${paymentMethodName}?`,
    type: 'info',
    confirmText: 'Xác nhận thanh toán',
    cancelText: 'Kiểm tra lại'
  });
  
  if (!confirmed) {
    return; // User cancelled
  }

  submitting.value = true;

  try {
    // Lấy MySQL user_id từ localStorage
    const storedUser = localStorage.getItem('user');
    let userId = null;
    
    if (storedUser) {
      try {
        const userData = JSON.parse(storedUser);
        userId = userData.id; // MySQL ID
      } catch (e) {
        console.error('Error parsing user data:', e);
      }
    }
    
    // Fallback to authStore
    if (!userId) {
      userId = authStore.user?.id || authStore.user?.uid;
    }
    
    console.log('Creating order from cart with user_id:', userId);
    
    // Add minimum delay for UX
    const [response] = await Promise.all([
      createOrder({
        user_id: userId,
        customer_name: form.customer_name,
        customer_email: form.customer_email,
        customer_phone: form.customer_phone,
        payment_method: form.payment_method,
        note: form.note,
        coupon_code: appliedCoupon.value?.code || null
      }),
      new Promise(resolve => setTimeout(resolve, 800))
    ]);

    if (response.success) {
      // Clear form draft after successful order
      clearStorage();
      
      const orderCode = response.data.order_code;
      const orderTotal = response.data.total;
      
      // Xử lý thanh toán theo phương thức
      if (form.payment_method === 'vnpay') {
        // VNPay
        const paymentData = {
          order_code: orderCode,
          amount: orderTotal,
          order_info: `Thanh toan don hang ${orderCode}`
        };
        const paymentResponse = await paymentService.createVNPayPayment(paymentData);
        if (paymentResponse.success) {
          window.location.href = paymentResponse.payment_url;
        }
      } else if (form.payment_method === 'momo') {
        // MoMo
        const paymentData = {
          order_code: orderCode,
          amount: orderTotal,
          order_info: `Thanh toan don hang ${orderCode}`
        };
        const paymentResponse = await paymentService.createMoMoPayment(paymentData);
        if (paymentResponse.success) {
          window.location.href = paymentResponse.payment_url;
        }
      } else if (form.payment_method === 'zalopay') {
        // ZaloPay
        const paymentData = {
          order_code: orderCode,
          amount: orderTotal,
          order_info: `Thanh toan don hang ${orderCode}`
        };
        const paymentResponse = await paymentService.createZaloPayPayment(paymentData);
        if (paymentResponse.success) {
          window.location.href = paymentResponse.payment_url;
        }
      } else if (form.payment_method === 'bank_transfer') {
        // Bank Transfer - Chờ xác nhận
        toast.success('Đặt hàng thành công! Vui lòng chuyển khoản để kích hoạt gói.');
        router.push(`/payment-return?order_code=${orderCode}&status=pending&method=bank_transfer`);
      } else {
        // COD - Kích hoạt ngay
        toast.success('Đặt hàng thành công!');
        // Kích hoạt gói ngay cho COD
        try {
          await axios.post(`${import.meta.env.VITE_API_BASE_URL}/payment/activate_subscription.php`, {
            order_code: orderCode
          });
        } catch (error) {
          console.error('Failed to activate subscription:', error);
        }
        router.push(`/payment-return?order_code=${orderCode}&status=success`);
      }
    }
  } catch (error) {
    const errorMsg = error.response?.data?.message || 'Không thể tạo đơn hàng';
    const helpText = !isOnline.value 
      ? ' Vui lòng kiểm tra kết nối Internet và thử lại.'
      : ' Vui lòng thử lại hoặc liên hệ hỗ trợ nếu vấn đề vẫn tiếp diễn.';
    
    toast.error(errorMsg + helpText);
  } finally {
    submitting.value = false;
  }
};

const handleRetry = async () => {
  if (isOnline.value) {
    await cartStore.fetchCart();
    toast.success('Đã kết nối lại');
  }
};

const getIcon = (slug) => {
  const icons = {
    free: '🎬',
    basic: '⭐',
    premium: '🔥',
    vip: '👑'
  };
  return icons[slug] || '🎬';
};

const getIconBgClass = (slug) => {
  const classes = {
    free: 'bg-gradient-to-br from-gray-700 to-gray-600',
    basic: 'bg-gradient-to-br from-blue-600 to-cyan-500',
    premium: 'bg-gradient-to-br from-red-600 to-yellow-500',
    vip: 'bg-gradient-to-br from-purple-600 to-pink-500'
  };
  return classes[slug] || classes.free;
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: all 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
