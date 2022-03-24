import Field from "../Field";
import {register} from '../../../js/scripts';

export default register('select-field', class Select extends Field {
    get events() {
        return Object.assign({}, super.events, {
            // event selector
            'change select': 'onChange',
        });
    }

    data(data) {
        if (this.changed) {
            data[this.name] = this.value;
        }
    }

    onAttached() {
        this.initial_value = this.select_element.value;
        this.initial_input_padding_right = parseFloat(
            getComputedStyle(this.select_element).paddingRight);

        super.onAttached();

        requestAnimationFrame(() => {
            this.updateActions();
        });
    }

    get changed() {
        return this.cleared_all_values ||
            this.select_element.value !== this.initial_value;
    }

    reset() {
        this.select_element.value = this.initial_value;
    }

    accept() {
        if (!(this.cleared_all_values || this.changed)) {
            return;
        }

        this.initial_value = this.select_element.value;
        this.options.multiple = false;
        this.onResetInitialValue();
    }

    get value() {
        const value = this.select_element.value;

        return value !== '' ? value : null;
    }

    get name() {
        return this.select_element.name;
    }

    get select_element() {
        return this.element.querySelector('select');
    }

    onChange() {
        this.updateActions();
    }

    updateActions() {
        super.updateActions();

        this.select_element.style.paddingRight =
            (this.initial_input_padding_right +
                this.actions_element.offsetWidth) + "px";
    }
});