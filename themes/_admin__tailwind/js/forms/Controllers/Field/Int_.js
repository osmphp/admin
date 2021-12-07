import Field from "../Field";
import {register} from '../../../js/scripts';

export default register('int-field', class Int_ extends Field {
    data(data) {
        if (this.value.trim()) {
            data[this.name] = parseInt(this.value.trim());
        }
    }

    get input_element() {
        return this.element.querySelector('input');
    }

    get name() {
        return this.input_element.name;
    }

    get value() {
        return this.input_element.value;
    }
});