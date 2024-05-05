{{ Field::select($model, 'translation[locale]', [
    'options' => $languages,
    'value' => $selected,
    'class' => 'form-control select-language',
    'label' => trans('cms::app.language'),
]) }}
