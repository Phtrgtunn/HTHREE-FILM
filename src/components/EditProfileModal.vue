<template>
  <Transition name="modal">
    <div
      v-if="modelValue"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
      @click.self="closeModal"
    >
      <div class="relative w-full max-w-lg bg-gray-900 rounded-2xl shadow-2xl overflow-hidden border border-gray-700">
        <!-- Header -->
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 px-6 py-4">
          <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold text-black">Chỉnh sửa hồ sơ</h3>
            <button
              @click="closeModal"
              class="text-black/70 hover:text-black transition-colors"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Content -->
        <form @submit.prevent="handleSave" class="p-6 space-y-6">
          <!-- Avatar -->
          <div class="flex flex-col items-center">
            <div class="relative group">
              <img
                :src="previewAvatar || localProfile.avatar || defaultAvatar"
                class="w-24 h-24 rounded-full border-4 border-yellow-500 object-cover"
                :alt="localProfile.full_name"
              />
              <label
                class="absolute inset-0 flex items-center justify-center bg-black/60 rounded-full opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer"
              >
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <input
                  type="file"
                  accept="image/*"
                  class="hidden"
                  @change="handleAvatarChange"
                />
              </label>
            </div>
            <p class="text-xs text-gray-400 mt-2">Click để thay đổi ảnh đại diện</p>
          </div>

          <!-- Full Name -->
          <FormInput
            id="fullName"
            v-model="localProfile.full_name"
            label="Họ và tên"
            placeholder="Nhập họ và tên"
            :required="true"
            :validation="(value) => {
              if (!value) return 'Họ và tên là bắt buộc';
              if (value.length < 2) return 'Họ và tên phải có ít nhất 2 ký tự';
              return true;
            }"
          />

          <!-- Username -->
          <FormInput
            id="username"
            v-model="localProfile.username"
            label="Tên đăng nhập"
            placeholder="Nhập tên đăng nhập"
            :required="true"
            :validation="(value) => {
              if (!value) return 'Tên đăng nhập là bắt buộc';
              if (value.length < 3) return 'Tên đăng nhập phải có ít nhất 3 ký tự';
              if (!/^[a-zA-Z0-9_]+$/.test(value)) return 'Chỉ được dùng chữ, số và dấu gạch dưới';
              return true;
            }"
            helper-text="Chỉ chữ, số và dấu gạch dưới"
          />

          <!-- Email -->
          <FormInput
            id="email"
            v-model="localProfile.email"
            type="email"
            label="Email"
            placeholder="Nhập email"
            :required="true"
            :validation="(value) => {
              if (!value) return 'Email là bắt buộc';
              const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
              if (!emailRegex.test(value)) return 'Email không hợp lệ';
              return true;
            }"
          />

          <!-- Phone (Optional) -->
          <FormInput
            id="phone"
            v-model="localProfile.phone"
            type="tel"
            label="Số điện thoại"
            placeholder="Nhập số điện thoại (tùy chọn)"
            :validation="(value) => {
              if (!value) return true; // Optional
              if (!/^[0-9]{10,11}$/.test(value)) return 'Số điện thoại không hợp lệ';
              return true;
            }"
            helper-text="10-11 chữ số"
          />

          <!-- Bio (Optional) -->
          <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">
              Giới thiệu bản thân
            </label>
            <textarea
              v-model="localProfile.bio"
              rows="3"
              placeholder="Viết vài dòng về bạn..."
              class="w-full px-4 py-2.5 rounded-lg bg-gray-800 text-white placeholder-gray-500 border border-gray-700 focus:outline-none focus:border-yellow-400 focus:ring-1 focus:ring-yellow-400 resize-none"
              maxlength="200"
            ></textarea>
            <p class="text-xs text-gray-500 mt-1 text-right">
              {{ localProfile.bio?.length || 0 }}/200
            </p>
          </div>
        </form>

        <!-- Footer -->
        <div class="bg-gray-800/50 px-6 py-4 flex gap-3">
          <button
            @click="closeModal"
            type="button"
            class="flex-1 px-4 py-3 rounded-lg bg-gray-700 hover:bg-gray-600 text-white font-medium transition-colors"
          >
            Hủy
          </button>
          <LoadingButton
            @click="handleSave"
            :loading="saving"
            variant="primary"
            size="lg"
            class="flex-1"
          >
            Lưu thay đổi
          </LoadingButton>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import FormInput from './FormInput.vue';
import LoadingButton from './LoadingButton.vue';

const props = defineProps({
  modelValue: Boolean,
  user: Object
});

const emit = defineEmits(['update:modelValue', 'save']);

const localProfile = ref({
  full_name: '',
  username: '',
  email: '',
  phone: '',
  bio: '',
  avatar: ''
});

const previewAvatar = ref('');
const avatarFile = ref(null);
const saving = ref(false);

const defaultAvatar = computed(() => {
  const name = localProfile.value.full_name || localProfile.value.username || 'User';
  return `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=f59e0b&color=000`;
});

const handleAvatarChange = (event) => {
  const file = event.target.files[0];
  if (file) {
    avatarFile.value = file;
    const reader = new FileReader();
    reader.onload = (e) => {
      previewAvatar.value = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const closeModal = () => {
  emit('update:modelValue', false);
  previewAvatar.value = '';
  avatarFile.value = null;
};

const handleSave = async () => {
  saving.value = true;
  
  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1000));
    
    emit('save', {
      ...localProfile.value,
      avatarFile: avatarFile.value
    });
    
    closeModal();
  } catch (error) {
    console.error('Error saving profile:', error);
  } finally {
    saving.value = false;
  }
};

// Initialize values when modal opens
watch(() => props.modelValue, (newVal) => {
  if (newVal && props.user) {
    localProfile.value = {
      full_name: props.user.full_name || props.user.displayName || '',
      username: props.user.username || '',
      email: props.user.email || '',
      phone: props.user.phone || '',
      bio: props.user.bio || '',
      avatar: props.user.avatar || props.user.photoURL || ''
    };
    previewAvatar.value = '';
    avatarFile.value = null;
  }
});
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-active > div,
.modal-leave-active > div {
  transition: transform 0.3s ease;
}

.modal-enter-from > div {
  transform: scale(0.9);
}

.modal-leave-to > div {
  transform: scale(0.9);
}
</style>
