/**
 * Pinia Store: Quản lý authentication
 */

import { defineStore } from 'pinia';
import { getAuth, onAuthStateChanged } from 'firebase/auth';
import authService from '@/services/authService';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    loading: true,
    error: null
  }),

  getters: {
    /**
     * Kiểm tra user đã đăng nhập chưa
     */
    isAuthenticated: (state) => !!state.user,

    /**
     * Lấy user ID
     */
    userId: (state) => state.user?.id || state.user?.uid || null,

    /**
     * Lấy user email
     */
    userEmail: (state) => state.user?.email || null,

    /**
     * Lấy user name
     */
    userName: (state) => {
      if (!state.user) return null;
      return state.user.full_name || state.user.displayName || state.user.username || 'User';
    },

    /**
     * Lấy user avatar
     */
    userAvatar: (state) => {
      if (!state.user) return null;
      return state.user.avatar || state.user.photoURL || null;
    }
  },

  actions: {
    /**
     * Khởi tạo auth state
     */
    async initAuth() {
      this.loading = true;

      try {
        console.log('🔄 Initializing auth...');
        
        // FIRST: Check localStorage SYNCHRONOUSLY (fastest)
        const syncUser = authService.getCurrentUserSync();
        const syncToken = authService.getTokenSync();
        
        if (syncUser && syncToken) {
          // Set user IMMEDIATELY from localStorage (no await needed)
          this.user = syncUser;
          this.loading = false;
          console.log('⚡ AuthStore - User restored INSTANTLY from localStorage:', syncUser.email);
          
          // Background: Verify with persistent storage (IndexedDB)
          this.verifyPersistentStorage();
          
          return syncUser;
        }
        
        // SECOND: Check persistent storage (IndexedDB/SessionStorage/Cookie)
        const localUser = await authService.getCurrentUser();
        const localToken = await authService.getToken();
        
        console.log('🔍 Checking persistent storage on init:', {
          hasUser: !!localUser,
          hasToken: !!localToken,
          user: localUser
        });
        
        if (localUser && localToken) {
          // Set user from persistent storage
          this.user = localUser;
          console.log('✅ AuthStore - User restored from persistent storage:', localUser.email);
          this.loading = false;
          return localUser;
        }
        
        // THIRD: Check Firebase auth (async)
        const auth = getAuth();
        
        return new Promise((resolve) => {
          onAuthStateChanged(auth, (firebaseUser) => {
            if (firebaseUser) {
              // User đăng nhập bằng Firebase (Google)
              this.user = {
                id: firebaseUser.uid,
                uid: firebaseUser.uid,
                email: firebaseUser.email,
                displayName: firebaseUser.displayName,
                photoURL: firebaseUser.photoURL,
                emailVerified: firebaseUser.emailVerified
              };
              console.log('✅ AuthStore - Firebase user loaded:', firebaseUser.email);
            } else {
              // No Firebase user and no persistent storage user
              this.user = null;
              console.log('❌ AuthStore - No user logged in');
            }
            
            this.loading = false;
            resolve(this.user);
          });
        });
      } catch (error) {
        console.error('❌ Error initializing auth:', error);
        this.error = error.message;
        this.loading = false;
        return null;
      }
    },

    /**
     * Verify persistent storage in background (không block UI)
     */
    async verifyPersistentStorage() {
      try {
        const persistentUser = await authService.getCurrentUser();
        const persistentToken = await authService.getToken();
        
        if (persistentUser && persistentToken) {
          console.log('✅ Persistent storage verified');
        } else {
          console.warn('⚠️ Persistent storage missing, syncing from localStorage...');
          // Sync back to persistent storage
          if (this.user) {
            await authService.login(null, null); // This will trigger save to persistent storage
          }
        }
      } catch (error) {
        console.warn('⚠️ Persistent storage verification failed:', error);
      }
    },

    /**
     * Set user sau khi đăng nhập
     */
    setUser(userData) {
      this.user = userData;
      this.error = null;
    },

    /**
     * Clear user khi đăng xuất
     */
    clearUser() {
      this.user = null;
      this.error = null;
    },

    /**
     * Đăng xuất
     */
    async logout() {
      try {
        // Logout Firebase
        const auth = getAuth();
        await auth.signOut();

        // Logout PHP
        authService.logout();

        // Clear state
        this.clearUser();

        console.log('🔐 AuthStore - User logged out');
      } catch (error) {
        console.error('Error logging out:', error);
        this.error = error.message;
        throw error;
      }
    }
  }
});
