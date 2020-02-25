<?php
/**
 * Defines the \TraderInteractive\Util\Arrays class.
 */

namespace TraderInteractive\Util;

use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Class of static array utility functions.
 */
final class Arrays
{
    /**
     * Const for lower cased array keys.
     *
     * @const integer
     */
    const CASE_LOWER = 1;

    /**
     * Const for upper cased array keys.
     *
     * @const integer
     */
    const CASE_UPPER = 2;

    /**
     * Const for camel caps cased array keys.
     *
     * @const integer
     */
    const CASE_CAMEL_CAPS = 4;

    /**
     * Const for underscored cased array keys.
     *
     * @const integer
     */
    const CASE_UNDERSCORE = 8;

    /**
     * Simply returns an array value if the key exist or null if it does not.
     *
     * @param array $array the array to be searched
     * @param string|integer $key the key to search for
     * @param mixed $default the value to return if the $key is not found in $array
     *
     * @return mixed array value or given default value
     */
    public static function get(array $array, $key, $default = null)
    {
        return array_key_exists($key, $array) ? $array[$key] : $default;
    }

    /**
     * Simply returns an array value if the key isset,4 $default if it is not
     *
     * @param array $array the array to be searched
     * @param string|integer $key the key to search for
     * @param mixed $default the value to return if the $key is not found in $array or if the value of $key element is
     *                       null
     *
     * @return mixed array value or given default value
     */
    public static function getIfSet(array $array, $key, $default = null)
    {
        return isset($array[$key]) ? $array[$key] : $default;
    }

    /**
     * Sets destination array values to be the source values if the source key exist in the source array.
     *
     * @param array $source
     * @param array &$dest
     * @param array $keyMap mapping of dest keys to source keys. If $keyMap is associative, the keys will be the
     *                      destination keys. If numeric the values will be the destination keys
     *
     * @return void
     */
    public static function copyIfKeysExist(array $source, array &$dest, array $keyMap)
    {
        $callable = function (array $source, $key) {
            return array_key_exists($key, $source);
        };
        self::copyValueIf($source, $dest, $keyMap, $callable);
    }

    /**
     * Sets destination array values to be the source values if the source key is set in the source array.
     *
     * @param array $source
     * @param array &$dest
     * @param array $keyMap mapping of dest keys to source keys. If $keyMap is associative, the keys will be the
     *                      destination keys. If numeric the values will be the destination keys
     *
     * @return void
     */
    public static function copyIfSet(array $source, array &$dest, array $keyMap)
    {
        $callable = function (array $source, $key) {
            return isset($source[$key]);
        };
        self::copyValueIf($source, $dest, $keyMap, $callable);
    }

    /**
     * Returns true and fills $value if $key exists in $array, otherwise fills $value with null and returns false
     *
     * @param array $array The array to pull from
     * @param string|integer $key The key to get
     * @param mixed &$value The value to set
     *
     * @return bool true if $key was found and filled in $value, false if $key was not found and $value was set to null
     */
    public static function tryGet(array $array, $key, &$value) : bool
    {
        if ((is_string($key) || is_int($key)) && array_key_exists($key, $array)) {
            $value = $array[$key];
            return true;
        }

        $value = null;
        return false;
    }

    /**
     * Projects values of a key into an array.
     *
     * if $input = [
     *     ['key 1' => 'item 1 value 1', 'key 2' => 'item 1 value 2'],
     *     ['key 1' => 'item 2 value 1', 'key 2' => 'item 2 value 2'],
     *     ['key 1' => 'item 3 value 1'],
     * ]
     * and $key = 'key 2'
     * and $strictKeyCheck = false
     *
     * then return ['item 1 value 2', 'item 2 value 2']
     *
     * but if $strictKeyCheck = true then an InvalidArgumentException occurs since 'key 2' wasnt in item 3
     *
     * @param array $input the array to project from
     * @param string|integer $key the key which values we are to project
     * @param boolean $strictKeyCheck ensure key is in each $input array or not
     *
     * @return array the projection
     *
     * @throws \InvalidArgumentException if a value in $input was not an array
     * @throws \InvalidArgumentException if a key was not in one of the $input arrays
     */
    public static function project(array $input, $key, bool $strictKeyCheck = true) : array
    {
        $projection = [];

        foreach ($input as $itemKey => $item) {
            self::ensureIsArray($item, 'a value in $input was not an array');

            if (array_key_exists($key, $item)) {
                $projection[$itemKey] = $item[$key];
            } elseif ($strictKeyCheck) {
                throw new \InvalidArgumentException('key was not in one of the $input arrays');
            }
        }

        return $projection;
    }

    /**
     * Returns a sub set of the given $array based on the given $conditions
     *
     * @param array[] $array an array of arrays to be checked
     * @param array $conditions array of key/value pairs to filter by
     * @param bool  $preserveKeys Flag to preserve the original keys of the array
     *
     * @return array the subset
     *
     * @throws \InvalidArgumentException if a value in $array was not an array
     */
    public static function where(array $array, array $conditions, bool $preserveKeys = false) : array
    {
        $result = [];
        foreach ($array as $index => $item) {
            self::ensureIsArray($item, 'a value in $array was not an array');

            foreach ($conditions as $key => $value) {
                if (!array_key_exists($key, $item) || $item[$key] !== $value) {
                    continue 2; // continue to the next item in $array
                }
            }

            $result[$index] = $item;
        }

        return $preserveKeys ? $result : array_values($result);
    }

    /**
     * Takes each item and embeds it into the destination array, returning the result.
     *
     * Each item's key is used as the key in the destination array so that keys are preserved.  Each resulting item in
     * the destination will be embedded into a field named by $fieldName.  Any items that don't have an entry in
     * destination already will be added, not skipped.
     *
     * For example, embedInto(['Joe', 'Sue'], 'lastName', [['firstName' => 'Billy'], ['firstName' => 'Bobby']]) will
     * return [['firstName' => 'Billy', 'lastName' => 'Joe'], ['firstName' => 'Bobby', 'lastName' => 'Sue']]
     *
     * @param array $items The items to embed into the result.
     * @param string $fieldName The name of the field to embed the items into.  This field must not exist in the
     *                          destination items already.
     * @param array $destination An optional array of arrays to embed the items into.  If this is not provided then
     *                           empty records are assumed and the new record will be created only containing
     *                           $fieldName.
     * @param bool $overwrite whether to overwrite $fieldName in $destination array
     *
     * @return array $destination, with all items in $items added using their keys, but underneath a nested $fieldName
     *               key.
     *
     * @throws \InvalidArgumentException if $fieldName was not a string
     * @throws \InvalidArgumentException if a value in $destination was not an array
     * @throws \Exception if $fieldName key already exists in a $destination array
     */
    public static function embedInto(
        array $items,
        string $fieldName,
        array $destination = [],
        bool $overwrite = false
    ) : array {
        foreach ($items as $key => $item) {
            if (!array_key_exists($key, $destination)) {
                $destination[$key] = [$fieldName => $item];
                continue;
            }

            self::ensureIsArray($destination[$key], 'a value in $destination was not an array');

            if (!$overwrite && array_key_exists($fieldName, $destination[$key])) {
                throw new \Exception('$fieldName key already exists in a $destination array');
            }

            $destination[$key][$fieldName] = $item;
        }

        return $destination;
    }

    /**
     * Fills the given $template array with values from the $source array
     *
     * @param array $template the array to be filled
     * @param array $source the array to fetch values from
     *
     * @return array Returns a filled version of $template
     */
    public static function fillIfKeysExist(array $template, array $source)
    {
        $result = $template;
        foreach ($template as $key => $value) {
            if (array_key_exists($key, $source)) {
                $result[$key] = $source[$key];
            }
        }

        return $result;
    }

    /**
     * Extracts an associative array from the given multi-dimensional array.
     *
     * @param array $input The multi-dimensional array.
     * @param string|int $keyIndex The index to be used as the key of the resulting single dimensional result array.
     * @param string|int $valueIndex The index to be used as the value of the resulting single dimensional result array.
     *                               If a sub array does not contain this element null will be used as the value.
     * @param string $duplicateBehavior Instruct how to handle duplicate resulting values, 'takeFirst', 'takeLast',
     *                                  'throw'
     *
     * @return array an associative array
     *
     * @throws \InvalidArgumentException Thrown if $input is not an multi-dimensional array
     * @throws \InvalidArgumentException Thrown if $keyIndex is not an int or string
     * @throws \InvalidArgumentException Thrown if $valueIndex is not an int or string
     * @throws \InvalidArgumentException Thrown if $duplicateBehavior is not 'takeFirst', 'takeLast', 'throw'
     * @throws \UnexpectedValueException Thrown if a $keyIndex value is not a string or integer
     * @throws \Exception Thrown if $duplicatedBehavior is 'throw' and duplicate entries are found.
     */
    public static function extract(
        array $input,
        $keyIndex,
        $valueIndex,
        string $duplicateBehavior = 'takeLast'
    ) : array {
        if (!in_array($duplicateBehavior, ['takeFirst', 'takeLast', 'throw'])) {
            throw new \InvalidArgumentException("\$duplicateBehavior was not 'takeFirst', 'takeLast', or 'throw'");
        }

        self::ensureValidKey($keyIndex, '$keyIndex was not a string or integer');
        self::ensureValidKey($valueIndex, '$valueIndex was not a string or integer');

        $result = [];
        foreach ($input as $index => $array) {
            self::ensureIsArray($array, '$arrays was not a multi-dimensional array');

            $key = self::get($array, $keyIndex);
            $message = "Value for \$arrays[{$index}][{$keyIndex}] was not a string or integer";
            self::ensureValidKey($key, $message, UnexpectedValueException::class);

            $value = self::get($array, $valueIndex);
            if (!array_key_exists($key, $result)) {
                $result[$key] = $value;
                continue;
            }

            if ($duplicateBehavior === 'throw') {
                throw new \Exception("Duplicate entry for '{$key}' found.");
            }

            if ($duplicateBehavior === 'takeLast') {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Returns the first set {@see isset()} value specified by the given array of keys.
     *
     * @param array $array The array containing the possible values.
     * @param array $keys Array of keys to search for. The first set value will be returned.
     * @param mixed $default The default value to return if no set value was found in the array.
     *
     * @return mixed Returns the found set value or the given default value.
     */
    public static function getFirstSet(array $array, array $keys, $default = null)
    {
        foreach ($keys as $key) {
            if (isset($array[$key])) {
                return $array[$key];
            }
        }

        return $default;
    }

    /**
     * Partitions the given $input array into an array of $partitionCount sub arrays.
     *
     * This is a slight modification of the function suggested on
     * http://php.net/manual/en/function.array-chunk.php#75022. This method does not pad with empty partitions and
     * ensures positive partition count.
     *
     * @param array $input The array to partition.
     * @param int $partitionCount The maximum number of partitions to create.
     * @param bool $preserveKeys Flag to preserve numeric array indexes. Associative indexes are preserved by default.
     *
     * @return array A multi-dimensional array containing $partitionCount sub arrays.
     *
     * @throws \InvalidArgumentException Thrown if $partitionCount is not a positive integer.
     * @throws \InvalidArgumentException Thrown if $preserveKeys is not a boolean value.
     */
    public static function partition(array $input, int $partitionCount, bool $preserveKeys = false) : array
    {
        if ($partitionCount < 1) {
            throw new \InvalidArgumentException('$partitionCount must be a positive integer');
        }

        $inputLength = count($input);
        $partitionLength = floor($inputLength / $partitionCount);
        $partitionRemainder = $inputLength % $partitionCount;
        $partitions = [];
        $sliceOffset = 0;
        for ($partitionIndex = 0; $partitionIndex < $partitionCount && $sliceOffset < $inputLength; $partitionIndex++) {
            $sliceLength = ($partitionIndex < $partitionRemainder) ? $partitionLength + 1 : $partitionLength;
            $partitions[$partitionIndex] = array_slice($input, $sliceOffset, $sliceLength, $preserveKeys);
            $sliceOffset += $sliceLength;
        }

        return $partitions;
    }

    /**
     * Unsets all elements in the given $array specified by $keys
     *
     * @param array &$array The array containing the elements to unset.
     * @param array $keys Array of keys to unset.
     *
     * @return void
     */
    public static function unsetAll(array &$array, array $keys)
    {
        foreach ($keys as $key) {
            unset($array[$key]);
        }
    }

    /**
     * Convert all empty strings or strings that contain only whitespace to null in the given array
     *
     * @param array &$array The array containing empty strings
     *
     * @return void
     */
    public static function nullifyEmptyStrings(array &$array)
    {
        foreach ($array as &$value) {
            if (is_string($value) && trim($value) === '') {
                $value = null;
            }
        }
    }

    /**
     * Traverses the given $array using the key path specified by $delimitedKey and returns the final value.
     *
     * Example:
     * <br />
     * <pre>
     * use TraderInteractive\Util\Arrays;
     * $array = [
     *     'db' => [
     *         'host' => 'localhost',
     *         'login' => [
     *             'username' => 'scott',
     *             'password' => 'tiger',
     *         ],
     *     ],
     * ];
     * echo Arrays::getNested($array, 'db.login.username');
     * </pre>
     * <br />
     * Output:
     * <pre>
     * scott
     * </pre>
     *
     * @param array  $array        The array to traverse.
     * @param string $delimitedKey A string of keys to traverse into the array.
     * @param string $delimiter    A string specifiying how the keys are delimited. The default is '.'.
     *
     * @return mixed The value at the inner most key or null if a key does not exist.
     */
    final public static function getNested(array $array, string $delimitedKey, string $delimiter = '.')
    {
        $pointer = $array;
        foreach (explode($delimiter, $delimitedKey) as $key) {
            if (is_array($pointer) && array_key_exists($key, $pointer)) {
                $pointer = $pointer[$key];
                continue;
            }

            return null;
        }

        return $pointer;
    }

    /**
     * Changes the case of all keys in an array. Numbered indices are left as is.
     *
     * @param array   $input The array to work on.
     * @param integer $case  The case to which the keys should be set.
     *
     * @return array Returns an array with its keys case changed.
     */
    public static function changeKeyCase(array $input, int $case = self::CASE_LOWER) : array
    {
        if ($case & self::CASE_UNDERSCORE) {
            $input = self::underscoreKeys($input);
        }

        if ($case & self::CASE_CAMEL_CAPS) {
            $input = self::camelCaseKeys($input);
        }

        if ($case & self::CASE_UPPER) {
            $input = array_change_key_case($input, \CASE_UPPER);
        }

        if ($case & self::CASE_LOWER) {
            $input = array_change_key_case($input, \CASE_LOWER);
        }

        return $input;
    }

    /**
     * Converts a multi-dimensional array into a single associative array whose keys are the concatinated keys
     *
     * @param array  $input     The array to flatten
     * @param string $delimiter The separator for the concatinated keys.
     *
     * @return array The flattened array
     */
    final public static function flatten(array $input, string $delimiter = '.') : array
    {
        $args = func_get_args();
        $prefix = count($args) === 3 ? array_pop($args) : '';
        $result = [];
        foreach ($input as $key => $value) {
            $newKey = $prefix . (empty($prefix) ? '' : $delimiter) . $key;
            if (is_array($value)) {
                $result = array_merge($result, self::flatten($value, $delimiter, $newKey));
                continue;
            }

            $result[$newKey] = $value;
        }

        return $result;
    }

    /**
     * Returns all elements in the given $input array which contain the specifed $targetKey index.
     *
     * @param array          $input     The multi-dimensional array to check.
     * @param string|integer $targetKey The key to search for.
     *
     * @return array All elements of $input which contained $targetKey element with original keys preserved.
     */
    final public static function getAllWhereKeyExists(array $input, $targetKey) : array
    {
        $result = [];
        foreach ($input as $key => $value) {
            if (array_key_exists($targetKey, $value)) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Returns TRUE if any of the given $keys exist in the $input array.
     *
     * @param array $array An array with keys to check.
     * @param array $keys  The keys to check
     *
     * @return bool
     */
    final public static function anyKeysExist(array $array, array $keys) : bool
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $array)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Method to rename a key within array to a new key name.
     *
     * @param array  $input     The array containing the element to rename.
     * @param string $oldKey    The old key value.
     * @param string $newKey    The new key value.
     * @param bool   $overwrite Flag to allow overwriting if the $newKey exists in the array
     *
     * @return void
     */
    final public static function rename(array &$input, string $oldKey, string $newKey, bool $overwrite = false)
    {
        if (!array_key_exists($oldKey, $input)) {
            throw new InvalidArgumentException("{$oldKey} does not exist in the given array");
        }

        if (array_key_exists($newKey, $input) && !$overwrite) {
            throw new InvalidArgumentException("{$newKey} found the given array");
        }

        $value = $input[$oldKey];
        unset($input[$oldKey]);
        $input[$newKey] = $value;
    }

    private static function underscoreKeys(array $input) : array
    {
        $copy = [];
        foreach ($input as $key => $value) {
            $copy[preg_replace("/([a-z])([A-Z0-9])/", '$1_$2', $key)] = $value;
        }

        $input = $copy;
        unset($copy); //garbage collection
        return $input;
    }

    private static function camelCaseKeys(array $input) : array
    {
        $copy = [];
        foreach ($input as $key => $value) {
            $key = implode(' ', array_filter(preg_split('/[^a-z0-9]/i', $key)));
            $key = lcfirst(str_replace(' ', '', ucwords(strtolower($key))));
            $copy[$key] = $value;
        }

        $input = $copy;
        unset($copy); //garbage collection
        return $input;
    }

    private static function ensureValidKey(
        $key,
        string $message,
        string $exceptionClass = '\\InvalidArgumentException'
    ) {
        if (!is_string($key) && !is_int($key)) {
            $reflectionClass = new \ReflectionClass($exceptionClass);
            throw $reflectionClass->newInstanceArgs([$message]);
        }
    }

    private static function ensureIsArray(
        $value,
        string $message,
        string $exceptionClass = '\\InvalidArgumentException'
    ) {
        if (!is_array($value)) {
            $reflectionClass = new \ReflectionClass($exceptionClass);
            throw $reflectionClass->newInstanceArgs([$message]);
        }
    }

    private static function copyValueIf(array $source, array &$dest, array $keyMap, callable $condition)
    {
        foreach ($keyMap as $destKey => $sourceKey) {
            if (is_int($destKey)) {
                $destKey = $sourceKey;
            }

            if ($condition($source, $sourceKey)) {
                $dest[$destKey] = $source[$sourceKey];
            }
        }
    }
}
