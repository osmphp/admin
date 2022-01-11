import Field from "../Field";

export default class Input extends Field {
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
        this.initial_value = this.input_element.value;
        this.initial_input_padding_right = parseFloat(
            getComputedStyle(this.input_element).paddingRight);

        super.onAttached();

        requestAnimationFrame(() => {
            this.updateActions();
        });
    }

    get changed() {
        return this.input_element.value !== this.initial_value;
    }

    set cleared(cleared) {
        super.cleared = cleared;

        if (!this.options.multiple) {
            return;
        }

        this.input_element.value = this.initial_value;
        this.input_element.placeholder = cleared
            ? '<empty>'
            : '<multiple values>';
    }

    reset() {
        this.input_element.value = this.initial_value;
        if (this.options.multiple) {
            this.cleared = false;
        }
    }

    accept() {
        if (!(this._cleared || this.changed)) {
            return;
        }

        this.initial_value = this.input_element.value;
        this.input_element.placeholder = '';
        this.options.multiple = false;
        this.cleared = false;
    }

    get value() {
        const value = this.input_element.value.trim();

        return value !== '' ? value : null;
    }

    get name() {
        return this.input_element.name;
    }

    get input_element() {
        return this.element.querySelector('input');
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
};