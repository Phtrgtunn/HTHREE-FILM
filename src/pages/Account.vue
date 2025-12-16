<template>
  <div class="min-h-screen bg-black font-sans flex flex-col">
    <!-- Edit Profile Modal -->
    <EditProfileModal
      v-model="showEditProfile"
      :user="user"
      @save="handleSaveProfile"
    />

    <!-- Delete Account Confirmation Modal -->
    <Transition
      enter-active-class="transition-all duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-all duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="showDeleteAccountModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
        @click.self="showDeleteAccountModal = false"
      >
        <Transition
          enter-active-class="transition-all duration-300 ease-out"
          enter-from-class="opacity-0 scale-95 translate-y-4"
          enter-to-class="opacity-100 scale-100 translate-y-0"
          leave-active-class="transition-all duration-200 ease-in"
          leave-from-class="opacity-100 scale-100 translate-y-0"
          leave-to-class="opacity-0 scale-95 translate-y-4"
        >
          <div
            v-if="showDeleteAccountModal"
            class="relative w-full max-w-md bg-gray-900 rounded-xl shadow-2xl overflow-hidden border border-red-500/30"
          >
            <!-- Header -->
            <div class="bg-gradient-to-r from-red-500 to-red-600 px-5 py-4">
              <div class="flex items-center gap-2">
                <svg
                  class="w-5 h-5 text-white"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path
                    fill-rule="evenodd"
                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                    clip-rule="evenodd"
                  />
                </svg>
                <h3 class="text-lg font-bold text-white">
                  Xác nhận xóa tài khoản
                </h3>
              </div>
            </div>

            <!-- Content -->
            <div class="p-5 space-y-4">
              <div
                class="bg-red-500/10 border border-red-500/30 rounded-lg p-3"
              >
                <p class="text-red-400 font-semibold mb-2 text-sm">
                  ⚠️ Cảnh báo:
                </p>
                <ul class="text-red-200 text-xs space-y-1.5">
                  <li class="flex items-start gap-2">
                    <span class="text-red-400 mt-0.5">•</span>
                    <span>Tất cả thông tin cá nhân sẽ bị xóa vĩnh viễn</span>
                  </li>
                  <li class="flex items-start gap-2">
                    <span class="text-red-400 mt-0.5">•</span>
                    <span>Lịch sử giao dịch và gói đăng ký sẽ mất</span>
                  </li>
                  <li class="flex items-start gap-2">
                    <span class="text-red-400 mt-0.5">•</span>
                    <span>Không thể khôi phục tài khoản sau khi xóa</span>
                  </li>
                  <li class="flex items-start gap-2">
                    <span class="text-red-400 mt-0.5">•</span>
                    <span
                      ><strong>Hành động này KHÔNG THỂ HOÀN TÁC</strong></span
                    >
                  </li>
                </ul>
              </div>

              <p class="text-gray-300 text-sm text-center">
                Bạn có chắc chắn muốn xóa tài khoản vĩnh viễn không?
              </p>
            </div>

            <!-- Footer -->
            <div class="bg-gray-800/50 px-5 py-4 flex gap-3">
              <button
                @click="showDeleteAccountModal = false"
                class="flex-1 px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 text-white font-medium transition-colors text-sm flex items-center justify-center"
              >
                Hủy bỏ
              </button>
              <button
                @click="confirmDeleteAccount"
                class="flex-1 px-4 py-2.5 rounded-lg bg-red-500 hover:bg-red-600 text-white font-bold transition-colors flex items-center justify-center gap-2 text-sm"
              >
                <svg
                  class="w-4 h-4"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                  />
                </svg>
                Xóa tài khoản
              </button>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>

    <!-- Cancel Subscription Confirmation Modal -->
    <Transition name="modal">
      <div
        v-if="showCancelModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
        @click.self="showCancelModal = false"
      >
        <div
          class="relative w-full max-w-md bg-gray-900 rounded-2xl shadow-2xl overflow-hidden border border-red-500/30"
        >
          <!-- Header -->
          <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4">
            <div class="flex items-center gap-3">
              <svg
                class="w-6 h-6 text-white"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path
                  fill-rule="evenodd"
                  d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                  clip-rule="evenodd"
                />
              </svg>
              <h3 class="text-xl font-bold text-white">
                Xác nhận dừng gia hạn
              </h3>
            </div>
          </div>

          <!-- Content -->
          <div class="p-6 space-y-4">
            <div class="bg-red-500/10 border border-red-500/30 rounded-lg p-4">
              <p class="text-red-400 font-bold mb-2">⚠️ Lưu ý quan trọng:</p>
              <ul class="text-red-200 text-sm space-y-2">
                <li class="flex items-start gap-2">
                  <span class="text-red-400 mt-0.5">•</span>
                  <span>Dừng gia hạn sẽ <strong>KHÔNG HOÀN TIỀN</strong></span>
                </li>
                <li class="flex items-start gap-2">
                  <span class="text-red-400 mt-0.5">•</span>
                  <span
                    >Bạn vẫn có thể sử dụng gói đến hết ngày
                    <strong>{{
                      subscriptionToCancel?.end_date_formatted
                    }}</strong></span
                  >
                </li>
                <li class="flex items-start gap-2">
                  <span class="text-red-400 mt-0.5">•</span>
                  <span>Sau khi hết hạn, gói sẽ không tự động gia hạn</span>
                </li>
              </ul>
            </div>

            <div class="bg-gray-800 rounded-lg p-4">
              <p class="text-gray-300 text-sm mb-2">Thông tin gói:</p>
              <div class="flex items-center justify-between">
                <span class="text-white font-bold">{{
                  subscriptionToCancel?.plan_name
                }}</span>
                <span class="text-yellow-400 font-bold">{{
                  subscriptionToCancel?.quality
                }}</span>
              </div>
              <p class="text-gray-400 text-xs mt-2">
                {{ getRealtimeProgress(subscriptionToCancel).timeRemaining }}
              </p>
            </div>

            <p class="text-gray-300 text-sm">
              Bạn có chắc chắn muốn dừng gia hạn gói này không?
            </p>
          </div>

          <!-- Footer -->
          <div class="bg-gray-800/50 px-6 py-4 flex gap-3">
            <button
              @click="showCancelModal = false"
              class="flex-1 px-4 py-3 rounded-lg bg-gray-700 hover:bg-gray-600 text-white font-medium transition-colors flex items-center justify-center"
            >
              Giữ lại gói
            </button>
            <button
              @click="confirmCancelSubscription"
              :disabled="cancellingSubscription"
              class="flex-1 px-4 py-3 rounded-lg bg-red-500 hover:bg-red-600 text-white font-bold transition-colors disabled:opacity-50 flex items-center justify-center gap-2"
            >
              <svg
                v-if="!cancellingSubscription"
                class="w-5 h-5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"
                />
              </svg>
              <svg
                v-else
                class="w-5 h-5 animate-spin"
                fill="none"
                viewBox="0 0 24 24"
              >
                <circle
                  class="opacity-25"
                  cx="12"
                  cy="12"
                  r="10"
                  stroke="currentColor"
                  stroke-width="4"
                ></circle>
                <path
                  class="opacity-75"
                  fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
              </svg>
              <span>{{
                cancellingSubscription ? "Đang hủy..." : "Đồng ý hủy"
              }}</span>
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Top Header with Logo -->
    <div class="bg-black border-b border-gray-800 px-8 py-4">
      <div class="flex items-center justify-between">
        <router-link to="/home" class="flex items-center gap-2">
          <svg
            class="w-8 h-8 text-yellow-400"
            fill="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"
            />
          </svg>
          <span class="text-2xl font-bold text-white">HTHREE</span>
        </router-link>
      </div>
    </div>

    <div class="flex flex-1 justify-center">
      <div class="flex w-full max-w-7xl">
        <!-- Sidebar -->
        <aside class="w-80 flex-shrink-0 flex flex-col pl-8 pr-8 py-8">
          <!-- Back Button -->
          <div class="mb-8">
            <router-link
              :to="{ name: 'Homepage' }"
              class="group relative inline-flex items-center text-gray-300 hover:text-yellow-400 transition-all duration-300 font-medium text-sm no-underline"
              style="text-decoration: none !important"
            >
              <svg
                class="w-4 h-4 mr-2 transition-transform duration-300 group-hover:-translate-x-1"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M15 19l-7-7 7-7"
                />
              </svg>
              <span class="relative" style="text-decoration: none !important">
                Quay lại HTHREE Film
                <!-- Underline animation -->
                <span
                  class="absolute bottom-0 left-0 w-0 h-0.5 bg-yellow-400 transition-all duration-300 group-hover:w-full"
                ></span>
              </span>
            </router-link>
          </div>

          <!-- Menu Items -->
          <nav class="flex-1 space-y-1">
            <button
              v-for="item in menuItems"
              :key="item.id"
              @click="activeSection = item.id"
              :class="[
                'w-full flex items-center py-3 pl-4 pr-4 text-left rounded-lg transition-all duration-500 ease-out relative group',
                activeSection === item.id
                  ? 'text-white font-semibold'
                  : 'text-gray-400 hover:text-white',
              ]"
              style="isolation: isolate"
            >
              <!-- Hover background (fade in) -->
              <div
                v-if="activeSection !== item.id"
                class="absolute inset-0 bg-[#2a2a2a]/40 rounded-lg transition-opacity duration-300 ease-out opacity-0 group-hover:opacity-100"
                style="z-index: -1"
              ></div>

              <!-- Active background -->
              <div
                v-if="activeSection === item.id"
                class="absolute inset-0 bg-[#2a2a2a]/40 rounded-lg"
                style="z-index: -1"
              ></div>

              <!-- Active bar (slide down) -->
              <div
                v-if="activeSection === item.id"
                class="absolute left-0 top-0 w-1 h-full bg-yellow-400 animate-slide-down-bar"
                style="z-index: 10"
              ></div>

              <!-- Content -->
              <div
                class="w-10 flex items-center justify-start flex-shrink-0 relative z-10"
              >
                <component
                  :is="item.icon"
                  class="w-5 h-5 transition-transform duration-500 ease-out group-hover:scale-105"
                />
              </div>
              <span class="text-base relative z-10 text-left flex-1">{{
                item.label
              }}</span>
            </button>
          </nav>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 overflow-y-auto">
          <div class="max-w-4xl px-8 py-8">
            <main class="w-full">
              <!-- Loading State -->
              <div
                v-if="loading"
                class="flex justify-center items-center min-h-[400px]"
              >
                <svg
                  class="animate-spin h-8 w-8 text-yellow-400"
                  fill="none"
                  viewBox="0 0 24 24"
                >
                  <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                  ></circle>
                  <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                  ></path>
                </svg>
              </div>

              <!-- Not Logged In -->
              <div v-else-if="!user" class="text-center py-12">
                <p class="text-xl text-gray-400 mb-4">
                  Vui lòng đăng nhập để xem thông tin tài khoản.
                </p>
                <router-link
                  to="/auth"
                  class="inline-block bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-3 px-6 rounded-lg transition-colors"
                >
                  Đăng Nhập Ngay
                </router-link>
              </div>

              <!-- Account Content -->
              <div v-else>
                <!-- Tổng quan -->
                <div v-if="activeSection === 'overview'">
                  <div class="flex items-center justify-between mb-4">
                    <div>
                      <h1
                        class="text-3xl font-bold text-white mb-2 tracking-tight"
                      >
                        Tài khoản
                      </h1>
                      <p class="text-gray-300">Thông tin tư cách thành viên</p>
                    </div>
                    <button
                      @click="showEditProfile = true"
                      class="flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-black px-4 py-2 rounded-lg font-medium transition-all hover:scale-105"
                    >
                      <svg
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                        />
                      </svg>
                      Chỉnh sửa hồ sơ
                    </button>
                  </div>

                  <!-- Header -->
                  <div class="flex items-center justify-between mb-4">
                    <div>
                      <span
                        class="inline-block bg-yellow-400 text-black text-xs font-bold px-3 py-1 rounded-full"
                      >
                        Thành viên từ tháng {{ getJoinMonth() }}
                      </span>
                    </div>
                    <button
                      @click="router.push('/pricing')"
                      class="text-yellow-400 hover:text-yellow-300 text-sm font-medium flex items-center transition-colors"
                    >
                      <svg
                        class="w-4 h-4 mr-1"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M12 4v16m8-8H4"
                        />
                      </svg>
                      Mua thêm gói
                    </button>
                  </div>

                  <!-- Cancelled Subscription Notice -->
                  <div
                    v-if="
                      activeSubscription &&
                      activeSubscription.status === 'cancelled'
                    "
                    class="mb-4 bg-orange-500/10 border border-orange-500/30 rounded-xl p-4"
                  >
                    <div class="flex items-start gap-3">
                      <svg
                        class="w-6 h-6 text-orange-400 flex-shrink-0 mt-0.5"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      <div class="flex-1">
                        <h4 class="text-orange-400 font-bold mb-1">
                          🔄 Đã dừng gia hạn tự động
                        </h4>
                        <p class="text-orange-200 text-sm">
                          Gói
                          <span class="font-semibold">{{
                            activeSubscription.plan_name
                          }}</span>
                          sẽ hết hạn vào
                          <span class="font-semibold">{{
                            activeSubscription.end_date_formatted
                          }}</span>
                          và không tự động gia hạn. Bạn vẫn có thể sử dụng đến
                          hết thời gian đã trả.
                        </p>
                        <button
                          @click="router.push('/pricing')"
                          class="mt-3 bg-orange-400 hover:bg-orange-500 text-black font-bold px-4 py-2 rounded-lg text-sm transition-all"
                        >
                          Kích hoạt lại
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Expiring Soon Warning -->
                  <div
                    v-if="
                      activeSubscription &&
                      activeSubscription.status === 'active' &&
                      getRealtimeProgress(activeSubscription).status ===
                        'expiring_soon'
                    "
                    class="mb-4 bg-yellow-500/10 border border-yellow-500/30 rounded-xl p-4"
                  >
                    <div class="flex items-start gap-3">
                      <svg
                        class="w-6 h-6 text-yellow-400 flex-shrink-0 mt-0.5"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      <div class="flex-1">
                        <h4 class="text-yellow-400 font-bold mb-1">
                          ⚠️ Gói của bạn sắp hết hạn!
                        </h4>
                        <p class="text-yellow-200 text-sm">
                          Gói
                          <span class="font-semibold">{{
                            activeSubscription.plan_name
                          }}</span>
                          sẽ hết hạn vào
                          <span class="font-semibold">{{
                            activeSubscription.end_date_formatted
                          }}</span>
                          (còn
                          {{
                            getRealtimeProgress(activeSubscription)
                              .timeRemaining
                          }}).
                        </p>
                        <button
                          @click="router.push('/pricing')"
                          class="mt-3 bg-yellow-400 hover:bg-yellow-500 text-black font-bold px-4 py-2 rounded-lg text-sm transition-all"
                        >
                          Gia hạn ngay
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Expired Warning -->
                  <div
                    v-if="
                      activeSubscription &&
                      getRealtimeProgress(activeSubscription).status ===
                        'expired'
                    "
                    class="mb-4 bg-red-500/10 border border-red-500/30 rounded-xl p-4"
                  >
                    <div class="flex items-start gap-3">
                      <svg
                        class="w-6 h-6 text-red-400 flex-shrink-0 mt-0.5"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      <div class="flex-1">
                        <h4 class="text-red-400 font-bold mb-1">
                          ❌ Gói của bạn đã hết hạn
                        </h4>
                        <p class="text-red-200 text-sm">
                          Gói
                          <span class="font-semibold">{{
                            activeSubscription.plan_name
                          }}</span>
                          đã hết hạn vào
                          <span class="font-semibold">{{
                            activeSubscription.end_date_formatted
                          }}</span
                          >. Gia hạn ngay để tiếp tục sử dụng dịch vụ.
                        </p>
                        <button
                          @click="router.push('/pricing')"
                          class="mt-3 bg-red-500 hover:bg-red-600 text-white font-bold px-4 py-2 rounded-lg text-sm transition-all"
                        >
                          Gia hạn ngay
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Active Subscription -->
                  <div v-if="activeSubscription" class="mb-6">
                    <h3
                      class="text-white font-bold text-lg mb-4 flex items-center gap-2"
                    >
                      <svg
                        class="w-5 h-5"
                        :class="{
                          'text-green-400':
                            activeSubscription.status === 'active' &&
                            getRealtimeProgress(activeSubscription).status ===
                              'active',
                          'text-orange-400':
                            activeSubscription.status === 'cancelled',
                          'text-yellow-400':
                            getRealtimeProgress(activeSubscription).status ===
                            'expiring_soon',
                          'text-red-400':
                            getRealtimeProgress(activeSubscription).status ===
                            'expired',
                        }"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      <span>Gói đang sử dụng</span>
                      <span
                        class="text-xs px-2 py-1 rounded-full font-medium"
                        :class="{
                          'bg-green-500/20 text-green-400':
                            getRealtimeProgress(activeSubscription).status ===
                            'active',
                          'bg-yellow-500/20 text-yellow-400':
                            getRealtimeProgress(activeSubscription).status ===
                            'expiring_soon',
                          'bg-red-500/20 text-red-400':
                            getRealtimeProgress(activeSubscription).status ===
                            'expired',
                        }"
                      >
                        {{
                          activeSubscription.status === "cancelled"
                            ? "Đã dừng gia hạn"
                            : getRealtimeProgress(activeSubscription).status ===
                              "active"
                            ? "Hoạt động tốt"
                            : getRealtimeProgress(activeSubscription).status ===
                              "expiring_soon"
                            ? "Sắp hết hạn"
                            : "Đã hết hạn"
                        }}
                      </span>
                    </h3>

                    <div
                      class="bg-gradient-to-br border-2 rounded-2xl p-6 shadow-xl"
                      :class="getPlanColor(activeSubscription.plan_slug)"
                    >
                      <!-- Header -->
                      <div class="flex items-start justify-between mb-6">
                        <div>
                          <div class="flex items-center gap-3 mb-2">
                            <h2 class="text-2xl font-black text-white">
                              {{ activeSubscription.plan_name }}
                            </h2>
                            <span
                              class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-sm font-bold text-white"
                            >
                              {{ activeSubscription.quality }}
                            </span>
                          </div>
                          <p class="text-white/80 text-sm">
                            {{
                              getPlanDescription(activeSubscription.plan_slug)
                            }}
                          </p>
                        </div>
                        <span
                          class="px-4 py-2 rounded-full text-sm font-bold shadow-lg"
                          :class="{
                            'bg-green-500 text-white':
                              getRealtimeProgress(activeSubscription).status ===
                              'active',
                            'bg-yellow-500 text-black':
                              getRealtimeProgress(activeSubscription).status ===
                              'expiring_soon',
                            'bg-red-500 text-white':
                              getRealtimeProgress(activeSubscription).status ===
                              'expired',
                          }"
                        >
                          {{
                            getRealtimeProgress(activeSubscription).status ===
                            "active"
                              ? "✓ Đang hoạt động"
                              : getRealtimeProgress(activeSubscription)
                                  .status === "expiring_soon"
                              ? "⚠ Sắp hết hạn"
                              : "✕ Đã hết hạn"
                          }}
                        </span>
                      </div>

                      <!-- Timeline -->
                      <div
                        class="bg-black/20 backdrop-blur-sm rounded-xl p-5 mb-5"
                      >
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                          <div>
                            <p
                              class="text-white/60 text-xs mb-1 flex items-center gap-1"
                            >
                              <span>📅</span>
                              <span>Ngày bắt đầu</span>
                            </p>
                            <p class="text-white font-bold">
                              {{ activeSubscription.start_date_formatted }}
                            </p>
                          </div>
                          <div>
                            <p
                              class="text-white/60 text-xs mb-1 flex items-center gap-1"
                            >
                              <span>⏱️</span>
                              <span>Thời gian</span>
                            </p>
                            <p class="text-white font-bold">
                              {{
                                activeSubscription.used_minutes_display || 0
                              }}/{{
                                activeSubscription.total_minutes_display || 3
                              }}
                              phút
                            </p>
                          </div>
                          <div>
                            <p
                              class="text-white/60 text-xs mb-1 flex items-center gap-1"
                            >
                              <span>📆</span>
                              <span>Ngày hết hạn</span>
                            </p>
                            <p class="text-white font-bold">
                              {{ activeSubscription.end_date_formatted }}
                            </p>
                          </div>
                          <div class="lg:text-right">
                            <p
                              class="text-white/60 text-xs mb-1 flex items-center gap-1 lg:justify-end"
                            >
                              <span>💰</span>
                              <span>Giá đã trả</span>
                            </p>
                            <p class="text-green-400 font-bold">
                              {{ formatPrice(activeSubscription.price) }}đ
                            </p>
                          </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="relative">
                          <div
                            class="w-full bg-white/10 rounded-full h-3 overflow-hidden"
                          >
                            <div
                              class="h-full rounded-full transition-all duration-500 ease-out relative"
                              :class="{
                                'bg-gradient-to-r from-green-400 to-green-500':
                                  getRealtimeProgress(activeSubscription)
                                    .progress < 70,
                                'bg-gradient-to-r from-yellow-400 to-yellow-500':
                                  getRealtimeProgress(activeSubscription)
                                    .progress >= 70 &&
                                  getRealtimeProgress(activeSubscription)
                                    .progress < 90,
                                'bg-gradient-to-r from-red-400 to-red-500':
                                  getRealtimeProgress(activeSubscription)
                                    .progress >= 90,
                              }"
                              :style="{
                                width:
                                  getRealtimeProgress(activeSubscription)
                                    .progress + '%',
                                transition: 'width 0.5s ease-out',
                              }"
                            >
                              <div
                                class="absolute inset-0 bg-white/20 animate-pulse"
                              ></div>
                            </div>
                          </div>
                          <p
                            class="text-center text-white font-bold text-sm mt-2"
                          >
                            {{
                              getRealtimeProgress(activeSubscription)
                                .timeRemaining
                            }}
                          </p>
                        </div>
                      </div>

                      <!-- Actions -->
                      <div class="grid grid-cols-3 gap-3">
                        <!-- Gia hạn gói hiện tại -->
                        <button
                          @click="renewCurrentPlan(activeSubscription)"
                          class="bg-green-500/20 hover:bg-green-500/30 text-green-400 font-bold py-3 rounded-lg transition-all flex items-center justify-center gap-2"
                        >
                          <svg
                            class="w-5 h-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                          >
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                            />
                          </svg>
                          <span>Gia hạn</span>
                        </button>

                        <!-- Nâng cấp gói -->
                        <button
                          @click="handleUpgrade"
                          class="bg-white/10 hover:bg-white/20 backdrop-blur-sm text-white font-bold py-3 rounded-lg transition-all flex items-center justify-center gap-2"
                        >
                          <svg
                            class="w-5 h-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                          >
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M5 10l7-7m0 0l7 7m-7-7v18"
                            />
                          </svg>
                          <span>Nâng cấp</span>
                        </button>

                        <!-- Dừng gia hạn -->
                        <button
                          @click="showCancelConfirmModal(activeSubscription)"
                          :disabled="
                            cancellingSubscription === activeSubscription.id
                          "
                          class="bg-red-500/20 hover:bg-red-500/30 text-red-400 font-bold py-3 rounded-lg transition-all disabled:opacity-50 flex items-center justify-center gap-2"
                        >
                          <svg
                            v-if="
                              cancellingSubscription !== activeSubscription.id
                            "
                            class="w-5 h-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                          >
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                          </svg>
                          <svg
                            v-else
                            class="w-5 h-5 animate-spin"
                            fill="none"
                            viewBox="0 0 24 24"
                          >
                            <circle
                              class="opacity-25"
                              cx="12"
                              cy="12"
                              r="10"
                              stroke="currentColor"
                              stroke-width="4"
                            ></circle>
                            <path
                              class="opacity-75"
                              fill="currentColor"
                              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                            ></path>
                          </svg>
                          <span>{{
                            cancellingSubscription === activeSubscription.id
                              ? "Đang xử lý..."
                              : "Dừng gia hạn"
                          }}</span>
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Pending Subscriptions -->
                  <div v-if="pendingSubscriptions.length > 0" class="mb-6">
                    <!-- Info Banner -->
                    <div
                      class="bg-blue-500/10 border border-blue-500/30 rounded-xl p-4 mb-4"
                    >
                      <div class="flex items-start gap-3">
                        <svg
                          class="w-6 h-6 text-blue-400 flex-shrink-0 mt-0.5"
                          fill="currentColor"
                          viewBox="0 0 20 20"
                        >
                          <path
                            fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd"
                          />
                        </svg>
                        <div class="flex-1">
                          <h4 class="text-blue-400 font-bold mb-1">
                            ℹ️ Gói chờ kích hoạt
                          </h4>
                          <p class="text-blue-200 text-sm">
                            Bạn có
                            <span class="font-semibold"
                              >{{ pendingSubscriptions.length }} gói</span
                            >
                            đang chờ kích hoạt. Các gói này sẽ tự động kích hoạt
                            khi gói hiện tại hết hạn.
                          </p>
                        </div>
                      </div>
                    </div>

                    <h3
                      class="text-white font-bold text-lg mb-4 flex items-center gap-2"
                    >
                      <svg
                        class="w-5 h-5 text-blue-400"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      Danh sách gói chờ ({{ pendingSubscriptions.length }})
                    </h3>

                    <div class="space-y-3">
                      <div
                        v-for="(sub, index) in pendingSubscriptions"
                        :key="sub.id"
                        class="bg-gradient-to-r from-blue-900/20 to-blue-800/20 border border-blue-700/50 rounded-xl p-5 hover:border-blue-500/70 transition-all"
                      >
                        <div class="flex items-start justify-between mb-3">
                          <div class="flex items-start gap-4 flex-1">
                            <div
                              class="w-14 h-14 rounded-xl bg-blue-500/20 flex items-center justify-center flex-shrink-0 border border-blue-500/30"
                            >
                              <span class="text-blue-400 font-black text-xl">{{
                                index + 1
                              }}</span>
                            </div>
                            <div class="flex-1">
                              <div class="flex items-center gap-2 mb-1">
                                <h4 class="text-white font-bold text-lg">
                                  {{ sub.plan_name }}
                                </h4>
                                <span
                                  class="px-2 py-0.5 bg-blue-500/30 text-blue-300 rounded text-xs font-bold"
                                >
                                  {{ sub.quality }}
                                </span>
                              </div>
                              <p class="text-gray-300 text-sm mb-2">
                                {{ getPlanDescription(sub.plan_slug) }}
                              </p>
                              <div class="grid grid-cols-2 gap-3 text-sm">
                                <div>
                                  <p class="text-gray-400 text-xs mb-0.5">
                                    Ngày bắt đầu
                                  </p>
                                  <p class="text-white font-semibold">
                                    {{ sub.start_date_formatted }}
                                  </p>
                                </div>
                                <div>
                                  <p class="text-gray-400 text-xs mb-0.5">
                                    Thời hạn
                                  </p>
                                  <p class="text-white font-semibold">
                                    {{ sub.total_minutes_display || 3 }} phút
                                  </p>
                                </div>
                              </div>
                            </div>
                          </div>
                          <span
                            class="px-3 py-1.5 bg-blue-500/20 text-blue-300 rounded-lg text-xs font-bold border border-blue-500/30 flex items-center gap-1.5"
                          >
                            <svg
                              class="w-3.5 h-3.5 animate-pulse"
                              fill="currentColor"
                              viewBox="0 0 20 20"
                            >
                              <path
                                fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                clip-rule="evenodd"
                              />
                            </svg>
                            Chờ kích hoạt
                          </span>
                        </div>

                        <!-- Timeline preview -->
                        <div class="mt-3 pt-3 border-t border-blue-700/30">
                          <p class="text-gray-400 text-xs mb-2">
                            Thời gian sử dụng dự kiến:
                          </p>
                          <div class="flex items-center gap-2 text-xs">
                            <span class="text-gray-300">{{
                              sub.start_date_formatted
                            }}</span>
                            <svg
                              class="w-4 h-4 text-gray-500"
                              fill="none"
                              stroke="currentColor"
                              viewBox="0 0 24 24"
                            >
                              <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6"
                              />
                            </svg>
                            <span class="text-gray-300">{{
                              sub.end_date_formatted
                            }}</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- No Subscription -->
                  <div
                    v-else-if="
                      !loadingSubscription &&
                      !activeSubscription &&
                      pendingSubscriptions.length === 0
                    "
                    class="bg-gradient-to-br from-gray-800 to-gray-900 border border-gray-700 rounded-lg p-6 mb-6 text-center"
                  >
                    <svg
                      class="w-16 h-16 mx-auto mb-4 text-gray-600"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                      />
                    </svg>
                    <h3 class="text-xl font-bold text-white mb-2">
                      Chưa có gói đăng ký
                    </h3>
                    <p class="text-gray-400 mb-4">
                      Đăng ký gói để trải nghiệm đầy đủ tính năng
                    </p>

                    <button
                      @click="router.push('/pricing')"
                      class="bg-gradient-to-r from-yellow-400 to-yellow-500 text-black font-bold px-6 py-3 rounded-lg hover:from-yellow-500 hover:to-yellow-600 transition-all"
                    >
                      Xem các gói đăng ký
                    </button>
                  </div>

                  <!-- Quick Links -->
                  <div class="space-y-3">
                    <h3
                      class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4"
                    >
                      Liên kết nhanh
                    </h3>

                    <!-- Cập nhật mật khẩu -->
                    <button
                      @click="activeSection = 'password'"
                      class="w-full bg-[#1a1a1a] border border-gray-800 rounded-lg p-4 hover:border-yellow-400 hover:bg-[#2a2a2a] transition-all flex items-center justify-between group"
                    >
                      <div class="flex items-center">
                        <svg
                          class="w-5 h-5 mr-3 text-gray-300 group-hover:text-yellow-400 transition-colors"
                          fill="none"
                          stroke="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                          />
                        </svg>
                        <span class="text-white font-medium"
                          >Cập nhật mật khẩu</span
                        >
                      </div>
                      <svg
                        class="w-5 h-5 text-gray-500 group-hover:text-yellow-400 transition-colors"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 5l7 7-7 7"
                        />
                      </svg>
                    </button>
                  </div>

                  <!-- Giao dịch gần đây -->
                  <div class="mt-8">
                    <div class="flex items-center justify-between mb-4">
                      <h3
                        class="text-sm font-semibold text-gray-400 uppercase tracking-wider"
                      >
                        Giao dịch gần đây
                      </h3>
                      <button
                        v-if="transactions && transactions.length > 3"
                        @click="activeSection = 'transactions'"
                        class="text-yellow-400 hover:text-yellow-300 text-sm font-medium transition-colors"
                      >
                        Xem tất cả →
                      </button>
                    </div>

                    <!-- Loading -->
                    <div
                      v-if="loadingTransactions"
                      class="flex justify-center py-8"
                    >
                      <svg
                        class="animate-spin h-6 w-6 text-yellow-400"
                        fill="none"
                        viewBox="0 0 24 24"
                      >
                        <circle
                          class="opacity-25"
                          cx="12"
                          cy="12"
                          r="10"
                          stroke="currentColor"
                          stroke-width="4"
                        ></circle>
                        <path
                          class="opacity-75"
                          fill="currentColor"
                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                        ></path>
                      </svg>
                    </div>

                    <!-- No transactions -->
                    <div
                      v-else-if="!transactions || transactions.length === 0"
                      class="text-center py-8 bg-[#1a1a1a] border border-gray-800 rounded-lg"
                    >
                      <svg
                        class="w-12 h-12 text-gray-600 mx-auto mb-3"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                        />
                      </svg>
                      <p class="text-gray-400 text-sm">Chưa có giao dịch nào</p>
                    </div>

                    <!-- Recent transactions (max 3) -->
                    <div v-else class="space-y-2">
                      <div
                        v-for="transaction in transactions.slice(0, 3)"
                        :key="transaction.id"
                        class="bg-[#1a1a1a] border border-gray-800 rounded-lg p-3 hover:border-gray-700 transition-colors"
                      >
                        <div class="flex items-center justify-between">
                          <div class="flex items-center gap-3 flex-1 min-w-0">
                            <div
                              class="w-8 h-8 rounded bg-yellow-500/20 flex items-center justify-center flex-shrink-0"
                            >
                              <svg
                                class="w-4 h-4 text-yellow-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                              >
                                <path
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M5 13l4 4L19 7"
                                />
                              </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                              <div class="flex items-center gap-2 mb-0.5">
                                <p
                                  class="text-white text-sm font-medium truncate"
                                >
                                  {{ transaction.plan_name }}
                                </p>
                                <span
                                  class="px-1.5 py-0.5 rounded text-xs font-semibold flex-shrink-0"
                                  :class="{
                                    'bg-green-500/20 text-green-400':
                                      transaction.status === 'completed',
                                    'bg-yellow-500/20 text-yellow-400':
                                      transaction.status === 'pending',
                                    'bg-red-500/20 text-red-400':
                                      transaction.status === 'cancelled',
                                  }"
                                >
                                  {{ getStatusText(transaction.status) }}
                                </span>
                              </div>
                              <p class="text-gray-400 text-xs">
                                {{ transaction.created_at_formatted }}
                              </p>
                            </div>
                          </div>
                          <p
                            class="text-yellow-400 font-semibold text-sm ml-3 flex-shrink-0"
                          >
                            {{ formatPrice(transaction.total_price) }}đ
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Bảo mật -->
                <div v-if="activeSection === 'password'">
                  <h1 class="text-4xl font-bold text-white mb-2 tracking-tight">
                    Bảo mật
                  </h1>
                  <p class="text-gray-300 mb-8">
                    Quản lý thông tin bảo mật của bạn
                  </p>

                  <!-- Thông tin đăng nhập -->
                  <!-- Thông tin đăng nhập -->
                  <div
                    class="bg-[#1a1a1a] border border-gray-800 rounded-lg p-5 mb-6"
                  >
                    <h3
                      class="text-base font-semibold text-white mb-4 flex items-center gap-2"
                    >
                      <svg
                        class="w-5 h-5 text-yellow-400"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      Thông tin đăng nhập
                    </h3>

                    <!-- Thông báo nếu đăng nhập bằng Google -->
                    <div
                      v-if="isGoogleUser"
                      class="mb-4 bg-blue-500/10 rounded-lg p-3 border border-blue-500/30"
                    >
                      <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24">
                          <path
                            fill="#4285F4"
                            d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                          />
                          <path
                            fill="#34A853"
                            d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                          />
                          <path
                            fill="#FBBC05"
                            d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                          />
                          <path
                            fill="#EA4335"
                            d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                          />
                        </svg>
                        <p class="text-blue-300 text-sm">
                          Đăng nhập bằng Google - Mật khẩu được quản lý bởi
                          Google
                        </p>
                      </div>
                    </div>

                    <div class="space-y-3">
                      <!-- Mật khẩu -->
                      <button
                        v-if="!isGoogleUser"
                        @click="openPasswordModal"
                        class="w-full flex items-center justify-between p-3 bg-[#2a2a2a] hover:bg-[#3a3a3a] rounded-lg transition-all group border border-gray-700 hover:border-yellow-400"
                      >
                        <div class="flex items-center gap-3">
                          <div
                            class="w-9 h-9 rounded-lg bg-yellow-500/20 flex items-center justify-center flex-shrink-0"
                          >
                            <svg
                              class="w-4 h-4 text-yellow-400"
                              fill="none"
                              stroke="currentColor"
                              viewBox="0 0 24 24"
                            >
                              <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                              />
                            </svg>
                          </div>
                          <div class="text-left">
                            <p class="text-white font-medium text-sm">
                              Mật khẩu
                            </p>
                            <p class="text-gray-400 text-xs">••••••••</p>
                          </div>
                        </div>
                        <svg
                          class="w-4 h-4 text-gray-500 group-hover:text-yellow-400 transition-colors flex-shrink-0"
                          fill="none"
                          stroke="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 5l7 7-7 7"
                          />
                        </svg>
                      </button>

                      <!-- Email -->
                      <div
                        class="w-full p-3 bg-[#2a2a2a] rounded-lg border border-gray-700"
                      >
                        <div class="flex items-center gap-3">
                          <div
                            class="w-9 h-9 rounded-lg bg-green-500/20 flex items-center justify-center flex-shrink-0"
                          >
                            <svg
                              class="w-4 h-4 text-green-400"
                              fill="none"
                              stroke="currentColor"
                              viewBox="0 0 24 24"
                            >
                              <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                              />
                            </svg>
                          </div>
                          <div class="flex-1 min-w-0">
                            <p class="text-white font-medium text-sm">Email</p>
                            <p class="text-gray-400 text-xs truncate">
                              {{ user.email }}
                            </p>
                          </div>
                          <svg
                            class="w-3 h-3 text-green-500 flex-shrink-0"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                          >
                            <path
                              fill-rule="evenodd"
                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                              clip-rule="evenodd"
                            />
                          </svg>
                        </div>
                      </div>

                      <!-- Điện thoại -->
                      <button
                        @click="openPhoneModal"
                        class="w-full flex items-center justify-between p-3 bg-[#2a2a2a] hover:bg-[#3a3a3a] rounded-lg transition-all group border border-gray-700 hover:border-yellow-400"
                      >
                        <div class="flex items-center gap-3">
                          <div
                            class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0"
                            :class="
                              savedPhoneNumber
                                ? 'bg-green-500/20'
                                : 'bg-gray-700'
                            "
                          >
                            <svg
                              class="w-4 h-4"
                              :class="
                                savedPhoneNumber
                                  ? 'text-green-400'
                                  : 'text-gray-400'
                              "
                              fill="none"
                              stroke="currentColor"
                              viewBox="0 0 24 24"
                            >
                              <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"
                              />
                            </svg>
                          </div>
                          <div class="text-left">
                            <p class="text-white font-medium text-sm">
                              Số điện thoại
                            </p>
                            <p class="text-gray-400 text-xs">
                              {{ savedPhoneNumber || "Chưa có" }}
                            </p>
                          </div>
                        </div>
                        <svg
                          class="w-4 h-4 text-gray-500 group-hover:text-yellow-400 transition-colors flex-shrink-0"
                          fill="none"
                          stroke="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 5l7 7-7 7"
                          />
                        </svg>
                      </button>
                    </div>
                  </div>

                  <!-- Xóa tài khoản -->
                  <div
                    class="bg-red-500/10 border border-red-500/30 rounded-lg p-5"
                  >
                    <div class="flex items-center gap-3 mb-3">
                      <svg
                        class="w-5 h-5 text-red-400"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      <div>
                        <h3 class="text-red-400 font-semibold">
                          Xóa tài khoản
                        </h3>
                        <p class="text-red-200 text-xs">
                          Hành động này không thể hoàn tác
                        </p>
                      </div>
                    </div>
                    <button
                      @click="showDeleteAccountModal = true"
                      class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-2.5 px-4 rounded-lg transition-all flex items-center justify-center gap-2"
                    >
                      <svg
                        class="w-4 h-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                        />
                      </svg>
                      Xóa tài khoản vĩnh viễn
                    </button>
                  </div>
                </div>

                <!-- Lịch sử giao dịch -->
                <div v-if="activeSection === 'transactions'">
                  <!-- Header -->
                  <div class="flex items-center justify-between mb-6">
                    <div>
                      <h1
                        class="text-4xl font-bold text-white mb-2 tracking-tight"
                      >
                        Lịch sử giao dịch
                      </h1>
                      <p
                        class="text-gray-300"
                        v-if="transactions && transactions.length > 0"
                      >
                        {{ transactions.length }} giao dịch • Tổng:
                        <span class="text-yellow-400 font-semibold"
                          >{{
                            formatPrice(
                              transactions
                                .filter((t) => t.status === "completed")
                                .reduce(
                                  (sum, t) => sum + parseFloat(t.total_price),
                                  0
                                )
                            )
                          }}đ</span
                        >
                      </p>
                      <p class="text-gray-300" v-else>Các gói bạn đã mua</p>
                    </div>
                    <button
                      @click="fetchTransactions"
                      :disabled="loadingTransactions"
                      class="flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-black px-4 py-2 rounded-lg font-medium transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                      title="Làm mới"
                    >
                      <svg
                        class="w-4 h-4"
                        :class="{ 'animate-spin': loadingTransactions }"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                        />
                      </svg>
                      <span>Làm mới</span>
                    </button>
                  </div>

                  <!-- Loading -->
                  <div
                    v-if="loadingTransactions"
                    class="flex justify-center items-center py-12"
                  >
                    <svg
                      class="animate-spin h-8 w-8 text-yellow-400"
                      fill="none"
                      viewBox="0 0 24 24"
                    >
                      <circle
                        class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4"
                      ></circle>
                      <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                      ></path>
                    </svg>
                  </div>

                  <!-- No transactions -->
                  <div
                    v-else-if="!transactions || transactions.length === 0"
                    class="text-center py-12 bg-[#1a1a1a] border border-gray-800 rounded-lg"
                  >
                    <svg
                      class="w-16 h-16 text-gray-600 mx-auto mb-4"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                      />
                    </svg>
                    <h3 class="text-white font-bold mb-2">
                      Chưa có giao dịch nào
                    </h3>
                    <p class="text-gray-400 text-sm mb-6">
                      Lịch sử mua gói sẽ hiển thị ở đây
                    </p>
                    <button
                      @click="router.push('/pricing')"
                      class="bg-yellow-500 hover:bg-yellow-600 text-black font-bold px-6 py-3 rounded-lg transition-colors"
                    >
                      Xem các gói
                    </button>
                  </div>

                  <!-- Transactions list -->
                  <div v-else class="space-y-3">
                    <div
                      v-for="transaction in transactions"
                      :key="transaction.id"
                      class="bg-[#1a1a1a] border border-gray-800 rounded-lg p-4 hover:border-gray-700 transition-all group"
                    >
                      <div class="flex items-center justify-between">
                        <!-- Left: Icon + Info -->
                        <div class="flex items-center gap-3 flex-1">
                          <!-- Icon with status color -->
                          <div
                            class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0"
                            :class="{
                              'bg-green-500/20':
                                transaction.status === 'completed',
                              'bg-yellow-500/20':
                                transaction.status === 'pending',
                              'bg-red-500/20':
                                transaction.status === 'cancelled',
                            }"
                          >
                            <svg
                              class="w-5 h-5"
                              :class="{
                                'text-green-400':
                                  transaction.status === 'completed',
                                'text-yellow-400':
                                  transaction.status === 'pending',
                                'text-red-400':
                                  transaction.status === 'cancelled',
                              }"
                              fill="none"
                              stroke="currentColor"
                              viewBox="0 0 24 24"
                            >
                              <path
                                v-if="transaction.status === 'completed'"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"
                              />
                              <path
                                v-else-if="transaction.status === 'pending'"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                              />
                              <path
                                v-else
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                              />
                            </svg>
                          </div>

                          <!-- Info -->
                          <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                              <h3 class="text-white font-semibold">
                                {{ transaction.plan_name }}
                              </h3>
                              <span
                                class="px-2 py-0.5 bg-yellow-500/20 text-yellow-400 rounded text-xs font-semibold"
                              >
                                {{ transaction.quality }}
                              </span>
                              <span
                                class="px-2 py-0.5 rounded text-xs font-semibold"
                                :class="{
                                  'bg-green-500/20 text-green-400':
                                    transaction.status === 'completed',
                                  'bg-yellow-500/20 text-yellow-400':
                                    transaction.status === 'pending',
                                  'bg-red-500/20 text-red-400':
                                    transaction.status === 'cancelled',
                                }"
                              >
                                {{ getStatusText(transaction.status) }}
                              </span>
                            </div>
                            <div
                              class="flex items-center gap-3 text-xs text-gray-400"
                            >
                              <span>{{
                                transaction.created_at_formatted
                              }}</span>
                              <span>•</span>
                              <span>{{ transaction.duration }} ngày</span>
                              <span>•</span>
                              <span>{{
                                transaction.payment_method_name ||
                                "Chuyển khoản"
                              }}</span>
                            </div>
                          </div>
                        </div>

                        <!-- Right: Price -->
                        <div class="text-right flex-shrink-0 ml-4">
                          <p class="text-yellow-400 font-bold">
                            {{ formatPrice(transaction.total_price) }}đ
                          </p>
                          <p class="text-gray-500 text-xs font-mono">
                            #{{ transaction.order_code }}
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Cài đặt -->
                <div v-if="activeSection === 'settings'">
                  <h1 class="text-3xl font-bold text-white mb-2 tracking-tight">
                    Quản lý hồ sơ và tùy chọn
                  </h1>

                  <!-- Quản lý hồ sơ -->
                  <div
                    class="bg-[#1a1a1a] border border-gray-800 rounded-lg p-6 mb-6"
                  >
                    <div class="flex items-center justify-between mb-4">
                      <div class="flex items-center">
                        <div
                          class="w-12 h-12 bg-blue-500 rounded-lg mr-4 flex items-center justify-center text-white font-bold text-xl"
                        >
                          {{
                            user.displayName?.charAt(0) ||
                            user.email?.charAt(0) ||
                            "U"
                          }}
                        </div>
                        <div>
                          <p class="text-white font-medium">
                            {{ user.displayName || user.email?.split("@")[0] }}
                          </p>
                          <p class="text-gray-400 text-sm">
                            Đã thông tin tất cả nội dung và tên hộ
                          </p>
                        </div>
                      </div>
                      <svg
                        class="w-5 h-5 text-gray-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 5l7 7-7 7"
                        />
                      </svg>
                    </div>

                    <!-- Khóa hồ sơ -->
                    <button
                      class="w-full flex items-center justify-between p-4 hover:bg-[#2a2a2a] rounded-lg transition-colors group border-t border-gray-800"
                    >
                      <div class="flex items-center">
                        <svg
                          class="w-6 h-6 mr-4 text-gray-400"
                          fill="none"
                          stroke="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                          />
                        </svg>
                        <div class="text-left">
                          <p class="text-white font-medium">Khóa hồ sơ</p>
                          <p class="text-gray-400 text-sm">Bật</p>
                        </div>
                      </div>
                      <svg
                        class="w-5 h-5 text-gray-400 group-hover:text-yellow-400 transition-colors"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 5l7 7-7 7"
                        />
                      </svg>
                    </button>
                  </div>

                  <!-- Tùy chọn -->
                  <div
                    class="bg-[#1a1a1a] border border-gray-800 rounded-lg p-6 mb-6"
                  >
                    <h3
                      class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4"
                    >
                      Tùy chọn
                    </h3>

                    <!-- Ngôn ngữ -->
                    <button
                      class="w-full flex items-center justify-between p-4 hover:bg-[#2a2a2a] rounded-lg transition-colors group"
                    >
                      <div class="flex items-center">
                        <svg
                          class="w-6 h-6 mr-4 text-gray-400"
                          fill="none"
                          stroke="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"
                          />
                        </svg>
                        <div class="text-left flex-1">
                          <p class="text-white font-medium">Ngôn ngữ</p>
                          <p class="text-gray-400 text-sm">
                            Tiếng Việt, English
                          </p>
                        </div>
                      </div>
                      <svg
                        class="w-5 h-5 text-gray-400 group-hover:text-yellow-400 transition-colors"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 5l7 7-7 7"
                        />
                      </svg>
                    </button>

                    <!-- Điều chỉnh tính năng kiểm soát của cha mẹ -->
                    <button
                      class="w-full flex items-center justify-between p-4 hover:bg-[#2a2a2a] rounded-lg transition-colors group border-t border-gray-800 mt-2"
                    >
                      <div class="flex items-center">
                        <svg
                          class="w-6 h-6 mr-4 text-gray-400"
                          fill="none"
                          stroke="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                          />
                        </svg>
                        <div class="text-left flex-1">
                          <p class="text-white font-medium">
                            Điều chỉnh tính năng kiểm soát của cha mẹ
                          </p>
                          <p class="text-gray-400 text-sm">
                            Đặt giới hạn độ tuổi và các phần
                          </p>
                        </div>
                      </div>
                      <svg
                        class="w-5 h-5 text-gray-400 group-hover:text-yellow-400 transition-colors"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 5l7 7-7 7"
                        />
                      </svg>
                    </button>

                    <!-- Hình thức phụ đề -->
                    <button
                      class="w-full flex items-center justify-between p-4 hover:bg-[#2a2a2a] rounded-lg transition-colors group border-t border-gray-800 mt-2"
                    >
                      <div class="flex items-center">
                        <svg
                          class="w-6 h-6 mr-4 text-gray-400"
                          fill="none"
                          stroke="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"
                          />
                        </svg>
                        <div class="text-left flex-1">
                          <p class="text-white font-medium">Hình thức phụ đề</p>
                          <p class="text-gray-400 text-sm">
                            Tùy chỉnh cách hiển thị phụ đề
                          </p>
                        </div>
                      </div>
                      <svg
                        class="w-5 h-5 text-gray-400 group-hover:text-yellow-400 transition-colors"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 5l7 7-7 7"
                        />
                      </svg>
                    </button>

                    <!-- Cài đặt phát lại -->
                    <button
                      class="w-full flex items-center justify-between p-4 hover:bg-[#2a2a2a] rounded-lg transition-colors group border-t border-gray-800 mt-2"
                    >
                      <div class="flex items-center">
                        <svg
                          class="w-6 h-6 mr-4 text-gray-400"
                          fill="none"
                          stroke="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"
                          />
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                          />
                        </svg>
                        <div class="text-left flex-1">
                          <p class="text-white font-medium">Cài đặt phát lại</p>
                          <p class="text-gray-400 text-sm">
                            Cài đặt tự động phát và chất lượng âm thanh, hình
                            ảnh
                          </p>
                        </div>
                      </div>
                      <svg
                        class="w-5 h-5 text-gray-400 group-hover:text-yellow-400 transition-colors"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 5l7 7-7 7"
                        />
                      </svg>
                    </button>

                    <!-- Cài đặt thông báo -->
                    <button
                      class="w-full flex items-center justify-between p-4 hover:bg-[#2a2a2a] rounded-lg transition-colors group border-t border-gray-800 mt-2"
                    >
                      <div class="flex items-center">
                        <svg
                          class="w-6 h-6 mr-4 text-gray-400"
                          fill="none"
                          stroke="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                          />
                        </svg>
                        <div class="text-left flex-1">
                          <p class="text-white font-medium">
                            Cài đặt thông báo
                          </p>
                          <p class="text-gray-400 text-sm">
                            Quản lý thông báo cho email, tin nhắn và thông báo
                            đẩy
                          </p>
                        </div>
                      </div>
                      <svg
                        class="w-5 h-5 text-gray-400 group-hover:text-yellow-400 transition-colors"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 5l7 7-7 7"
                        />
                      </svg>
                    </button>

                    <!-- Hoạt động xem -->
                    <button
                      class="w-full flex items-center justify-between p-4 hover:bg-[#2a2a2a] rounded-lg transition-colors group border-t border-gray-800 mt-2"
                    >
                      <div class="flex items-center">
                        <svg
                          class="w-6 h-6 mr-4 text-gray-400"
                          fill="none"
                          stroke="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                          />
                        </svg>
                        <div class="text-left flex-1">
                          <p class="text-white font-medium">Hoạt động xem</p>
                          <p class="text-gray-400 text-sm">
                            Quản lý lịch sử xem và đánh giá
                          </p>
                        </div>
                      </div>
                      <svg
                        class="w-5 h-5 text-gray-400 group-hover:text-yellow-400 transition-colors"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 5l7 7-7 7"
                        />
                      </svg>
                    </button>

                    <!-- Cài đặt quyền riêng tư và dữ liệu -->
                    <button
                      class="w-full flex items-center justify-between p-4 hover:bg-[#2a2a2a] rounded-lg transition-colors group border-t border-gray-800 mt-2"
                    >
                      <div class="flex items-center">
                        <svg
                          class="w-6 h-6 mr-4 text-gray-400"
                          fill="none"
                          stroke="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                          />
                        </svg>
                        <div class="text-left flex-1">
                          <p class="text-white font-medium">
                            Cài đặt quyền riêng tư và dữ liệu
                          </p>
                          <p class="text-gray-400 text-sm">
                            Quản lý việc sử dụng thông tin cá nhân
                          </p>
                        </div>
                      </div>
                      <svg
                        class="w-5 h-5 text-gray-400 group-hover:text-yellow-400 transition-colors"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 5l7 7-7 7"
                        />
                      </svg>
                    </button>
                  </div>

                  <!-- Xóa hồ sơ -->
                  <div
                    class="bg-[#1a1a1a] border border-gray-800 rounded-lg p-6"
                  >
                    <button
                      class="w-full flex items-center justify-center p-4 hover:bg-[#2a2a2a] rounded-lg transition-colors group"
                    >
                      <svg
                        class="w-5 h-5 mr-2 text-gray-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                        />
                      </svg>
                      <span class="text-gray-400 font-medium">Xóa hồ sơ</span>
                    </button>
                    <p class="text-gray-500 text-xs text-center mt-2">
                      Không thể khôi phục hồ sơ đã xóa.
                    </p>
                  </div>
                </div>

                <!-- Hồ sơ -->
                <div v-if="activeSection === 'profile'">
                  <h1 class="text-3xl font-bold text-white mb-2 tracking-tight">
                    Hồ sơ
                  </h1>
                  <p class="text-gray-400 mb-8">
                    Tính năng kiểm soát và cấp phép của cha mẹ
                  </p>

                  <!-- Tính năng kiểm soát -->
                  <div
                    class="bg-[#1a1a1a] border border-gray-800 rounded-lg p-6 mb-6"
                  >
                    <!-- Điều chỉnh tính năng kiểm soát của cha mẹ -->
                    <button
                      class="w-full flex items-center justify-between p-4 hover:bg-[#2a2a2a] rounded-lg transition-colors group"
                    >
                      <div class="flex items-center flex-1">
                        <svg
                          class="w-6 h-6 mr-4 text-gray-400"
                          fill="none"
                          stroke="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                          />
                        </svg>
                        <div class="text-left flex-1">
                          <p class="text-white font-medium">
                            Điều chỉnh tính năng kiểm soát của cha mẹ
                          </p>
                          <p class="text-gray-400 text-sm">
                            Cài đặt giới hạn độ tuổi, chặn tác phẩm
                          </p>
                        </div>
                      </div>
                      <svg
                        class="w-5 h-5 text-gray-500 group-hover:text-yellow-400 transition-colors"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 5l7 7-7 7"
                        />
                      </svg>
                    </button>

                    <!-- Chuyển hồ sơ -->
                    <div class="border-t border-gray-800 mt-2 pt-2">
                      <button
                        class="w-full flex items-center justify-between p-4 hover:bg-[#2a2a2a] rounded-lg transition-colors group"
                      >
                        <div class="flex items-center flex-1">
                          <svg
                            class="w-6 h-6 mr-4 text-gray-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                          >
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"
                            />
                          </svg>
                          <div class="text-left flex-1">
                            <p class="text-white font-medium">Chuyển hồ sơ</p>
                            <p class="text-gray-400 text-sm">
                              Sao chép hồ sơ sang tài khoản khác
                            </p>
                          </div>
                        </div>
                        <svg
                          class="w-5 h-5 text-gray-500 group-hover:text-yellow-400 transition-colors"
                          fill="none"
                          stroke="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 5l7 7-7 7"
                          />
                        </svg>
                      </button>
                    </div>
                  </div>

                  <!-- Thiết lập hồ sơ -->
                  <div
                    class="bg-[#1a1a1a] border border-gray-800 rounded-lg p-6"
                  >
                    <h3 class="text-lg font-semibold text-white mb-4">
                      Thiết lập hồ sơ
                    </h3>

                    <!-- Profile 1 -->
                    <button
                      class="w-full flex items-center justify-between p-4 hover:bg-[#2a2a2a] rounded-lg transition-colors group mb-2"
                    >
                      <div class="flex items-center flex-1">
                        <div
                          class="w-12 h-12 bg-blue-500 rounded-lg mr-4 flex items-center justify-center text-white font-bold text-xl"
                        >
                          {{
                            user.displayName?.charAt(0) ||
                            user.email?.charAt(0) ||
                            "U"
                          }}
                        </div>
                        <div class="text-left flex-1">
                          <p class="text-white font-medium">
                            {{ user.displayName || user.email?.split("@")[0] }}
                          </p>
                          <span
                            class="bg-blue-500 text-white text-xs font-bold px-2 py-0.5 rounded"
                            >Hồ sơ của bạn</span
                          >
                        </div>
                      </div>
                      <svg
                        class="w-5 h-5 text-gray-500 group-hover:text-yellow-400 transition-colors"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 5l7 7-7 7"
                        />
                      </svg>
                    </button>

                    <!-- Profile 2 -->
                    <button
                      class="w-full flex items-center justify-between p-4 hover:bg-[#2a2a2a] rounded-lg transition-colors group mb-2"
                    >
                      <div class="flex items-center flex-1">
                        <div
                          class="w-12 h-12 bg-yellow-500 rounded-lg mr-4 flex items-center justify-center text-white font-bold text-xl"
                        >
                          P2
                        </div>
                        <p class="text-white font-medium">Profile 2</p>
                      </div>
                      <svg
                        class="w-5 h-5 text-gray-500 group-hover:text-yellow-400 transition-colors"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 5l7 7-7 7"
                        />
                      </svg>
                    </button>

                    <!-- Profile 3 -->
                    <button
                      class="w-full flex items-center justify-between p-4 hover:bg-[#2a2a2a] rounded-lg transition-colors group mb-2"
                    >
                      <div class="flex items-center flex-1">
                        <div
                          class="w-12 h-12 bg-purple-500 rounded-lg mr-4 flex items-center justify-center text-white font-bold text-xl"
                        >
                          P3
                        </div>
                        <p class="text-white font-medium">Profile 3</p>
                      </div>
                      <svg
                        class="w-5 h-5 text-gray-500 group-hover:text-yellow-400 transition-colors"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 5l7 7-7 7"
                        />
                      </svg>
                    </button>

                    <!-- Profile 4 -->
                    <button
                      class="w-full flex items-center justify-between p-4 hover:bg-[#2a2a2a] rounded-lg transition-colors group mb-2"
                    >
                      <div class="flex items-center flex-1">
                        <div
                          class="w-12 h-12 bg-pink-500 rounded-lg mr-4 flex items-center justify-center text-white font-bold text-xl"
                        >
                          P4
                        </div>
                        <p class="text-white font-medium">Profile 4</p>
                      </div>
                      <svg
                        class="w-5 h-5 text-gray-500 group-hover:text-yellow-400 transition-colors"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 5l7 7-7 7"
                        />
                      </svg>
                    </button>

                    <!-- Profile 5 -->
                    <button
                      class="w-full flex items-center justify-between p-4 hover:bg-[#2a2a2a] rounded-lg transition-colors group"
                    >
                      <div class="flex items-center flex-1">
                        <div
                          class="w-12 h-12 bg-cyan-500 rounded-lg mr-4 flex items-center justify-center text-white font-bold text-xl"
                        >
                          P5
                        </div>
                        <p class="text-white font-medium">Profile 5</p>
                      </div>
                      <svg
                        class="w-5 h-5 text-gray-500 group-hover:text-yellow-400 transition-colors"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 5l7 7-7 7"
                        />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </main>
          </div>
        </div>
      </div>
    </div>

    <!-- Phone Number Modal -->
    <div
      v-if="showPhoneModal"
      class="fixed inset-0 bg-black/80 flex items-center justify-center z-50 p-4"
      @click.self="closePhoneModal"
    >
      <div
        class="bg-[#1a1a1a] border border-gray-800 rounded-lg p-6 max-w-md w-full animate-fade-in"
      >
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-2xl font-bold text-white">Số điện thoại</h2>
          <button
            @click="closePhoneModal"
            class="text-gray-400 hover:text-white transition-colors"
          >
            <svg
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>
        </div>

        <div class="mb-6">
          <label class="block text-gray-300 text-sm font-medium mb-2">
            Nhập số điện thoại của bạn
          </label>
          <input
            v-model="phoneNumber"
            type="tel"
            placeholder="0123456789"
            class="w-full bg-[#2a2a2a] border border-gray-700 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-yellow-400 transition-colors"
            maxlength="11"
          />
          <p class="text-gray-500 text-xs mt-2">Nhập 10-11 chữ số</p>
        </div>

        <div class="flex gap-3">
          <button
            @click="savePhoneNumber"
            class="flex-1 bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-3 px-6 rounded-lg transition-colors"
          >
            Lưu
          </button>
          <button
            v-if="savedPhoneNumber"
            @click="removePhoneNumber"
            class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transition-colors"
          >
            Xóa
          </button>
          <button
            @click="closePhoneModal"
            class="flex-1 bg-gray-700 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded-lg transition-colors"
          >
            Hủy
          </button>
        </div>
      </div>
    </div>

    <!-- Change Password Modal -->
    <div
      v-if="showPasswordModal"
      class="fixed inset-0 bg-black/80 flex items-center justify-center z-50 p-4"
    >
      <div
        class="bg-[#1a1a1a] border border-gray-800 rounded-lg p-6 max-w-md w-full animate-fade-in"
      >
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-2xl font-bold text-white">Đổi mật khẩu</h2>
          <button
            @click="closePasswordModal"
            class="text-gray-400 hover:text-white transition-colors"
          >
            <svg
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>
        </div>

        <!-- Step 1: Nhập email -->
        <div v-if="passwordStep === 1">
          <p class="text-gray-300 mb-4">
            Nhập email của bạn để nhận mã xác thực
          </p>
          <div class="mb-6">
            <label class="block text-gray-300 text-sm font-medium mb-2"
              >Email</label
            >
            <input
              v-model="passwordChangeForm.email"
              type="email"
              placeholder="email@example.com"
              class="w-full bg-[#2a2a2a] border border-gray-700 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-yellow-400 transition-colors"
            />
          </div>
          <button
            @click="sendVerificationCode"
            :disabled="isLoadingPassword"
            class="w-full bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-3 px-6 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ isLoadingPassword ? "Đang gửi..." : "Gửi mã xác thực" }}
          </button>
        </div>

        <!-- Step 2: Nhập mã và mật khẩu mới -->
        <div v-if="passwordStep === 2">
          <p class="text-gray-300 mb-4">
            Nhập mã xác thực đã gửi đến email và mật khẩu mới
          </p>

          <div class="mb-4">
            <label class="block text-gray-300 text-sm font-medium mb-2"
              >Mã xác thực (6 số)</label
            >
            <input
              v-model="passwordChangeForm.token"
              type="text"
              placeholder="• • • • • •"
              maxlength="6"
              autocomplete="off"
              class="w-full bg-[#2a2a2a] border border-gray-700 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-yellow-400 transition-colors text-center text-2xl tracking-widest"
            />
          </div>

          <div class="mb-4">
            <label class="block text-gray-300 text-sm font-medium mb-2"
              >Mật khẩu mới</label
            >
            <div class="relative">
              <input
                v-model="passwordChangeForm.newPassword"
                :type="showNewPassword ? 'text' : 'password'"
                placeholder="• • • • • • • •"
                autocomplete="new-password"
                class="w-full bg-[#2a2a2a] border border-gray-700 rounded-lg px-4 py-3 pr-12 text-white placeholder-gray-500 focus:outline-none focus:border-yellow-400 transition-colors"
              />
              <button
                @click="showNewPassword = !showNewPassword"
                type="button"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-yellow-400 transition-colors"
              >
                <svg
                  v-if="!showNewPassword"
                  class="w-5 h-5"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                  />
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                  />
                </svg>
                <svg
                  v-else
                  class="w-5 h-5"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"
                  />
                </svg>
              </button>
            </div>
          </div>

          <div class="mb-6">
            <label class="block text-gray-300 text-sm font-medium mb-2"
              >Xác nhận mật khẩu</label
            >
            <div class="relative">
              <input
                v-model="passwordChangeForm.confirmPassword"
                :type="showConfirmPassword ? 'text' : 'password'"
                placeholder="• • • • • • • •"
                autocomplete="new-password"
                class="w-full bg-[#2a2a2a] border border-gray-700 rounded-lg px-4 py-3 pr-12 text-white placeholder-gray-500 focus:outline-none focus:border-yellow-400 transition-colors"
              />
              <button
                @click="showConfirmPassword = !showConfirmPassword"
                type="button"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-yellow-400 transition-colors"
              >
                <svg
                  v-if="!showConfirmPassword"
                  class="w-5 h-5"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                  />
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                  />
                </svg>
                <svg
                  v-else
                  class="w-5 h-5"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"
                  />
                </svg>
              </button>
            </div>
          </div>

          <div class="flex gap-3">
            <button
              @click="passwordStep = 1"
              class="flex-1 bg-gray-700 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded-lg transition-colors"
            >
              Quay lại
            </button>
            <button
              @click="verifyAndChangePassword"
              :disabled="isLoadingPassword"
              class="flex-1 bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-3 px-6 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ isLoadingPassword ? "Đang xử lý..." : "Đổi mật khẩu" }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, reactive, h, computed, watch } from "vue";
import {
  getAuth,
  onAuthStateChanged,
  updateProfile,
  signOut,
} from "firebase/auth";
import { useToast } from "@/composables/useToast";
import { useRouter } from "vue-router";
import EditProfileModal from "@/components/EditProfileModal.vue";

const auth = getAuth();
const toast = useToast();
const router = useRouter();

const user = ref(null);
const loading = ref(true);
const activeSection = ref("overview");
const showEditProfile = ref(false);
const showPhoneModal = ref(false);
const phoneNumber = ref("");
const savedPhoneNumber = ref(localStorage.getItem("userPhoneNumber") || "");
const showPasswordModal = ref(false);
const isGoogleUser = ref(false);
const passwordStep = ref(1); // 1: nhập email, 2: nhập mã, 3: nhập mật khẩu mới
const passwordChangeForm = reactive({
  email: "",
  token: "",
  newPassword: "",
  confirmPassword: "",
});
const isLoadingPassword = ref(false);
const showNewPassword = ref(false);
const showConfirmPassword = ref(false);

const passwordForm = reactive({
  current: "",
  new: "",
  confirm: "",
});

const profileForm = reactive({
  displayName: "",
});

// Menu items with icons - Simplified for movie streaming website
const menuItems = [
  {
    id: "overview",
    label: "Tổng quan",
    icon: () =>
      h(
        "svg",
        {
          class: "w-5 h-5",
          fill: "none",
          stroke: "currentColor",
          viewBox: "0 0 24 24",
        },
        [
          h("path", {
            "stroke-linecap": "round",
            "stroke-linejoin": "round",
            "stroke-width": "2",
            d: "M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z",
          }),
        ]
      ),
  },
  {
    id: "transactions",
    label: "Lịch sử giao dịch",
    icon: () =>
      h(
        "svg",
        {
          class: "w-5 h-5",
          fill: "none",
          stroke: "currentColor",
          viewBox: "0 0 24 24",
        },
        [
          h("path", {
            "stroke-linecap": "round",
            "stroke-linejoin": "round",
            "stroke-width": "2",
            d: "M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01",
          }),
        ]
      ),
  },
  {
    id: "password",
    label: "Bảo mật",
    icon: () =>
      h(
        "svg",
        {
          class: "w-5 h-5",
          fill: "none",
          stroke: "currentColor",
          viewBox: "0 0 24 24",
        },
        [
          h("path", {
            "stroke-linecap": "round",
            "stroke-linejoin": "round",
            "stroke-width": "2",
            d: "M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z",
          }),
        ]
      ),
  },
];

const subscriptions = ref([]);
const loadingSubscription = ref(false);
const realtimeTimer = ref(0); // Để force re-render progress bar

// Computed: Active subscription (highest priority)
const activeSubscription = computed(() => {
  // Lấy gói active HOẶC cancelled nhưng chưa hết hạn (như Netflix)
  const active = subscriptions.value.filter((sub) => {
    const isActiveOrCancelled =
      sub.status === "active" || sub.status === "cancelled";
    // Sử dụng thời gian từ server thay vì local time
    const notExpired =
      sub.minutes_remaining > 0 || sub.subscription_status === "active";
    return isActiveOrCancelled && notExpired;
  });

  if (active.length === 0) return null;

  // Sort by priority: vip > premium > basic > free
  const priority = { vip: 4, premium: 3, basic: 2, free: 1 };
  return active.sort((a, b) => {
    return (priority[b.plan_slug] || 0) - (priority[a.plan_slug] || 0);
  })[0];
});

// Computed: Pending subscriptions
const pendingSubscriptions = computed(() => {
  return subscriptions.value.filter((sub) => sub.status === "pending");
});
const currentTime = ref(new Date());

// Removed demo functions - using real payment system only

// Fetch user subscriptions (multiple)
const fetchSubscription = async () => {
  const storedUser = localStorage.getItem("user");
  if (!storedUser) {
    console.log("❌ No user in localStorage");
    return;
  }

  try {
    const userData = JSON.parse(storedUser);
    console.log("👤 User data:", userData);

    // Dùng trực tiếp user ID từ localStorage (user ID 109)
    const userId = 109; // Hard-code user ID 109 cho đơn giản

    console.log("🆔 Using User ID:", userId);
    loadingSubscription.value = true;

    const API_URL = "http://localhost/HTHREE_film/backend/api";
    const apiUrl = `${API_URL}/user_subscription.php?user_id=${userId}`;
    console.log("🌐 Calling API:", apiUrl);

    const response = await fetch(apiUrl);

    if (!response.ok) {
      console.error(
        "❌ API Response not OK:",
        response.status,
        response.statusText
      );
      throw new Error(`API Error: ${response.status}`);
    }

    const data = await response.json();
    console.log("📡 API Response:", data);

    if (data.success) {
      if (data.data && data.data.length > 0) {
        subscriptions.value = Array.isArray(data.data)
          ? data.data
          : [data.data];
        console.log("📦 Subscriptions found:", subscriptions.value.length);
        console.log("📦 Subscription details:", subscriptions.value);
      } else {
        subscriptions.value = [];
        console.log("📭 No subscriptions found for user:", userId);
      }
    } else {
      console.error("❌ API Error:", data.message);
      subscriptions.value = [];
    }
  } catch (error) {
    console.error("❌ Failed to fetch subscription:", error);
  } finally {
    loadingSubscription.value = false;
  }
};

// Get plan color based on slug
const getPlanColor = (slug) => {
  const colors = {
    basic: "from-blue-900 via-blue-800 to-cyan-900 border-blue-700",
    premium: "from-red-900 via-red-800 to-orange-900 border-red-700",
    vip: "from-purple-900 via-purple-800 to-pink-900 border-purple-700",
    free: "from-gray-800 via-gray-700 to-gray-900 border-gray-700",
  };
  return colors[slug] || colors.free;
};

// Get plan description
const getPlanDescription = (slug) => {
  const descriptions = {
    free: "Xem phim miễn phí với quảng cáo",
    basic: "Xem phim HD không quảng cáo trên 1 thiết bị",
    premium: "Xem phim Full HD trên 2 thiết bị, tải về được",
    vip: "Xem phim 4K trên 4 thiết bị, xem trước phim mới",
  };
  return descriptions[slug] || "";
};

// Format price
const formatPrice = (price) => {
  return new Intl.NumberFormat("vi-VN").format(price);
};

// Calculate real-time progress with smooth animation
const getRealtimeProgress = (sub) => {
  if (!sub || !sub.start_date || !sub.end_date) {
    return {
      progress: 0,
      minutesRemaining: 0,
      secondsRemaining: 0,
      timeRemaining: "Đã hết hạn",
      status: "expired",
    };
  }

  // Force reactivity by accessing currentTime.value
  const now = currentTime.value;
  const startTime = new Date(sub.start_date);
  const endTime = new Date(sub.end_date);

  const totalDuration = endTime.getTime() - startTime.getTime(); // milliseconds
  const elapsed = now.getTime() - startTime.getTime(); // milliseconds
  const remaining = endTime.getTime() - now.getTime(); // milliseconds

  // Tính progress theo %
  const progress =
    totalDuration > 0
      ? Math.min(100, Math.max(0, (elapsed / totalDuration) * 100))
      : 0;

  // Tính thời gian còn lại
  const minutesRemaining = Math.max(0, Math.ceil(remaining / (1000 * 60)));
  const secondsRemaining = Math.max(0, Math.ceil(remaining / 1000));

  let timeRemaining;
  if (remaining <= 0) {
    timeRemaining = "Đã hết hạn";
  } else if (minutesRemaining > 0) {
    const seconds = Math.floor((remaining % (1000 * 60)) / 1000);
    timeRemaining = `${minutesRemaining}:${seconds
      .toString()
      .padStart(2, "0")}`;
  } else {
    timeRemaining = `${secondsRemaining} giây`;
  }

  // Xác định status
  let status;
  if (remaining <= 0) {
    status = "expired";
  } else if (minutesRemaining <= 1) {
    status = "expiring_soon";
  } else {
    status = "active";
  }

  // Debug logging for progress tracking
  if (sub.plan_name) {
    console.log(
      `📊 Progress for ${sub.plan_name}: ${progress.toFixed(
        1
      )}% - ${timeRemaining} remaining`
    );
  }

  return {
    progress: parseFloat(progress.toFixed(2)),
    minutesRemaining,
    secondsRemaining,
    timeRemaining,
    status,
  };
};

// Renew current plan
const renewCurrentPlan = (sub) => {
  // Navigate to pricing page with plan pre-selected
  router.push({
    path: "/pricing",
    query: { plan: sub.plan_slug },
  });
  toast.success(`Đang chuyển đến trang mua gói ${sub.plan_name}...`);
};

// Handle upgrade
const handleUpgrade = () => {
  router.push("/pricing");
  toast.info("💎 Khám phá các gói cao cấp hơn!");
};

// Show cancel confirmation modal
const showCancelConfirmModal = (sub) => {
  subscriptionToCancel.value = sub;
  showCancelModal.value = true;
};

// Cancel subscription
const cancellingSubscription = ref(null);
const showCancelModal = ref(false);
const subscriptionToCancel = ref(null);

// Delete account
const showDeleteAccountModal = ref(false);

const confirmDeleteAccount = async () => {
  // TODO: Implement delete account API call
  console.log("Delete account confirmed");
  toast.info("Chức năng xóa tài khoản đang được phát triển");
  showDeleteAccountModal.value = false;
};

const confirmCancelSubscription = async () => {
  const sub = subscriptionToCancel.value;
  if (!sub) return;

  cancellingSubscription.value = true;

  try {
    const API_URL = "http://localhost/HTHREE_film/backend/api";
    const response = await fetch(`${API_URL}/cancel_subscription.php`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ subscription_id: sub.id }),
    });

    const data = await response.json();

    if (data.success) {
      toast.success(
        "Gói đã được hủy thành công! Bạn vẫn có thể sử dụng đến hết hạn."
      );
      showCancelModal.value = false;
      // Refresh subscriptions
      await fetchSubscription();
    } else {
      toast.error(data.message || "Không thể hủy gói");
    }
  } catch (error) {
    console.error("Error cancelling subscription:", error);
    toast.error("Có lỗi xảy ra khi hủy gói");
  } finally {
    cancellingSubscription.value = false;
  }
};

// Transactions
const transactions = ref([]);
const loadingTransactions = ref(false);

const fetchTransactions = async () => {
  const storedUser = localStorage.getItem("user");
  if (!storedUser) return;

  try {
    const userData = JSON.parse(storedUser);
    const userId = userData.id;

    loadingTransactions.value = true;
    const API_URL = "http://localhost/HTHREE_film/backend/api";
    const response = await fetch(
      `${API_URL}/user_transactions.php?user_id=${userId}`
    );
    const data = await response.json();

    if (data.success && data.transactions) {
      transactions.value = data.transactions;
      console.log("💳 Transactions:", transactions.value);
    }
  } catch (error) {
    console.error("Failed to fetch transactions:", error);
  } finally {
    loadingTransactions.value = false;
  }
};

const getStatusText = (status) => {
  const statusMap = {
    completed: "Thành công",
    pending: "Đang xử lý",
    cancelled: "Đã hủy",
  };
  return statusMap[status] || status;
};

const viewTransactionDetail = (transaction) => {
  toast.info(`Chi tiết đơn hàng #${transaction.order_code}`);
  // TODO: Open transaction detail modal
};

// Reactive timer for smooth progress bar animation
let progressTimer = null;
let dataRefreshTimer = null;

onMounted(() => {
  console.log("🚀 onMounted: Starting fetchSubscription");

  // Initial data fetch
  fetchSubscription();
  fetchTransactions();
  loading.value = false;

  // Kiểm tra user trong localStorage trước
  const storedUser = localStorage.getItem("user");
  if (storedUser) {
    try {
      const userData = JSON.parse(storedUser);
      console.log("👤 Found user in localStorage:", userData);

      user.value = {
        uid: userData.uid || userData.id,
        email: userData.email,
        displayName: userData.displayName || userData.full_name,
      };
    } catch (e) {
      console.log("❌ Error parsing user:", e);
    }
  }

  // Firebase auth fallback
  onAuthStateChanged(auth, (currentUser) => {
    if (currentUser && !user.value) {
      user.value = currentUser;
      profileForm.displayName = currentUser.displayName || "";
      isGoogleUser.value = currentUser.providerData.some(
        (provider) => provider.providerId === "google.com"
      );
    } else if (!currentUser && !storedUser) {
      router.push("/home");
    }
    loading.value = false;
  });

  // Start reactive timers for smooth progress animation
  startProgressTimer();
});

// Start progress timer for smooth animation
const startProgressTimer = () => {
  // Update progress bar every second for smooth animation
  progressTimer = setInterval(() => {
    currentTime.value = new Date();
    console.log("⏱️ Progress timer tick:", new Date().toLocaleTimeString());
  }, 1000);

  // Refresh subscription data every 10 seconds to get latest from server
  dataRefreshTimer = setInterval(() => {
    console.log("🔄 Auto-refreshing subscription data");
    fetchSubscription();
  }, 10000);
};

// Cleanup intervals on unmount
onUnmounted(() => {
  if (progressTimer) {
    clearInterval(progressTimer);
    console.log("🧹 Cleaned up progress timer");
  }
  if (dataRefreshTimer) {
    clearInterval(dataRefreshTimer);
    console.log("🧹 Cleaned up data refresh timer");
  }
});

// Watch activeSection to fetch transactions when needed
watch(activeSection, (newSection) => {
  if (newSection === "transactions") {
    // Always fetch when switching to transactions tab (to get latest data)
    fetchTransactions();
  }
});

// Handle save profile
const handleSaveProfile = async (profileData) => {
  try {
    // Update Firebase profile
    if (auth.currentUser) {
      await updateProfile(auth.currentUser, {
        displayName: profileData.full_name,
      });
    }

    // Update local user ref
    user.value = {
      ...user.value,
      ...profileData,
    };

    toast.success("✅ Đã cập nhật hồ sơ thành công");
  } catch (error) {
    console.error("Error updating profile:", error);
    toast.error("❌ Không thể cập nhật hồ sơ");
  }
};

const getJoinMonth = () => {
  if (!user.value?.metadata?.creationTime) return "1/2025";
  const date = new Date(user.value.metadata.creationTime);
  return `${date.getMonth() + 1}/${date.getFullYear()}`;
};

const getNextPaymentDate = () => {
  const date = new Date();
  date.setMonth(date.getMonth() + 1);
  return `${date.getDate()} tháng ${
    date.getMonth() + 1
  }, ${date.getFullYear()}`;
};

const saveProfile = async () => {
  if (!user.value) return;

  try {
    await updateProfile(user.value, {
      displayName: profileForm.displayName,
    });

    // Update user object in localStorage for checkout auto-fill
    const storedUser = localStorage.getItem("user");
    if (storedUser) {
      const userData = JSON.parse(storedUser);
      userData.full_name = profileForm.displayName;
      userData.displayName = profileForm.displayName;
      localStorage.setItem("user", JSON.stringify(userData));
      console.log(
        "👤 Updated user name in localStorage:",
        profileForm.displayName
      );
    }

    toast.success("Cập nhật thông tin thành công!");
  } catch (error) {
    toast.error("Lỗi khi cập nhật thông tin: " + error.message);
  }
};

const changePassword = () => {
  if (passwordForm.new !== passwordForm.confirm) {
    toast.error("Mật khẩu xác nhận không khớp!");
    return;
  }
  toast.info("Chức năng đổi mật khẩu đang được phát triển");
};

const openPhoneModal = () => {
  phoneNumber.value = savedPhoneNumber.value;
  showPhoneModal.value = true;
};

const closePhoneModal = () => {
  showPhoneModal.value = false;
  phoneNumber.value = "";
};

const savePhoneNumber = () => {
  if (!phoneNumber.value.trim()) {
    toast.error("Vui lòng nhập số điện thoại!");
    return;
  }

  // Validate phone number (basic validation)
  const phoneRegex = /^[0-9]{10,11}$/;
  if (!phoneRegex.test(phoneNumber.value.replace(/\s/g, ""))) {
    toast.error("Số điện thoại không hợp lệ! Vui lòng nhập 10-11 chữ số.");
    return;
  }

  savedPhoneNumber.value = phoneNumber.value;
  localStorage.setItem("userPhoneNumber", phoneNumber.value);

  // Update user object in localStorage for checkout auto-fill
  const storedUser = localStorage.getItem("user");
  if (storedUser) {
    const userData = JSON.parse(storedUser);
    userData.phone = phoneNumber.value;
    userData.phone_number = phoneNumber.value;
    localStorage.setItem("user", JSON.stringify(userData));
    console.log("📱 Updated user phone in localStorage:", phoneNumber.value);
  }

  toast.success("Lưu số điện thoại thành công!");
  closePhoneModal();
};

const removePhoneNumber = () => {
  savedPhoneNumber.value = "";
  localStorage.removeItem("userPhoneNumber");
  toast.success("Đã xóa số điện thoại!");
  closePhoneModal();
};

const openPasswordModal = () => {
  passwordStep.value = 1;
  passwordChangeForm.email = user.value?.email || "";
  passwordChangeForm.token = "";
  passwordChangeForm.newPassword = "";
  passwordChangeForm.confirmPassword = "";
  showPasswordModal.value = true;
};

const closePasswordModal = () => {
  showPasswordModal.value = false;
  passwordStep.value = 1;
};

const sendVerificationCode = async () => {
  if (!passwordChangeForm.email) {
    toast.error("Vui lòng nhập email!");
    return;
  }

  isLoadingPassword.value = true;

  try {
    const response = await fetch(
      "http://localhost/HTHREE_Film/backend/api/change-password.php",
      {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          action: "send_code",
          email: passwordChangeForm.email,
        }),
      }
    );

    const data = await response.json();

    if (data.success) {
      toast.success(data.message);
      passwordStep.value = 2;
    } else {
      toast.error(data.message);
    }
  } catch (error) {
    toast.error("Lỗi kết nối server!");
  } finally {
    isLoadingPassword.value = false;
  }
};

const verifyAndChangePassword = async () => {
  if (!passwordChangeForm.token) {
    toast.error("Vui lòng nhập mã xác thực!");
    return;
  }

  if (!passwordChangeForm.newPassword) {
    toast.error("Vui lòng nhập mật khẩu mới!");
    return;
  }

  if (passwordChangeForm.newPassword !== passwordChangeForm.confirmPassword) {
    toast.error("Mật khẩu xác nhận không khớp!");
    return;
  }

  if (passwordChangeForm.newPassword.length < 6) {
    toast.error("Mật khẩu phải có ít nhất 6 ký tự!");
    return;
  }

  isLoadingPassword.value = true;

  try {
    const response = await fetch(
      "http://localhost/HTHREE_Film/backend/api/change-password.php",
      {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          action: "verify_and_change",
          email: passwordChangeForm.email,
          token: passwordChangeForm.token,
          newPassword: passwordChangeForm.newPassword,
        }),
      }
    );

    const data = await response.json();

    if (data.success) {
      toast.success("Đổi mật khẩu thành công! Mật khẩu mới đã được lưu.");
      closePasswordModal();
    } else {
      toast.error(data.message);
    }
  } catch (error) {
    toast.error("Lỗi kết nối server!");
  } finally {
    isLoadingPassword.value = false;
  }
};
</script>

<style scoped>
.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.animate-slide-down-bar {
  animation: slideDownBar 0.3s ease-out;
  transform-origin: top;
}

@keyframes slideDownBar {
  from {
    transform: scaleY(0);
  }
  to {
    transform: scaleY(1);
  }
}

.animate-fade-in {
  animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

/* Smooth progress bar animation */
.progress-bar {
  transition: width 0.5s ease-out !important;
}

/* Pulse animation for active progress */
.progress-pulse {
  animation: progressPulse 2s ease-in-out infinite;
}

@keyframes progressPulse {
  0%,
  100% {
    opacity: 0.8;
  }
  50% {
    opacity: 1;
  }
}
</style>
