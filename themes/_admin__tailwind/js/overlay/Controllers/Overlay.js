import Controller from "../../js/Controller";
import {register} from '../../js/scripts';

export default register('overlay', class Overlay extends Controller {
   visible = false;

   show() {
      this.visible = true;
      this.element.classList.remove(this.options.hidden_class);
   }

   hide() {
      this.visible = false;
      this.element.classList.add(this.options.hidden_class);
   }
});
