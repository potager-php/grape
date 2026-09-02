import { defineConfig } from 'vitepress';
import { siteConfig } from './site.config';

export default defineConfig({
	base: process.env.DOCS_BASE || (process.env.GITHUB_PAGES ? '/grape/' : '/'),
	title: 'Grape',
	description: siteConfig.tagline,
	appearance: 'dark',
	head: [
		[
			'script',
			{},
			`(()=>{const e=localStorage.getItem("vitepress-theme-appearance");(!e||e==="dark"||e==="auto"&&window.matchMedia("(prefers-color-scheme: dark)").matches)&&document.documentElement.classList.add("dark")})();`
		],
		[
			'style',
			{},
			`html.dark{background-color:#0c0c0f!important;color-scheme:dark;}html:not(.dark){background-color:#ffffff;color-scheme:light;}body{background-color:inherit;}`
		],
		['link', { rel: 'icon', type: 'image/svg+xml', href: '/favicon.svg' }],
		['link', { rel: 'icon', href: 'data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🍇</text></svg>' }],
	],
	markdown: {
		theme: {
			light: 'min-light', // for light mode
			dark: 'min-dark', // for dark mode
		},
	},
	themeConfig: {
		siteConfig,
		siteTitle: `<span class="brand-ns">${siteConfig.brandNamespace}</span><span class="brand-sep">/</span><span class="brand-title">${siteConfig.brandName}</span>`,
		search: {
			provider: 'local',
		},
		nav: [
			{ text: 'Home', link: '/' },
			{ text: 'Getting started', link: '/guide/getting-started' },
			{ text: 'Validators & Rules', link: '/validator/types/string' },
		],
		sidebar: [
			{
				text: 'Guide',
				items: [
					{ text: 'Introduction', link: '/guide/introduction' },
					{ text: 'Getting Started', link: '/guide/getting-started' },
					{ text: 'Custom Messages', link: '/guide/custom-messages' },
					{ text: 'Field Context', link: '/guide/field-context' },
					{ text: 'Error Collector', link: '/guide/error-collectors' },
					{ text: 'Required & Nullable', link: '/guide/required-and-nullable' },
					{ text: 'Validation Helpers', link: '/guide/helpers' },
					{ text: 'Global Configuration', link: '/guide/global-configuration' },
				],
			},
			{
				text: 'Types & Structures',
				items: [
					{
						text: 'Type Validators',
						items: [
							{ text: 'String', link: '/validator/types/string' },
							{ text: 'Number', link: '/validator/types/number' },
							{ text: 'Integer', link: '/validator/types/integer' },
							{ text: 'Float', link: '/validator/types/float' },
							{ text: 'Boolean', link: '/validator/types/boolean' },
							{ text: 'Accepted', link: '/validator/types/accepted' },
							{ text: 'Literal', link: '/validator/types/literal' },
							{ text: 'Null', link: '/validator/types/null' },
							{ text: 'Mixed', link: '/validator/types/mixed' },
						],
					},
					{
						text: 'Array Validators',
						items: [
							{ text: 'Collection', link: '/validator/types/collection' },
							{ text: 'Tuple', link: '/validator/types/tuple' },
							{ text: 'Schema', link: '/validator/types/schema' },
						],
					},
				],
			},
		],
		socialLinks: [{ icon: 'github', link: siteConfig.repoUrl }],
	},
});
