/**
 * Single Source of Truth (SSOT) for Grape Documentation & Project Metadata.
 * Update this file when releasing a new version or changing repository details.
 */
export interface SiteConfig {
	name: string;
	brandNamespace: string;
	brandName: string;
	packageName: string;
	version: string;
	tagline: string;
	repoUrl: string;
	packagistUrl: string;
	licenseUrl: string;
	releasesUrl: string;
	docsUrl: string;
	copyright: string;
}

export const siteConfig: SiteConfig = {
	name: 'potager / grape',
	brandNamespace: 'potager',
	brandName: 'grape',
	packageName: 'potagerphp/grape',
	version: 'v0.1.1',
	tagline: 'Type-safe schema validation and data sanitization for PHP 8.2+.',
	repoUrl: 'https://github.com/potager-php/grape',
	packagistUrl: 'https://packagist.org/packages/potagerphp/grape',
	licenseUrl: 'https://github.com/potager-php/grape/blob/main/LICENSE',
	releasesUrl: 'https://github.com/potager-php/grape/releases',
	docsUrl: 'https://potager-php.github.io/grape/',
	copyright: 'potager-php & contributors',
};
