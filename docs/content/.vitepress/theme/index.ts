import DefaultTheme from 'vitepress/theme';
import Layout from './Layout.vue';
import CustomHome from './CustomHome.vue';
import './custom.css';

export default {
	extends: DefaultTheme,
	Layout,
	enhanceApp({ app }) {
		app.component('CustomHome', CustomHome);
	}
};
