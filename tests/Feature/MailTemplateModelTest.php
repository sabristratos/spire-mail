<?php

use SpireMail\Models\MailTemplate;

describe('MailTemplate creation', function () {
    it('creates a template with required fields', function () {
        $template = MailTemplate::create([
            'name' => 'Welcome Email',
            'subject' => 'Welcome to our app!',
        ]);

        expect($template->id)->not->toBeNull();
        expect($template->name)->toBe('Welcome Email');
        expect($template->subject)->toBe('Welcome to our app!');
    });

    it('generates slug from name automatically', function () {
        $template = MailTemplate::create([
            'name' => 'Welcome Email Template',
            'subject' => 'Welcome!',
        ]);

        expect($template->slug)->toBe('welcome-email-template');
    });

    it('uses custom slug if provided', function () {
        $template = MailTemplate::create([
            'name' => 'Welcome Email',
            'slug' => 'custom-slug',
            'subject' => 'Welcome!',
        ]);

        expect($template->slug)->toBe('custom-slug');
    });

    it('sets default content structure', function () {
        $template = MailTemplate::create([
            'name' => 'Test',
            'subject' => 'Test',
        ]);

        expect($template->content)->toBeArray();
        expect($template->content['version'])->toBe('1.0');
        expect($template->content['blocks'])->toBeArray();
    });

    it('sets default settings', function () {
        $template = MailTemplate::create([
            'name' => 'Test',
            'subject' => 'Test',
        ]);

        expect($template->settings)->toBeArray();
        expect($template->settings)->toHaveKeys(['fontFamily', 'backgroundColor', 'contentBackgroundColor', 'contentWidth']);
    });

    it('preserves custom content if provided', function () {
        $customContent = [
            'version' => '2.0',
            'blocks' => [['type' => 'text', 'content' => 'Hello']],
        ];

        $template = MailTemplate::create([
            'name' => 'Test',
            'subject' => 'Test',
            'content' => $customContent,
        ]);

        expect($template->content['version'])->toBe('2.0');
        expect($template->content['blocks'])->toHaveCount(1);
    });
});

describe('MailTemplate scopes', function () {
    it('filters active templates', function () {
        MailTemplate::create(['name' => 'Active', 'subject' => 'Test', 'is_active' => true]);
        MailTemplate::create(['name' => 'Inactive', 'subject' => 'Test', 'is_active' => false]);

        $active = MailTemplate::active()->get();

        expect($active)->toHaveCount(1);
        expect($active->first()->name)->toBe('Active');
    });
});

describe('MailTemplate findBySlugOrFail', function () {
    it('finds template by slug', function () {
        $created = MailTemplate::create([
            'name' => 'Test Template',
            'subject' => 'Test',
        ]);

        $found = MailTemplate::findBySlugOrFail('test-template');

        expect($found->id)->toBe($created->id);
    });

    it('returns same instance if already MailTemplate', function () {
        $template = MailTemplate::create([
            'name' => 'Test',
            'subject' => 'Test',
        ]);

        $result = MailTemplate::findBySlugOrFail($template);

        expect($result)->toBe($template);
    });

    it('throws exception for non-existent slug', function () {
        MailTemplate::findBySlugOrFail('non-existent');
    })->throws(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
});

describe('MailTemplate tags management', function () {
    it('returns empty array when no tags set', function () {
        $template = MailTemplate::create([
            'name' => 'Test',
            'subject' => 'Test',
        ]);

        expect($template->getTags())->toBe([]);
    });

    it('sets and gets tags', function () {
        $template = MailTemplate::create([
            'name' => 'Test',
            'subject' => 'Test',
        ]);

        $template->setTags([
            ['key' => 'user.name', 'label' => 'User Name', 'required' => true],
            ['key' => 'order.id', 'required' => false],
        ]);
        $template->save();

        $tags = $template->fresh()->getTags();

        expect($tags)->toHaveCount(2);
        expect($tags[0]['key'])->toBe('user.name');
        expect($tags[0]['label'])->toBe('User Name');
        expect($tags[0]['required'])->toBeTrue();
        expect($tags[1]['key'])->toBe('order.id');
        expect($tags[1]['label'])->toBe('Order Id');
    });

    it('adds a single tag', function () {
        $template = MailTemplate::create([
            'name' => 'Test',
            'subject' => 'Test',
        ]);

        $template->addTag('user.email', [
            'label' => 'Email Address',
            'required' => true,
        ]);

        expect($template->getTags())->toHaveCount(1);
        expect($template->hasTag('user.email'))->toBeTrue();
    });

    it('removes a tag', function () {
        $template = MailTemplate::create([
            'name' => 'Test',
            'subject' => 'Test',
        ]);

        $template->setTags([
            ['key' => 'name'],
            ['key' => 'email'],
        ]);

        $template->removeTag('name');

        expect($template->getTags())->toHaveCount(1);
        expect($template->hasTag('name'))->toBeFalse();
        expect($template->hasTag('email'))->toBeTrue();
    });

    it('checks if tag exists', function () {
        $template = MailTemplate::create([
            'name' => 'Test',
            'subject' => 'Test',
        ]);

        $template->addTag('test_tag');

        expect($template->hasTag('test_tag'))->toBeTrue();
        expect($template->hasTag('nonexistent'))->toBeFalse();
    });

    it('gets required tag keys', function () {
        $template = MailTemplate::create([
            'name' => 'Test',
            'subject' => 'Test',
        ]);

        $template->setTags([
            ['key' => 'name', 'required' => true],
            ['key' => 'email', 'required' => true],
            ['key' => 'phone', 'required' => false],
        ]);

        $required = $template->getRequiredTagKeys();

        expect($required)->toHaveCount(2);
        expect($required)->toContain('name');
        expect($required)->toContain('email');
        expect($required)->not->toContain('phone');
    });
});

describe('MailTemplate content helpers', function () {
    it('gets blocks from content', function () {
        $template = MailTemplate::create([
            'name' => 'Test',
            'subject' => 'Test',
            'content' => [
                'version' => '1.0',
                'blocks' => [
                    ['type' => 'text', 'content' => 'Hello'],
                    ['type' => 'button', 'text' => 'Click'],
                ],
            ],
        ]);

        expect($template->getBlocks())->toHaveCount(2);
    });

    it('gets version from content', function () {
        $template = MailTemplate::create([
            'name' => 'Test',
            'subject' => 'Test',
            'content' => ['version' => '2.0', 'blocks' => []],
        ]);

        expect($template->getVersion())->toBe('2.0');
    });

    it('returns default version if not set', function () {
        $template = new MailTemplate();
        $template->content = ['blocks' => []];

        expect($template->getVersion())->toBe('1.0');
    });
});

describe('MailTemplate soft deletes', function () {
    it('soft deletes template', function () {
        $template = MailTemplate::create([
            'name' => 'Test',
            'subject' => 'Test',
        ]);

        $template->delete();

        expect(MailTemplate::find($template->id))->toBeNull();
        expect(MailTemplate::withTrashed()->find($template->id))->not->toBeNull();
    });
});
