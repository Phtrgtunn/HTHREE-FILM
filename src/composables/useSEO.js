import { watch, onMounted } from 'vue';
import { useRoute } from 'vue-router';

/**
 * Composable để quản lý SEO meta tags
 * Hỗ trợ: title, description, keywords, Open Graph, Twitter Card
 */
export function useSEO(options = {}) {
  const route = useRoute();
  
  const defaultMeta = {
    title: 'HTHREE Film - Xem Phim Online Miễn Phí',
    description: 'Xem phim online chất lượng cao, phim bộ, phim lẻ, anime, TV shows. Cập nhật liên tục phim mới nhất.',
    keywords: 'xem phim, phim online, phim bo, phim le, anime, tv shows, phim moi',
    image: 'https://hthree.com/og-image.jpg',
    url: 'https://hthree.com',
    type: 'website',
    siteName: 'HTHREE Film'
  };

  const meta = { ...defaultMeta, ...options };

  /**
   * Set document title
   */
  const setTitle = (title) => {
    document.title = title;
  };

  /**
   * Set meta tag
   */
  const setMetaTag = (name, content, isProperty = false) => {
    if (!content) return;
    
    const attribute = isProperty ? 'property' : 'name';
    let element = document.querySelector(`meta[${attribute}="${name}"]`);
    
    if (!element) {
      element = document.createElement('meta');
      element.setAttribute(attribute, name);
      document.head.appendChild(element);
    }
    
    element.setAttribute('content', content);
  };

  /**
   * Set canonical URL
   */
  const setCanonical = (url) => {
    let link = document.querySelector('link[rel="canonical"]');
    
    if (!link) {
      link = document.createElement('link');
      link.setAttribute('rel', 'canonical');
      document.head.appendChild(link);
    }
    
    link.setAttribute('href', url);
  };

  /**
   * Update all meta tags
   */
  const updateMeta = () => {
    // Title
    setTitle(meta.title);
    
    // Basic meta tags
    setMetaTag('description', meta.description);
    setMetaTag('keywords', meta.keywords);
    
    // Open Graph
    setMetaTag('og:title', meta.title, true);
    setMetaTag('og:description', meta.description, true);
    setMetaTag('og:image', meta.image, true);
    setMetaTag('og:url', meta.url, true);
    setMetaTag('og:type', meta.type, true);
    setMetaTag('og:site_name', meta.siteName, true);
    
    // Twitter Card
    setMetaTag('twitter:card', 'summary_large_image');
    setMetaTag('twitter:title', meta.title);
    setMetaTag('twitter:description', meta.description);
    setMetaTag('twitter:image', meta.image);
    
    // Canonical URL
    setCanonical(meta.url);
  };

  /**
   * Generate structured data (JSON-LD)
   */
  const setStructuredData = (data) => {
    let script = document.querySelector('script[type="application/ld+json"]');
    
    if (!script) {
      script = document.createElement('script');
      script.setAttribute('type', 'application/ld+json');
      document.head.appendChild(script);
    }
    
    script.textContent = JSON.stringify(data);
  };

  /**
   * Movie structured data
   */
  const setMovieStructuredData = (movie) => {
    const structuredData = {
      '@context': 'https://schema.org',
      '@type': 'Movie',
      name: movie.name,
      alternateName: movie.origin_name,
      description: movie.content,
      image: movie.poster_url || movie.thumb_url,
      datePublished: movie.year,
      genre: movie.category?.map(c => c.name).join(', '),
      contentRating: movie.quality,
      inLanguage: movie.lang,
      aggregateRating: movie.rating ? {
        '@type': 'AggregateRating',
        ratingValue: movie.rating,
        bestRating: '10',
        worstRating: '1'
      } : undefined
    };
    
    setStructuredData(structuredData);
  };

  /**
   * Website structured data
   */
  const setWebsiteStructuredData = () => {
    const structuredData = {
      '@context': 'https://schema.org',
      '@type': 'WebSite',
      name: 'HTHREE Film',
      url: 'https://hthree.com',
      potentialAction: {
        '@type': 'SearchAction',
        target: 'https://hthree.com/search?q={search_term_string}',
        'query-input': 'required name=search_term_string'
      }
    };
    
    setStructuredData(structuredData);
  };

  // Update meta on mount
  onMounted(() => {
    updateMeta();
  });

  // Watch for route changes
  watch(() => route.path, () => {
    updateMeta();
  });

  return {
    setTitle,
    setMetaTag,
    setCanonical,
    updateMeta,
    setStructuredData,
    setMovieStructuredData,
    setWebsiteStructuredData
  };
}

/**
 * Helper function to generate movie meta
 */
export function generateMovieMeta(movie) {
  return {
    title: `${movie.name} - ${movie.origin_name} | HTHREE Film`,
    description: movie.content?.substring(0, 160) || `Xem phim ${movie.name} (${movie.origin_name}) chất lượng ${movie.quality} vietsub miễn phí tại HTHREE Film.`,
    keywords: `${movie.name}, ${movie.origin_name}, xem phim, ${movie.category?.map(c => c.name).join(', ')}`,
    image: movie.poster_url || movie.thumb_url,
    url: `https://hthree.com/film/${movie.slug}`,
    type: 'video.movie'
  };
}

/**
 * Helper function to generate page meta
 */
export function generatePageMeta(page, customData = {}) {
  const pageMeta = {
    home: {
      title: 'HTHREE Film - Xem Phim Online Miễn Phí, Phim Bộ, Phim Lẻ, Anime',
      description: 'Xem phim online chất lượng cao HD, phim bộ, phim lẻ, anime, TV shows. Cập nhật liên tục phim mới nhất 2024.',
      keywords: 'xem phim online, phim bo, phim le, anime, tv shows, phim moi 2024'
    },
    pricing: {
      title: 'Bảng Giá Gói Dịch Vụ - HTHREE Film',
      description: 'Chọn gói dịch vụ phù hợp với bạn. Xem phim không giới hạn, không quảng cáo, chất lượng HD/FHD.',
      keywords: 'gia goi dich vu, goi vip, xem phim khong quang cao'
    },
    library: {
      title: 'Thư Viện Của Tôi - HTHREE Film',
      description: 'Quản lý phim yêu thích, danh sách xem sau và lịch sử xem của bạn.',
      keywords: 'thu vien phim, phim yeu thich, lich su xem'
    },
    search: {
      title: `Tìm kiếm: ${customData.query || ''} - HTHREE Film`,
      description: `Kết quả tìm kiếm cho "${customData.query || ''}". Tìm thấy ${customData.count || 0} phim.`,
      keywords: `tim kiem phim, ${customData.query || ''}`
    }
  };

  return {
    ...pageMeta[page],
    url: `https://hthree.com${customData.path || ''}`,
    type: 'website'
  };
}
