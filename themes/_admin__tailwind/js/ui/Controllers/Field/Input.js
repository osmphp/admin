import Field from "../Field";
import {register} from '../../../js/scripts';

export default register('input-field', class Input extends Field {
    get events() {
        return Object.assign({}, super.events, {
            // event selector
            'input .field__single-input': 'onInput',
        });
    }

    data(data) {
        if (this.changed) {
            data[this.name] = this.value;
        }
    }

    onAttached() {
        this.initial_value = this.input_element.value;
        this.initial_input_padding_right = parseFloat(
            getComputedStyle(this.input_element).paddingRight);

        super.onAttached();

        requestAnimationFrame(() => {
            this.updateActions();
        });
    }

    get changed() {
        return this.cleared_all_values ||
            this.input_element.value !== this.initial_value;
    }

    reset() {
        this.input_element.value = this.initial_value;
    }

    accept() {
        if (!(this.cleared_all_values || this.changed)) {
            return;
        }

        this.initial_value = this.input_element.value;
        this.options.multiple = false;
        this.onResetInitialValue();
    }

    get value() {
        const value = this.input_element.value.trim();

        return value !== '' ? value : null;
    }

    get name() {
        return this.input_element.name;
    }

    get input_element() {
        return this.element.querySelector('.field__single-input');
    }

    onInput() {
        this.updateActions();
    }

    updateActions() {
        super.updateActions();

        this.input_element.style.paddingRight =
            (this.initial_input_padding_right +
                this.actions_element.offsetWidth) + "px";
    }
});