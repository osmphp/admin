import Controller from "../../js/Controller";
import {register} from '../../js/scripts';
import {notice, fetch} from '../../messages/var/messages';
import Field from "./Field";

export default register('form', class Form extends Controller {
    get events() {
        return Object.assign({}, super.events, {
            'submit': 'onSubmit',
        });
    }

    onSubmit(e) {
        e.preventDefault();

        fetch(this.element.action, {
            method: this.element.method,
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(this.data),
            message: 'Saving new scope ...',
        })
        .then(response => {
            notice('Scope saved successfully.');

            throw 'not implemented';
        })
        .catch(() => null);
    }

    get $fields() {
        return this.options.$fields || '.field';
    }

    get field_elements() {
        return this.element.querySelectorAll(this.$fields);
    }

    get fields() {
        let fields = [];

        this.field_elements.forEach(element => {
            if (!element.osm_controllers) {
                return;
            }

            for (let controllerName in element.osm_controllers) {
                if (!element.osm_controllers.hasOwnProperty(controllerName)) {
                    continue;
                }

                let controller = element.osm_controllers[controllerName];
                if (controller instanceof Field) {
                    fields.push(controller);
                    return;
                }
            }
        });

        return fields;
    }

    get data() {
        let data = {};

        this.fields.forEach(field => field.data(data));

        return data;
    }
});