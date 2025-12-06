/**
 * Unit tests for useAnalytics composable
 */

import { describe, it, expect, beforeEach, vi } from 'vitest';
import { useAnalytics } from '@/composables/useAnalytics';

describe('useAnalytics', () => {
  beforeEach(() => {
    // Mock gtag
    window.gtag = vi.fn();
    window.dataLayer = [];
  });

  describe('trackPageView', () => {
    it('should track page view with correct parameters', () => {
      const { trackPageView } = useAnalytics();
      trackPageView('/home', 'Home Page');

      expect(window.gtag).toHaveBeenCalledWith('event', 'page_view', {
        page_path: '/home',
        page_title: 'Home Page',
        page_location: expect.any(String)
      });
    });
  });

  describe('trackEvent', () => {
    it('should track custom event', () => {
      const { trackEvent } = useAnalytics();
      trackEvent('button_click', { button_name: 'play' });

      expect(window.gtag).toHaveBeenCalledWith('event', 'button_click', {
        button_name: 'play'
      });
    });
  });

  describe('trackMoviePlay', () => {
    it('should track movie play event', () => {
      const { trackMoviePlay } = useAnalytics();
      const movie = {
        id: '123',
        name: 'Test Movie',
        type: 'single',
        quality: 'HD'
      };

      trackMoviePlay(movie);

      expect(window.gtag).toHaveBeenCalledWith('event', 'play_movie', {
        movie_id: '123',
        movie_title: 'Test Movie',
        movie_type: 'single',
        movie_quality: 'HD'
      });
    });
  });

  describe('trackSearch', () => {
    it('should track search event', () => {
      const { trackSearch } = useAnalytics();
      trackSearch('action movies', 15);

      expect(window.gtag).toHaveBeenCalledWith('event', 'search', {
        search_term: 'action movies',
        results_count: 15
      });
    });
  });

  describe('trackAddToCart', () => {
    it('should track add to cart event', () => {
      const { trackAddToCart } = useAnalytics();
      const item = {
        id: 'premium-plan',
        name: 'Premium Plan',
        price: 99000,
        quantity: 1
      };

      trackAddToCart(item);

      expect(window.gtag).toHaveBeenCalledWith('event', 'add_to_cart', {
        item_id: 'premium-plan',
        item_name: 'Premium Plan',
        price: 99000,
        quantity: 1
      });
    });
  });

  describe('trackPurchase', () => {
    it('should track purchase event', () => {
      const { trackPurchase } = useAnalytics();
      const transaction = {
        id: 'order-123',
        total: 99000,
        items: [{ id: 'premium-plan', name: 'Premium Plan' }]
      };

      trackPurchase(transaction);

      expect(window.gtag).toHaveBeenCalledWith('event', 'purchase', {
        transaction_id: 'order-123',
        value: 99000,
        currency: 'VND',
        items: transaction.items
      });
    });
  });

  describe('trackSignup', () => {
    it('should track signup event', () => {
      const { trackSignup } = useAnalytics();
      trackSignup('google');

      expect(window.gtag).toHaveBeenCalledWith('event', 'sign_up', {
        method: 'google'
      });
    });
  });

  describe('trackLogin', () => {
    it('should track login event', () => {
      const { trackLogin } = useAnalytics();
      trackLogin('email');

      expect(window.gtag).toHaveBeenCalledWith('event', 'login', {
        method: 'email'
      });
    });
  });
});
