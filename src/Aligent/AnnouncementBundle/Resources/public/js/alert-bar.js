import BaseComponent from "oroui/js/app/components/base/component";
import mediator from "oroui/js/mediator";
const alertBarComponent = BaseComponent.extend({
    initialize() {
        const alertBar = document.querySelector('[data-js="alertBar"]');
        const alertClose = document.querySelector('[data-js="closeAlert"]');
        const alertContent = document.querySelector('[data-js="alertContent"]');
        const hideAlert = sessionStorage.getItem('hideAlert');
        // check if alert bar contains content and hasn't been hidden by user and is inside the date range
        if (alertContent.innerText.trim() != "" && !hideAlert) {
            alertBar.classList.remove('hidden');
            mediator.trigger('layout:reposition');
        }
        // if alertBox close clicked then hide alertBox for this session
        alertClose.addEventListener('click', (e) => {
            e.preventDefault();
            sessionStorage.setItem('hideAlert', true);
            const alertBar = document.querySelector('[data-js="alertBar"]');
            alertBar.classList.add('hidden');
            mediator.trigger('layout:reposition');
        });
    }
});
export default alertBarComponent;
