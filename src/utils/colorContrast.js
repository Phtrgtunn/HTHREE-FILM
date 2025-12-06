/**
 * Color Contrast Utilities
 * Giúp đảm bảo accessibility bằng cách kiểm tra contrast ratio
 * Theo WCAG 2.1 guidelines
 */

/**
 * Convert hex color to RGB
 * @param {string} hex - Hex color (e.g., '#f59e0b')
 * @returns {object} RGB values
 */
export function hexToRgb(hex) {
  const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
  return result ? {
    r: parseInt(result[1], 16),
    g: parseInt(result[2], 16),
    b: parseInt(result[3], 16)
  } : null;
}

/**
 * Calculate relative luminance
 * @param {object} rgb - RGB values
 * @returns {number} Relative luminance
 */
export function getLuminance(rgb) {
  const { r, g, b } = rgb;
  
  const [rs, gs, bs] = [r, g, b].map(c => {
    c = c / 255;
    return c <= 0.03928 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4);
  });
  
  return 0.2126 * rs + 0.7152 * gs + 0.0722 * bs;
}

/**
 * Calculate contrast ratio between two colors
 * @param {string} color1 - First color (hex)
 * @param {string} color2 - Second color (hex)
 * @returns {number} Contrast ratio
 */
export function getContrastRatio(color1, color2) {
  const rgb1 = hexToRgb(color1);
  const rgb2 = hexToRgb(color2);
  
  if (!rgb1 || !rgb2) return 0;
  
  const lum1 = getLuminance(rgb1);
  const lum2 = getLuminance(rgb2);
  
  const lighter = Math.max(lum1, lum2);
  const darker = Math.min(lum1, lum2);
  
  return (lighter + 0.05) / (darker + 0.05);
}

/**
 * Check if contrast ratio meets WCAG AA standard
 * @param {string} foreground - Foreground color (hex)
 * @param {string} background - Background color (hex)
 * @param {string} level - 'AA' or 'AAA'
 * @param {boolean} isLargeText - Is text large (18pt+ or 14pt+ bold)
 * @returns {boolean} Meets standard
 */
export function meetsWCAG(foreground, background, level = 'AA', isLargeText = false) {
  const ratio = getContrastRatio(foreground, background);
  
  if (level === 'AAA') {
    return isLargeText ? ratio >= 4.5 : ratio >= 7;
  }
  
  // AA standard
  return isLargeText ? ratio >= 3 : ratio >= 4.5;
}

/**
 * Get recommended text color (black or white) for a background
 * @param {string} backgroundColor - Background color (hex)
 * @returns {string} Recommended text color
 */
export function getRecommendedTextColor(backgroundColor) {
  const whiteRatio = getContrastRatio('#ffffff', backgroundColor);
  const blackRatio = getContrastRatio('#000000', backgroundColor);
  
  return whiteRatio > blackRatio ? '#ffffff' : '#000000';
}

/**
 * Validate color palette for accessibility
 * @param {object} palette - Color palette object
 * @returns {object} Validation results
 */
export function validateColorPalette(palette) {
  const results = {
    valid: true,
    warnings: [],
    errors: []
  };
  
  // Check primary text on background
  if (palette.text && palette.background) {
    const ratio = getContrastRatio(palette.text, palette.background);
    if (ratio < 4.5) {
      results.valid = false;
      results.errors.push({
        type: 'text-background',
        ratio: ratio.toFixed(2),
        message: `Text contrast ratio (${ratio.toFixed(2)}) is below WCAG AA standard (4.5)`
      });
    } else if (ratio < 7) {
      results.warnings.push({
        type: 'text-background',
        ratio: ratio.toFixed(2),
        message: `Text contrast ratio (${ratio.toFixed(2)}) meets AA but not AAA standard`
      });
    }
  }
  
  // Check accent color on background
  if (palette.accent && palette.background) {
    const ratio = getContrastRatio(palette.accent, palette.background);
    if (ratio < 3) {
      results.warnings.push({
        type: 'accent-background',
        ratio: ratio.toFixed(2),
        message: `Accent color contrast (${ratio.toFixed(2)}) may be too low for UI elements`
      });
    }
  }
  
  return results;
}

/**
 * Common color palette for HTHREE
 */
export const HTHREE_COLORS = {
  background: '#000000',
  text: '#ffffff',
  accent: '#f59e0b', // Yellow
  secondary: '#1f2937', // Gray-800
  error: '#ef4444', // Red-500
  success: '#10b981', // Green-500
  warning: '#f59e0b', // Yellow-500
  info: '#3b82f6' // Blue-500
};

/**
 * Validate HTHREE color palette
 */
export function validateHTHREEPalette() {
  return validateColorPalette(HTHREE_COLORS);
}

export default {
  hexToRgb,
  getLuminance,
  getContrastRatio,
  meetsWCAG,
  getRecommendedTextColor,
  validateColorPalette,
  validateHTHREEPalette,
  HTHREE_COLORS
};
