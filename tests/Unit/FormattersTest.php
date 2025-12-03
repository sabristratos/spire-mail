<?php

use SpireMail\Tags\Formatters\CapitalizeFormatter;
use SpireMail\Tags\Formatters\CountFormatter;
use SpireMail\Tags\Formatters\CurrencyFormatter;
use SpireMail\Tags\Formatters\DateFormatter;
use SpireMail\Tags\Formatters\DefaultFormatter;
use SpireMail\Tags\Formatters\FormatterRegistry;
use SpireMail\Tags\Formatters\LowercaseFormatter;
use SpireMail\Tags\Formatters\NumberFormatter;
use SpireMail\Tags\Formatters\TruncateFormatter;
use SpireMail\Tags\Formatters\UppercaseFormatter;

describe('FormatterRegistry', function () {
    beforeEach(function () {
        $this->registry = new FormatterRegistry();
    });

    it('has default formatters registered', function () {
        $names = $this->registry->getNames();

        expect($names)->toContain('default');
        expect($names)->toContain('date');
        expect($names)->toContain('currency');
        expect($names)->toContain('uppercase');
        expect($names)->toContain('lowercase');
        expect($names)->toContain('capitalize');
        expect($names)->toContain('truncate');
        expect($names)->toContain('count');
        expect($names)->toContain('number');
    });

    it('can check if formatter exists', function () {
        expect($this->registry->has('uppercase'))->toBeTrue();
        expect($this->registry->has('nonexistent'))->toBeFalse();
    });

    it('returns empty string for unknown formatter with non-scalar value', function () {
        $result = $this->registry->apply('unknown', ['array']);

        expect($result)->toBe('');
    });

    it('returns string value for unknown formatter with scalar value', function () {
        $result = $this->registry->apply('unknown', 'test');

        expect($result)->toBe('test');
    });
});

describe('UppercaseFormatter', function () {
    beforeEach(function () {
        $this->formatter = new UppercaseFormatter();
    });

    it('has correct name', function () {
        expect($this->formatter->getName())->toBe('uppercase');
    });

    it('converts string to uppercase', function () {
        expect($this->formatter->format('hello world', null))->toBe('HELLO WORLD');
    });

    it('handles empty string', function () {
        expect($this->formatter->format('', null))->toBe('');
    });

    it('handles null value', function () {
        expect($this->formatter->format(null, null))->toBe('');
    });
});

describe('LowercaseFormatter', function () {
    beforeEach(function () {
        $this->formatter = new LowercaseFormatter();
    });

    it('has correct name', function () {
        expect($this->formatter->getName())->toBe('lowercase');
    });

    it('converts string to lowercase', function () {
        expect($this->formatter->format('HELLO WORLD', null))->toBe('hello world');
    });
});

describe('CapitalizeFormatter', function () {
    beforeEach(function () {
        $this->formatter = new CapitalizeFormatter();
    });

    it('has correct name', function () {
        expect($this->formatter->getName())->toBe('capitalize');
    });

    it('capitalizes each word', function () {
        expect($this->formatter->format('hello world', null))->toBe('Hello World');
    });
});

describe('TruncateFormatter', function () {
    beforeEach(function () {
        $this->formatter = new TruncateFormatter();
    });

    it('has correct name', function () {
        expect($this->formatter->getName())->toBe('truncate');
    });

    it('truncates string to specified length', function () {
        $result = $this->formatter->format('Hello World', '5');

        expect($result)->toBe('Hello...');
    });

    it('does not truncate if string is shorter than limit', function () {
        $result = $this->formatter->format('Hi', '10');

        expect($result)->toBe('Hi');
    });

    it('uses default limit of 100', function () {
        $longString = str_repeat('a', 150);
        $result = $this->formatter->format($longString, null);

        expect(strlen($result))->toBe(103);
    });
});

describe('CountFormatter', function () {
    beforeEach(function () {
        $this->formatter = new CountFormatter();
    });

    it('has correct name', function () {
        expect($this->formatter->getName())->toBe('count');
    });

    it('counts array elements', function () {
        expect($this->formatter->format([1, 2, 3], null))->toBe('3');
    });

    it('counts string length', function () {
        expect($this->formatter->format('hello', null))->toBe('5');
    });

    it('returns 0 for null', function () {
        expect($this->formatter->format(null, null))->toBe('0');
    });
});

describe('NumberFormatter', function () {
    beforeEach(function () {
        $this->formatter = new NumberFormatter();
    });

    it('has correct name', function () {
        expect($this->formatter->getName())->toBe('number');
    });

    it('formats number with default decimals (0)', function () {
        expect($this->formatter->format(1234.5678, null))->toBe('1,235');
    });

    it('formats number with specified decimals', function () {
        expect($this->formatter->format(1234.5, '0'))->toBe('1,235');
        expect($this->formatter->format(1234.5, '2'))->toBe('1,234.50');
        expect($this->formatter->format(1234.5, '3'))->toBe('1,234.500');
    });
});

describe('DateFormatter', function () {
    beforeEach(function () {
        $this->formatter = new DateFormatter();
    });

    it('has correct name', function () {
        expect($this->formatter->getName())->toBe('date');
    });

    it('formats date with default format (Y-m-d)', function () {
        $result = $this->formatter->format('2024-01-15', null);

        expect($result)->toBe('2024-01-15');
    });

    it('formats date with custom format', function () {
        $result = $this->formatter->format('2024-01-15', 'Y/m/d');

        expect($result)->toBe('2024/01/15');
    });

    it('handles DateTime object', function () {
        $date = new DateTime('2024-01-15');
        $result = $this->formatter->format($date, 'd-m-Y');

        expect($result)->toBe('15-01-2024');
    });

    it('returns original string for invalid date', function () {
        expect($this->formatter->format('not-a-date', null))->toBe('not-a-date');
    });
});

describe('CurrencyFormatter', function () {
    beforeEach(function () {
        $this->formatter = new CurrencyFormatter();
    });

    it('has correct name', function () {
        expect($this->formatter->getName())->toBe('currency');
    });

    it('formats with USD by default', function () {
        $result = $this->formatter->format(99.99, null);

        expect($result)->toContain('99');
        expect($result)->toMatch('/[$\x{0024}]/u');
    });

    it('formats with EUR', function () {
        $result = $this->formatter->format(99.99, 'EUR');

        expect($result)->toContain('99');
    });

    it('formats with GBP', function () {
        $result = $this->formatter->format(99.99, 'GBP');

        expect($result)->toContain('99');
    });

    it('returns empty string for null', function () {
        expect($this->formatter->format(null, null))->toBe('');
    });

    it('returns empty string for empty string', function () {
        expect($this->formatter->format('', null))->toBe('');
    });
});

describe('DefaultFormatter', function () {
    beforeEach(function () {
        $this->formatter = new DefaultFormatter();
    });

    it('has correct name', function () {
        expect($this->formatter->getName())->toBe('default');
    });

    it('returns default value when value is null', function () {
        expect($this->formatter->format(null, 'fallback'))->toBe('fallback');
    });

    it('returns default value when value is empty string', function () {
        expect($this->formatter->format('', 'fallback'))->toBe('fallback');
    });

    it('returns original value when not empty', function () {
        expect($this->formatter->format('hello', 'fallback'))->toBe('hello');
    });

    it('returns 0 as valid value', function () {
        expect($this->formatter->format(0, 'fallback'))->toBe('0');
    });
});
