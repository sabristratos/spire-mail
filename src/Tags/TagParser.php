<?php

namespace SpireMail\Tags;

class TagParser
{
    /**
     * Pattern for matching tags with optional pipes.
     * Matches: {{key}}, {{key.path}}, {{key|formatter}}, {{key|formatter:arg}}, {{key|default:value}}
     */
    protected string $tagPattern = '/\{\{([a-zA-Z0-9_.]+)(\|[^}]+)?\}\}/';

    /**
     * Pattern for matching conditional blocks.
     * Matches: {{#if condition}}...{{/if}}, {{#unless condition}}...{{/unless}}
     */
    protected string $conditionalPattern = '/\{\{#(if|unless)\s+([a-zA-Z0-9_.]+)\}\}(.*?)(?:\{\{else\}\}(.*?))?\{\{\/\1\}\}/s';

    /**
     * Parse a tag string and extract its components.
     *
     * @return array{key: string, pipes: array<int, array{name: string, argument: string|null}>}
     */
    public function parseTag(string $tag): array
    {
        $tag = trim($tag, '{}');

        $parts = explode('|', $tag);
        $key = array_shift($parts);

        $pipes = [];
        foreach ($parts as $pipe) {
            $pipes[] = $this->parsePipe($pipe);
        }

        return [
            'key' => $key,
            'pipes' => $pipes,
        ];
    }

    /**
     * Parse a single pipe and its argument.
     *
     * @return array{name: string, argument: string|null}
     */
    public function parsePipe(string $pipe): array
    {
        $colonPos = strpos($pipe, ':');

        if ($colonPos === false) {
            return [
                'name' => trim($pipe),
                'argument' => null,
            ];
        }

        return [
            'name' => trim(substr($pipe, 0, $colonPos)),
            'argument' => substr($pipe, $colonPos + 1),
        ];
    }

    /**
     * Extract all tag placeholders from content.
     *
     * @return array<int, array{full: string, key: string, pipes: array<int, array{name: string, argument: string|null}>}>
     */
    public function extractTags(string $content): array
    {
        preg_match_all($this->tagPattern, $content, $matches, PREG_SET_ORDER);

        $tags = [];
        foreach ($matches as $match) {
            $fullTag = $match[0];
            $key = $match[1];
            $pipeString = $match[2] ?? '';

            $pipes = [];
            if ($pipeString) {
                $pipeString = ltrim($pipeString, '|');
                foreach ($this->splitPipes($pipeString) as $pipe) {
                    $pipes[] = $this->parsePipe($pipe);
                }
            }

            $tags[] = [
                'full' => $fullTag,
                'key' => $key,
                'pipes' => $pipes,
            ];
        }

        return $tags;
    }

    /**
     * Extract all unique variable keys from content (without pipes).
     *
     * @return array<int, string>
     */
    public function extractVariableKeys(string $content): array
    {
        $tags = $this->extractTags($content);
        $keys = array_map(fn (array $tag) => $tag['key'], $tags);

        return array_values(array_unique($keys));
    }

    /**
     * Extract all conditional blocks from content.
     *
     * @return array<int, array{full: string, type: string, condition: string, truthy: string, falsy: string|null}>
     */
    public function extractConditionals(string $content): array
    {
        preg_match_all($this->conditionalPattern, $content, $matches, PREG_SET_ORDER);

        $conditionals = [];
        foreach ($matches as $match) {
            $conditionals[] = [
                'full' => $match[0],
                'type' => $match[1],
                'condition' => $match[2],
                'truthy' => $match[3],
                'falsy' => $match[4] ?? null,
            ];
        }

        return $conditionals;
    }

    /**
     * Check if content contains any conditional blocks.
     */
    public function hasConditionals(string $content): bool
    {
        return (bool) preg_match($this->conditionalPattern, $content);
    }

    /**
     * Check if content contains any tags.
     */
    public function hasTags(string $content): bool
    {
        return (bool) preg_match($this->tagPattern, $content);
    }

    /**
     * Split pipes while respecting colons in arguments.
     *
     * @return array<int, string>
     */
    protected function splitPipes(string $pipeString): array
    {
        $pipes = [];
        $current = '';
        $depth = 0;

        for ($i = 0; $i < strlen($pipeString); $i++) {
            $char = $pipeString[$i];

            if ($char === '|' && $depth === 0) {
                if ($current !== '') {
                    $pipes[] = $current;
                }
                $current = '';
            } else {
                $current .= $char;
            }
        }

        if ($current !== '') {
            $pipes[] = $current;
        }

        return $pipes;
    }

    /**
     * Get the tag pattern for external use.
     */
    public function getTagPattern(): string
    {
        return $this->tagPattern;
    }

    /**
     * Get the conditional pattern for external use.
     */
    public function getConditionalPattern(): string
    {
        return $this->conditionalPattern;
    }
}
