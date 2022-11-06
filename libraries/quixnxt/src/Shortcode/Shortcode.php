<?php

namespace QuixNxt\Shortcode;

class Shortcode
{
  /**
   * Applies callback to shortcode tags.
   *
   * @param  string  $tag
   * @param  string  $text
   * @param  callable  $callback
   *
   * @return mixed|string
   * @since 3.0.0
   */
    public function parse(string $tag, string $text, callable $callback)
    {
        if (false === strpos($text, '[')) {
            return $text;
        }

        $self = $this;

        return preg_replace_callback($this->getRegexp($tag), static function ($matches) use ($self, $callback) {

          # allow [[foo]] syntax for escaping a tag
            if ($matches[1] === '[' && $matches[6] === ']') {
                return substr($matches[0], 1, -1);
            }

            $tag   = $matches[2];
            $attrs = $self->attrs($matches[3]);

            if (isset($matches[5])) {
              # enclosing tag - extra parameter
                return $matches[1].$callback($attrs, $matches[5], $tag, $matches[0]).$matches[6];
            }

    # self-closing tag
            return $matches[1].$callback($attrs, null, $tag, $matches[0]).$matches[6];
        }, $text);
    }

  /**
   * Retrieve attributes from the shortcode tag.
   *
   * @param  string  $text
   *
   * @return array
   * @since 3.0.0
   */
    public function attrs(string $text): array
    {
        $attrs   = array();
        $pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
        $text    = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text);

        if (preg_match_all($pattern, $text, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                if ($match[1]) {
                    $attrs[strtolower($match[1])] = $match[2];
                } elseif ($match[3]) {
                    $attrs[strtolower($match[3])] = $match[4];
                } elseif ($match[5]) {
                    if ($match[6] === 'true') {
                        $attrs[strtolower($match[5])] = true;
                    } elseif ($match[6] === 'false') {
                        $attrs[strtolower($match[5])] = false;
                    } else {
                        $attrs[strtolower($match[5])] = $match[6];
                    }
                } elseif ($match[7]) {
                    $attrs[$match[7]] = true;
                } elseif ($match[8]) {
                    $attrs[$match[8]] = true;
                }
            }
        } else {
            $attrs = ltrim($text);
        }

        return $attrs;
    }

  /**
   * Gets the shortcode regular expression pattern.
   *
   * @param  string  $tag
   *
   * @return string
   * @since 3.0.0
   */
    protected function getRegexp(string $tag): string
    {
        return '/
                \[                               # Opening bracket
                    (\[?)                        # 1: Optional second opening bracket for escaping shortcodes: [[tag]]
                    ('.$tag.')               # 2: Shortcode name
                    (?![\w-])                    # Not followed by word character or hyphen
                    (                            # 3: Unroll the loop: Inside the opening shortcode tag
                        [^\]\/]*                 # Not a closing bracket or forward slash
                        (?:
                            \/(?!\])             # A forward slash not followed by a closing bracket
                            [^\]\/]*             # Not a closing bracket or forward slash
                        )*?
                    )
                    (?:
                        (\/)                     # 4: Self closing tag ...
                        \]                       # ... and closing bracket
                        |
                        \]                       # Closing bracket
                        (?:
                            (                    # 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
                                [^\[]*           # Not an opening bracket
                                (?:
                                    \[(?!\/\2\]) # An opening bracket not followed by the closing shortcode tag
                                    [^\[]*       # Not an opening bracket
                                )*
                            )
                            (\[\/\2\])           # Closing shortcode tag
                        )?
                    )
                (\]?)                            # 6: Optional second closing bracket for escaping shortcodes: [[tag]]
                /sx';
    }
}
