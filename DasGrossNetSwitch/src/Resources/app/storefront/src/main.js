import DasTaxSwitcherPlugin from './das-tax-switcher/das-tax-switcher.plugin';

const PluginManager = window.PluginManager;
PluginManager.register('DasTaxSwitcher', DasTaxSwitcherPlugin,'[data-gross-net-switch]');

document.addEventListener('DOMContentLoaded', function (){
        document.addEventListener('change', function ( event ){
                console.log(event.target);
                if (event.target.id == 'grossNetToggleOffcanvas') {
                    const isChecked = event.target.checked;
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
                                    document.getElementById("grossNetToggleOffcanvas").checked = true;
                                    document.getElementById("grossNetToggleOffcanvas").setAttribute("checked",'checked') ;
                                    document.querySelectorAll(".gross-net--toggle.gross").forEach(el => el.classList.remove("d-none"));
                                    document.querySelectorAll(".gross-net--toggle.net").forEach(el => el.classList.add("d-none"));
                                    break;
                                case 'net':
                                    console.log('Switch net toggled successfully', data);
                                    document.getElementById("grossNetToggleOffcanvas").checked = false;
                                    document.getElementById("grossNetToggleOffcanvas").removeAttribute("checked") ;
                                    document.querySelectorAll(".gross-net--toggle.net").forEach(el => el.classList.remove("d-none"));
                                    document.querySelectorAll(".gross-net--toggle.gross").forEach(el => el.classList.add("d-none"));
                                    break;
                                default:
                                    console.error('Failed to toggle gross/net prices -- attribute "grossNetSwitch" not set');
                            }
                        })
                        .catch(error => console.error('Error occurred when toggling gross/net prices:', error));
                }
        });

});