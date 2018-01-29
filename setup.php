<?php
/**
 * Define the package settings
 */
$package = [
    'name' => 'vendor/package',
    'title' => 'My Package',
    'description' => 'This is my package, description.',
    'type' => 'library',
    'keywords' => [
        'example', 'project', 'boilerplate', 'package'
    ],
    'homepage' => 'http://github.com/vendor/package',
    'authors' => [
        [
            'name' => 'Your Name',
            'email' => 'your-email@example.com',
            'homepage' => 'http://github.com/vendor',
            'role' => 'Owner'
        ]
    ],
    'autoload' => [
        'psr-4' => [
            'Vendor\\Package\\' => 'src',
        ]
    ],
    'autoload-dev' => [
        'psr-4' => [
            'Vendor\\Package\\Tests\\' => 'tests',
        ]
    ]
];

### Dont change anything below this point ###

/**
 * Define directory constants
 */
define('SOURCE_DIR', __DIR__.'/setup');
define('TARGET_DIR', __DIR__);

/**
 * Create src directory
 */
if (!file_exists(TARGET_DIR.'/src')) {
    mkdir(TARGET_DIR.'/src', 0755, true);
}

/**
 * Create [tests|fixtures] directory
 */
if (!file_exists(TARGET_DIR.'/tests/fixtures')) {
    mkdir(TARGET_DIR.'/tests/fixtures', 0755, true);
}

/**
 * Replace placeholders in files
 */
function process_file($filename, $replace) {
    file_put_contents(
        TARGET_DIR.'/'.$filename, 
        preg_replace_callback("/{{([\w_]{1,})}}/", function ($match) use ($replace) {
            return array_key_exists($match[1], $replace) ? $replace[$match[1]] : '';
        }, file_get_contents(SOURCE_DIR.'/'.$filename))
    );
}

/**
 * Move unchanged files
 */
foreach ([
    '.gitignore',
    '.scrutinizer.yml',
    '.styleci.yml',
    '.travis.yml',
    'LICENSE',
    'CONTRIBUTING.md',
    'phpunit.xml',
    'tests/bootstrap.php',
] as $file) {
    if (file_exists(SOURCE_DIR.'/'.$file)) {
        copy(SOURCE_DIR.'/'.$file, TARGET_DIR.'/'.$file);
    }
}

/**
 * Process/Create files which change
 */
 
// README.md
$authors = null;
foreach ($package['authors'] as $author) {
    $authors = ' - '.sprintf('[%s](%s)', $author['name'], $author['homepage']).PHP_EOL;
}
process_file('README.md', [
    'name' => $package['name'],
    'title' => $package['title'],
    'description' => $package['description'],
    'authors' => $authors
]);

// composer.json
file_put_contents(
    TARGET_DIR.'/composer.json',
    json_encode([
        'name' => $package['name'],
        'type' => 'library',
        'description' => $package['description'],
        'license' => 'MIT',
        'keywords' => $package['keywords'],
        'homepage' => $package['homepage'],
        'authors' => $package['authors'],
        'require' => [
            'php' => '~5.6|~7.0'
        ],
        'require-dev' => [
            'phpunit/phpunit' => '4.*',
        ],
        'autoload' => $package['autoload'],
        'autoload-dev' => $package['autoload-dev'],
        'scripts' => [
            'test' => 'phpunit --configuration phpunit.xml --coverage-text',
        ],
        'minimum-stability' => 'stable'
    ], JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT)
);

// unit test
$namespace = rtrim(array_search('src', $package['autoload']['psr-4']), '\\');
$testName = str_replace(' ', null, ucwords(str_replace('\\', ' ', $namespace))).'Test';

file_put_contents(TARGET_DIR.'/tests/'.$testName.'.php', '<?php

namespace '.$namespace.';

use PHPUnit\Framework\TestCase;

class '.$testName.' extends TestCase
{

    /**
     *
     */
    public function setUp()
    {
        
    }
    
    /**
     * @coversNothing
     */
    public function testTrueIsTrue()
    {
        $this->assertTrue(true);
    }
    
}'.PHP_EOL);

# done
echo 'Done, you can now remove the setup.php and the ./setup/ folder.'.PHP_EOL;
echo 'Happy coding!'.PHP_EOL;
