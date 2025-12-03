<?php

use SpireMail\Tags\TagParser;

beforeEach(function () {
    $this->parser = new TagParser();
});

describe('parseTag', function () {
    it('parses simple tag', function () {
        $result = $this->parser->parseTag('{{name}}');

        expect($result['key'])->toBe('name');
        expect($result['pipes'])->toBeEmpty();
    });

    it('parses tag with dot notation', function () {
        $result = $this->parser->parseTag('{{user.name}}');

        expect($result['key'])->toBe('user.name');
        expect($result['pipes'])->toBeEmpty();
    });

    it('parses tag with single pipe', function () {
        $result = $this->parser->parseTag('{{name|uppercase}}');

        expect($result['key'])->toBe('name');
        expect($result['pipes'])->toHaveCount(1);
        expect($result['pipes'][0]['name'])->toBe('uppercase');
        expect($result['pipes'][0]['argument'])->toBeNull();
    });

    it('parses tag with pipe and argument', function () {
        $result = $this->parser->parseTag('{{date|date:Y-m-d}}');

        expect($result['key'])->toBe('date');
        expect($result['pipes'])->toHaveCount(1);
        expect($result['pipes'][0]['name'])->toBe('date');
        expect($result['pipes'][0]['argument'])->toBe('Y-m-d');
    });

    it('parses tag with multiple pipes', function () {
        $result = $this->parser->parseTag('{{name|default:Guest|uppercase}}');

        expect($result['key'])->toBe('name');
        expect($result['pipes'])->toHaveCount(2);
        expect($result['pipes'][0]['name'])->toBe('default');
        expect($result['pipes'][0]['argument'])->toBe('Guest');
        expect($result['pipes'][1]['name'])->toBe('uppercase');
    });
});

describe('parsePipe', function () {
    it('parses pipe without argument', function () {
        $result = $this->parser->parsePipe('uppercase');

        expect($result['name'])->toBe('uppercase');
        expect($result['argument'])->toBeNull();
    });

    it('parses pipe with argument', function () {
        $result = $this->parser->parsePipe('date:d/m/Y');

        expect($result['name'])->toBe('date');
        expect($result['argument'])->toBe('d/m/Y');
    });

    it('parses pipe with colon in argument', function () {
        $result = $this->parser->parsePipe('default:10:30:00');

        expect($result['name'])->toBe('default');
        expect($result['argument'])->toBe('10:30:00');
    });
});

describe('extractTags', function () {
    it('extracts single tag from content', function () {
        $content = 'Hello {{name}}!';
        $tags = $this->parser->extractTags($content);

        expect($tags)->toHaveCount(1);
        expect($tags[0]['full'])->toBe('{{name}}');
        expect($tags[0]['key'])->toBe('name');
    });

    it('extracts multiple tags from content', function () {
        $content = 'Hello {{user.name}}, your order {{order.id}} is ready.';
        $tags = $this->parser->extractTags($content);

        expect($tags)->toHaveCount(2);
        expect($tags[0]['key'])->toBe('user.name');
        expect($tags[1]['key'])->toBe('order.id');
    });

    it('extracts tags with pipes', function () {
        $content = 'Hello {{name|uppercase}}, total: {{amount|currency:USD}}';
        $tags = $this->parser->extractTags($content);

        expect($tags)->toHaveCount(2);
        expect($tags[0]['pipes'][0]['name'])->toBe('uppercase');
        expect($tags[1]['pipes'][0]['name'])->toBe('currency');
        expect($tags[1]['pipes'][0]['argument'])->toBe('USD');
    });

    it('returns empty array for content without tags', function () {
        $content = 'Hello world!';
        $tags = $this->parser->extractTags($content);

        expect($tags)->toBeEmpty();
    });
});

describe('extractVariableKeys', function () {
    it('returns unique variable keys', function () {
        $content = '{{name}} and {{name}} and {{email}}';
        $keys = $this->parser->extractVariableKeys($content);

        expect($keys)->toHaveCount(2);
        expect($keys)->toContain('name');
        expect($keys)->toContain('email');
    });
});

describe('extractConditionals', function () {
    it('extracts if conditional', function () {
        $content = '{{#if premium}}Premium user{{/if}}';
        $conditionals = $this->parser->extractConditionals($content);

        expect($conditionals)->toHaveCount(1);
        expect($conditionals[0]['type'])->toBe('if');
        expect($conditionals[0]['condition'])->toBe('premium');
        expect($conditionals[0]['truthy'])->toBe('Premium user');
        expect($conditionals[0]['falsy'])->toBeNull();
    });

    it('extracts if-else conditional', function () {
        $content = '{{#if premium}}Premium{{else}}Basic{{/if}}';
        $conditionals = $this->parser->extractConditionals($content);

        expect($conditionals)->toHaveCount(1);
        expect($conditionals[0]['truthy'])->toBe('Premium');
        expect($conditionals[0]['falsy'])->toBe('Basic');
    });

    it('extracts unless conditional', function () {
        $content = '{{#unless verified}}Please verify{{/unless}}';
        $conditionals = $this->parser->extractConditionals($content);

        expect($conditionals)->toHaveCount(1);
        expect($conditionals[0]['type'])->toBe('unless');
        expect($conditionals[0]['condition'])->toBe('verified');
    });
});

describe('hasTags', function () {
    it('returns true when content has tags', function () {
        expect($this->parser->hasTags('Hello {{name}}'))->toBeTrue();
    });

    it('returns false when content has no tags', function () {
        expect($this->parser->hasTags('Hello world'))->toBeFalse();
    });
});

describe('hasConditionals', function () {
    it('returns true when content has conditionals', function () {
        expect($this->parser->hasConditionals('{{#if test}}yes{{/if}}'))->toBeTrue();
    });

    it('returns false when content has no conditionals', function () {
        expect($this->parser->hasConditionals('Hello {{name}}'))->toBeFalse();
    });
});
