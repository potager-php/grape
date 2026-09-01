import { defineConfig } from 'vitepress';

export default defineConfig({
	title: 'Grape',
	description: 'A PHP Validation Package',
	markdown: {
		theme: {
			light: 'min-light', // for light mode
			dark: 'min-dark', // for dark mode
		},
	},
	themeConfig: {
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
		socialLinks: [{ icon: 'github', link: 'https://github.com/vuejs/vitepress' }],
	},
});
