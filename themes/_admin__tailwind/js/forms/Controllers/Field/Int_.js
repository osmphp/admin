import Input from "./Input";
import {register} from '../../../js/scripts';

export default register('int-field', class Int_ extends Input {
    get value() {
        const value = super.value;
        return value !== null ? parseInt(value) : null;
    }
});