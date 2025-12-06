<template>
  <Transition name="modal">
    <div
      v-if="modelValue"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
      @click.self="closeModal"
    >
      <div
        class="relative w-full max-w-lg bg-gray-900 rounded-2xl shadow-2xl overflow-hidden border border-gray-700 max-h-[90vh] flex flex-col"
      >
        <!-- Header -->
        <div
          class="bg-gradient-to-r from-yellow-500 to-yellow-600 px-6 py-4 flex-shrink-0"
        >
          <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold text-black">Chỉnh sửa hồ sơ</h3>
            <button
              @click="closeModal"
              class="text-black/70 hover:text-black transition-colors"
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
        </div>

        <!-- Content (Scrollable) -->
        <form
          @submit.prevent="handleSave"
          class="p-6 space-y-6 overflow-y-auto flex-1"
        >
          <!-- Avatar -->
          <div class="flex flex-col items-center">
            <div class="relative group">
              <img
                :src="
                  previewAvatar ||
                  getAvatarUrl(localProfile.avatar) ||
                  defaultAvatar
                "
                class="w-24 h-24 rounded-full border-4 border-yellow-500 object-cover"
                :alt="localProfile.full_name"
                @error="(e) => (e.target.src = defaultAvatar)"
              />
              <!-- Only show upload button if NOT Google login -->
              <label
                v-if="!isGoogleLogin"
                class="absolute inset-0 flex items-center justify-center bg-black/60 rounded-full opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer"
              >
                <svg
                  class="w-8 h-8 text-white"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"
                  />
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"
                  />
                </svg>
                <input
                  type="file"
                  accept="image/*"
                  class="hidden"
                  @change="handleAvatarChange"
                />
              </label>
              <!-- Google login badge -->
              <div
                v-if="isGoogleLogin"
                class="absolute inset-0 flex items-center justify-center bg-black/60 rounded-full opacity-0 group-hover:opacity-100 transition-opacity"
              >
                <svg
                  class="w-8 h-8 text-white"
                  viewBox="0 0 24 24"
                  fill="currentColor"
                >
                  <path
                    d="M12.545,10.239v3.821h5.445c-0.712,2.315-2.647,3.972-5.445,3.972c-3.332,0-6.033-2.701-6.033-6.032s2.701-6.032,6.033-6.032c1.498,0,2.866,0.549,3.921,1.453l2.814-2.814C17.503,2.988,15.139,2,12.545,2C7.021,2,2.543,6.477,2.543,12s4.478,10,10.002,10c8.396,0,10.249-7.85,9.426-11.748L12.545,10.239z"
                  />
                </svg>
              </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">
              {{
                isGoogleLogin
                  ? "Avatar từ Google (không thể thay đổi)"
                  : "Click để thay đổi ảnh đại diện"
              }}
            </p>
          </div>

          <!-- Full Name -->
          <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">
              Họ và tên <span class="text-red-400">*</span>
            </label>
            <input
              v-model="localProfile.full_name"
              type="text"
              placeholder="Nhập họ và tên"
              class="w-full px-4 py-2.5 rounded-lg bg-gray-800 text-white placeholder-gray-500 border transition-colors"
              :class="
                validationErrors.full_name
                  ? 'border-red-500 focus:border-red-500 focus:ring-red-500'
                  : 'border-gray-700 focus:border-yellow-400 focus:ring-yellow-400'
              "
            />
            <p
              v-if="validationErrors.full_name"
              class="text-xs text-red-400 mt-1"
            >
              {{ validationErrors.full_name }}
            </p>
          </div>

          <!-- Username (Read-only) -->
          <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">
              Tên đăng nhập
            </label>
            <input
              type="text"
              :value="localProfile.username"
              disabled
              class="w-full px-4 py-2.5 rounded-lg bg-gray-800/50 text-gray-500 border border-gray-700 cursor-not-allowed"
            />
            <p class="text-xs text-gray-500 mt-1">
              Tên đăng nhập không thể thay đổi
            </p>
          </div>

          <!-- Email (Read-only) -->
          <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">
              Email
            </label>
            <input
              type="email"
              :value="localProfile.email"
              disabled
              class="w-full px-4 py-2.5 rounded-lg bg-gray-800/50 text-gray-500 border border-gray-700 cursor-not-allowed"
            />
            <p class="text-xs text-gray-500 mt-1">Email không thể thay đổi</p>
          </div>

          <!-- Phone (Optional) -->
          <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">
              Số điện thoại
            </label>
            <input
              v-model="localProfile.phone"
              type="tel"
              placeholder="Nhập số điện thoại (tùy chọn)"
              class="w-full px-4 py-2.5 rounded-lg bg-gray-800 text-white placeholder-gray-500 border transition-colors"
              :class="
                validationErrors.phone
                  ? 'border-red-500 focus:border-red-500 focus:ring-red-500'
                  : 'border-gray-700 focus:border-yellow-400 focus:ring-yellow-400'
              "
            />
            <p v-if="validationErrors.phone" class="text-xs text-red-400 mt-1">
              {{ validationErrors.phone }}
            </p>
            <p v-else class="text-xs text-gray-500 mt-1">10-11 chữ số</p>
          </div>

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

        <!-- Footer (Fixed at bottom) -->
        <div
          class="bg-gray-800/50 px-6 py-4 flex gap-3 flex-shrink-0 border-t border-gray-700"
        >
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
import { ref, computed, watch } from "vue";
import FormInput from "./FormInput.vue";
import LoadingButton from "./LoadingButton.vue";
import { useToast } from "@/composables/useToast";

const toast = useToast();

const props = defineProps({
  modelValue: Boolean,
  user: Object,
});

const emit = defineEmits(["update:modelValue", "save"]);

const localProfile = ref({
  full_name: "",
  username: "",
  email: "",
  phone: "",
  bio: "",
  avatar: "",
});

const previewAvatar = ref("");
const avatarFile = ref(null);
const saving = ref(false);
const validationErrors = ref({});

// Check if user logged in with Google
const isGoogleLogin = computed(() => {
  const storedUser = localStorage.getItem("user");
  if (!storedUser) return false;

  try {
    const userData = JSON.parse(storedUser);
    // Check if user has Firebase UID (Google login) and photoURL starts with googleusercontent
    return (
      (userData.uid &&
        userData.photoURL &&
        userData.photoURL.includes("googleusercontent.com")) ||
      userData.provider === "google" ||
      userData.providerId === "google.com"
    );
  } catch (error) {
    return false;
  }
});

const defaultAvatar = computed(() => {
  const name =
    localProfile.value.full_name || localProfile.value.username || "User";
  return `https://ui-avatars.com/api/?name=${encodeURIComponent(
    name
  )}&background=f59e0b&color=000`;
});

const getAvatarUrl = (avatar) => {
  if (!avatar) return null;

  // If already full URL, return as is
  if (avatar.startsWith("http://") || avatar.startsWith("https://")) {
    return avatar;
  }

  // If relative path, add base URL
  if (avatar.startsWith("/uploads/")) {
    return `http://localhost/HTHREE_film/public${avatar}`;
  }

  return avatar;
};

const handleAvatarChange = (event) => {
  const file = event.target.files[0];
  if (file) {
    // Validate file size (max 2MB)
    if (file.size > 2 * 1024 * 1024) {
      toast.error("Ảnh không được vượt quá 2MB");
      return;
    }

    // Validate file type
    const allowedTypes = ["image/jpeg", "image/jpg", "image/png", "image/webp"];
    if (!allowedTypes.includes(file.type)) {
      toast.error("Chỉ chấp nhận file ảnh (JPG, PNG, WEBP)");
      return;
    }

    avatarFile.value = file;
    const reader = new FileReader();
    reader.onload = (e) => {
      previewAvatar.value = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const closeModal = () => {
  emit("update:modelValue", false);
  previewAvatar.value = "";
  avatarFile.value = null;
  validationErrors.value = {};
};

const validateForm = () => {
  const errors = {};

  // Validate full name
  if (
    !localProfile.value.full_name ||
    localProfile.value.full_name.trim().length < 2
  ) {
    errors.full_name = "Họ và tên phải có ít nhất 2 ký tự";
  }

  // Validate phone (optional but must be valid if provided)
  if (
    localProfile.value.phone &&
    !/^[0-9]{10,11}$/.test(localProfile.value.phone)
  ) {
    errors.phone = "Số điện thoại không hợp lệ (10-11 chữ số)";
  }

  // Validate bio length
  if (localProfile.value.bio && localProfile.value.bio.length > 200) {
    errors.bio = "Giới thiệu không được vượt quá 200 ký tự";
  }

  validationErrors.value = errors;
  return Object.keys(errors).length === 0;
};

const handleSave = async () => {
  // Validate form
  if (!validateForm()) {
    toast.error("Vui lòng kiểm tra lại thông tin");
    return;
  }

  saving.value = true;

  try {
    // Get user ID from localStorage
    const storedUser = localStorage.getItem("user");
    if (!storedUser) {
      throw new Error("Không tìm thấy thông tin user");
    }

    const userData = JSON.parse(storedUser);
    const userId = userData.id || userData.uid;

    if (!userId) {
      throw new Error("Không tìm thấy User ID");
    }

    const API_URL =
      import.meta.env.VITE_API_BASE_URL ||
      "http://localhost/HTHREE_film/backend/api";

    // Get current avatar from localStorage or localProfile
    const currentUserData = JSON.parse(storedUser);
    let avatarUrl = currentUserData.avatar || localProfile.value.avatar;

    // Upload avatar if changed (only for non-Google users)
    if (isGoogleLogin.value && avatarFile.value) {
      toast.error("Không thể thay đổi avatar khi đăng nhập bằng Google");
      saving.value = false;
      return;
    }

    // Upload avatar if changed
    if (avatarFile.value) {
      const formData = new FormData();
      formData.append("avatar", avatarFile.value);
      formData.append("user_id", userId);

      const uploadResponse = await fetch(
        `${API_URL}/upload_avatar_cloudinary.php`,
        {
          method: "POST",
          body: formData,
        }
      );

      const uploadData = await uploadResponse.json();

      if (!uploadData.success) {
        throw new Error(uploadData.message || "Không thể upload ảnh");
      }

      avatarUrl = uploadData.avatar_url;

      // Immediately update localStorage with new avatar
      const tempUser = JSON.parse(storedUser);
      tempUser.avatar = avatarUrl;
      tempUser.photoURL = avatarUrl;
      localStorage.setItem("user", JSON.stringify(tempUser));

      toast.success("Upload ảnh thành công!");
    }

    console.log("💾 Avatar URL to save:", avatarUrl);

    // Update profile via API
    const response = await fetch(`${API_URL}/update_profile.php`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        user_id: userId,
        full_name: localProfile.value.full_name.trim(),
        phone: localProfile.value.phone?.trim() || null,
        bio: localProfile.value.bio?.trim() || null,
      }),
    });

    const data = await response.json();

    if (!data.success) {
      throw new Error(data.message || "Không thể cập nhật thông tin");
    }

    // Update localStorage - get fresh data
    const freshUserData = localStorage.getItem("user");
    if (freshUserData) {
      const currentUser = JSON.parse(freshUserData);

      // Update all fields
      currentUser.full_name = data.user.full_name;
      currentUser.displayName = data.user.full_name;
      currentUser.phone = data.user.phone || null;
      currentUser.bio = data.user.bio || null;

      // Update avatar if we have one
      if (avatarUrl) {
        currentUser.avatar = avatarUrl;
        currentUser.photoURL = avatarUrl;
        console.log("✅ Setting avatar in localStorage:", avatarUrl);
      } else {
        console.log("⚠️ No avatar URL to save");
      }

      // Save back to localStorage
      localStorage.setItem("user", JSON.stringify(currentUser));
      console.log("✅ localStorage updated:", currentUser);
    }

    toast.success("Cập nhật thông tin thành công!");

    // Emit updated user data
    emit("save", {
      ...data.user,
      avatar: avatarUrl,
    });

    closeModal();

    // Force reload to update all UI components
    window.location.reload();
  } catch (error) {
    console.error("Error saving profile:", error);
    toast.error(error.message || "Có lỗi xảy ra khi cập nhật thông tin");
  } finally {
    saving.value = false;
  }
};

// Initialize values when modal opens
watch(
  () => props.modelValue,
  async (newVal) => {
    if (newVal) {
      // Load fresh data from localStorage to get latest avatar
      const storedUser = localStorage.getItem("user");
      const userData = storedUser ? JSON.parse(storedUser) : props.user;

      console.log("📸 Modal opened - User data:", userData);
      console.log("📸 Avatar from localStorage:", userData?.avatar);
      console.log("📸 PhotoURL from localStorage:", userData?.photoURL);

      if (userData) {
        // Fetch latest user data from database to ensure avatar is up-to-date
        try {
          const API_URL =
            import.meta.env.VITE_API_BASE_URL ||
            "http://localhost/HTHREE_film/backend/api";
          const userId = userData.id || userData.uid;

          const response = await fetch(
            `${API_URL}/get_user.php?user_id=${userId}`
          );
          const data = await response.json();

          if (data.success && data.user) {
            // Update localStorage with fresh data
            const updatedUser = {
              ...userData,
              avatar: data.user.avatar || userData.avatar,
              photoURL: data.user.avatar || userData.photoURL,
              full_name: data.user.full_name || userData.full_name,
              phone: data.user.phone || userData.phone,
              bio: data.user.bio || userData.bio,
            };

            localStorage.setItem("user", JSON.stringify(updatedUser));

            console.log("✅ Fresh user data from DB:", data.user);
            console.log("✅ Avatar from DB:", data.user.avatar);

            localProfile.value = {
              full_name: data.user.full_name || userData.displayName || "",
              username: data.user.username || userData.username || "",
              email: data.user.email || userData.email || "",
              phone: data.user.phone || "",
              bio: data.user.bio || "",
              avatar: data.user.avatar || "",
            };
          } else {
            // Fallback to localStorage data
            localProfile.value = {
              full_name: userData.full_name || userData.displayName || "",
              username: userData.username || "",
              email: userData.email || "",
              phone: userData.phone || "",
              bio: userData.bio || "",
              avatar: userData.avatar || userData.photoURL || "",
            };
          }
        } catch (error) {
          console.error("Error fetching user data:", error);
          // Fallback to localStorage data
          localProfile.value = {
            full_name: userData.full_name || userData.displayName || "",
            username: userData.username || "",
            email: userData.email || "",
            phone: userData.phone || "",
            bio: userData.bio || "",
            avatar: userData.avatar || userData.photoURL || "",
          };
        }

        console.log(
          "📸 localProfile.avatar set to:",
          localProfile.value.avatar
        );

        previewAvatar.value = "";
        avatarFile.value = null;
      }
    }
  }
);
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
