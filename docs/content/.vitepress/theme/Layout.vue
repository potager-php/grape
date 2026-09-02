<script setup>
import DefaultTheme from 'vitepress/theme';
import { useData } from 'vitepress';
import { siteConfig } from '../site.config';
import CustomFooter from './CustomFooter.vue';

const { Layout } = DefaultTheme;
const { frontmatter } = useData();
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

    <template #doc-after>
      <CustomFooter />
    </template>

    <template #layout-bottom>
      <CustomFooter v-if="frontmatter.layout === 'page' || frontmatter.layout === 'home'" />
    </template>
  </Layout>
</template>
