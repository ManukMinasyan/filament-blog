<?php

declare(strict_types=1);

namespace Relaticle\Ink\Support;

final class SchemaExtractor
{
    /**
     * Extract FAQ Question/Answer entities from rendered HTML containing
     * an `## FAQ` H2 followed by `### Question?` / `<p>answer</p>` pairs.
     *
     * @return list<array{'@type':string,name:string,acceptedAnswer:array{'@type':string,text:string}}>
     */
    public static function extractFaqEntities(string $html): array
    {
        $section = self::findSectionByHeading($html, 'FAQ');

        if ($section === null) {
            return [];
        }

        preg_match_all(
            '/<h3[^>]*>(.+?)<\/h3>\s*<p[^>]*>(.+?)<\/p>/is',
            $section,
            $pairs,
            PREG_SET_ORDER,
        );

        $entities = [];

        foreach ($pairs as $pair) {
            $name = self::cleanText($pair[1]);
            $text = self::cleanText($pair[2]);

            if ($name === '' || $text === '') {
                continue;
            }

            $entities[] = [
                '@type' => 'Question',
                'name' => $name,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $text,
                ],
            ];
        }

        return $entities;
    }

    /**
     * Extract HowTo step entities from rendered HTML containing a `## Steps`
     * H2 followed by H3 step headings, each followed by one or more paragraphs.
     *
     * @return list<array{'@type':string,name:string,text:string,position:int}>
     */
    public static function extractHowToSteps(string $html): array
    {
        $section = self::findSectionByHeading($html, 'Steps');

        if ($section === null) {
            return [];
        }

        preg_match_all(
            '/<h3[^>]*>(.+?)<\/h3>\s*<p[^>]*>(.+?)<\/p>/is',
            $section,
            $pairs,
            PREG_SET_ORDER,
        );

        $steps = [];

        foreach ($pairs as $index => $pair) {
            $name = self::cleanText($pair[1]);
            $text = self::cleanText($pair[2]);

            if ($name === '' || $text === '') {
                continue;
            }

            $steps[] = [
                '@type' => 'HowToStep',
                'position' => $index + 1,
                'name' => $name,
                'text' => $text,
            ];
        }

        return $steps;
    }

    private static function findSectionByHeading(string $html, string $heading): ?string
    {
        if (! preg_match_all(
            '/<h2\b[^>]*>(?<heading>.*?)<\/h2>(?<body>.*?)(?=<h2\b|\z)/is',
            $html,
            $matches,
            PREG_SET_ORDER,
        )) {
            return null;
        }

        foreach ($matches as $match) {
            $headingText = self::cleanText($match['heading']);

            if (strcasecmp($headingText, $heading) === 0) {
                return $match['body'];
            }
        }

        return null;
    }

    private static function cleanText(string $html): string
    {
        $html = preg_replace('/<a[^>]+class="[^"]*heading-permalink[^"]*"[^>]*>.*?<\/a>/is', '', $html) ?? $html;

        return trim(html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5));
    }
}
