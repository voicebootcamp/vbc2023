<?php

namespace QuixNxt\Utils\Image;

use Joomla\Filesystem\Folder as JFolder;


class Download
{
    /**
     * Copy the file locally
     *
     * @param  string  $final_path
     * @param  string  $original_domain
     *
     * @return bool
     * @since 4.1.12
     */
    public static function copyFile($final_path, $original_domain)
    {
        $pathInfo    = pathinfo($final_path);
        $folder_path = str_replace($original_domain, '', $pathInfo['dirname']);
        $folder_path = "/".$folder_path."/";

        //Path validator
        if ( ! file_exists(JPATH_SITE.$folder_path)) {
            JFolder::create(JPATH_SITE.$folder_path);
        }

        //File generator
        $fp = JPATH_SITE.$folder_path.$pathInfo['filename'].'.'.$pathInfo['extension'];

        if ( ! file_exists($fp)) {

            $data = file_get_contents($final_path);

            if ($data != false) {
                if (file_put_contents($fp,  $data)) {
                    // Downloaded successfully";
                    return true;
                } else {
                    // Downloading failed.";
                    return false;
                }
            }
            else {
                // Downloading failed.";
                return false;
            }
        }
        else{
            //if the image path is already in storage. 
            return true;
        }

    }

    /**
     * Validate json format
     *
     * @param $string
     *
     * @return bool
     * @since 4.1.12
     */
    public static function isJson($string)
    {
        try {
            json_decode($string);

            return json_last_error() === JSON_ERROR_NONE;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Replace source or base domain for copied image
     *
     * @param $data
     *
     * @return void
     * @since 4.1.12
     */
    public static function dataCheckerParent(&$data)
    {

        $pageData = $data['data'];

        if (is_string($pageData) && Download::isJson($pageData)) {
            $pageData = json_decode($pageData, true);
        }

        if (isset($pageData['data'])) {
            $pageData = $pageData['data'];
        }

        $pageData = self::arrayFindAndReplaceByKey($pageData, 'base_domain', \JUri::root());


        $pageData     = json_encode($pageData);
        $data['data'] = $pageData;
    }

    /**
     * Generic arrayFindAndReplaceByKey
     * @param $sourceData
     * @param $find
     * @param $replace
     *
     * @return array|mixed|string|void
     * @since 4.1.12
     */
    public static function arrayFindAndReplaceByKey($sourceData, $find, $replace)
    {
        if (is_array($sourceData)) {
            foreach ($sourceData as $key => $val) {

                /**
                 * if an array, find it again. we are looking for string value for base_domain
                 */
                if (is_array($val)) {
                    $sourceData[$key] = self::arrayFindAndReplaceByKey($val, $find, $replace);
                } else {
                    /**
                     * let's match the key.
                     */
                    if ($key === $find) {
                        // $sourceData[$key] = $Replace;
                        /**
                         * we have found it
                         * now map the image, find and replace
                         *
                         * Note: We are passing base_domain.
                         * If "base_domain" is there then
                         * "source" must be there.
                         */
                        $sourceData = self::downloadImageAndReplaceTheBaseDomain($val, $key, $sourceData, $replace);
                    }
                }
            }
        }

        return $sourceData;
    }

    /**
     * @param $value       string base_domain value, ex:https://www.themexpert.com
     * @param $key         string base_domain
     * @param  $sourceData array Image array
     * @param $replace     string our current domain
     *
     * @since 4.1.9
     */
    private static function downloadImageAndReplaceTheBaseDomain(string $value, string $key, array $sourceData, string $replace)
    {

        if ($key == "base_domain") {

            $original_domain = $sourceData['base_domain'];
            $source_path     = $sourceData['source'];

            $current_domain = $replace;

            $default_image = \JURI::base().'media/quixnxt/images/placeholder.png';


            // Final url for download the image.
            $final_path = $original_domain.$source_path;


            if ($current_domain != $original_domain) {

                if (stripos(@get_headers($original_domain.$source_path, 1)[0], "200 OK")) {
                    // If the file is exists, go for download.
                    Download::copyFile($final_path, $original_domain);
                    // }
                } else {
                    // If the file is not exists, pass placeholder image.
                    Download::copyFile($default_image, \JURI::base());
                }

                return $sourceData;
            }
        } /*
        *This section will work for placeholder update. 
        */
        else {
            return $replace;
        }
    }
}
