// src/supabaseClient.js
import { createClient } from "@supabase/supabase-js";

const supabaseUrl = import.meta.env.VITE_SUPABASE_URL;
const supabaseKey = import.meta.env.VITE_SUPABASE_KEY;

// Kiểm tra xem Supabase có được cấu hình đúng không
const isValidUrl = (url) => {
  if (!url || url === 'your_supabase_url') return false;
  try {
    new URL(url);
    return true;
  } catch {
    return false;
  }
};

// Chỉ tạo client nếu có đầy đủ thông tin hợp lệ
let supabase = null;

if (!isValidUrl(supabaseUrl) || !supabaseKey || supabaseKey === 'your_supabase_key') {
  console.warn("⚠️ Supabase chưa được cấu hình. Một số tính năng có thể không hoạt động.");
  // Tạo một mock client để tránh lỗi
  supabase = {
    auth: {
      signUp: () => Promise.reject(new Error('Supabase not configured')),
      signInWithPassword: () => Promise.reject(new Error('Supabase not configured')),
      signOut: () => Promise.reject(new Error('Supabase not configured')),
      getSession: () => Promise.resolve({ data: { session: null }, error: null }),
      onAuthStateChange: () => ({ data: { subscription: { unsubscribe: () => {} } } }),
    },
    from: () => ({
      select: () => Promise.resolve({ data: [], error: null }),
      insert: () => Promise.reject(new Error('Supabase not configured')),
      update: () => Promise.reject(new Error('Supabase not configured')),
      delete: () => Promise.reject(new Error('Supabase not configured')),
    })
  };
} else {
  supabase = createClient(supabaseUrl, supabaseKey);
}

export { supabase };
