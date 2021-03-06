<?php

/*
 * This file is part of the webmozart/key-value-store package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webmozart\KeyValueStore;

use Exception;
use Memcache;
use Memcached;
use RuntimeException;
use Webmozart\KeyValueStore\Api\KeyValueStore;
use Webmozart\KeyValueStore\Api\SerializationFailedException;
use Webmozart\KeyValueStore\Assert\Assert;

/**
 * A key-value store backed by a Memcached instance.
 *
 * @since  1.0
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class MemcachedStore implements KeyValueStore
{
    /**
     * @var Memcache
     */
    private $client;

    /**
     * Creates the store using the given Memcached instance.
     *
     * If no instance is passed, a new instance is created connecting to
     * "127.0.0.1" and the default port 11211.
     *
     * @param Memcached $client The Memcached client.
     */
    public function __construct(Memcached $client = null)
    {
        if (null === $client) {
            $client = new Memcached();
            $client->addServer('127.0.0.1', 11211);
        }

        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        Assert::key($key);

        if (is_resource($value)) {
            throw SerializationFailedException::forValue($value);
        }

        try {
            $serialized = serialize($value);
        } catch (Exception $e) {
            throw SerializationFailedException::forValue($value, $e->getCode(), $e);
        }

        $this->client->set($key, $serialized);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        Assert::key($key);

        if (false === ($serialized = $this->client->get($key))) {
            if (Memcached::RES_NOTFOUND === $this->client->getResultCode()) {
                return $default;
            }

            throw new RuntimeException($this->client->getResultMessage(), $this->client->getResultCode());
        }

        return unserialize($serialized);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key)
    {
        Assert::key($key);

        return $this->client->delete($key);
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        Assert::key($key);

        return false !== $this->client->get($key);
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->client->flush();
    }
}
