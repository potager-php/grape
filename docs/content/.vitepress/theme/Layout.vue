<script setup>
import DefaultTheme from 'vitepress/theme';
import { siteConfig } from '../site.config';
import CustomFooter from './CustomFooter.vue';

const { Layout } = DefaultTheme;
</script>

<template>
  <Layout>
    <template #nav-bar-content-after>
      <a
        :href="siteConfig.releasesUrl"
        target="_blank"
        rel="noopener noreferrer"
        class="nav-version-badge"
        title="View latest releases on GitHub"
      >
        {{ siteConfig.version }}
      </a>
    </template>

    <template #layout-bottom>
      <CustomFooter />
    </template>
  </Layout>
</template>
