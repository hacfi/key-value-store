<?php

/*
 * This file is part of the webmozart/key-value-store package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webmozart\KeyValueStore\Tests;

use Symfony\Component\Filesystem\Filesystem;
use Webmozart\KeyValueStore\JsonFileStore;

/**
 * @since  1.0
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class JsonFileStoreTest extends AbstractKeyValueStoreTest
{
    private $tempDir;

    protected function setUp()
    {
        while (false === @mkdir($this->tempDir = sys_get_temp_dir().'/webmozart-JsonFileStoreTest'.rand(10000, 99999), 0777, true)) {}

        parent::setUp();
    }

    protected function tearDown()
    {
        parent::tearDown();

        $filesystem = new Filesystem();
        $filesystem->remove($this->tempDir);
    }

    protected function createStore()
    {
        return new JsonFileStore($this->tempDir.'/data.json', false);
    }

    public function provideScalarValues()
    {
        $values = parent::provideScalarValues();
        $values[] = array(JsonFileStore::MAX_FLOAT);

        return $values;
    }

    public function testCreateMissingDirectoriesOnDemand()
    {
        $store = new JsonFileStore($this->tempDir.'/new/data.json', false);
        $store->set('foo', 'bar');

        $this->assertFileExists($this->tempDir.'/new/data.json');
    }

    /**
     * @dataProvider provideScalarValues
     */
    public function testSetSupportsScalarValues($value)
    {
        if (is_float($value) && $value > JsonFileStore::MAX_FLOAT) {
            $this->setExpectedException('\Webmozart\KeyValueStore\Api\UnsupportedValueException');
        }

        parent::testSetSupportsScalarValues($value);
    }

    /**
     * @dataProvider provideBinaryValues
     */
    public function testSetSupportsBinaryValues($value)
    {
        // JSON cannot handle binary data
        $this->setExpectedException('\Webmozart\KeyValueStore\Api\UnsupportedValueException');

        parent::testSetSupportsBinaryValues($value);
    }
}
