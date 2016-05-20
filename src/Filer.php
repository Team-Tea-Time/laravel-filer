<?php namespace TeamTeaTime\Filer;

use Exception;
use Illuminate\Routing\Router;

class Filer
{
    /**
     * Define the standard routes.
     *
     * @param  Router  $router
     * @param  string  $namespace
     * @return void
     */
    public static function routes(Router $router, $namespace = 'TeamTeaTime\Filer\Controllers')
    {
        $router->group(compact('namespace'), function ($router) {
            $router->get('{id}', [
                'as'    => 'filer.file.view',
                'uses'  => 'LocalFileController@view'
            ]);
            $router->get('{id}/download', [
                'as'    => 'filer.file.download',
                'uses'  => 'LocalFileController@download'
            ]);
        });
    }

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
            // Item is a string; check to see if it's a URL or filepath
            if (filter_var($item, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
                // Item is a URL
                return Type::URL;
            } elseif (is_file(config('filer.path.absolute') . "/{$item}")) {
                // Item is a filepath
                return Type::FILEPATH;
            }
        } elseif (is_a($item, 'SplFileInfo')) {
            // Item is a file object
            return Type::FILE;
        }

        // Throw an exception if item doesn't match any known types
        throw new Exception('Unknown item type');
    }

    /**
     * Returns a file's relative path.
     *
     * @param  mixed  $file
     * @return string
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
     * @param  string  $string
     * @return string
     */
    public static function convertSlashes($string)
    {
        return str_replace("\\", '/', $string);
    }
}
