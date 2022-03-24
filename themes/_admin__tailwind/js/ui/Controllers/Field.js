import Controller from "../../js/Controller";

/**
 * @property {boolean} edit True if editing existing record
 * @property {boolean} multiple True if the field edits multiple values
 */
export default class Field extends Controller {
    get events() {
        return Object.assign({}, super.events, {
            // event selector
            'click .field__clear-multiple-values': 'onClearMultipleValues',
            'click .field__reset-initial-value': 'onResetInitialValue',
        });
    }

    data(data) {
        throw 'Not implemented';
    }

    get changed() {
        throw 'Not implemented';
    }

    reset() {
        throw 'Not implemented';
    }

    get cleared_all_values() {
        return this.options.multiple &&
            this.multiple_element.classList.contains('hidden');
    }

    accept() {
        throw 'Not implemented';
    }

    get actions_element() {
        return this.element.querySelector('.field__actions');
    }

    get multiple_element() {
        return this.element.querySelector('.field__multiple');
    }

    get single_element() {
        return this.element.querySelector('.field__single');
    }

    get reset_initial_value_element() {
        return this.element.querySelector('.field__reset-initial-value');
    }

    onClearMultipleValues() {
        this.multiple_element.classList.add('hidden');
        this.single_element.classList.remove('hidden');

        this.updateActions();
    }

    onResetInitialValue() {
        this.reset();

        if (this.options.multiple) {
            this.single_element.classList.add('hidden');
            this.multiple_element.classList.remove('hidden');
        }

        this.updateActions();
    }

    updateActions() {
        if (this.changed) {
            this.reset_initial_value_element.classList.remove('hidden');
        }
        else {
            this.reset_initial_value_element.classList.add('hidden');
        }
    }
};