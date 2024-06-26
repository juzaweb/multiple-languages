@extends('cms::layouts.backend')

@section('content')
    <div class="row">
        <div class="col-md-4"></div>

        @php
            $type = get_config('mlla_type');
        @endphp

        <div class="col-md-8">
            @component('cms::components.form')
                {{ Field::select(trans('cms::app.type'), 'mlla_type', [
                    'value' => $type,
                    'options' => [
                        '' => '-----',
                        'prefix' => trans('mlla::content.prefix'),
                        'session' => trans('cms::app.multilingual_settings.session'),
                        'subdomain' => trans('cms::app.multilingual_settings.sub_domain'),
                    ],
                ]) }}

                <div class="box-form box-subdomain @if($type != 'subdomain') box-hidden @endif">
                    <table class="table" id="subdomain-table">
                        <thead>
                            <tr>
                                <th width="30%">{{ trans('cms::app.language') }}</th>
                                <th>{{ trans('cms::app.domain') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td colspan="2" align="right">
                                    <a href="javascript:void(0)" id="add-subdomain">{{ trans('cms::app.multilingual_settings.add_subdomain') }}</a>
                                </td>
                            </tr>

                            @foreach($subdomains as $subdomain)
                                @component('mlla::components.subdomain_item', [
                                    'marker' => $subdomain['sub'],
                                    'languages' => $languages,
                                    'item' => $subdomain,
                                ])

                                @endcomponent
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> {{ trans('cms::app.save') }}
                </button>
            @endcomponent
        </div>
    </div>

    <template id="subdomain-template">
        @component('mlla::components.subdomain_item', [
            'marker' => '{marker}',
            'languages' => $languages,
        ])

        @endcomponent
    </template>

    <script type="text/javascript">
        $('select[name=mlla_type]').on('change', function () {
            let val = $(this).val();
            $('.box-form').hide();
            $('.box-' + val).show('slow');
        });

        $('#add-subdomain').on('click', function () {
            let temp = document.getElementById('subdomain-template').innerHTML;
            let marker = (new Date()).getTime();
            $('#subdomain-table tbody').append(replace_template(temp, {marker: marker}));
        });

        $(document).on('change', '.select-language', function () {
            let val = $(this).val();
            $(this).closest('tr').find('.sub-domain').val(val);
        });
    </script>
@endsection