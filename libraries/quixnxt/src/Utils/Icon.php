<?php

namespace QuixNxt\Utils;

class Icon
{
    private $source_url = 'https://getquix.net/index.php?option=com_quixblocks&view=flaticons&format=json';
    private $storage_path;
    private $error;


    /**
     * @var string
     *
     * @since 3.0.0
     */
    private $name;

    /**
     * Icon constructor.
     *
     * @param  string  $name
     *
     * @since 3.0.0
     */
    public function __construct(string $name)
    {
        $this->name = $name;

        $this->storage_path = \QuixAppHelper::getQuixMediaPath().'/storage/icons';
        if ( ! file_exists($this->storage_path) && ! is_dir($this->storage_path)) {
            /* instead of using mkdir, we used it to handle all exceptions */
            if (\JFolder::create($this->storage_path) !== true) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $this->storage_path));
            }

            $this->_download();
        }
    }

    /**
     * @since 3.0.0
     */
    private function _download(): void
    {
        $iconsJson = file_get_contents($this->source_url);
        if ( ! $iconsJson) {
            $this->error = 'Could not download icons list';

            return;
        }

        $icons = json_decode($iconsJson, true);
        if ( ! $icons) {
            $this->error = 'Could not decode icons JSON';

            return;
        }

        foreach ($icons as $icon) {
            $group        = $icon['group'];
            $i            = 0;
            $group_prefix = $group[$i];
            while ($group_prefix === 'i') {
                $group_prefix = $group[$i++];
            }
            $prefix = 'qxi'.$group_prefix;
            file_put_contents("{$this->storage_path}/{$prefix}-{$icon['name']}.svg", $icon['svg']);
        }
    }

    /**
     * @return string
     *
     * @since 3.0.0
     */
    public function __toString(): string
    {
        if (isset($this->error)) {
            return "<strong>{$this->error}</strong>";
        }

        if (startsWith($this->name, '<svg') || startsWith($this->name, '<?xml')) {
            if (startsWith($this->name, '<?xml')) {
                $this->name = preg_replace('/<\?xml[^>]+\/?>/im', '', $this->name);
            }

            return $this->name;
        }

        $ext = pathinfo($this->name, PATHINFO_EXTENSION);
        if ($ext === 'svg') {
            // let's load using qx-svg way
            // if (file_exists(JPATH_SITE . '/' . $this->name)) {
            //     return file_get_contents(JPATH_SITE . '/' . $this->name);
            // }
            $root = \JUri::root();

            if (preg_match('/^(https?:\/\/)/', $this->name)) {
                return "<img data-src=\"{$this->name}\" alt=\"{$this->name}\" class=\"qx-preserve qx-img-fluid blur-up lazyload\" />";
            } else {
                $path = $root.$this->name;
                return "<img data-src=\"{$path}\" alt=\"icon-alt\" class=\"qx-preserve\" qx-svg=\"\" />";
            }

        } else {

            $filepath = "{$this->storage_path}/{$this->name}.svg";
            if (file_exists($filepath)) {
                // return file_get_contents($filepath);

                $path = \JUri::root()."media/quixnxt/storage/icons/{$this->name}.svg";

                return "<img data-src=\"{$path}\" alt=\"{$this->name}\" class=\"qx-preserve\" qx-svg=\"\" />";

            }

            return "<i class=\"{$this->name}\"></i>";
        }

    }
}
