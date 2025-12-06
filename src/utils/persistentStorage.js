/**
 * Persistent Storage Utility
 * Sử dụng nhiều phương thức lưu trữ để đảm bảo data không bị mất
 * Priority: IndexedDB > LocalStorage > SessionStorage > Cookies
 */

class PersistentStorage {
  constructor() {
    this.dbName = 'hthree_auth_db';
    this.storeName = 'auth_store';
    this.db = null;
    this.initDB();
  }

  /**
   * Khởi tạo IndexedDB
   */
  async initDB() {
    return new Promise((resolve, reject) => {
      if (!window.indexedDB) {
        console.warn('⚠️ IndexedDB not supported');
        resolve(null);
        return;
      }

      const request = indexedDB.open(this.dbName, 1);

      request.onerror = () => {
        console.error('❌ IndexedDB error:', request.error);
        reject(request.error);
      };

      request.onsuccess = () => {
        this.db = request.result;
        console.log('✅ IndexedDB initialized');
        resolve(this.db);
      };

      request.onupgradeneeded = (event) => {
        const db = event.target.result;
        if (!db.objectStoreNames.contains(this.storeName)) {
          db.createObjectStore(this.storeName);
          console.log('✅ IndexedDB object store created');
        }
      };
    });
  }

  /**
   * Lưu data vào tất cả storage methods
   */
  async setItem(key, value) {
    const stringValue = typeof value === 'string' ? value : JSON.stringify(value);
    const results = {
      indexedDB: false,
      localStorage: false,
      sessionStorage: false,
      cookie: false
    };

    // 1. IndexedDB (most persistent)
    try {
      await this.setIndexedDB(key, stringValue);
      results.indexedDB = true;
      console.log(`✅ Saved to IndexedDB: ${key}`);
    } catch (e) {
      console.warn(`⚠️ IndexedDB save failed: ${e.message}`);
    }

    // 2. LocalStorage
    try {
      localStorage.setItem(key, stringValue);
      results.localStorage = true;
      console.log(`✅ Saved to LocalStorage: ${key}`);
    } catch (e) {
      console.warn(`⚠️ LocalStorage save failed: ${e.message}`);
    }

    // 3. SessionStorage (backup)
    try {
      sessionStorage.setItem(key, stringValue);
      results.sessionStorage = true;
      console.log(`✅ Saved to SessionStorage: ${key}`);
    } catch (e) {
      console.warn(`⚠️ SessionStorage save failed: ${e.message}`);
    }

    // 4. Cookie (last resort)
    try {
      // Only save to cookie if value is not too large (< 4KB)
      if (stringValue.length < 4000) {
        document.cookie = `${key}=${encodeURIComponent(stringValue)}; path=/; max-age=2592000; SameSite=Lax`;
        results.cookie = true;
        console.log(`✅ Saved to Cookie: ${key}`);
      }
    } catch (e) {
      console.warn(`⚠️ Cookie save failed: ${e.message}`);
    }

    console.log(`📊 Save results for "${key}":`, results);
    return results;
  }

  /**
   * Đọc data từ storage (ưu tiên IndexedDB > LocalStorage > SessionStorage > Cookie)
   */
  async getItem(key) {
    let value = null;

    // 1. Try IndexedDB first
    try {
      value = await this.getIndexedDB(key);
      if (value) {
        console.log(`✅ Retrieved from IndexedDB: ${key}`);
        // Sync back to other storages
        this.syncToOtherStorages(key, value);
        return value;
      }
    } catch (e) {
      console.warn(`⚠️ IndexedDB read failed: ${e.message}`);
    }

    // 2. Try LocalStorage
    try {
      value = localStorage.getItem(key);
      if (value) {
        console.log(`✅ Retrieved from LocalStorage: ${key}`);
        // Sync back to IndexedDB
        this.setIndexedDB(key, value);
        return value;
      }
    } catch (e) {
      console.warn(`⚠️ LocalStorage read failed: ${e.message}`);
    }

    // 3. Try SessionStorage
    try {
      value = sessionStorage.getItem(key);
      if (value) {
        console.log(`✅ Retrieved from SessionStorage: ${key}`);
        // Sync back to other storages
        this.syncToOtherStorages(key, value);
        return value;
      }
    } catch (e) {
      console.warn(`⚠️ SessionStorage read failed: ${e.message}`);
    }

    // 4. Try Cookie
    try {
      value = this.getCookie(key);
      if (value) {
        console.log(`✅ Retrieved from Cookie: ${key}`);
        // Sync back to other storages
        this.syncToOtherStorages(key, value);
        return value;
      }
    } catch (e) {
      console.warn(`⚠️ Cookie read failed: ${e.message}`);
    }

    console.log(`❌ No data found for: ${key}`);
    return null;
  }

  /**
   * Xóa data khỏi tất cả storages
   */
  async removeItem(key) {
    // IndexedDB
    try {
      await this.removeIndexedDB(key);
      console.log(`✅ Removed from IndexedDB: ${key}`);
    } catch (e) {
      console.warn(`⚠️ IndexedDB remove failed: ${e.message}`);
    }

    // LocalStorage
    try {
      localStorage.removeItem(key);
      console.log(`✅ Removed from LocalStorage: ${key}`);
    } catch (e) {
      console.warn(`⚠️ LocalStorage remove failed: ${e.message}`);
    }

    // SessionStorage
    try {
      sessionStorage.removeItem(key);
      console.log(`✅ Removed from SessionStorage: ${key}`);
    } catch (e) {
      console.warn(`⚠️ SessionStorage remove failed: ${e.message}`);
    }

    // Cookie
    try {
      document.cookie = `${key}=; path=/; max-age=0`;
      console.log(`✅ Removed from Cookie: ${key}`);
    } catch (e) {
      console.warn(`⚠️ Cookie remove failed: ${e.message}`);
    }
  }

  /**
   * IndexedDB operations
   */
  async setIndexedDB(key, value) {
    if (!this.db) await this.initDB();
    if (!this.db) throw new Error('IndexedDB not available');

    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction([this.storeName], 'readwrite');
      const store = transaction.objectStore(this.storeName);
      const request = store.put(value, key);

      request.onsuccess = () => resolve(true);
      request.onerror = () => reject(request.error);
    });
  }

  async getIndexedDB(key) {
    if (!this.db) await this.initDB();
    if (!this.db) return null;

    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction([this.storeName], 'readonly');
      const store = transaction.objectStore(this.storeName);
      const request = store.get(key);

      request.onsuccess = () => resolve(request.result || null);
      request.onerror = () => reject(request.error);
    });
  }

  async removeIndexedDB(key) {
    if (!this.db) await this.initDB();
    if (!this.db) return;

    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction([this.storeName], 'readwrite');
      const store = transaction.objectStore(this.storeName);
      const request = store.delete(key);

      request.onsuccess = () => resolve(true);
      request.onerror = () => reject(request.error);
    });
  }

  /**
   * Cookie operations
   */
  getCookie(key) {
    const cookies = document.cookie.split(';');
    for (let cookie of cookies) {
      const [cookieKey, cookieValue] = cookie.trim().split('=');
      if (cookieKey === key) {
        return decodeURIComponent(cookieValue);
      }
    }
    return null;
  }

  /**
   * Sync data to other storages
   */
  async syncToOtherStorages(key, value) {
    try {
      await this.setIndexedDB(key, value);
      localStorage.setItem(key, value);
      sessionStorage.setItem(key, value);
    } catch (e) {
      console.warn('⚠️ Sync failed:', e.message);
    }
  }
}

// Export singleton instance
export default new PersistentStorage();
