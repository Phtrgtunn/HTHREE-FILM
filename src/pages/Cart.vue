<template>
  <div class="min-h-screen bg-gradient-to-b from-black via-gray-900 to-black">
    <!-- Confirm Modal -->
    <ConfirmModal
      v-model="showConfirmModal"
      :title="confirmModal.title"
      :message="confirmModal.message"
      :type="confirmModal.type"
      :confirm-text="confirmModal.confirmText"
      @confirm="confirmModal.onConfirm"
    />
    <!-- Header -->
    <div class="relative overflow-hidden">
      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-5">
        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 40px 40px;"></div>
      </div>
      
      <div class="container mx-auto px-4 py-10 relative">
        <!-- Breadcrumb -->
        <div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
          <router-link to="/" class="hover:text-white transition-colors">Trang chủ</router-link>
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
          <router-link to="/pricing" class="hover:text-white transition-colors">Gói dịch vụ</router-link>
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
          <span class="text-white font-medium">Giỏ hàng</span>
        </div>

        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <!-- Cart Icon -->
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-red-600 to-yellow-500 flex items-center justify-center shadow-lg shadow-red-500/20">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
            </div>
            <div>
              <h1 class="text-4xl font-black text-white mb-1 bg-gradient-to-r from-white to-gray-300 bg-clip-text text-transparent">
                Giỏ hàng của bạn
              </h1>
              <p class="text-gray-400 flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-600 text-white text-xs font-bold">
                  {{ cartStore.count }}
                </span>
                gói đã chọn
              </p>
            </div>
          </div>
          <router-link
            to="/pricing"
            class="group flex items-center gap-2 bg-gray-800/50 backdrop-blur-sm text-gray-300 hover:text-white px-6 py-3 rounded-xl transition-all hover:bg-gray-800 border border-gray-700 hover:border-gray-600"
          >
            <svg class="w-5 h-5 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Tiếp tục mua sắm
          </router-link>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="container mx-auto px-4 py-12">
      <!-- Loading -->
      <div v-if="cartStore.loading" class="text-center py-20">
        <div class="inline-block animate-spin rounded-full h-16 w-16 border-4 border-t-red-600 border-r-transparent border-b-red-600 border-l-transparent"></div>
        <p class="text-gray-400 mt-4">Đang tải giỏ hàng...</p>
      </div>

      <!-- Empty Cart -->
      <div v-else-if="cartStore.isEmpty" class="max-w-2xl mx-auto text-center py-20">
        <!-- Animated Empty Cart Illustration -->
        <div class="relative w-48 h-48 mx-auto mb-8">
          <!-- Background Circle -->
          <div class="absolute inset-0 bg-gradient-to-br from-gray-800 to-gray-900 rounded-full animate-pulse"></div>
          
          <!-- Cart Icon -->
          <div class="absolute inset-0 flex items-center justify-center">
            <svg class="w-24 h-24 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
          </div>
          
          <!-- Floating Dots -->
          <div class="absolute top-0 right-0 w-3 h-3 bg-red-500 rounded-full animate-bounce"></div>
          <div class="absolute bottom-0 left-0 w-2 h-2 bg-yellow-500 rounded-full animate-bounce" style="animation-delay: 0.2s;"></div>
        </div>

        <h2 class="text-4xl font-black text-white mb-4">Giỏ hàng trống</h2>
        <p class="text-gray-400 text-lg mb-10 max-w-md mx-auto">
          Hãy chọn gói phù hợp với bạn để bắt đầu trải nghiệm xem phim không giới hạn
        </p>

        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <router-link
            to="/pricing"
            class="group inline-flex items-center justify-center gap-2 bg-gradient-to-r from-red-600 to-yellow-500 text-white px-8 py-4 rounded-xl font-bold hover:from-red-700 hover:to-yellow-600 transition-all hover:scale-105 shadow-lg shadow-red-500/20"
          >
            <svg class="w-5 h-5 transition-transform group-hover:rotate-12" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
            Xem các gói dịch vụ
          </router-link>
          
          <router-link
            to="/"
            class="inline-flex items-center justify-center gap-2 bg-gray-800/50 backdrop-blur-sm text-white px-8 py-4 rounded-xl font-bold hover:bg-gray-800 transition-all border border-gray-700 hover:border-gray-600"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
            </svg>
            Khám phá phim
          </router-link>
        </div>
      </div>

      <!-- Cart Items -->
      <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
        <!-- Items List -->
        <div class="lg:col-span-2 space-y-5">
          <TransitionGroup name="cart-item">
            <div
              v-for="item in cartStore.items"
              :key="item.id"
              class="group relative bg-gradient-to-br from-gray-900/90 to-gray-800/90 backdrop-blur-sm rounded-2xl p-6 border border-gray-700/50 hover:border-gray-600 transition-all duration-300 hover:shadow-2xl hover:shadow-red-500/10 hover:-translate-y-1"
            >
              <!-- Glow Effect on Hover -->
              <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-red-600/0 to-yellow-500/0 group-hover:from-red-600/5 group-hover:to-yellow-500/5 transition-all duration-300"></div>
              
              <div class="relative flex items-start gap-6">
                <!-- Icon with Animation -->
                <div 
                  class="w-20 h-20 rounded-2xl flex items-center justify-center text-3xl shadow-lg flex-shrink-0 transition-transform group-hover:scale-110 group-hover:rotate-3"
                  :class="getIconBgClass(item.plan_slug)"
                >
                  {{ getIcon(item.plan_slug) }}
                </div>

                <!-- Info -->
                <div class="flex-1 min-w-0">
                  <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                      <div class="flex items-center gap-3 mb-1">
                        <h3 class="text-2xl font-bold text-white">{{ item.plan_name }}</h3>
                        <span 
                          class="px-2 py-1 rounded-lg text-xs font-bold"
                          :class="getPlanBadgeClass(item.plan_slug)"
                        >
                          {{ getPlanBadge(item.plan_slug) }}
                        </span>
                      </div>
                      <p class="text-gray-400 text-sm">{{ item.description }}</p>
                    </div>
                    <button
                      @click="removeItem(item.id)"
                      class="group/btn text-gray-500 hover:text-red-500 transition-all p-2 hover:bg-red-500/10 rounded-lg hover:scale-110"
                      title="Xóa khỏi giỏ hàng"
                    >
                      <svg class="w-5 h-5 transition-transform group-hover/btn:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                    </button>
                  </div>

                  <!-- Features with Icons -->
                  <div class="flex flex-wrap gap-2 mb-5">
                    <span class="flex items-center gap-1.5 bg-gradient-to-r from-gray-700 to-gray-600 text-white px-3 py-1.5 rounded-full text-xs font-bold shadow-sm">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                      </svg>
                      {{ item.quality }}
                    </span>
                    <span class="flex items-center gap-1.5 bg-gradient-to-r from-blue-600 to-blue-500 text-white px-3 py-1.5 rounded-full text-xs font-bold shadow-sm">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                      </svg>
                      {{ item.max_devices }} thiết bị
                    </span>
                    <span v-if="!item.has_ads" class="flex items-center gap-1.5 bg-gradient-to-r from-green-600 to-green-500 text-white px-3 py-1.5 rounded-full text-xs font-bold shadow-sm">
                      <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                      </svg>
                      Không quảng cáo
                    </span>
                    <span v-if="item.can_download" class="flex items-center gap-1.5 bg-gradient-to-r from-purple-600 to-purple-500 text-white px-3 py-1.5 rounded-full text-xs font-bold shadow-sm">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                      </svg>
                      Tải về
                    </span>
                  </div>

                  <!-- Quantity & Price -->
                  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-4 border-t border-gray-700/50">
                    <div class="flex items-center gap-3">
                      <span class="text-gray-400 text-sm font-medium">Thời hạn:</span>
                      <div class="flex items-center gap-2 bg-gray-800/80 backdrop-blur-sm rounded-xl p-1.5 border border-gray-700 relative">
                        <!-- Loading Overlay -->
                        <div v-if="updating === item.id" class="absolute inset-0 bg-gray-900/80 backdrop-blur-sm rounded-xl flex items-center justify-center z-10">
                          <div class="w-5 h-5 border-2 border-t-red-500 border-r-transparent border-b-red-500 border-l-transparent rounded-full animate-spin"></div>
                        </div>
                        
                        <button
                          @click="updateQuantity(item.id, item.quantity - 1)"
                          :disabled="item.quantity <= 1 || updating === item.id"
                          class="w-9 h-9 rounded-lg bg-gray-700 hover:bg-red-600 flex items-center justify-center transition-all disabled:opacity-30 disabled:cursor-not-allowed hover:scale-110 active:scale-95"
                        >
                          <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/>
                          </svg>
                        </button>
                        <div class="relative w-24 text-center">
                          <span class="text-white font-bold text-lg transition-all block" :class="{ 'scale-110': updating === item.id }">
                            {{ item.quantity }} tháng
                          </span>
                          <!-- Change Indicator -->
                          <Transition name="bounce">
                            <span 
                              v-if="quantityChange.id === item.id && quantityChange.delta !== 0"
                              class="absolute -top-2 right-0 text-xs font-bold px-1.5 py-0.5 rounded-full"
                              :class="quantityChange.delta > 0 ? 'bg-green-500 text-white' : 'bg-red-500 text-white'"
                            >
                              {{ quantityChange.delta > 0 ? '+' : '' }}{{ quantityChange.delta }}
                            </span>
                          </Transition>
                        </div>
                        <button
                          @click="updateQuantity(item.id, item.quantity + 1)"
                          :disabled="item.quantity >= 12 || updating === item.id"
                          class="w-9 h-9 rounded-lg bg-gray-700 hover:bg-green-600 flex items-center justify-center transition-all disabled:opacity-30 disabled:cursor-not-allowed hover:scale-110 active:scale-95"
                        >
                          <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                          </svg>
                        </button>
                      </div>
                    </div>

                    <div class="text-right">
                      <p class="text-3xl font-black bg-gradient-to-r from-white to-gray-300 bg-clip-text text-transparent">
                        {{ item.subtotal_formatted }}
                      </p>
                      <p class="text-sm text-gray-400 flex items-center justify-end gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        {{ item.price_formatted }}/tháng
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </TransitionGroup>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
          <div class="sticky top-4 space-y-4">
            <!-- Summary Card -->
            <div class="relative overflow-hidden bg-gradient-to-br from-gray-900/95 to-gray-800/95 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-2xl">
              <!-- Decorative Background -->
              <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-red-600/10 to-yellow-500/10 rounded-full blur-3xl"></div>
              
              <div class="relative">
                <div class="flex items-center gap-3 mb-6">
                  <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-red-600 to-yellow-500 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                  </div>
                  <h2 class="text-2xl font-bold text-white">Tổng đơn hàng</h2>
                </div>

                <!-- Coupon Code -->
                <div class="mb-6">
                  <label class="flex items-center gap-2 text-gray-400 text-sm font-medium mb-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    Mã giảm giá
                  </label>
                  <div class="flex gap-2">
                    <input
                      v-model="couponCode"
                      type="text"
                      placeholder="Nhập mã giảm giá"
                      class="flex-1 bg-gray-800/80 backdrop-blur-sm text-white px-4 py-3 rounded-xl border border-gray-700 focus:border-red-600 focus:outline-none focus:ring-2 focus:ring-red-600/20 transition-all placeholder:text-gray-500"
                      @keyup.enter="applyCoupon"
                    />
                    <button
                      @click="applyCoupon"
                      :disabled="applyingCoupon || !couponCode"
                      class="bg-gradient-to-r from-red-600 to-red-700 text-white px-5 py-3 rounded-xl hover:from-red-700 hover:to-red-800 disabled:opacity-50 disabled:cursor-not-allowed transition-all font-bold hover:scale-105 active:scale-95 shadow-lg shadow-red-600/20"
                    >
                      {{ applyingCoupon ? '...' : 'Áp dụng' }}
                    </button>
                  </div>
                  
                  <!-- Coupon Messages -->
                  <Transition name="fade">
                    <p v-if="couponError" class="flex items-center gap-2 text-red-400 text-sm mt-3 bg-red-500/10 px-3 py-2 rounded-lg border border-red-500/20">
                      <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                      </svg>
                      {{ couponError }}
                    </p>
                  </Transition>
                  
                  <Transition name="fade">
                    <p v-if="appliedCoupon" class="flex items-center gap-2 text-green-400 text-sm mt-3 bg-green-500/10 px-3 py-2 rounded-lg border border-green-500/20">
                      <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                      </svg>
                      Đã áp dụng mã <span class="font-bold">{{ appliedCoupon.code }}</span>
                    </p>
                  </Transition>
                </div>

                <!-- Price Breakdown -->
                <div class="space-y-3 mb-6 pb-6 border-b border-gray-700/50">
                  <div class="flex justify-between text-gray-400">
                    <span class="flex items-center gap-2">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                      </svg>
                      Tạm tính
                    </span>
                    <span class="font-semibold text-white">{{ cartStore.cartTotalFormatted }}</span>
                  </div>
                  <Transition name="fade">
                    <div v-if="appliedCoupon" class="flex justify-between text-green-400 font-bold">
                      <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd"/>
                        </svg>
                        Giảm giá
                      </span>
                      <span>-{{ appliedCoupon.discount_amount_formatted }}</span>
                    </div>
                  </Transition>
                </div>

                <!-- Total -->
                <div class="flex justify-between items-center mb-6 p-4 bg-gradient-to-r from-red-600/10 to-yellow-500/10 rounded-xl border border-red-600/20">
                  <span class="text-gray-300 text-lg font-semibold">Tổng cộng</span>
                  <span class="text-3xl font-black bg-gradient-to-r from-red-500 to-yellow-500 bg-clip-text text-transparent">
                    {{ finalTotal }}
                  </span>
                </div>

                <!-- Checkout Button -->
                <router-link
                  to="/checkout"
                  class="group block w-full bg-gradient-to-r from-red-600 to-yellow-500 text-white text-center py-4 rounded-xl font-bold hover:from-red-700 hover:to-yellow-600 transition-all hover:scale-105 active:scale-95 shadow-lg shadow-red-600/30 mb-3"
                >
                  <span class="flex items-center justify-center gap-2">
                    <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Thanh toán ngay
                  </span>
                </router-link>

                <button
                  @click="clearCart"
                  class="group w-full text-center text-gray-400 hover:text-red-400 transition-all text-sm font-medium py-2 hover:bg-red-500/5 rounded-lg"
                >
                  <span class="flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 transition-transform group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Xóa toàn bộ giỏ hàng
                  </span>
                </button>
              </div>
            </div>

            <!-- Trust Badges -->
            <div class="bg-gray-900/50 backdrop-blur-sm rounded-xl p-4 border border-gray-800">
              <div class="flex items-center justify-center gap-6 text-gray-400 text-xs">
                <div class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                  </svg>
                  <span>Thanh toán an toàn</span>
                </div>
                <div class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                  </svg>
                  <span>Hủy bất kỳ lúc nào</span>
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
import { ref, computed, onMounted } from 'vue';
import { useCartStore } from '@/stores/cartStore';
import { useAuthStore } from '@/stores/authStore';
import { validateCoupon } from '@/services/ecommerceApi';
import { useToast } from '@/composables/useToast';
import ConfirmModal from '@/components/ConfirmModal.vue';

const cartStore = useCartStore();
const authStore = useAuthStore();
const toast = useToast();

const couponCode = ref('');
const appliedCoupon = ref(null);
const applyingCoupon = ref(false);
const couponError = ref(null);
const updating = ref(null);
const quantityChange = ref({ id: null, delta: 0 });

// Confirm Modal State
const showConfirmModal = ref(false);
const confirmModal = ref({
  title: '',
  message: '',
  type: 'warning',
  confirmText: 'Xác nhận',
  onConfirm: () => {}
});

onMounted(async () => {
  await cartStore.fetchCart();
});

const finalTotal = computed(() => {
  if (appliedCoupon.value) {
    return appliedCoupon.value.final_total_formatted;
  }
  return cartStore.cartTotalFormatted;
});

const updateQuantity = async (cartId, quantity) => {
  // Get current quantity to calculate delta
  const currentItem = cartStore.items.find(item => item.id === cartId);
  const delta = quantity - currentItem.quantity;
  
  // Show visual feedback
  quantityChange.value = { id: cartId, delta };
  setTimeout(() => {
    quantityChange.value = { id: null, delta: 0 };
  }, 1000);
  
  updating.value = cartId;
  
  try {
    // Add minimum delay for better UX (so users can see the loading state)
    const [result] = await Promise.all([
      cartStore.updateQuantity(cartId, quantity),
      new Promise(resolve => setTimeout(resolve, 600)) // Minimum 600ms delay
    ]);
    
    // Show specific quantity in toast
    const action = delta > 0 ? 'Tăng' : 'Giảm';
    toast.success(`${action} thời hạn thành công: ${quantity} tháng`);
    appliedCoupon.value = null;
  } catch (error) {
    toast.error(error.message || 'Không thể cập nhật');
  } finally {
    updating.value = null;
  }
};

const removeItem = async (cartId) => {
  confirmModal.value = {
    title: 'Xóa gói khỏi giỏ hàng?',
    message: 'Bạn có chắc chắn muốn xóa gói này khỏi giỏ hàng? Hành động này không thể hoàn tác.',
    type: 'danger',
    confirmText: 'Xóa ngay',
    onConfirm: async () => {
      try {
        // Add minimum delay for better UX
        await Promise.all([
          cartStore.removeItem(cartId),
          new Promise(resolve => setTimeout(resolve, 400))
        ]);
        toast.success('Đã xóa khỏi giỏ hàng');
        appliedCoupon.value = null;
      } catch (error) {
        toast.error(error.message || 'Không thể xóa');
      }
    }
  };
  showConfirmModal.value = true;
};

const clearCart = async () => {
  confirmModal.value = {
    title: 'Xóa toàn bộ giỏ hàng?',
    message: 'Bạn có chắc chắn muốn xóa tất cả các gói trong giỏ hàng? Hành động này không thể hoàn tác.',
    type: 'danger',
    confirmText: 'Xóa tất cả',
    onConfirm: async () => {
      try {
        // Add minimum delay for better UX
        await Promise.all([
          cartStore.clear(),
          new Promise(resolve => setTimeout(resolve, 500))
        ]);
        toast.success('Đã xóa toàn bộ giỏ hàng');
        appliedCoupon.value = null;
      } catch (error) {
        toast.error(error.message || 'Không thể xóa');
      }
    }
  };
  showConfirmModal.value = true;
};

const applyCoupon = async () => {
  if (!couponCode.value) return;

  applyingCoupon.value = true;
  couponError.value = null;

  try {
    const response = await validateCoupon(
      couponCode.value,
      cartStore.total,
      authStore.user?.id
    );

    if (response.success) {
      appliedCoupon.value = response.data;
      toast.success(response.message);
    }
  } catch (error) {
    couponError.value = error.response?.data?.message || 'Mã không hợp lệ';
    appliedCoupon.value = null;
  } finally {
    applyingCoupon.value = false;
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

const getPlanBadge = (slug) => {
  const badges = {
    free: 'MIỄN PHÍ',
    basic: 'PHỔ BIẾN',
    premium: 'BÁN CHẠY',
    vip: 'CAO CẤP'
  };
  return badges[slug] || '';
};

const getPlanBadgeClass = (slug) => {
  const classes = {
    free: 'bg-gray-600 text-white',
    basic: 'bg-blue-600 text-white',
    premium: 'bg-gradient-to-r from-red-600 to-yellow-500 text-white',
    vip: 'bg-gradient-to-r from-purple-600 to-pink-500 text-white'
  };
  return classes[slug] || classes.free;
};
</script>

<style scoped>
.cart-item-enter-active,
.cart-item-leave-active {
  transition: all 0.4s ease;
}

.cart-item-enter-from {
  opacity: 0;
  transform: translateX(-30px);
}

.cart-item-leave-to {
  opacity: 0;
  transform: translateX(30px);
}

.cart-item-move {
  transition: transform 0.4s ease;
}

.fade-enter-active,
.fade-leave-active {
  transition: all 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

/* Bounce animation for quantity change indicator */
.bounce-enter-active {
  animation: bounce-in 0.5s ease-out;
}

.bounce-leave-active {
  animation: bounce-out 0.3s ease-in;
}

@keyframes bounce-in {
  0% {
    opacity: 0;
    transform: scale(0) translateY(10px);
  }
  50% {
    transform: scale(1.2) translateY(-5px);
  }
  100% {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

@keyframes bounce-out {
  0% {
    opacity: 1;
    transform: scale(1);
  }
  100% {
    opacity: 0;
    transform: scale(0.5) translateY(-10px);
  }
}
</style>
