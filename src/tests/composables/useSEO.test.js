/**
 * Unit tests for useSEO composable
 */

import { describe, it, expect, beforeEach, vi } from 'vitest';
import { useSEO, generateMovieMeta, generatePageMeta } from '@/composables/useSEO';

// Mock vue-router
vi.mock('vue-router', () => ({
  useRoute: () => ({
    path: '/test'
  })
}));

describe('useSEO', () => {
  beforeEach(() => {
    // Clear document head before each test
    document.head.innerHTML = '';
    document.title = '';
  });

  describe('setTitle', () => {
    it('should set document title', () => {
      const { setTitle } = useSEO();
      setTitle('Test Title');
      expect(document.title).toBe('Test Title');
    });
  });

  describe('setMetaTag', () => {
    it('should create and set meta tag', () => {
      const { setMetaTag } = useSEO();
      setMetaTag('description', 'Test description');
      
      const metaTag = document.querySelector('meta[name="description"]');
      expect(metaTag).toBeTruthy();
      expect(metaTag.getAttribute('content')).toBe('Test description');
    });

    it('should update existing meta tag', () => {
      const { setMetaTag } = useSEO();
      setMetaTag('description', 'First description');
      setMetaTag('description', 'Updated description');
      
      const metaTags = document.querySelectorAll('meta[name="description"]');
      expect(metaTags.length).toBe(1);
      expect(metaTags[0].getAttribute('content')).toBe('Updated description');
    });

    it('should set Open Graph meta tag', () => {
      const { setMetaTag } = useSEO();
      setMetaTag('og:title', 'OG Title', true);
      
      const metaTag = document.querySelector('meta[property="og:title"]');
      expect(metaTag).toBeTruthy();
      expect(metaTag.getAttribute('content')).toBe('OG Title');
    });
  });

  describe('setCanonical', () => {
    it('should create and set canonical link', () => {
      const { setCanonical } = useSEO();
      setCanonical('https://example.com/page');
      
      const link = document.querySelector('link[rel="canonical"]');
      expect(link).toBeTruthy();
      expect(link.getAttribute('href')).toBe('https://example.com/page');
    });
  });

  describe('setStructuredData', () => {
    it('should create and set structured data script', () => {
      const { setStructuredData } = useSEO();
      const data = {
        '@context': 'https://schema.org',
        '@type': 'Movie',
        name: 'Test Movie'
      };
      
      setStructuredData(data);
      
      const script = document.querySelector('script[type="application/ld+json"]');
      expect(script).toBeTruthy();
      expect(JSON.parse(script.textContent)).toEqual(data);
    });
  });
});

describe('generateMovieMeta', () => {
  it('should generate correct movie meta', () => {
    const movie = {
      name: 'Test Movie',
      origin_name: 'Original Name',
      content: 'Movie description',
      quality: 'HD',
      category: [{ name: 'Action' }, { name: 'Drama' }],
      poster_url: 'https://example.com/poster.jpg',
      slug: 'test-movie'
    };

    const meta = generateMovieMeta(movie);

    expect(meta.title).toContain('Test Movie');
    expect(meta.title).toContain('Original Name');
    expect(meta.description).toContain('Movie description');
    expect(meta.keywords).toContain('Action');
    expect(meta.keywords).toContain('Drama');
    expect(meta.image).toBe('https://example.com/poster.jpg');
    expect(meta.url).toContain('test-movie');
    expect(meta.type).toBe('video.movie');
  });
});

describe('generatePageMeta', () => {
  it('should generate home page meta', () => {
    const meta = generatePageMeta('home', { path: '/home' });

    expect(meta.title).toContain('HTHREE Film');
    expect(meta.description).toBeTruthy();
    expect(meta.keywords).toBeTruthy();
    expect(meta.url).toContain('/home');
  });

  it('should generate pricing page meta', () => {
    const meta = generatePageMeta('pricing');

    expect(meta.title).toContain('Bảng Giá');
    expect(meta.description).toBeTruthy();
  });

  it('should generate search page meta with query', () => {
    const meta = generatePageMeta('search', { query: 'action movies', count: 10 });

    expect(meta.title).toContain('action movies');
    expect(meta.description).toContain('10');
  });
});
