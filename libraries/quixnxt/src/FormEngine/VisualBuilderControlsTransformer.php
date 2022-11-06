<?php

namespace QuixNxt\FormEngine;

use QuixNxt\FormEngine\Contracts\ControlsTransformer;
use QuixNxt\FormEngine\Transformers\BackgroundTransformer;
use QuixNxt\FormEngine\Transformers\BorderTransformer;
use QuixNxt\FormEngine\Transformers\ChooseTransformer;
use QuixNxt\FormEngine\Transformers\CodeTransformer;
use QuixNxt\FormEngine\Transformers\ColorPickerTransformer;
use QuixNxt\FormEngine\Transformers\DatePickerTransformer;
use QuixNxt\FormEngine\Transformers\DimensionsTransformer;
use QuixNxt\FormEngine\Transformers\DividerTransformer;
use QuixNxt\FormEngine\Transformers\EditorTransformer;
use QuixNxt\FormEngine\Transformers\FieldsGroupTransformer;
use QuixNxt\FormEngine\Transformers\GroupRepeaterTransformer;
use QuixNxt\FormEngine\Transformers\IconPickerTransformer;
use QuixNxt\FormEngine\Transformers\InputRepeaterTransformer;
use QuixNxt\FormEngine\Transformers\LinkTransformer;
use QuixNxt\FormEngine\Transformers\MediaTransformer;
use QuixNxt\FormEngine\Transformers\NoteTransformer;
use QuixNxt\FormEngine\Transformers\SelectTransformer;
use QuixNxt\FormEngine\Transformers\SliderTransformer;
use QuixNxt\FormEngine\Transformers\SwitchTransformer;
use QuixNxt\FormEngine\Transformers\TextareaTransformer;
use QuixNxt\FormEngine\Transformers\TextTransformer;
use QuixNxt\FormEngine\Transformers\TimePickerTransformer;
use QuixNxt\FormEngine\Transformers\TypographyTransformer;

class VisualBuilderControlsTransformer extends ControlsTransformer
{
  /**
   * Create a new instance of controls transform.
   *
   * @since 3.0.0
   */
    public function __construct()
    {
        $this->_prepare();
    }

  /**
   * Register the transformers
   *
   * @since 3.0.0
   */
    private function _prepare(): void
    {
        $this->add('editor', EditorTransformer::class)
         ->add('border', BorderTransformer::class)
         ->add('select', SelectTransformer::class)
         ->add('media', MediaTransformer::class)
         ->add('textarea', TextareaTransformer::class)
         ->add('link', LinkTransformer::class)
         ->add('note', NoteTransformer::class)
         ->add('divider', DividerTransformer::class)
         ->add('switch', SwitchTransformer::class)
         ->add('group-repeater', GroupRepeaterTransformer::class)
         ->add('input-repeater', InputRepeaterTransformer::class)
         ->add('fields-group', FieldsGroupTransformer::class)
         ->add('color', ColorPickerTransformer::class)
         ->add('date', DatePickerTransformer::class)
         ->add('time', TimePickerTransformer::class)
         ->add('code', CodeTransformer::class)
         ->add('icon', IconPickerTransformer::class)
         ->add('slider', SliderTransformer::class)
         ->add('typography', TypographyTransformer::class)
         ->add('margin', DimensionsTransformer::class)
         ->add('padding', DimensionsTransformer::class)
         ->add('dimensions', DimensionsTransformer::class)
         ->add('background', BackgroundTransformer::class)
         ->add('choose', ChooseTransformer::class)
         ->add('text', TextTransformer::class)
         ->add('default', TextTransformer::class);
    }
}
