const baseURL = process.env.NUXT_APP_BASE_URL || '/'

export default defineNuxtConfig({
    extends: 'docus',
    modules: ['@nuxt/image'],
    devtools: { enabled: true },
    site: {
        name: 'Filament Blog',
    },
    appConfig: {
        docus: {
            url: `https://manukminasyan.github.io${baseURL}`,
            header: {
                logo: false,
            },
        },
    },
    app: {
        baseURL,
        buildAssetsDir: 'assets',
        head: {
            link: [
                {
                    rel: 'icon',
                    type: 'image/x-icon',
                    href: baseURL + 'favicon.ico',
                },
            ],
        },
    },
    image: {
        provider: 'none',
    },
    content: {
        build: {
            markdown: {
                highlight: {
                    langs: ['php', 'blade', 'bash', 'json'],
                },
            },
        },
    },
    llms: {
        domain: `https://manukminasyan.github.io${baseURL.replace(/\/$/, '')}`,
    },
    nitro: {
        preset: 'github_pages',
    },
})
