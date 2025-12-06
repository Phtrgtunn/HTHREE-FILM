/**
 * Sitemap Generator for HTHREE Film
 * Generates sitemap.xml for SEO
 */

import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const BASE_URL = 'https://hthree.com';

// Static pages
const staticPages = [
  { url: '/', priority: 1.0, changefreq: 'daily' },
  { url: '/home', priority: 1.0, changefreq: 'daily' },
  { url: '/pricing', priority: 0.8, changefreq: 'weekly' },
  { url: '/library', priority: 0.7, changefreq: 'daily' },
  { url: '/cart', priority: 0.6, changefreq: 'weekly' },
  { url: '/account', priority: 0.6, changefreq: 'monthly' },
  { url: '/auth', priority: 0.5, changefreq: 'monthly' }
];

// Movie categories
const categories = [
  'phim-bo',
  'phim-le',
  'hoat-hinh',
  'tv-shows'
];

/**
 * Generate sitemap XML
 */
function generateSitemap() {
  const now = new Date().toISOString();
  
  let xml = '<?xml version="1.0" encoding="UTF-8"?>\n';
  xml += '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n';
  
  // Add static pages
  staticPages.forEach(page => {
    xml += '  <url>\n';
    xml += `    <loc>${BASE_URL}${page.url}</loc>\n`;
    xml += `    <lastmod>${now}</lastmod>\n`;
    xml += `    <changefreq>${page.changefreq}</changefreq>\n`;
    xml += `    <priority>${page.priority}</priority>\n`;
    xml += '  </url>\n';
  });
  
  // Add category pages
  categories.forEach(category => {
    for (let page = 1; page <= 5; page++) {
      xml += '  <url>\n';
      xml += `    <loc>${BASE_URL}/list/${category}/page/${page}</loc>\n`;
      xml += `    <lastmod>${now}</lastmod>\n`;
      xml += `    <changefreq>daily</changefreq>\n`;
      xml += `    <priority>0.8</priority>\n`;
      xml += '  </url>\n';
    }
  });
  
  // Note: Movie detail pages should be added dynamically
  // by fetching from API or database
  // Example:
  // xml += '  <url>\n';
  // xml += `    <loc>${BASE_URL}/film/${movie.slug}</loc>\n`;
  // xml += `    <lastmod>${movie.updated_at}</lastmod>\n`;
  // xml += `    <changefreq>weekly</changefreq>\n`;
  // xml += `    <priority>0.9</priority>\n`;
  // xml += '  </url>\n';
  
  xml += '</urlset>';
  
  return xml;
}

/**
 * Save sitemap to public folder
 */
function saveSitemap() {
  const sitemap = generateSitemap();
  const outputPath = path.join(__dirname, '../public/sitemap.xml');
  
  fs.writeFileSync(outputPath, sitemap, 'utf8');
  console.log('✅ Sitemap generated successfully:', outputPath);
  console.log(`📊 Total URLs: ${staticPages.length + (categories.length * 5)}`);
}

// Run generator
saveSitemap();
