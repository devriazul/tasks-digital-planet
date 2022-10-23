@extends('layouts.app')


<style>
    .pointer {cursor: pointer;}
</style>

@section('filter-section')

    <x-filters.filter-box>


        <!-- SEARCH BY TASK START -->
        <div class="task-search d-flex  py-1 pr-lg-3 px-0 border-right-grey align-items-center">
            <form class="w-100 mr-1 mr-lg-0 mr-md-1 ml-md-1 ml-0 ml-lg-0">
                <div class="input-group bg-grey rounded">
                    <div class="input-group-prepend">
                        <span class="input-group-text border-0 bg-additional-grey">
                            <i class="fa fa-search f-13 text-dark-grey"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control f-14 p-1 border-additional-grey" id="search-text-field"
                        placeholder="@lang('app.startTyping')">
                </div>
            </form>
        </div>
        <!-- SEARCH BY TASK END -->

        <!-- RESET START -->
        <div class="select-box d-flex py-1 px-lg-2 px-md-2 px-0">
            <x-forms.button-secondary class="btn-xs d-none" id="reset-filters" icon="times-circle">
                @lang('app.clearFilters')
            </x-forms.button-secondary>
        </div>
        <!-- RESET END -->
    </x-filters.filter-box>

@endsection

@php
$deleteKnowledgebasePermission = user()->permission('delete_knowledgebase');
$addknowledgebasePermission = user()->permission('add_knowledgebase');
@endphp

@section('content')
    <!-- CONTENT WRAPPER START -->
    <div class="content-wrapper">
        <!-- Add Task Export Buttons Start -->
        <div class="d-flex justify-content-between action-bar">
            <div id="table-actions" class="flex-grow-1 align-items-center mt-3">
                @if ($addknowledgebasePermission == 'all' || $addknowledgebasePermission == 'added')
                    <x-forms.link-primary :link="route('knowledgebase.create')" class="mr-3 openRightModal float-left" icon="plus">
                        @lang('modules.knowledgeBase.addknowledgebase')
                    </x-forms.link-primary>
                @endif
            </div>

            @if ($deleteKnowledgebasePermission == 'all' || $deleteKnowledgebasePermission == 'added')
                <x-datatable.actions>
                    <div class="select-status mr-3 pl-3">
                        <select name="action_type" class="form-control select-picker" id="quick-action-type" disabled>
                            <option value="">@lang('app.selectAction')</option>
                            <option value="delete">@lang('app.delete')</option>
                        </select>
                    </div>
                </x-datatable.actions>
            @endif

        </div>
        <!-- Add Task Export Buttons End -->
        <!-- Task Box Start -->

        <!-- Task Box End -->
        <div class="row mt-4" id="know_data">
            @php
                $knowledgebase_count = 0;
            @endphp

            @forelse ($categories as $category)

                <div class="col-md-4">
                    <div class="card border-0 b-shadow-4 mb-3 e-d-info" style="height: 200px;overflow: auto;">
                        <div class="card-horizontal">
                            <div class="card-body border-0">
                                <h4 class="card-title f-15 f-w-500 mb-3 d-flex justify-content-between">

                                <span> <i class="fa fa-folder-open mr-2"></i>{{ ucfirst($category->name) }} ({{ $count[$knowledgebase_count]['counts'] }})  </span>

                                @if ($addknowledgebasePermission == 'all' || $addknowledgebasePermission == 'added')
                                <span>
                                        <i class="icon-options-vertical icons pointer" id="dropdownMenuLink-3" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false"></i>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item openRightModal" href="{{ route('knowledgebase.create', ['id' => $category->id]) }}">
                                                <i class="fa fa-plus mr-2"></i>
                                                @lang('modules.knowledgeBase.addknowledgebase')
                                            </a>
                                        </div>
                                </span>
                                @endif
                            </h4>
                                <ul class="list-group">
                                    @foreach($knowledgebases as $knowledgebase)
                                        @if($knowledgebase->category_id == $category->id)
                                            <li class="list-group-item">
                                                <a href="{{ route('knowledgebase.show', $knowledgebase->id) }}" class="openRightModal text-darkest-grey d-block" ><i class="icon icon-doc"></i> {{  $knowledgebase->heading }}</a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                @php
                    $knowledgebase_count++;
                @endphp

            @empty
                <x-cards.no-record :message="__('messages.noRecordFound')" icon="file-alt" />
            @endforelse

        </div>
    </div>
    <!-- CONTENT WRAPPER END -->

@endsection
@push('scripts')

    <script>
        $('#search-text-field').on('change keyup', function() {
            if ($('#search-text-field').val() != "") {
                $('#reset-filters').removeClass('d-none');
            } else {
                $('#reset-filters').addClass('d-none');
            }
        });

        $('#reset-filters').click(function() {
            $('#filter-form')[0].reset();
            $('.select-picker').val('all');

            $('.select-picker').selectpicker("refresh");
            $('#reset-filters').addClass('d-none');

            showSearchData();
        });

        $('#quick-action-type').change(function() {
            const actionValue = $(this).val();
            if (actionValue != '') {
                $('#quick-action-apply').removeAttr('disabled');
            } else {
                $('#quick-action-apply').attr('disabled', true);
            }
        });

        function showSearchData()
        {
            var srch = $('#search-text-field').val();
            var url = "{{ route('knowledgebase.searchQuery', ':query') }}";
            url = url.replace(':query', srch);

            var token = "{{ csrf_token() }}";

            $.easyAjax({
                type: 'GET',
                url: url,
                data: {
                    '_token': token
                },
                success: function(response) {
                    if (response.status == "success") {
                        $("#know_data").html(response.html);
                    }
                }
            });
        }

        $('#search-text-field').on('change keyup', function() {
            showSearchData();
        });
    </script>
@endpush
