/**
 * Unit tests for API cache utility
 */

import { describe, it, expect, beforeEach, vi } from 'vitest';
import { getCachedData, setCachedData, clearCache, clearExpiredCache } from '@/utils/apiCache';

describe('apiCache', () => {
  beforeEach(() => {
    clearCache();
    vi.useFakeTimers();
  });

  afterEach(() => {
    vi.useRealTimers();
  });

  describe('setCachedData', () => {
    it('should cache data with default TTL', () => {
      const data = { movies: ['Movie 1', 'Movie 2'] };
      setCachedData('test-key', data);

      const cached = getCachedData('test-key');
      expect(cached).toEqual(data);
    });

    it('should cache data with custom TTL', () => {
      const data = { movies: ['Movie 1'] };
      setCachedData('test-key', data, 1000); // 1 second

      const cached = getCachedData('test-key');
      expect(cached).toEqual(data);
    });
  });

  describe('getCachedData', () => {
    it('should return cached data if not expired', () => {
      const data = { movies: ['Movie 1'] };
      setCachedData('test-key', data, 5000);

      vi.advanceTimersByTime(3000); // 3 seconds

      const cached = getCachedData('test-key');
      expect(cached).toEqual(data);
    });

    it('should return null if data expired', () => {
      const data = { movies: ['Movie 1'] };
      setCachedData('test-key', data, 1000); // 1 second

      vi.advanceTimersByTime(2000); // 2 seconds

      const cached = getCachedData('test-key');
      expect(cached).toBeNull();
    });

    it('should return null if key does not exist', () => {
      const cached = getCachedData('non-existent-key');
      expect(cached).toBeNull();
    });
  });

  describe('clearCache', () => {
    it('should clear all cached data', () => {
      setCachedData('key1', { data: 1 });
      setCachedData('key2', { data: 2 });

      clearCache();

      expect(getCachedData('key1')).toBeNull();
      expect(getCachedData('key2')).toBeNull();
    });
  });

  describe('clearExpiredCache', () => {
    it('should only clear expired entries', () => {
      setCachedData('key1', { data: 1 }, 1000); // 1 second
      setCachedData('key2', { data: 2 }, 5000); // 5 seconds

      vi.advanceTimersByTime(2000); // 2 seconds

      clearExpiredCache();

      expect(getCachedData('key1')).toBeNull();
      expect(getCachedData('key2')).toEqual({ data: 2 });
    });
  });
});
