<?php

namespace SpireMail\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;
use SpireMail\Support\BlockRegistry;

class ValidBlockStructure implements ValidationRule
{
    protected const MAX_NESTING_DEPTH = 3;

    /**
     * Block types that can contain nested blocks.
     *
     * @var array<string, string>
     */
    protected const NESTED_BLOCK_PATHS = [
        'row' => 'columns.*.blocks',
    ];

    /**
     * Required props for specific block types.
     *
     * @var array<string, array<int, string>>
     */
    protected const REQUIRED_PROPS = [
        'button' => ['text', 'href'],
        'image' => ['src'],
    ];

    public function __construct(protected BlockRegistry $registry) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_array($value)) {
            $fail(__('spire-mail::validation.blocks_must_be_array'));

            return;
        }

        foreach ($value as $index => $block) {
            $this->validateBlockRecursive($block, $index, $fail, 0, "blocks[{$index}]");
        }
    }

    protected function validateBlockRecursive(
        mixed $block,
        int $index,
        Closure $fail,
        int $depth,
        string $path
    ): void {
        if ($depth > self::MAX_NESTING_DEPTH) {
            $fail(__('spire-mail::validation.max_nesting_exceeded', [
                'path' => $path,
                'max' => self::MAX_NESTING_DEPTH,
            ]));

            return;
        }

        if (! is_array($block)) {
            $fail(__('spire-mail::validation.block_must_be_array', ['path' => $path]));

            return;
        }

        if (! isset($block['id']) || ! is_string($block['id'])) {
            $fail(__('spire-mail::validation.block_invalid_id', ['path' => $path]));

            return;
        }

        if (! Str::isUuid($block['id'])) {
            $fail(__('spire-mail::validation.block_invalid_uuid', ['path' => $path]));

            return;
        }

        if (! isset($block['type']) || ! is_string($block['type'])) {
            $fail(__('spire-mail::validation.block_invalid_type', ['path' => $path]));

            return;
        }

        $type = $block['type'];

        if (! $this->registry->hasRenderer($type)) {
            $fail(__('spire-mail::validation.block_type_not_registered', [
                'type' => $type,
                'path' => $path,
            ]));

            return;
        }

        if (! isset($block['props']) || ! is_array($block['props'])) {
            $fail(__('spire-mail::validation.block_invalid_props', ['path' => $path]));

            return;
        }

        $this->validateRequiredProps($block, $fail, $path);

        $this->validateNestedBlocks($block, $fail, $depth, $path);
    }

    protected function validateRequiredProps(array $block, Closure $fail, string $path): void
    {
        $type = $block['type'];
        $props = $block['props'];

        if (! isset(self::REQUIRED_PROPS[$type])) {
            return;
        }

        foreach (self::REQUIRED_PROPS[$type] as $requiredProp) {
            if (! isset($props[$requiredProp]) || $props[$requiredProp] === '') {
                $fail(__('spire-mail::validation.block_missing_required_prop', [
                    'prop' => $requiredProp,
                    'type' => $type,
                    'path' => $path,
                ]));
            }
        }
    }

    protected function validateNestedBlocks(array $block, Closure $fail, int $depth, string $path): void
    {
        $type = $block['type'];
        $props = $block['props'];

        if ($type === 'row' && isset($props['columns']) && is_array($props['columns'])) {
            foreach ($props['columns'] as $colIndex => $column) {
                if (! is_array($column)) {
                    continue;
                }

                $nestedBlocks = $column['blocks'] ?? [];
                if (! is_array($nestedBlocks)) {
                    continue;
                }

                foreach ($nestedBlocks as $nestedIndex => $nestedBlock) {
                    $nestedPath = "{$path}.props.columns[{$colIndex}].blocks[{$nestedIndex}]";
                    $this->validateBlockRecursive($nestedBlock, $nestedIndex, $fail, $depth + 1, $nestedPath);
                }
            }
        }
    }
}
