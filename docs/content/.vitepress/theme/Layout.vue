<script setup>
import DefaultTheme from 'vitepress/theme';
import CustomFooter from './CustomFooter.vue';

const { Layout } = DefaultTheme;
</script>

<template>
  <Layout>
    <template #nav-bar-content-after>
      <a
        href="https://github.com/potagerphp/grape/releases"
        target="_blank"
        rel="noopener noreferrer"
        class="nav-version-badge"
        title="View latest releases on GitHub"
      >
        v0.1.0
      </a>
    </template>

    <template #layout-bottom>
      <CustomFooter />
    </template>
  </Layout>
</template>
