<?php
/**
 * @package         Regular Labs Library
 * @version         22.10.1331
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://regularlabs.com
 * @copyright       Copyright © 2022 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

namespace RegularLabs\Library\Form\Field;

defined('_JEXEC') or die;

use RegularLabs\Library\ArrayHelper as RL_Array;
use RegularLabs\Library\DB as RL_DB;
use RegularLabs\Library\Form\Form as RL_Form;
use RegularLabs\Library\Form\FormField as RL_FormField;

class ContentCategoriesField extends RL_FormField
{
    public $is_select_list  = true;
    public $use_ajax        = true;
    public $use_tree_select = true;

    public function getNamesByIds($values, $attributes)
    {
        $query = $this->db->getQuery(true)
            ->select('c.id, c.title as name, c.published, c.language')
            ->from('#__categories AS c')
            ->where('c.extension = ' . $this->db->quote('com_content'))
            ->where(RL_DB::is('c.id', $values))
            ->order('c.lft');
        $this->db->setQuery($query);
        $categories = $this->db->loadObjectList();

        return RL_Form::getNamesWithExtras($categories, ['language', 'unpublished']);
    }

    protected function getOptions()
    {
        $query = $this->db->getQuery(true)
            ->select('COUNT(*)')
            ->from('#__categories as c')
            ->where('c.extension = ' . $this->db->quote('com_content'))
            ->where('c.parent_id > 0')
            ->where('c.published > -1');
        $this->db->setQuery($query);
        $total = $this->db->loadResult();

        if ($total > $this->max_list_count)
        {
            return -1;
        }

        $this->value = RL_Array::toArray($this->value);

        $query->clear('select')
            ->select('c.id, c.title as name, c.published, c.language, c.level')
            ->order('c.lft');
        $this->db->setQuery($query);
        $list = $this->db->loadObjectList();

        return $this->getOptionsByList($list, ['language', 'unpublished'], -1);
    }
}
