@php
    $survey_name = config('voyager.survey.survey_name', 'Form');
@endphp

{{-- @include('saidy-voyager-survey::bread.styles.survey_action_styles') --}}
<a class="btn btn-info" id="bulk_survey_btn"><i class="voyager-megaphone"></i> <span>{{ $survey_name }}</span></a>

{{-- Bulk import modal --}}
<div class="modal modal-info fade" tabindex="-1" id="bulk_survey_modal" role="dialog">
    <form action="{{ route('voyager.' . $dataType->slug . '.action') }}" id="bulk_survey_form" method="POST">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">
                        <i class="voyager-upload"></i> {{ $survey_name }}:  <span id="bulk_survey_count"></span> <span
                            id="bulk_survey_display_name"></span>
                    </h4>
                </div>
                <div class="modal-body" id="bulk_survey_modal_body">
                    {{ csrf_field() }}
                    <div class="form-group col-md-12 ">
                        <label class="control-label" for="entity-slug">Entity</label>
                        <input id="entity-slug" type="text" class="form-control" name="slug"
                            value="{{ $dataType->slug }}" readonly>
                    </div>

                    <div class="form-group col-md-12 ">
                        <label class="control-label" for="entity-starts_at">Start Date</label>
                        <input id="entity-starts_at" type="date" class="form-control" name="starts_at"
                            value="{{ $action->getSurveyModelData('starts_at', null, true) }}">
                    </div>

                    <div class="form-group col-md-12 ">
                        <label class="control-label" for="entity-ends_at">End Date</label>
                        <input id="entity-ends_at" type="date" class="form-control" name="ends_at"
                            value="{{ $action->getSurveyModelData('ends_at', null, true) }}">
                    </div>

                    <div class="form-group col-md-12 ">
                        <label class="control-label" for="entity-options">Optional Details</label>
                        <textarea id="entity-options" class="form-control" name="options" rows="4">{{ $action->getSurveyModelData('options', '{}') }}</textarea>
                    </div>
                    @if ($action->hasSurveyModel())
                        <div class="form-group col-sm-12">
                            <label class="control-label" for="entity-link">Survey Link</label>
                            <div class="d-flex" style="display: flex;">
                                <input id="entity-link" type="text" class="form-control"
                                    value="{{ $action->getSurveyLink() }}" readonly>
                                <span id="survey-link-copy-btn" class="btn btn-sm btn-default"
                                    style="margin-left: 1em;margin-top: 0;">
                                    <span class="icon voyager-documentation"></span>
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    @if ($action->hasSurveyModel())
                        <input type="hidden" name="id" value="{{ $action->getSurveyModelData('id') }}">
                    @endif
                    <!-- <button type="submit" {!! $action->convertAttributesToHtml() !!}><i class="{{ $action->getIcon() }}"></i> <span class="hidden-xs hidden-sm">{{ $action->getTitle() }}</span></button> -->
                    <input type="hidden" name="action" value="{{ get_class($action) }}">
                    <!-- <input type="hidden" name="ids" value="" class="selected_ids"> -->
                    <!-- <input type="hidden" name="ids" id="bulk_survey_input" value=""> -->
                    <input type="submit" class="btn btn-info pull-right import-confirm" value="{{ __('Confirm') }}">
                    <button type="button" class="btn btn-default pull-right"
                        data-dismiss="modal">{{ __('Cancel') }}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </form>
</div><!-- /.modal -->

@push('javascript')
    <script>
        $(function() {
            // Bulk import selectors
            var $bulkImportBtn = $('#bulk_survey_btn');
            var $bulkImportModal = $('#bulk_survey_modal');
            var $bulkImportCount = $('#bulk_survey_count');
            var $bulkImportDisplayName = $('#bulk_survey_display_name');
            var $bulkImportInput = $('#bulk_survey_input');
            var $copyBtn = $('#survey-link-copy-btn');
            // Reposition modal to prevent z-index issues
            $bulkImportModal.appendTo('body');
            // Bulk import listener
            $bulkImportBtn.click(function() {
                var ids = [];
                var $checkedBoxes = $('#dataTable input[type=checkbox]:checked').not('.select_all');
                var count = $checkedBoxes.length;
                // if (count) {
                // Reset input value
                $bulkImportInput.val('');
                // Deletion info
                var displayName = count > 1 ?
                    '{{ $dataType->getTranslatedAttribute('display_name_plural') }}' :
                    '{{ $dataType->getTranslatedAttribute('display_name_singular') }}';
                displayName = displayName.toLowerCase();
                // $bulkImportCount.html(count);
                $bulkImportDisplayName.html(displayName);
                // Gather IDs
                $.each($checkedBoxes, function() {
                    var value = $(this).val();
                    ids.push(value);
                })
                // Set input value
                $bulkImportInput.val(ids);
                // Show modal
                $bulkImportModal.modal('show');
                // } else {
                //     // No row selected
                //     toastr.warning('{{ __('saidy-voyager-survey::generic.bulk_survey_nothing') }}');
                // }
            });

            $copyBtn.click(function(e) {
                var $copyBtnLink = document.getElementById('entity-link');

                // Select the text field
                $copyBtnLink.select();
                $copyBtnLink.setSelectionRange(0, 99999); // For mobile devices

                toastr.success('Link copied.')

                // Copy the text inside the text field
                navigator.clipboard.writeText($copyBtnLink.value);

            })
        });
    </script>
@endpush
