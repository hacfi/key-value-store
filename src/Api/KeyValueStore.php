<?php

/*
 * This file is part of the webmozart/key-value-store package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webmozart\KeyValueStore\Api;

/**
 * A key-value store.
 *
 * Key-value stores support storing values for integer or string keys chosen
 * by the user. Any serializable value can be stored, although an implementation
 * of this interface may further restrict the range of accepted values. See the
 * documentation of the implementation for more information.
 *
 * @since  1.0
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
interface KeyValueStore
{
    /**
     * Sets the value for a key in the store.
     *
     * If the backend of the store cannot be written, a {@link WriteException}
     * is thrown. You should always handle this exception in your code:
     *
     * ```php
     * try {
     *     $store->set($key, $value);
     * } catch (WriteException $e) {
     *     // write failed
     * }
     * ```
     *
     * Any integer or string value is accepted as key. If any other type is
     * passed for the key, an {@link InvalidKeyException} is thrown. You should
     * make sure that you only pass valid keys to the store.
     *
     * The key-value store accepts any serializable value. If a value is not
     * serializable, a {@link SerializationFailedException} is thrown.
     * Additionally, implementations may put further restrictions on their
     * accepted values. If an unsupported value is passed, an
     * {@link UnsupportedValueException} is thrown. Check the documentation of
     * the implementation to learn more about its supported values.
     *
     * @param int|string $key   The key to set.
     * @param mixed      $value The value to set for the key.
     *
     * @throws WriteException If the store cannot be written.
     * @throws InvalidKeyException If the key is not a string or integer.
     * @throws SerializationFailedException If the value cannot be serialized.
     * @throws UnsupportedValueException If the value is not supported by the
     *                                   implementation.
     */
    public function set($key, $value);

    /**
     * Returns the value of a key in the store.
     *
     * If the backend of the store cannot be read, a {@link ReadException}
     * is thrown. You should always handle this exception in your code:
     *
     * ```php
     * try {
     *     $value = $store->get($key);
     * } catch (ReadException $e) {
     *     // read failed
     * }
     * ```
     *
     * If a key does not exist in the store, this method returns `null`. You
     * can pass a manual default value in the second argument:
     *
     * ```php
     * try {
     *     $value = $store->get('host', '127.0.0.1');
     * } catch (ReadException $e) {
     *     // read failed
     * }
     * ```
     *
     * If you want to ensure that a key exists in the store before retrieving
     * it, call {@link has()}:
     *
     * ```php
     * try {
     *     if (!$store->has($key)) {
     *         // fail
     *     }
     *
     *     $value = $store->get($key);
     * } catch (ReadException $e) {
     *     // read failed
     * }
     * ```
     *
     * Any integer or string value is accepted as key. If any other type is
     * passed for the key, an {@link InvalidKeyException} is thrown. You should
     * make sure that you only pass valid keys to the store.
     *
     * @param int|string $key     The key to get.
     * @param mixed      $default The value to return if the key is not set.
     *
     * @return mixed The value of the key or the default value if the key is
     *               not set.
     *
     * @throws ReadException If the store cannot be read.
     * @throws InvalidKeyException If the key is not a string or integer.
     */
    public function get($key, $default = null);

    /**
     * Removes a key from the store.
     *
     * If the store does not contain the key, this method returns `false`.
     *
     * If the backend of the store cannot be written, a {@link WriteException}
     * is thrown. You should always handle this exception in your code:
     *
     * ```php
     * try {
     *     $store->remove($key);
     * } catch (WriteException $e) {
     *     // write failed
     * }
     * ```
     *
     * Any integer or string value is accepted as key. If any other type is
     * passed for the key, an {@link InvalidKeyException} is thrown. You should
     * make sure that you only pass valid keys to the store.
     *
     * @param int|string $key The key to remove.
     *
     * @return bool Returns `true` if a key was removed from the store.
     *
     * @throws WriteException If the store cannot be written.
     * @throws InvalidKeyException If the key is not a string or integer.
     */
    public function remove($key);

    /**
     * Returns whether the store contains a key.
     *
     * If the backend of the store cannot be read, a {@link ReadException}
     * is thrown. You should always handle this exception in your code:
     *
     * ```php
     * try {
     *     if ($store->has($key)) {
     *         // ...
     *     }
     * } catch (ReadException $e) {
     *     // read failed
     * }
     * ```
     *
     * Any integer or string value is accepted as key. If any other type is
     * passed for the key, an {@link InvalidKeyException} is thrown. You should
     * make sure that you only pass valid keys to the store.
     *
     * @param int|string $key The key to test.
     *
     * @return bool Whether the store contains the key.
     *
     * @throws ReadException If the store cannot be read.
     * @throws InvalidKeyException If the key is not a string or integer.
     */
    public function has($key);

    /**
     * Removes all keys from the store.
     *
     * If the backend of the store cannot be written, a {@link WriteException}
     * is thrown. You should always handle this exception in your code:
     *
     * ```php
     * try {
     *     $store->clear();
     * } catch (WriteException $e) {
     *     // write failed
     * }
     * ```
     *
     * @throws WriteException If the store cannot be written.
     */
    public function clear();
}
