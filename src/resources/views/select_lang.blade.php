{{ Field::select($model, 'locale', [
    'options' => $languages,
    'value' => $selected,
    'class' => 'form-control select-language',
    'label' => trans('cms::app.language'),
]) }}
