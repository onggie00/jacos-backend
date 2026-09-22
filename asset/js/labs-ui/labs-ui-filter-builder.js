/* =====================================================
   labs-ui-filter-builder.js
   Dynamic filter builder — reusable lintas modul.
   Pakai: window.LabsFilterBuilder.create(config)
   ===================================================== */

(function($) {
    'use strict';

    var operatorLabels = {
        'contains': 'Mengandung',
        'equals': 'Sama dengan',
        'starts_with': 'Diawali',
        'ends_with': 'Diakhiri',
        'not_equals': 'Tidak sama',
        'greater_than': 'Lebih besar',
        'less_than': 'Lebih kecil'
    };

    function escapeAttr(s) {
        return String(s == null ? '' : s).replace(/"/g, '&quot;');
    }

    function escapeHtml(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function(c) {
            return { '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[c];
        });
    }

    var LabsFilterBuilder = {

        /**
         * config: {
         *   container: '#selector',
         *   fields: [{value, label, type, operators, options?}],
         *   prefix: 'mhcu',                    // hidden input prefix
         *   initialFilters: [{field, operator, value}]
         * }
         * Hidden inputs: <prefix>_ff[], <prefix>_fo[], <prefix>_fv[]
         */
        create: function(config) {
            if (!config || !config.container) return null;

            var self = this;
            var $container = $(config.container);
            var $rows = $container.find('.labs-filter-builder__rows');
            var $empty = $container.find('.labs-filter-builder__empty');
            var $addBtn = $container.find('[data-labs-fb-add]');
            var prefix = config.prefix || 'fb';

            var state = {
                fields: config.fields || [],
                prefix: prefix,
                index: 0
            };

            // Tambah row
            $addBtn.on('click', function(e) {
                e.preventDefault();
                self._addRow(state, $rows, '', '', '');
            });

            // Hapus row
            $rows.on('click', '.labs-filter-row__remove', function(e) {
                e.preventDefault();
                $(this).closest('.labs-filter-row').remove();
                self._renumber($rows);
                self._updateEmpty($empty, $rows);
            });

            // Ganti field
            $rows.on('change', '.labs-filter-row__field', function() {
                var $row = $(this).closest('.labs-filter-row');
                var fieldValue = $(this).val();
                var ops = self._getOperators(state, fieldValue);
                var defaultOp = ops[0] || '';
                self._renderOperator($row, ops, defaultOp);
                self._renderValue($row, state, fieldValue, '');
                self._syncHidden($row);
            });

            // Ganti operator
            $rows.on('change', '.labs-filter-row__operator', function() {
                self._syncHidden($(this).closest('.labs-filter-row'));
            });

            // Ganti value
            $rows.on('change keyup', '.labs-filter-row__value', function() {
                self._syncHidden($(this).closest('.labs-filter-row'));
            });

            // Initial filters
            if (config.initialFilters && config.initialFilters.length) {
                config.initialFilters.forEach(function(f) {
                    self._addRow(state, $rows, f.field || '', f.operator || '', f.value || '');
                });
            }

            self._updateEmpty($empty, $rows);

            return {
                state: state,
                add: function(field, op, val) { self._addRow(state, $rows, field, op, val); },
                clear: function() {
                    $rows.empty();
                    state.index = 0;
                    self._updateEmpty($empty, $rows);
                }
            };
        },

        _getOperators: function(state, fieldValue) {
            for (var i = 0; i < state.fields.length; i++) {
                if (state.fields[i].value === fieldValue) return state.fields[i].operators || ['contains'];
            }
            return ['contains'];
        },

        _getFieldType: function(state, fieldValue) {
            for (var i = 0; i < state.fields.length; i++) {
                if (state.fields[i].value === fieldValue) return state.fields[i].type || 'text';
            }
            return 'text';
        },

        _getOptions: function(state, fieldValue) {
            for (var i = 0; i < state.fields.length; i++) {
                if (state.fields[i].value === fieldValue) return state.fields[i].options || [];
            }
            return [];
        },

        _renderOperator: function($row, operators, selected) {
            var h = '<select class="labs-filter-row__operator" title="Operator">';
            for (var i = 0; i < operators.length; i++) {
                var op = operators[i];
                var sel = (op === selected) ? ' selected' : '';
                h += '<option value="' + escapeAttr(op) + '"' + sel + '>' + escapeHtml(operatorLabels[op] || op) + '</option>';
            }
            h += '</select>';
            $row.find('.labs-filter-row__operator-wrap').html(h);
        },

        _renderValue: function($row, state, fieldValue, value) {
            var type = this._getFieldType(state, fieldValue);
            var options = this._getOptions(state, fieldValue);
            var h = '';

            if (type === 'select') {
                h = '<select class="labs-filter-row__value"><option value="">-- Pilih --</option>';
                for (var i = 0; i < options.length; i++) {
                    var opt = options[i];
                    var sel = (String(opt.id) === String(value)) ? ' selected' : '';
                    h += '<option value="' + escapeAttr(opt.id) + '"' + sel + '>' + escapeHtml(opt.text) + '</option>';
                }
                h += '</select>';
            } else {
                var inputType = (type === 'number') ? 'number' : 'text';
                h = '<input type="' + inputType + '" class="labs-filter-row__value" placeholder="Masukkan nilai..." value="' + escapeAttr(value) + '">';
            }
            $row.find('.labs-filter-row__value-wrap').html(h);
        },

        _addRow: function(state, $rows, fv, op, v) {
            state.index++;
            var num = state.index;
            var prefix = state.prefix;

            // Field select options
            var fieldOptions = '<option value="">-- Pilih Field --</option>';
            for (var i = 0; i < state.fields.length; i++) {
                var f = state.fields[i];
                var sel = (f.value === fv) ? ' selected' : '';
                fieldOptions += '<option value="' + escapeAttr(f.value) + '"' + sel + '>' + escapeHtml(f.label) + '</option>';
            }

            var ops = fv ? this._getOperators(state, fv) : ['contains'];
            if (!op || ops.indexOf(op) === -1) op = ops[0];

            var html =
                '<div class="labs-filter-row">' +
                  '<input type="hidden" name="' + prefix + '_ff[]" class="labs-filter-row__field-hidden" value="' + escapeAttr(fv) + '">' +
                  '<input type="hidden" name="' + prefix + '_fo[]" class="labs-filter-row__operator-hidden" value="' + escapeAttr(op) + '">' +
                  '<input type="hidden" name="' + prefix + '_fv[]" class="labs-filter-row__value-hidden" value="' + escapeAttr(v) + '">' +
                  '<span class="labs-filter-row__num">' + num + '</span>' +
                  '<select class="labs-filter-row__field">' + fieldOptions + '</select>' +
                  '<span class="labs-filter-row__operator-wrap"></span>' +
                  '<span class="labs-filter-row__value-wrap"></span>' +
                  '<button type="button" class="labs-filter-row__remove" title="Hapus filter"><i class="fa fa-times"></i></button>' +
                '</div>';

            var $row = $(html);
            $rows.append($row);

            this._renderOperator($row, ops, op);
            this._renderValue($row, state, fv, v);
        },

        _syncHidden: function($row) {
            $row.find('.labs-filter-row__field-hidden').val($row.find('.labs-filter-row__field').val());
            $row.find('.labs-filter-row__operator-hidden').val($row.find('.labs-filter-row__operator').val());
            $row.find('.labs-filter-row__value-hidden').val($row.find('.labs-filter-row__value').val());
        },

        _renumber: function($rows) {
            $rows.find('.labs-filter-row').each(function(idx) {
                $(this).find('.labs-filter-row__num').text(idx + 1);
            });
        },

        _updateEmpty: function($empty, $rows) {
            if ($rows.find('.labs-filter-row').length > 0) {
                $empty.hide();
            } else {
                $empty.show();
            }
        }
    };

    window.LabsFilterBuilder = LabsFilterBuilder;

})(jQuery);