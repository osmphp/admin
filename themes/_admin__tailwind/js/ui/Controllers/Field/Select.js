import Field from "../Field";
import {register} from '../../../js/scripts';

export default register('select-field', class Select extends Field {
    get events() {
        return Object.assign({}, super.events, {
            // event selector
            'input input': 'onInput',
        });
    }

    data(data) {
        if (this._cleared || this.changed) {
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
        return this.select_element.value !== this.initial_value;
    }

    set cleared(cleared) {
        super.cleared = cleared;

        if (!this.options.multiple) {
            return;
        }

        this.select_element.value = this.initial_value;
        this.select_element.placeholder = cleared
            ? this.options.s_empty
            : this.options.s_multiple_values;
    }

    reset() {
        this.select_element.value = this.initial_value;
        if (this.options.multiple) {
            this.cleared = false;
        }
    }

    accept() {
        if (!(this._cleared || this.changed)) {
            return;
        }

        this.initial_value = this.select_element.value;
        this.select_element.placeholder = '';
        this.options.multiple = false;
        this.cleared = false;
    }

    get value() {
        const value = this.select_element.value.trim();

        return value !== '' ? value : null;
    }

    get name() {
        return this.select_element.name;
    }

    get select_element() {
        return this.element.querySelector('select');
    }

    onInput() {
        this.updateActions();
    }

    updateActions() {
        super.updateActions();

        this.select_element.style.paddingRight =
            (this.initial_input_padding_right +
                this.actions_element.offsetWidth) + "px";
    }
});