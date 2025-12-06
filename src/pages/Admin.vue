<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-900 via-black to-gray-900">
    <!-- Notification Modal -->
    <NotificationModal
      v-model="showNotification"
      :type="notification.type"
      :title="notification.title"
      :message="notification.message"
    />

    <!-- Confirm Modal -->
    <ConfirmModal
      v-model="showConfirm"
      :title="confirmData.title"
      :message="confirmData.message"
      :type="confirmData.type"
      :confirm-text="confirmData.confirmText"
      :cancel-text="confirmData.cancelText"
      @confirm="confirmData.onConfirm"
    />

    <!-- Order Detail Modal -->
    <div
      v-if="showOrderDetail"
      class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4"
      @click.self="showOrderDetail = false"
    >
      <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto border border-gray-700 shadow-2xl">
        <!-- Header -->
        <div class="sticky top-0 bg-gradient-to-r from-red-600 to-yellow-500 p-6 flex items-center justify-between">
          <div>
            <h3 class="text-2xl font-black text-white">Chi tiết đơn hàng</h3>
            <p class="text-white/80 text-sm mt-1">{{ selectedOrder?.order_code }}</p>
          </div>
          <button
            @click="showOrderDetail = false"
            class="w-10 h-10 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center transition-all"
          >
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-6" v-if="selectedOrder">
          <!-- Thông tin khách hàng -->
          <div class="bg-gray-800/50 rounded-xl p-6 border border-gray-700">
            <h4 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
              <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
              </svg>
              Thông tin khách hàng
            </h4>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <p class="text-gray-400 text-sm">Họ tên</p>
                <p class="text-white font-medium">{{ selectedOrder.customer_name }}</p>
              </div>
              <div>
                <p class="text-gray-400 text-sm">Email</p>
                <p class="text-white font-medium">{{ selectedOrder.customer_email }}</p>
              </div>
              <div>
                <p class="text-gray-400 text-sm">Số điện thoại</p>
                <p class="text-white font-medium">{{ selectedOrder.customer_phone || 'Chưa có' }}</p>
              </div>
              <div>
                <p class="text-gray-400 text-sm">Ngày đặt</p>
                <p class="text-white font-medium">{{ selectedOrder.created_at }}</p>
              </div>
            </div>
          </div>

          <!-- Thông tin đơn hàng -->
          <div class="bg-gray-800/50 rounded-xl p-6 border border-gray-700">
            <h4 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
              <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/>
              </svg>
              Thông tin đơn hàng
            </h4>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <p class="text-gray-400 text-sm">Mã đơn hàng</p>
                <p class="text-white font-mono font-bold">{{ selectedOrder.order_code }}</p>
              </div>
              <div>
                <p class="text-gray-400 text-sm">Phương thức thanh toán</p>
                <p class="text-white font-medium">{{ getPaymentMethodText(selectedOrder.payment_method) }}</p>
              </div>
              <div>
                <p class="text-gray-400 text-sm">Trạng thái thanh toán</p>
                <span :class="['px-3 py-1 rounded-full text-xs font-bold inline-block', getStatusClass(selectedOrder.payment_status)]">
                  {{ getStatusText(selectedOrder.payment_status) }}
                </span>
              </div>
              <div>
                <p class="text-gray-400 text-sm">Trạng thái đơn hàng</p>
                <span class="text-white font-medium">{{ selectedOrder.status }}</span>
              </div>
            </div>
          </div>

          <!-- Chi tiết sản phẩm -->
          <div class="bg-gray-800/50 rounded-xl p-6 border border-gray-700">
            <h4 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
              <svg class="w-5 h-5 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/>
              </svg>
              Chi tiết sản phẩm
            </h4>
            <div class="space-y-3">
              <div
                v-for="item in orderItems"
                :key="item.id"
                class="flex items-center justify-between p-4 bg-gray-900/50 rounded-lg"
              >
                <div class="flex-1">
                  <p class="text-white font-bold">{{ item.plan_name }}</p>
                  <p class="text-gray-400 text-sm">{{ item.duration_months }} tháng × {{ item.quantity }}</p>
                </div>
                <div class="text-right">
                  <p class="text-white font-bold">{{ formatMoney(item.total) }}</p>
                  <p class="text-gray-400 text-sm">{{ formatMoney(item.price) }} / tháng</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Tổng tiền -->
          <div class="bg-gradient-to-r from-red-600/20 to-yellow-500/20 rounded-xl p-6 border border-red-500/30">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-gray-300 text-sm">Tổng cộng</p>
                <p class="text-3xl font-black text-white">{{ selectedOrder.total_formatted }}</p>
              </div>
              <div class="text-right">
                <p class="text-gray-300 text-sm">Giảm giá</p>
                <p class="text-xl font-bold text-yellow-400">{{ formatMoney(selectedOrder.discount || 0) }}</p>
              </div>
            </div>
          </div>

          <!-- Ghi chú -->
          <div v-if="selectedOrder.note" class="bg-gray-800/50 rounded-xl p-6 border border-gray-700">
            <h4 class="text-lg font-bold text-white mb-2">Ghi chú</h4>
            <p class="text-gray-300">{{ selectedOrder.note }}</p>
          </div>

          <!-- Thông báo tự động kích hoạt -->
          <div v-if="selectedOrder.payment_status === 'paid'" class="bg-green-500/10 border border-green-500/30 rounded-xl p-4">
            <div class="flex items-center gap-3">
              <svg class="w-6 h-6 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
              <div>
                <p class="text-green-400 font-bold">Đã kích hoạt tự động</p>
                <p class="text-green-300 text-sm">Subscription đã được kích hoạt ngay sau khi thanh toán thành công</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer Actions -->
        <div class="sticky bottom-0 bg-gray-900 p-6 border-t border-gray-700 flex gap-4">
          <button
            @click="showOrderDetail = false"
            class="flex-1 bg-gray-700 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded-xl transition-all"
          >
            Đóng
          </button>
        </div>
      </div>
    </div>

    <!-- Sidebar -->
    <aside class="fixed left-0 top-0 h-full w-64 bg-gray-900/95 backdrop-blur-xl border-r border-gray-800 z-40">
      <div class="p-6">
        <!-- Logo -->
        <router-link 
          to="/home" 
          class="flex items-center gap-3 mb-8 hover:opacity-80 transition-opacity cursor-pointer group"
        >
          <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-red-600 to-yellow-500 flex items-center justify-center group-hover:scale-110 transition-transform">
            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
              <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
            </svg>
          </div>
          <div>
            <h1 class="text-xl font-black text-white">HTHREE</h1>
            <p class="text-xs text-gray-400">Admin Panel</p>
          </div>
        </router-link>

        <!-- Menu -->
        <nav class="space-y-2">
          <button
            v-for="item in menuItems"
            :key="item.id"
            @click="activeTab = item.id"
            :class="[
              'w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 text-left',
              activeTab === item.id
                ? 'bg-gradient-to-r from-red-600 to-yellow-500 text-white shadow-lg shadow-red-500/20'
                : 'text-gray-400 hover:text-white hover:bg-gray-800'
            ]"
          >
            <component :is="item.icon" class="w-5 h-5 flex-shrink-0" />
            <span class="font-medium flex-1">{{ item.label }}</span>
            <span v-if="item.badge" class="bg-red-500 text-white text-xs px-2 py-0.5 rounded-full animate-pulse flex-shrink-0">
              {{ item.badge }}
            </span>
          </button>
        </nav>
      </div>

      <!-- User Info -->
      <div class="absolute bottom-0 left-0 right-0 p-6 border-t border-gray-800">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-600 to-pink-500 flex items-center justify-center">
            <span class="text-white font-bold">A</span>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-white font-medium text-sm truncate">Admin</p>
            <p class="text-gray-400 text-xs">Super Admin</p>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="ml-64 p-8">
      <!-- Header -->
      <header class="mb-8">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <div>
              <h2 class="text-3xl font-black text-white mb-2">{{ currentTabTitle }}</h2>
              <p class="text-gray-400">{{ currentTabDescription }}</p>
            </div>
            <!-- Loading Indicator -->
            <div v-if="loading" class="flex items-center gap-2 text-gray-400">
              <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span class="text-sm">Đang tải...</span>
            </div>
          </div>
          <div class="flex items-center gap-4">
            <!-- Refresh Button -->
            <button 
              @click="refreshData"
              class="p-3 bg-gray-800 rounded-xl hover:bg-gray-700 transition-all group"
              title="Làm mới dữ liệu"
            >
              <svg class="w-6 h-6 text-gray-400 group-hover:text-white group-hover:rotate-180 transition-all duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
              </svg>
            </button>
            
            <!-- Notifications -->
            <button class="relative p-3 bg-gray-800 rounded-xl hover:bg-gray-700 transition-all">
              <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
              </svg>
              <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>
          </div>
        </div>
      </header>

      <!-- Dashboard Tab -->
      <Transition name="slide-fade" mode="out-in">
        <div v-if="activeTab === 'dashboard'" key="dashboard" class="space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div
            v-for="(stat, index) in stats"
            :key="index"
            class="group relative overflow-hidden bg-gradient-to-br from-gray-900/90 to-gray-800/90 backdrop-blur-sm rounded-2xl p-6 border border-gray-700/50 hover:border-gray-600 transition-all duration-300 hover:scale-105"
          >
            <div class="absolute inset-0 bg-gradient-to-br opacity-0 group-hover:opacity-10 transition-opacity" :class="stat.gradient"></div>
            
            <div class="relative">
              <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center" :class="stat.iconBg">
                  <component :is="stat.icon" class="w-6 h-6 text-white" />
                </div>
                <span :class="['text-sm font-bold px-2 py-1 rounded-lg', stat.changeBg]">
                  {{ stat.change }}
                </span>
              </div>
              
              <h3 class="text-gray-400 text-sm mb-2">{{ stat.label }}</h3>
              <p class="text-3xl font-black text-white">{{ stat.value }}</p>
            </div>
          </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Recent Orders -->
          <div class="bg-gradient-to-br from-gray-900/90 to-gray-800/90 backdrop-blur-sm rounded-2xl p-6 border border-gray-700/50">
            <h3 class="text-xl font-bold text-white mb-4">Đơn hàng gần đây</h3>
            <div class="space-y-3">
              <div
                v-for="order in recentOrders.slice(0, 5)"
                :key="order.id"
                class="flex items-center justify-between p-4 bg-gray-800/50 rounded-xl hover:bg-gray-800 transition-all"
              >
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-600 to-cyan-500 flex items-center justify-center">
                    <span class="text-white font-bold text-sm">{{ order.order_code.slice(-3) }}</span>
                  </div>
                  <div>
                    <p class="text-white font-medium text-sm">{{ order.customer_name }}</p>
                    <p class="text-gray-400 text-xs">{{ order.created_at }}</p>
                  </div>
                </div>
                <div class="text-right">
                  <p class="text-white font-bold">{{ order.total_formatted }}</p>
                  <span :class="['text-xs px-2 py-1 rounded-full', getStatusClass(order.payment_status)]">
                    {{ getStatusText(order.payment_status) }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Top Plans -->
          <div class="bg-gradient-to-br from-gray-900/90 to-gray-800/90 backdrop-blur-sm rounded-2xl p-6 border border-gray-700/50">
            <h3 class="text-xl font-bold text-white mb-4">Gói phổ biến</h3>
            <div class="space-y-4">
              <div v-for="plan in topPlans" :key="plan.name" class="space-y-2">
                <div class="flex items-center justify-between">
                  <span class="text-white font-medium">{{ plan.name }}</span>
                  <span class="text-gray-400 text-sm">{{ plan.count }} đơn</span>
                </div>
                <div class="h-2 bg-gray-800 rounded-full overflow-hidden">
                  <div
                    class="h-full rounded-full transition-all duration-500"
                    :class="plan.color"
                    :style="{ width: plan.percentage + '%' }"
                  ></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      </Transition>

      <!-- Orders Tab -->
      <Transition name="slide-fade" mode="out-in">
        <div v-if="activeTab === 'orders'" key="orders" class="space-y-6">
        <!-- Filters -->
        <div class="flex items-center gap-4">
          <div class="flex-1">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Tìm kiếm đơn hàng..."
              class="w-full bg-gray-800/80 text-white px-4 py-3 rounded-xl border border-gray-700 focus:border-red-600 focus:outline-none focus:ring-2 focus:ring-red-600/20 transition-all"
            />
          </div>
          <select
            v-model="filterStatus"
            class="bg-gray-800/80 text-white px-4 py-3 rounded-xl border border-gray-700 focus:border-red-600 focus:outline-none"
          >
            <option value="">Tất cả trạng thái</option>
            <option value="pending">Chờ xử lý</option>
            <option value="paid">Đã thanh toán</option>
            <option value="failed">Thất bại</option>
          </select>
        </div>

        <!-- Orders Table -->
        <div class="bg-gradient-to-br from-gray-900/90 to-gray-800/90 backdrop-blur-sm rounded-2xl border border-gray-700/50 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="border-b border-gray-800">
                  <th class="text-left p-4 text-gray-400 font-medium text-sm">Mã đơn</th>
                  <th class="text-left p-4 text-gray-400 font-medium text-sm">Khách hàng</th>
                  <th class="text-left p-4 text-gray-400 font-medium text-sm">Số tiền</th>
                  <th class="text-left p-4 text-gray-400 font-medium text-sm">Thanh toán</th>
                  <th class="text-left p-4 text-gray-400 font-medium text-sm">Trạng thái</th>
                  <th class="text-left p-4 text-gray-400 font-medium text-sm">Ngày tạo</th>
                  <th class="text-left p-4 text-gray-400 font-medium text-sm">Thao tác</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="order in filteredOrders"
                  :key="order.id"
                  class="border-b border-gray-800 hover:bg-gray-800/50 transition-all"
                >
                  <td class="p-4">
                    <span class="text-white font-mono font-bold">{{ order.order_code }}</span>
                  </td>
                  <td class="p-4">
                    <div>
                      <p class="text-white font-medium">{{ order.customer_name }}</p>
                      <p class="text-gray-400 text-sm">{{ order.customer_email }}</p>
                    </div>
                  </td>
                  <td class="p-4">
                    <span class="text-white font-bold">{{ order.total_formatted }}</span>
                  </td>
                  <td class="p-4">
                    <span class="text-gray-400 text-sm">{{ getPaymentMethodText(order.payment_method) }}</span>
                  </td>
                  <td class="p-4">
                    <span :class="['px-3 py-1 rounded-full text-xs font-bold', getStatusClass(order.payment_status)]">
                      {{ getStatusText(order.payment_status) }}
                    </span>
                  </td>
                  <td class="p-4">
                    <span class="text-gray-400 text-sm">{{ order.created_at }}</span>
                  </td>
                  <td class="p-4">
                    <div class="flex items-center gap-2">
                      <button
                        @click="viewOrder(order)"
                        class="p-2 bg-blue-600 hover:bg-blue-700 rounded-lg transition-all"
                        title="Xem chi tiết"
                      >
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      </Transition>

      <!-- Users Tab -->
      <Transition name="slide-fade" mode="out-in">
        <div v-if="activeTab === 'users'" key="users" class="space-y-6">
        <div class="bg-gradient-to-br from-gray-900/90 to-gray-800/90 backdrop-blur-sm rounded-2xl border border-gray-700/50 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="border-b border-gray-800">
                  <th class="text-left p-4 text-gray-400 font-medium text-sm">ID</th>
                  <th class="text-left p-4 text-gray-400 font-medium text-sm">Tên</th>
                  <th class="text-left p-4 text-gray-400 font-medium text-sm">Email</th>
                  <th class="text-left p-4 text-gray-400 font-medium text-sm">Role</th>
                  <th class="text-left p-4 text-gray-400 font-medium text-sm">Đơn hàng</th>
                  <th class="text-left p-4 text-gray-400 font-medium text-sm">Tổng chi</th>
                  <th class="text-left p-4 text-gray-400 font-medium text-sm">Trạng thái</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="user in users" :key="user.id" class="border-b border-gray-800 hover:bg-gray-800/50 transition-all">
                  <td class="p-4"><span class="text-white font-mono">{{ user.id }}</span></td>
                  <td class="p-4"><span class="text-white font-medium">{{ user.username }}</span></td>
                  <td class="p-4"><span class="text-gray-400 text-sm">{{ user.email }}</span></td>
                  <td class="p-4">
                    <span :class="['px-3 py-1 rounded-full text-xs font-bold', user.role === 'admin' ? 'bg-purple-500/20 text-purple-400' : 'bg-blue-500/20 text-blue-400']">
                      {{ user.role }}
                    </span>
                  </td>
                  <td class="p-4"><span class="text-white">{{ user.total_orders }}</span></td>
                  <td class="p-4"><span class="text-white font-bold">{{ user.total_spent_formatted }}</span></td>
                  <td class="p-4">
                    <span :class="['px-3 py-1 rounded-full text-xs font-bold', user.is_active ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400']">
                      {{ user.is_active ? 'Active' : 'Inactive' }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      </Transition>

      <!-- Plans Tab -->
      <Transition name="slide-fade" mode="out-in">
        <div v-if="activeTab === 'plans'" key="plans" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div v-for="plan in plans" :key="plan.id" class="bg-gradient-to-br from-gray-900/90 to-gray-800/90 backdrop-blur-sm rounded-2xl p-6 border border-gray-700/50 hover:border-gray-600 transition-all">
            <h3 class="text-2xl font-black text-white mb-2">{{ plan.name }}</h3>
            <p class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-yellow-500 mb-4">{{ plan.price_formatted }}</p>
            <div class="space-y-2 text-sm">
              <p class="text-gray-400">Chất lượng: <span class="text-white font-bold">{{ plan.quality }}</span></p>
              <p class="text-gray-400">Thiết bị: <span class="text-white font-bold">{{ plan.max_devices }}</span></p>
              <p class="text-gray-400">Đã bán: <span class="text-white font-bold">{{ plan.times_sold }} lần</span></p>
              <p class="text-gray-400">Doanh thu: <span class="text-white font-bold">{{ plan.total_revenue_formatted }}</span></p>
              <p class="text-gray-400">Đang dùng: <span class="text-white font-bold">{{ plan.active_subscriptions }} người</span></p>
            </div>
          </div>
        </div>
      </div>

      </Transition>

      <!-- Coupons Tab -->
      <Transition name="slide-fade" mode="out-in">
        <div v-if="activeTab === 'coupons'" key="coupons" class="space-y-6">
        <div class="bg-gradient-to-br from-gray-900/90 to-gray-800/90 backdrop-blur-sm rounded-2xl border border-gray-700/50 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="border-b border-gray-800">
                  <th class="text-left p-4 text-gray-400 font-medium text-sm">Mã</th>
                  <th class="text-left p-4 text-gray-400 font-medium text-sm">Mô tả</th>
                  <th class="text-left p-4 text-gray-400 font-medium text-sm">Giảm giá</th>
                  <th class="text-left p-4 text-gray-400 font-medium text-sm">Đã dùng</th>
                  <th class="text-left p-4 text-gray-400 font-medium text-sm">Giới hạn</th>
                  <th class="text-left p-4 text-gray-400 font-medium text-sm">Hết hạn</th>
                  <th class="text-left p-4 text-gray-400 font-medium text-sm">Trạng thái</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="coupon in coupons" :key="coupon.id" class="border-b border-gray-800 hover:bg-gray-800/50 transition-all">
                  <td class="p-4"><span class="text-white font-mono font-bold">{{ coupon.code }}</span></td>
                  <td class="p-4"><span class="text-gray-400 text-sm">{{ coupon.description }}</span></td>
                  <td class="p-4"><span class="text-yellow-400 font-bold">{{ coupon.discount_formatted }}</span></td>
                  <td class="p-4"><span class="text-white">{{ coupon.usage_count }}</span></td>
                  <td class="p-4"><span class="text-gray-400">{{ coupon.usage_limit || '∞' }}</span></td>
                  <td class="p-4"><span class="text-gray-400 text-sm">{{ coupon.end_date_formatted || 'Không' }}</span></td>
                  <td class="p-4">
                    <span :class="['px-3 py-1 rounded-full text-xs font-bold', coupon.is_active && !coupon.is_expired ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400']">
                      {{ coupon.is_active && !coupon.is_expired ? 'Active' : 'Inactive' }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      </Transition>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { API_CONFIG } from '@/config/api';
import NotificationModal from '@/components/NotificationModal.vue';
import ConfirmModal from '@/components/ConfirmModal.vue';

const router = useRouter();
const activeTab = ref('dashboard');
const searchQuery = ref('');
const filterStatus = ref('');
const showNotification = ref(false);
const loading = ref(false);
const showOrderDetail = ref(false);
const selectedOrder = ref(null);
const orderItems = ref([]);
const showConfirm = ref(false);

// New tabs data
const users = ref([]);
const plans = ref([]);
const coupons = ref([]);

const notification = ref({
  type: 'success',
  title: '',
  message: ''
});

const confirmData = ref({
  title: '',
  message: '',
  type: 'warning',
  confirmText: 'Xác nhận',
  cancelText: 'Hủy',
  onConfirm: () => {}
});

const API_URL = API_CONFIG.BACKEND_URL;

// Pending orders count
const pendingOrdersCount = ref(0);

// Menu items
const menuItems = computed(() => [
  {
    id: 'dashboard',
    label: 'Dashboard',
    icon: 'DashboardIcon'
  },
  {
    id: 'orders',
    label: 'Đơn hàng',
    icon: 'OrdersIcon',
    badge: pendingOrdersCount.value > 0 ? pendingOrdersCount.value.toString() : null
  },
  {
    id: 'users',
    label: 'Người dùng',
    icon: 'UsersIcon'
  },
  {
    id: 'plans',
    label: 'Gói dịch vụ',
    icon: 'PlansIcon'
  },
  {
    id: 'coupons',
    label: 'Mã giảm giá',
    icon: 'CouponsIcon'
  }
]);

// Stats (will be populated from API)
const stats = ref([
  {
    label: 'Tổng doanh thu',
    value: '0 đ',
    change: '+0%',
    icon: 'MoneyIcon',
    iconBg: 'bg-gradient-to-br from-green-600 to-green-700',
    changeBg: 'bg-green-500/20 text-green-400',
    gradient: 'from-green-600 to-green-700'
  },
  {
    label: 'Đơn hàng',
    value: '0',
    change: '+0%',
    icon: 'CartIcon',
    iconBg: 'bg-gradient-to-br from-blue-600 to-blue-700',
    changeBg: 'bg-blue-500/20 text-blue-400',
    gradient: 'from-blue-600 to-blue-700'
  },
  {
    label: 'Người dùng',
    value: '0',
    change: '+0%',
    icon: 'UsersIcon',
    iconBg: 'bg-gradient-to-br from-purple-600 to-purple-700',
    changeBg: 'bg-purple-500/20 text-purple-400',
    gradient: 'from-purple-600 to-purple-700'
  },
  {
    label: 'Chờ xử lý',
    value: '0',
    change: '0',
    icon: 'ClockIcon',
    iconBg: 'bg-gradient-to-br from-orange-600 to-orange-700',
    changeBg: 'bg-orange-500/20 text-orange-400',
    gradient: 'from-orange-600 to-orange-700'
  }
]);

// Orders data
const recentOrders = ref([]);

// Top plans
const topPlans = ref([]);

// Computed
const currentTabTitle = computed(() => {
  const tab = menuItems.value.find(item => item.id === activeTab.value);
  return tab?.label || 'Dashboard';
});

const currentTabDescription = computed(() => {
  const descriptions = {
    dashboard: 'Tổng quan hệ thống',
    orders: 'Xem thông tin đơn hàng (Thanh toán tự động kích hoạt)',
    users: 'Quản lý người dùng',
    plans: 'Quản lý gói dịch vụ',
    coupons: 'Quản lý mã giảm giá'
  };
  return descriptions[activeTab.value] || '';
});

const filteredOrders = computed(() => {
  let orders = recentOrders.value;
  
  if (searchQuery.value) {
    orders = orders.filter(order =>
      order.order_code.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      order.customer_name.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
  }
  
  if (filterStatus.value) {
    orders = orders.filter(order => order.payment_status === filterStatus.value);
  }
  
  return orders;
});

// Methods
const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-500/20 text-yellow-400',
    paid: 'bg-green-500/20 text-green-400',
    failed: 'bg-red-500/20 text-red-400'
  };
  return classes[status] || classes.pending;
};

const getStatusText = (status) => {
  const texts = {
    pending: 'Chờ xử lý',
    paid: 'Đã thanh toán',
    failed: 'Thất bại'
  };
  return texts[status] || status;
};

const getPaymentMethodText = (method) => {
  const texts = {
    vnpay: 'VNPay',
    momo: 'MoMo',
    bank_transfer: 'Chuyển khoản',
    cod: 'Thanh toán sau'
  };
  return texts[method] || method;
};

const viewOrder = async (order) => {
  selectedOrder.value = order;
  showOrderDetail.value = true;
  
  // Fetch order items
  try {
    const response = await axios.get(`${API_URL}/orders.php?order_id=${order.id}`);
    if (response.data.success) {
      orderItems.value = response.data.items || [];
    }
  } catch (error) {
    console.error('Error fetching order items:', error);
    orderItems.value = [];
  }
};

// Đã bỏ tính năng phê duyệt thủ công - Thanh toán tự động kích hoạt

const formatMoney = (amount) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(amount);
};

// Fetch statistics với retry logic
const fetchStatistics = async (retries = 2) => {
  try {
    const response = await axios.get(`${API_URL}/admin/statistics.php`, {
      timeout: 5000 // 5 seconds timeout
    });
    if (response.data.success) {
      const data = response.data.data;
      
      // Update stats
      stats.value[0].value = data.total_revenue_formatted;
      stats.value[0].change = data.revenue_change_formatted;
      stats.value[0].changeBg = data.revenue_change >= 0 ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400';
      
      stats.value[1].value = data.total_orders.toString();
      stats.value[2].value = data.total_users.toString();
      stats.value[3].value = data.pending_orders.toString();
      
      // Update pending orders count for badge
      pendingOrdersCount.value = data.pending_orders;
      
      // Update top plans
      topPlans.value = data.top_plans.map(plan => ({
        name: plan.name,
        count: plan.order_count,
        percentage: plan.percentage,
        color: getPlanColor(plan.slug)
      }));
    }
  } catch (error) {
    console.error('Error fetching statistics:', error);
    
    // Retry nếu còn lần thử
    if (retries > 0) {
      console.log(`Retrying... (${retries} attempts left)`);
      await new Promise(resolve => setTimeout(resolve, 1000)); // Đợi 1s
      return fetchStatistics(retries - 1);
    }
    
    // Chỉ hiển thị lỗi nếu không phải do tab bị ẩn
    if (isPageVisible) {
      console.error('Failed to fetch statistics after retries');
    }
  }
};

// Fetch orders với timeout
const fetchOrders = async () => {
  try {
    const params = new URLSearchParams();
    if (filterStatus.value) params.append('status', filterStatus.value);
    if (searchQuery.value) params.append('search', searchQuery.value);
    params.append('limit', '50');
    
    const response = await axios.get(`${API_URL}/admin/orders.php?${params.toString()}`, {
      timeout: 5000
    });
    if (response.data.success) {
      recentOrders.value = response.data.data;
    }
  } catch (error) {
    if (isPageVisible) {
      console.error('Error fetching orders:', error);
    }
  }
};

// Đã bỏ function confirmPayment - Thanh toán tự động kích hoạt subscription

// Get plan color
const getPlanColor = (slug) => {
  const colors = {
    premium: 'bg-gradient-to-r from-red-600 to-yellow-500',
    basic: 'bg-gradient-to-r from-blue-600 to-cyan-500',
    vip: 'bg-gradient-to-r from-purple-600 to-pink-500',
    free: 'bg-gradient-to-r from-gray-600 to-gray-500'
  };
  return colors[slug] || colors.basic;
};



// Auto refresh với visibility detection
let refreshInterval = null;
let isPageVisible = true;

// Chỉ refresh khi tab đang active
const handleVisibilityChange = () => {
  isPageVisible = !document.hidden;
  
  if (isPageVisible) {
    // Tab được focus lại, refresh ngay
    refreshAllData();
    startAutoRefresh();
  } else {
    // Tab bị ẩn, dừng refresh để tiết kiệm tài nguyên
    stopAutoRefresh();
  }
};

const refreshAllData = async () => {
  // Chỉ refresh khi tab đang visible
  if (!isPageVisible) return;
  
  try {
    // Refresh song song để nhanh hơn
    await Promise.allSettled([
      fetchStatistics(),
      fetchOrders(),
      fetchUsers(),
      fetchPlans(),
      fetchCoupons()
    ]);
  } catch (error) {
    console.error('Error refreshing data:', error);
  }
};

const startAutoRefresh = () => {
  // Dừng interval cũ nếu có
  stopAutoRefresh();
  
  // Tạo interval mới
  refreshInterval = setInterval(() => {
    refreshAllData();
  }, 10000); // 10 seconds
};

const stopAutoRefresh = () => {
  if (refreshInterval) {
    clearInterval(refreshInterval);
    refreshInterval = null;
  }
};

// Manual refresh
const refreshData = () => {
  fetchStatistics();
  fetchOrders();
  notification.value = {
    type: 'success',
    title: 'Đã cập nhật!',
    message: 'Dữ liệu đã được làm mới'
  };
  showNotification.value = true;
};

// Fetch users
const fetchUsers = async () => {
  try {
    const response = await axios.get(`${API_URL}/admin/users.php?limit=100`, {
      timeout: 5000
    });
    if (response.data.success) {
      users.value = response.data.data;
    }
  } catch (error) {
    if (isPageVisible) {
      console.error('Error fetching users:', error);
    }
  }
};

// Fetch plans
const fetchPlans = async () => {
  try {
    const response = await axios.get(`${API_URL}/admin/plans.php`, {
      timeout: 5000
    });
    if (response.data.success) {
      plans.value = response.data.data;
    }
  } catch (error) {
    if (isPageVisible) {
      console.error('Error fetching plans:', error);
    }
  }
};

// Fetch coupons
const fetchCoupons = async () => {
  try {
    const response = await axios.get(`${API_URL}/admin/coupons.php`, {
      timeout: 5000
    });
    if (response.data.success) {
      coupons.value = response.data.data;
    }
  } catch (error) {
    if (isPageVisible) {
      console.error('Error fetching coupons:', error);
    }
  }
};

onMounted(() => {
  // Load dữ liệu ban đầu
  refreshAllData();
  
  // Bắt đầu auto-refresh
  startAutoRefresh();
  
  // Lắng nghe visibility change
  document.addEventListener('visibilitychange', handleVisibilityChange);
});

onUnmounted(() => {
  // Cleanup
  stopAutoRefresh();
  document.removeEventListener('visibilitychange', handleVisibilityChange);
});
</script>

<style scoped>
/* Custom scrollbar */
::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

::-webkit-scrollbar-track {
  background: #1f2937;
}

::-webkit-scrollbar-thumb {
  background: #4b5563;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: #6b7280;
}
</style>


<style scoped>
/* Slide Fade Animation */
.slide-fade-enter-active {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-fade-leave-active {
  transition: all 0.3s cubic-bezier(0.4, 0, 1, 1);
}

.slide-fade-enter-from {
  transform: translateY(-20px);
  opacity: 0;
}

.slide-fade-leave-to {
  transform: translateY(10px);
  opacity: 0;
}

/* Stagger animation cho các items trong tab */
.space-y-6 > * {
  animation: slideIn 0.5s ease-out backwards;
}

.space-y-6 > *:nth-child(1) { animation-delay: 0.05s; }
.space-y-6 > *:nth-child(2) { animation-delay: 0.1s; }
.space-y-6 > *:nth-child(3) { animation-delay: 0.15s; }
.space-y-6 > *:nth-child(4) { animation-delay: 0.2s; }
.space-y-6 > *:nth-child(5) { animation-delay: 0.25s; }

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Smooth scrollbar */
::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

::-webkit-scrollbar-track {
  background: #1f2937;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb {
  background: #4b5563;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: #6b7280;
}

/* Table row hover animation */
tbody tr {
  transition: all 0.2s ease;
}

tbody tr:hover {
  transform: translateX(4px);
}

/* Card hover animation */
.hover\:scale-105:hover {
  transform: scale(1.05) translateY(-4px);
}

</style>
