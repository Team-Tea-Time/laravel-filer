<?php

namespace TeamTeaTime\Filer;

class Utils
{
    /**
     * Attempts to determine what the given item is between a local filepath, a
     * local file object or a URL.
     *
     * @param  mixed  $item
     * @return string
     * @throws Exception
     */
    public static function checkType($item)
    {
        if (is_string($item)) {
            // Item is a string; check to see if it's a URL
            if (filter_var($item, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
                // Item is a URL
                return 'URL';
            } elseif (is_file($item)) {
                // Item is a filepath
                return 'LocalFile';
            }
        } elseif (is_a($item, 'SplFileInfo')) {
            // Item is a file object
            return 'LocalFile';
        }

        // Throw an exception if item doesn't match any known types
        throw new Exception('Unknown item type');
    }

    /**
     * Returns a file's relative path based on the current value of
     * filer.path.absolute.
     *
     * @param   $item           The item to extract the path from.
     *          mixed
     *
     * @return  string          The filepath.
     */
    public static function getRelativeFilepath($file)
    {
        $storageDir = self::convertSlashes(config('filer.path.absolute'));
        $absolutePath = self::convertSlashes($file->getRealPath());
        return dirname(str_replace($storageDir, '', $absolutePath));
    }

    /**
     * Converts backslashes to forward slashes.
     *
     * @param   $string         The string to alter.
     *          string
     *
     * @return  string          Converted string.
     */
    public static function convertSlashes($string)
    {
        return str_replace("\\", '/', $string);
    }
}
