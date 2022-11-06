<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_quix
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights
 *     reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;

/**
 * Supports a modal article picker.
 *
 * @since  1.6
 */
class JFormFieldModal_Page extends JFormField
{

    /**
     * The form field type.
     *
     * @var        string
     * @since   1.6
     */
    protected $type = 'Modal_Page';

    /**
     * Method to get the field input markup.
     *
     * @return  string    The field input markup.
     *
     * @since   1.6
     */
    protected function getInput()
    {
        if (JVERSION >= 4) {
            return $this->getInputJ4();
        } else {
            return $this->getInputJ3();
        }
    }


    protected function getInputJ3()
    {
        $allowEdit  = ((string) $this->element['edit'] == 'true') ? true
            : false;
        $allowClear = ((string) $this->element['clear'] != 'false') ? true
            : false;

        // Load language
        JFactory::getLanguage()->load('com_quix', JPATH_ADMINISTRATOR);

        // Build the script.
        $script = [];

        // Select button script
        $script[] = '	function jSelectPage_'.$this->id
            .'(id, title, catid, object) {';
        $script[] = '		document.getElementById("'.$this->id
            .'_id").value = id;';
        $script[] = '		document.getElementById("'.$this->id
            .'_name").value = title;';

        if ($allowEdit) {
            $script[] = '		jQuery("#'.$this->id
                .'_edit").removeClass("hidden");';
        }

        if ($allowClear) {
            $script[] = '		jQuery("#'.$this->id
                .'_clear").removeClass("hidden");';
        }

        $script[] = '		jQuery("#modalPage'.$this->id.'").modal("hide");';

        if ($this->required) {
            $script[]
                = '		document.formvalidator.validate(document.getElementById("'
                .$this->id.'_id"));';
            $script[]
                = '		document.formvalidator.validate(document.getElementById("'
                .$this->id.'_name"));';
        }

        $script[] = '	}';

        // Clear button script
        static $scriptClear;

        if ($allowClear && !$scriptClear) {
            $scriptClear = true;

            $script[] = '	function jClearPage(id) {';
            $script[]
                      = '		document.getElementById(id + "_id").value = "";';
            $script[] = '		document.getElementById(id + "_name").value = "'
                .
                htmlspecialchars(JText::_('COM_QUIX_SELECT_AN_PAGE', true),
                    ENT_COMPAT, 'UTF-8').'";';
            $script[] = '		jQuery("#"+id + "_clear").addClass("hidden");';
            $script[] = '		if (document.getElementById(id + "_edit")) {';
            $script[]
                      = '			jQuery("#"+id + "_edit").addClass("hidden");';
            $script[] = '		}';
            $script[] = '		return false;';
            $script[] = '	}';
        }

        // Add the script to the document head.
        JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

        // Setup variables for display.
        $html = [];
        $link
              = 'index.php?option=com_quix&amp;view=pages&amp;layout=modal&amp;tmpl=component&amp;function=jSelectPage_'
            .$this->id;

        if (isset($this->element['language'])) {
            $link .= '&amp;forcedLanguage='.$this->element['language'];
        }

        if ((int) $this->value > 0) {
            $db    = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select($db->quoteName('title'))
                ->from($db->quoteName('#__quix'))
                ->where($db->quoteName('id').' = '.(int) $this->value);
            $db->setQuery($query);

            try {
                $title = $db->loadResult();
            } catch (RuntimeException $e) {
                JError::raiseWarning(500, $e->getMessage());
            }
        }

        if (empty($title)) {
            $title = JText::_('COM_QUIX_SELECT_AN_PAGE');
        }
        $title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

        // The active article id field.
        if (0 == (int) $this->value) {
            $value = '';
        } else {
            $value = (int) $this->value;
        }

        $url = $link.'&amp;'.JSession::getFormToken().'=1';

        // The current article display field.
        $html[] = '<span class="input-append">';
        $html[] = '<input type="text" class="input-medium" id="'.$this->id
            .'_name" value="'.$title.'" disabled="disabled" size="35" />';
        $html[] = '<a href="#modalPage'.$this->id
            .'" class="btn hasTooltip" role="button"  data-toggle="modal" title="'
            .JHtml::tooltipText('COM_QUIX_CHANGE_ARTPAGE').'">'
            .'<span class="icon-file"></span> '
            .JText::_('JSELECT').'</a>';

        // Clear article button
        if ($allowClear) {
            $html[] = '<button id="'.$this->id.'_clear" class="btn'.($value ? ''
                    : ' hidden').'" onclick="return jClearPage(\''.
                $this->id.'\')"><span class="icon-remove"></span>'
                .JText::_('JCLEAR').'</button>';
        }

        $html[] = '</span>';

        // The class='required' for client side validation
        $class = '';

        if ($this->required) {
            $class = ' class="required modal-value"';
        }

        $html[] = '<input type="hidden" id="'.$this->id.'_id"'.$class.' name="'
            .$this->name.'" value="'.$value.'" />';

        $html[] = JHtml::_(
            'bootstrap.renderModal',
            'modalPage'.$this->id,
            [
                'url'    => $url,
                'title'  => JText::_('COM_QUIX_CHANGE_ARTPAGE'),
                'width'  => '800px',
                'height' => '300px',
                'footer' => '<button class="btn" data-dismiss="modal" aria-hidden="true">'
                    .JText::_("JLIB_HTML_BEHAVIOR_CLOSE").'</button>',
            ]
        );

        return implode("\n", $html);
    }

    protected function getInputJ4()
    {
        $modalId = 'Page_' . $this->id;

        /** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
        $wa = Factory::getApplication()->getDocument()->getWebAssetManager();

        // Add the modal field script to the document head.
        $wa->useScript('field.modal-fields');

        $wa->addInlineScript("
				window.jSelectPage_".$this->id." = function (id, title, catid, object, url, language) {
					window.processModalSelect('Page', '".$this->id."', id, title, catid, object, url, language);
				}", [], ['type' => 'module']
        );

        // Setup variables for display.
        $html = [];
        $link
              = 'index.php?option=com_quix&amp;view=pages&amp;layout=modal&amp;tmpl=component&amp;function=jSelectPage_'
            .$this->id;

        if (isset($this->element['language'])) {
            $link .= '&amp;forcedLanguage='.$this->element['language'];
        }

        if ((int) $this->value > 0) {
            $db    = Factory::getDbo();
            $query = $db->getQuery(true)
                ->select($db->quoteName('title'))
                ->from($db->quoteName('#__quix'))
                ->where($db->quoteName('id').' = '.(int) $this->value);
            $db->setQuery($query);

            try {
                $title = $db->loadResult();
            } catch (RuntimeException $e) {
                $app = Factory::getApplication();
                $app->enqueueMessage($e->getMessage(), 'warning');
                $app->setHeader('status', '500', true);
            }
        }

        if (empty($title)) {
            $title = Text::_('COM_QUIX_SELECT_AN_PAGE');
        }

        $title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

        // The active page id field.
        if (0 == (int) $this->value) {
            $value = '';
        } else {
            $value = (int) $this->value;
        }

        $url  = $link.'&amp;'.Session::getFormToken().'=1';
        $html = '';

        $html .= '<span class="input-group">';
        $html .= '<input class="form-control" id="'.$this->id
            .'_name" type="text" value="'.$title.'" readonly size="35">';
        $html .= '<button'
            .' class="btn btn-primary"'
            .' id="'.$this->id.'_select"'
            .' data-bs-toggle="modal"'
            .' type="button"'
            .' data-bs-target="#ModalSelect'.$modalId.'">'
            .'<span class="icon-file" aria-hidden="true"></span> '
            .Text::_('JSELECT')
            .'</button>';
        $html .= '</span>';

        // The class='required' for client side validation
        $class = '';

        if ($this->required) {
            $class = ' class="required modal-value"';
        }

        $html .= HTMLHelper::_(
            'bootstrap.renderModal',
            'ModalSelectPage_'.$this->id,
            [
                'url'        => $url,
                'title'      => Text::_('COM_QUIX_SELECT_AN_PAGE'),
                'height'     => '400px',
                'width'      => '800px',
                'bodyHeight' => 70,
                'modalWidth' => 80,
                'footer'     => '<button class="btn" data-bs-dismiss="modal" aria-hidden="true">'
                    .Text::_("JLIB_HTML_BEHAVIOR_CLOSE").'</button>',
            ]
        );

        $html .= '<input type="hidden" id="'.$this->id.'_id"'.$class.' name="'
            .$this->name.'" value="'.$value.'" />';

        return $html;
    }

    /**
     * Method to get the field label markup.
     *
     * @return  string  The field label markup.
     *
     * @since   3.4
     */
    protected function getLabel()
    {
        return str_replace($this->id, $this->id.'_id', parent::getLabel());
    }

}
