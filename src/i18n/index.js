/**
 * Vue I18n Configuration
 * Hỗ trợ đa ngôn ngữ: Tiếng Việt (vi), English (en)
 */

import { createI18n } from 'vue-i18n';
import vi from './locales/vi.json';
import en from './locales/en.json';

/**
 * Create i18n instance
 */
const i18n = createI18n({
  legacy: false, // Use Composition API
  locale: localStorage.getItem('locale') || 'vi', // Default locale
  fallbackLocale: 'vi',
  messages: {
    vi,
    en
  },
  globalInjection: true,
  missingWarn: false,
  fallbackWarn: false
});

/**
 * Change language
 * @param {string} locale - Language code (vi, en)
 */
export function setLocale(locale) {
  i18n.global.locale.value = locale;
  localStorage.setItem('locale', locale);
  document.documentElement.lang = locale;
}

/**
 * Get current locale
 * @returns {string} Current locale
 */
export function getLocale() {
  return i18n.global.locale.value;
}

/**
 * Get available locales
 * @returns {array} Available locales
 */
export function getAvailableLocales() {
  return [
    { code: 'vi', name: 'Tiếng Việt', flag: '🇻🇳' },
    { code: 'en', name: 'English', flag: '🇺🇸' }
  ];
}

export default i18n;
