import axios from 'axios';
import { forEach } from 'lodash';
import './bootstrap';

class App {

    init() {
        this.#taskChangeStatus();
        this.#taskShare();
        this.#autoHideAlert();
    }

    #taskChangeStatus() {
        if (document.querySelector('.js-taskChangeStatus')) {
            const target = document.querySelectorAll('.js-taskChangeStatus');
            forEach(target, element => {
                element.addEventListener('click', event => {
                    const id = element.parentElement.parentElement.id.split('-')[1];
                    axios.post('/task/changeStatus', { params: { id } });
                })
            });
        }
    }

    #taskShare() {
        if (document.querySelector('.js-taskShare')) {
            const target = document.querySelectorAll('.js-taskShare');
            forEach(target, element => {
                element.addEventListener('click', event => {
                    const id = element.parentElement.parentElement.id.split('-')[1];
                    axios.post('/task/share', { params: { id } })
                        .then(function (response) {
                            if (response.data) {
                                forEach(element.children, child => {
                                    child.classList.contains('d-none')
                                        ? child.classList.remove('d-none')
                                        : child.classList.add('d-none');
                                });
                            }
                        })
                    ;
                })
            });
        }
    }

    #autoHideAlert() {
        if (document.querySelector('.js-autoHideAlert')) {
            document.querySelectorAll('.js-autoHideAlert').forEach(element => {
                if (element.classList.contains('important')) return;
                setTimeout(() => {
                    element.classList.remove('show');
                    element.remove();
                }, 2000);
            });
        }
    }

}

window.App = new App;
window.App.init();