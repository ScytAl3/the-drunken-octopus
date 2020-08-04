/**
 * 
 * @property {HTMLElement} pagination
 * @property {HTMLElement} content
 * @property {HTMLElement} sorting
 * @property {HTMLElement} form
 */
export default class Filter {

    /**
     * 
     * @param {HTMLElement|null} element 
     */
    constructor(element) {
        if (element === null) {
            return
        }
        // Recuperation de tous les éléments à modifier qui composent la page du filtre produits
        this.pagination = element.querySelector('.js-filter-pagination');
        this.content = element.querySelector('.js-filter-content');
        this.sorting = element.querySelector('.js-filter-sorting');
        this.form = element.querySelector('.js-filter-form');

        this.bindEvents()
    }

    /**
     * Ajoute les comportements aux différents éléments
     */
    bindEvents() {
        // Ecoute sur l'élément sorting (js-filter-sorting)
        this.sorting.addEventListener('click', e => {
            // Si l'événement est sur un lien
            if (e.target.tagName === 'A') {
                e.preventDefault();
                this.loadUrl(e.target.getAttribute('href'));
            }            
        })
    }

    async loadUrl(url) {
        const response = await fetch(url, {
            headers: {
                'X-Requested-with': 'XMLHttpRequest'
            }
        })
        if (response.status >= 200 && response.status < 300) {
            const data = await response.json();
            this.content.innerHTML = data.content;
            this.sorting.innerHTML = data.sorting;
        } else {
            console.error(response);
        }
    }
}