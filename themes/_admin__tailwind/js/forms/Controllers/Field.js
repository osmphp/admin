import Controller from "../../js/Controller";

export default class Field extends Controller {
    _cleared = false;

    get events() {
        return Object.assign({}, super.events, {
            // event selector
            'click .field__clear': 'onClear',
            'click .field__reset': 'onReset',
        });
    }

    data(data) {
        throw 'Not implemented';
    }

    get changed() {
        // this.value !== this.options.value
        throw 'Not implemented';
    }

    reset() {
        throw 'Not implemented';
    }

    accept() {
        throw 'Not implemented';
    }

    set cleared(cleared) {
        this._cleared = cleared;

        this.updateActions();
    }

    get actions_element() {
        return this.element.querySelector('.field__actions');
    }

    get clear_element() {
        return this.element.querySelector('.field__clear');
    }

    get reset_element() {
        return this.element.querySelector('.field__reset');
    }

    onClear() {
        this.cleared = true;
    }

    onReset() {
        this.reset();

        this.updateActions();
    }

    updateActions() {
        if (this.options.multiple) {
            if (!this._cleared) {
                this.clear_element.classList.remove('hidden');
            }
            else {
                this.clear_element.classList.add('hidden');
            }
        }
        else if (this.clear_element) {
            this.clear_element.classList.add('hidden');
        }

        if (this._cleared || this.changed) {
            this.reset_element.classList.remove('hidden');
        }
        else {
            this.reset_element.classList.add('hidden');
        }
    }
};