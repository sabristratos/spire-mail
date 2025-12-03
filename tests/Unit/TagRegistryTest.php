<?php

use SpireMail\Tags\TagRegistry;

beforeEach(function () {
    $this->registry = new TagRegistry();
});

describe('registerTag', function () {
    it('registers a tag with value only', function () {
        $this->registry->registerTag('company', ['value' => 'Acme Inc']);

        expect($this->registry->hasGlobalTag('company'))->toBeTrue();

        $tag = $this->registry->getGlobalTag('company');
        expect($tag['value'])->toBe('Acme Inc');
        expect($tag['label'])->toBe('Company');
    });

    it('registers a tag with full definition', function () {
        $this->registry->registerTag('support_email', [
            'value' => 'support@test.com',
            'label' => 'Support Email',
            'description' => 'Contact email for support',
            'example' => 'support@example.com',
        ]);

        $tag = $this->registry->getGlobalTag('support_email');

        expect($tag['value'])->toBe('support@test.com');
        expect($tag['label'])->toBe('Support Email');
        expect($tag['description'])->toBe('Contact email for support');
        expect($tag['example'])->toBe('support@example.com');
    });

    it('humanizes key for label if not provided', function () {
        $this->registry->registerTag('user_first_name', ['value' => 'John']);

        $tag = $this->registry->getGlobalTag('user_first_name');
        expect($tag['label'])->toBe('User First Name');
    });

    it('humanizes dot notation keys', function () {
        $this->registry->registerTag('user.email', ['value' => 'test@example.com']);

        $tag = $this->registry->getGlobalTag('user.email');
        expect($tag['label'])->toBe('User Email');
    });
});

describe('registerTags', function () {
    it('registers multiple tags with simple values', function () {
        $this->registry->registerTags([
            'app_name' => 'Test App',
            'version' => '1.0.0',
        ]);

        expect($this->registry->hasGlobalTag('app_name'))->toBeTrue();
        expect($this->registry->hasGlobalTag('version'))->toBeTrue();

        expect($this->registry->getGlobalTag('app_name')['value'])->toBe('Test App');
        expect($this->registry->getGlobalTag('version')['value'])->toBe('1.0.0');
    });

    it('registers multiple tags with full definitions', function () {
        $this->registry->registerTags([
            'app_name' => [
                'value' => 'My App',
                'label' => 'Application Name',
            ],
            'year' => [
                'value' => '2024',
                'description' => 'Current year',
            ],
        ]);

        expect($this->registry->getGlobalTag('app_name')['label'])->toBe('Application Name');
        expect($this->registry->getGlobalTag('year')['description'])->toBe('Current year');
    });
});

describe('hasGlobalTag', function () {
    it('returns true for registered tag', function () {
        $this->registry->registerTag('test', ['value' => 'value']);

        expect($this->registry->hasGlobalTag('test'))->toBeTrue();
    });

    it('returns false for unregistered tag', function () {
        expect($this->registry->hasGlobalTag('nonexistent'))->toBeFalse();
    });
});

describe('getGlobalTag', function () {
    it('returns null for unregistered tag', function () {
        expect($this->registry->getGlobalTag('nonexistent'))->toBeNull();
    });
});

describe('resolveGlobalTagValues', function () {
    it('merges global tags into data array', function () {
        $this->registry->registerTags([
            'app_name' => 'Test App',
            'year' => '2024',
        ]);

        $data = $this->registry->resolveGlobalTagValues([
            'user' => 'John',
        ]);

        expect($data['user'])->toBe('John');
        expect($data['app_name'])->toBe('Test App');
        expect($data['year'])->toBe('2024');
    });

    it('does not override existing data keys', function () {
        $this->registry->registerTag('name', ['value' => 'Default Name']);

        $data = $this->registry->resolveGlobalTagValues([
            'name' => 'Custom Name',
        ]);

        expect($data['name'])->toBe('Custom Name');
    });

    it('resolves callable values', function () {
        $this->registry->registerTag('timestamp', [
            'value' => fn () => '2024-01-15',
        ]);

        $data = $this->registry->resolveGlobalTagValues([]);

        expect($data['timestamp'])->toBe('2024-01-15');
    });
});

describe('getGlobalTagsForEditor', function () {
    it('formats tags for editor UI', function () {
        $this->registry->registerTags([
            'custom_tag' => [
                'value' => 'My Value',
                'label' => 'Custom Tag',
                'description' => 'A custom tag',
            ],
        ]);

        $tags = $this->registry->getGlobalTagsForEditor();
        $customTag = collect($tags)->firstWhere('key', 'custom_tag');

        expect($customTag)->not->toBeNull();
        expect($customTag['key'])->toBe('custom_tag');
        expect($customTag['label'])->toBe('Custom Tag');
        expect($customTag['description'])->toBe('A custom tag');
        expect($customTag['global'])->toBeTrue();
    });

    it('uses resolved value as example when example not provided', function () {
        $this->registry->registerTag('year_custom', ['value' => '2025']);

        $tags = $this->registry->getGlobalTagsForEditor();
        $yearTag = collect($tags)->firstWhere('key', 'year_custom');

        expect($yearTag['example'])->toBe('2025');
    });
});
