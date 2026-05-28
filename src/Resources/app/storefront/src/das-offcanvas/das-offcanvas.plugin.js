import Plugin from 'src/plugin-system/plugin.class';
import OffCanvasMenuPlugin from 'src/plugin/main-menu/offcanvas-menu.plugin';

export default class DasOffcanvasPlugin extends Plugin {

    init() {
        this._registerEvents();
    }

    _registerEvents() {
        document.addEventListener('DOMContentLoaded', function (){
            document.addEventListener('change', function ( event ){
                console.log(event.target);
                this._onToggleChange(event);
            });
        });
    }

    _onToggleChange(event) {
        const isChecked = event.target.checked;
        console.log('_onToggleChange', this, isChecked);
        const url = `/das-gross-net/${isChecked}/`;

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                switch (data.grossNetSwitch) {
                    case 'failed':
                        console.error('Failed to toggle gross/net prices');
                        break;
                    case 'gross':
                        console.log('Switch gross toggled successfully', data);
                        document.querySelectorAll(".gross-net--toggle.gross").forEach(el => el.classList.remove("d-none"));
                        document.querySelectorAll(".gross-net--toggle.net").forEach(el => el.classList.add("d-none"));
                        break;
                    case 'net':
                        console.log('Switch net toggled successfully', data);
                        document.querySelectorAll(".gross-net--toggle.net").forEach(el => el.classList.remove("d-none"));
                        document.querySelectorAll(".gross-net--toggle.gross").forEach(el => el.classList.add("d-none"));
                        break;
                    default:
                        console.error('Failed to toggle gross/net prices -- attribute "grossNetSwitch" not set');
                }
            })
            .catch(error => console.error('Error occurred when toggling gross/net prices:', error));
    }
}