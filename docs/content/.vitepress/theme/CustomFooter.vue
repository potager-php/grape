<template>
  <footer class="custom-footer" :class="{ 'has-sidebar': hasSidebar }">
    <!-- Atmospheric Gravure Background matching the Hero -->
    <div class="footer-bg" aria-hidden="true"></div>

    <div class="footer-inner">
      <div class="footer-top">
        <div class="footer-brand-col">
          <a href="/" class="footer-brand" aria-label="Grape Documentation Home">
            <span class="brand-ns">potager</span>
            <span class="brand-sep">/</span>
            <span class="brand-name">{{ siteConfig.brandName }}</span>
            <span class="footer-version">{{ siteConfig.version }}</span>
          </a>
          <p class="footer-tagline">
            {{ siteConfig.tagline }}
          </p>
        </div>

        <nav class="footer-links" aria-label="Ecosystem links">
          <a
            :href="siteConfig.repoUrl"
            target="_blank"
            rel="noopener noreferrer"
            class="footer-link-pill"
          >
            <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor">
              <path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"></path>
            </svg>
            <span>GitHub</span>
          </a>

          <a
            :href="siteConfig.packagistUrl"
            target="_blank"
            rel="noopener noreferrer"
            class="footer-link-pill"
          >
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
              <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
              <line x1="12" y1="22.08" x2="12" y2="12"></line>
            </svg>
            <span>Packagist</span>
          </a>

          <a
            :href="siteConfig.licenseUrl"
            target="_blank"
            rel="noopener noreferrer"
            class="footer-link-pill"
          >
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
              <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
            <span>MIT License</span>
          </a>
        </nav>
      </div>

      <div class="footer-bottom">
        <div class="footer-legal">
          <p class="legal-text">
            Released under the MIT License. Copyright &copy; {{ new Date().getFullYear() }} potager-php &amp; contributors.
          </p>
          <p class="legal-subtext">
            Privacy Notice: This static documentation site does not use cookies, collect personal data, or run trackers. Hosted on GitHub Pages.
          </p>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { siteConfig } from '../site.config';
import { useSidebar } from 'vitepress/theme';

const { hasSidebar } = useSidebar();
</script>

<style scoped>
.custom-footer {
  position: relative;
  border-top: 1px solid var(--vp-c-divider);
  background: var(--vp-c-bg);
  padding: 4.5rem 2rem 4rem;
  margin-top: auto;
  overflow: hidden;
  z-index: 10;
}

/* Atmospheric Gravure Background (matching Hero) */
.footer-bg {
  position: absolute;
  inset: -10%;
  width: 120%;
  height: 120%;
  background-image: url('/assets/hero-bg.jpg');
  background-repeat: no-repeat;
  background-size: cover;
  background-position: center 80%;
  opacity: 0.22;
  pointer-events: none;
  z-index: 0;
  filter: grayscale(100%) contrast(115%);
  -webkit-mask-image: linear-gradient(
    to top,
    rgba(0, 0, 0, 0.9) 0%,
    rgba(0, 0, 0, 0.65) 35%,
    rgba(0, 0, 0, 0.35) 65%,
    rgba(0, 0, 0, 0.08) 88%,
    rgba(0, 0, 0, 0) 100%
  );
  mask-image: linear-gradient(
    to top,
    rgba(0, 0, 0, 0.9) 0%,
    rgba(0, 0, 0, 0.65) 35%,
    rgba(0, 0, 0, 0.35) 65%,
    rgba(0, 0, 0, 0.08) 88%,
    rgba(0, 0, 0, 0) 100%
  );
}

html:not(.dark) .footer-bg {
  opacity: 0.14;
}

@media (min-width: 960px) {
  .custom-footer.has-sidebar {
    margin-left: var(--vp-sidebar-width);
    width: calc(100% - var(--vp-sidebar-width));
    padding-left: 2.5rem;
    padding-right: 2.5rem;
  }
}

@media (min-width: 1440px) {
  .custom-footer.has-sidebar {
    margin-left: calc((100vw - var(--vp-layout-max-width)) / 2 + var(--vp-sidebar-width));
    width: calc(100% - ((100vw - var(--vp-layout-max-width)) / 2 + var(--vp-sidebar-width)));
    padding-left: 2.5rem;
    padding-right: calc((100vw - var(--vp-layout-max-width)) / 2 + 2.5rem);
  }
}

.footer-inner {
  max-width: 1180px;
  margin: 0 auto;
  position: relative;
  z-index: 1;
}

.footer-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 2.5rem;
  flex-wrap: wrap;
  padding-bottom: 2.25rem;
  border-bottom: 1px solid var(--vp-c-divider);
}

.footer-brand-col {
  max-width: 440px;
}

.footer-brand {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  font-family: var(--vp-font-family-mono);
  font-size: 1.05rem;
  font-weight: 700;
  letter-spacing: -0.03em;
  color: var(--vp-c-text-1);
  text-decoration: none !important;
  transition: opacity 0.15s ease;
}

.footer-brand:hover {
  opacity: 0.8;
}

.footer-brand .brand-ns {
  color: var(--vp-c-text-3);
  font-weight: 500;
  font-size: 0.9rem;
}

.footer-brand .brand-sep {
  color: var(--vp-c-text-3);
  opacity: 0.5;
  font-size: 0.85rem;
}

.footer-brand .brand-name {
  color: var(--vp-c-text-1);
  font-weight: 700;
}

.footer-brand .footer-version {
  font-family: var(--vp-font-family-mono);
  font-size: 0.6875rem;
  font-weight: 500;
  color: var(--vp-c-text-3);
  background: var(--vp-c-bg-alt);
  border: 1px solid var(--vp-c-border);
  padding: 0.12rem 0.42rem;
  border-radius: 4px;
  margin-left: 0.45rem;
  letter-spacing: 0.02em;
  line-height: 1.2;
}

.footer-tagline {
  margin-top: 0.6rem;
  font-size: 0.85rem;
  color: var(--vp-c-text-3);
  line-height: 1.6;
}

.footer-links {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  flex-wrap: wrap;
}

.footer-link-pill {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.4rem 0.85rem;
  border-radius: 6px;
  background: var(--vp-c-bg-alt);
  border: 1px solid var(--vp-c-border);
  font-family: var(--vp-font-family-mono);
  font-size: 0.8125rem;
  font-weight: 500;
  color: var(--vp-c-text-2);
  text-decoration: none !important;
  transition: all 0.15s ease;
}

.footer-link-pill:hover {
  border-color: var(--vp-c-text-3);
  background: var(--vp-c-bg-soft);
  color: var(--vp-c-text-1);
}

.footer-bottom {
  padding-top: 1.75rem;
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
}

.legal-text {
  font-family: var(--vp-font-family-mono);
  font-size: 0.8rem;
  color: var(--vp-c-text-2);
  margin: 0;
}

.legal-subtext {
  font-family: var(--vp-font-family-mono);
  font-size: 0.725rem;
  color: var(--vp-c-text-3);
  line-height: 1.5;
  margin: 0;
}

@media (max-width: 640px) {
  .footer-top {
    flex-direction: column;
    align-items: flex-start;
    gap: 1.5rem;
  }
}
</style>
