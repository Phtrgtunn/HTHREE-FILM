# ✅ Deployment Checklist - HTHREE Film

## 📋 Pre-Deployment Checklist

### 1. Code Quality

- [ ] All tests pass (`npm run test`)
- [ ] No console errors in browser
- [ ] No TypeScript/ESLint errors
- [ ] Code reviewed and approved
- [ ] Git commits are clean and descriptive

### 2. Build & Performance

- [ ] Production build successful (`npm run build`)
- [ ] Bundle size optimized (< 500KB gzipped)
- [ ] Images optimized (WebP, lazy loading)
- [ ] Code splitting implemented
- [ ] API caching working correctly

### 3. SEO Configuration

- [ ] Meta tags configured for all pages
- [ ] Open Graph tags present
- [ ] Twitter Card tags present
- [ ] Canonical URLs set
- [ ] Structured data (JSON-LD) implemented
- [ ] Sitemap generated (`npm run generate:sitemap`)
- [ ] robots.txt configured
- [ ] Test with [Facebook Debugger](https://developers.facebook.com/tools/debug/)
- [ ] Test with [Twitter Card Validator](https://cards-dev.twitter.com/validator)

### 4. Analytics Setup

- [ ] Google Analytics 4 configured
- [ ] GA4 Measurement ID added to `.env`
- [ ] Page view tracking working
- [ ] Event tracking working
- [ ] E-commerce tracking configured (if applicable)
- [ ] Error tracking setup
- [ ] Test in GA4 DebugView

### 5. Internationalization

- [ ] All UI text translated (vi, en)
- [ ] Language switcher working
- [ ] Locale persistence working
- [ ] Date/time formatting correct
- [ ] Currency formatting correct (if applicable)

### 6. Security

- [ ] Environment variables secured
- [ ] API keys not exposed in client code
- [ ] HTTPS enabled
- [ ] CORS configured correctly
- [ ] Input sanitization implemented
- [ ] XSS protection enabled
- [ ] CSRF protection enabled (backend)

### 7. Accessibility

- [ ] Keyboard navigation working
- [ ] Focus states visible
- [ ] ARIA labels present
- [ ] Alt text for images
- [ ] Color contrast meets WCAG 2.1 AA
- [ ] Screen reader tested
- [ ] Test with [WAVE](https://wave.webaim.org/)

### 8. Browser Compatibility

- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)
- [ ] Mobile Safari (iOS)
- [ ] Chrome Mobile (Android)

### 9. Responsive Design

- [ ] Mobile (320px - 767px)
- [ ] Tablet (768px - 1023px)
- [ ] Desktop (1024px+)
- [ ] Large Desktop (1920px+)
- [ ] Touch interactions working

### 10. Features Testing

- [ ] User authentication (login/logout)
- [ ] Movie search working
- [ ] Movie detail page loading
- [ ] Video player working
- [ ] Comments system working
- [ ] Cart functionality (if applicable)
- [ ] Payment integration (if applicable)
- [ ] Library management working
- [ ] Bulk actions working
- [ ] Keyboard shortcuts working
- [ ] Command palette working

### 11. Error Handling

- [ ] 404 page configured
- [ ] 500 error page configured
- [ ] Network error handling
- [ ] API error handling
- [ ] Form validation working
- [ ] Error messages user-friendly
- [ ] Offline banner working

### 12. Performance Metrics

- [ ] Lighthouse score > 90
- [ ] First Contentful Paint < 1.5s
- [ ] Time to Interactive < 3s
- [ ] Cumulative Layout Shift < 0.1
- [ ] Largest Contentful Paint < 2.5s

---

## 🚀 Deployment Steps

### Step 1: Final Testing

```bash
# Run all tests
npm run test

# Build for production
npm run build

# Test production build locally
npm run preview
```

### Step 2: Environment Variables

Create `.env.production`:

```env
VITE_SUPABASE_URL=your_production_url
VITE_SUPABASE_KEY=your_production_key
VITE_FIREBASE_API_KEY=your_production_key
VITE_GA4_MEASUREMENT_ID=G-XXXXXXXXXX
```

### Step 3: Generate SEO Files

```bash
# Generate sitemap
npm run generate:sitemap

# Verify robots.txt exists
ls public/robots.txt
```

### Step 4: Deploy to Vercel

#### Option A: Vercel CLI

```bash
# Install Vercel CLI
npm i -g vercel

# Login
vercel login

# Deploy
vercel --prod
```

#### Option B: GitHub Integration

1. Push code to GitHub
2. Connect repository to Vercel
3. Configure environment variables
4. Deploy automatically

### Step 5: Post-Deployment Verification

#### Check URLs:

- [ ] Homepage loads: `https://yourdomain.com`
- [ ] Movie detail: `https://yourdomain.com/film/movie-slug`
- [ ] Search: `https://yourdomain.com/search?q=test`
- [ ] Sitemap: `https://yourdomain.com/sitemap.xml`
- [ ] Robots: `https://yourdomain.com/robots.txt`

#### Check Analytics:

- [ ] Open GA4 Real-time view
- [ ] Visit homepage
- [ ] Verify page view tracked
- [ ] Click movie, verify event tracked

#### Check SEO:

- [ ] View page source, verify meta tags
- [ ] Test Open Graph: [Facebook Debugger](https://developers.facebook.com/tools/debug/)
- [ ] Test Twitter Card: [Twitter Validator](https://cards-dev.twitter.com/validator)
- [ ] Submit sitemap to Google Search Console

---

## 📊 Monitoring Setup

### Google Search Console

1. Add property: `https://yourdomain.com`
2. Verify ownership
3. Submit sitemap: `https://yourdomain.com/sitemap.xml`
4. Monitor coverage and performance

### Google Analytics 4

1. Verify tracking working
2. Set up custom events
3. Create conversion goals
4. Set up custom reports

### Error Monitoring (Optional)

1. Setup Sentry account
2. Add Sentry DSN to `.env`
3. Initialize Sentry in `main.js`
4. Test error tracking

---

## 🔄 Post-Deployment Tasks

### Immediate (Day 1)

- [ ] Monitor error logs
- [ ] Check analytics data
- [ ] Test all critical features
- [ ] Monitor server performance
- [ ] Check user feedback

### Short-term (Week 1)

- [ ] Review analytics reports
- [ ] Fix any reported bugs
- [ ] Optimize based on metrics
- [ ] Update documentation
- [ ] Plan next features

### Long-term (Month 1)

- [ ] Review SEO performance
- [ ] Analyze user behavior
- [ ] A/B testing results
- [ ] Performance optimization
- [ ] Feature usage analysis

---

## 🆘 Rollback Plan

If deployment fails:

### Option 1: Vercel Rollback

```bash
# List deployments
vercel ls

# Rollback to previous
vercel rollback [deployment-url]
```

### Option 2: Git Revert

```bash
# Revert last commit
git revert HEAD

# Push to trigger redeploy
git push origin main
```

### Option 3: Manual Rollback

1. Go to Vercel Dashboard
2. Select project
3. Go to Deployments
4. Click "..." on previous deployment
5. Click "Promote to Production"

---

## 📞 Support Contacts

### Technical Issues

- Developer: [your-email@example.com]
- DevOps: [devops@example.com]

### Service Providers

- Vercel Support: https://vercel.com/support
- Firebase Support: https://firebase.google.com/support
- Supabase Support: https://supabase.com/support

---

## 📝 Deployment Log

### Deployment #1

- **Date**: **_/_**/**\_\_**
- **Version**: v1.0.0
- **Deployed by**: ****\_\_\_****
- **Status**: ⬜ Success ⬜ Failed
- **Notes**: ********\_********

### Deployment #2

- **Date**: **_/_**/**\_\_**
- **Version**: v1.1.0
- **Deployed by**: ****\_\_\_****
- **Status**: ⬜ Success ⬜ Failed
- **Notes**: ********\_********

---

**Good luck with your deployment! 🚀**
