export default defineAppConfig({
    docus: {
        title: 'Filament Blog',
        description: 'Headless blog package for Filament with SEO, MCP tools, and publishable components.',
    },
    seo: {
        title: 'Filament Blog',
        description: 'Headless blog package for Filament with SEO, MCP tools, and publishable components.',
    },
    github: {
        repo: 'filament-blog',
        owner: 'ManukMinasyan',
        edit: true,
        rootDir: 'docs'
    },
    ui: {
        colors: {
            primary: 'violet',
            neutral: 'zinc'
        }
    },
    toc: {
        title: 'On this page',
        bottom: {
            title: 'Ecosystem',
            edit: 'https://github.com/ManukMinasyan/filament-blog',
            links: [
                {
                    icon: 'i-simple-icons-laravel',
                    label: 'Relaticle CRM',
                    to: 'https://relaticle.com',
                    target: '_blank'
                },
                {
                    icon: 'i-lucide-move',
                    label: 'Flowforge',
                    to: 'https://relaticle.github.io/flowforge',
                    target: '_blank'
                }
            ]
        }
    }
})
