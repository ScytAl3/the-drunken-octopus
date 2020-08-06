// Imports
import { Flipper } from 'flip-toolkit'

/**
 * 
 * @property {HTMLElement} pagination
 * @property {HTMLElement} content
 * @property {HTMLElement} sorting
 * @property {HTMLFormElement} form
 */
export default class Filter {

    /**
     * Constructeur de la class Filter
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
        // Récupère le href lors du click sur un hyperlink <a>
        const aClickListener = e => {
            // Si l'événement est sur un lien
            if (e.target.tagName === 'A') {
                e.preventDefault();

            } this.loadUrl(e.target.getAttribute('href'));
        };
        /*
        Écoute sur l'élément sorting (js-filter-sorting)
        */
        this.sorting.addEventListener('click', aClickListener);
        /*
        Écoute sur l'élément pagination (js-filter-pagination)
        */
        this.pagination.addEventListener('click', aClickListener);
        /*
        Écoute sur l'élément form du filtre (js-filter-form)
        */
        // Ajoute un listener pour écouter le changement sur chaque checkbox
        this.form.querySelectorAll('input[type=checkbox]').forEach(input => {
            input.addEventListener('change', this.loadForm.bind(this))
        });
        // Ajoute un listener pour écouter lors de la saisie dans le champ de recherche
        this.form.querySelector('input[type=text]').addEventListener('keyup', this.loadForm.bind(this));
    }

    /**
     * 
     */
    async loadForm() {
        // Récupère les données à partir du formulaire pour les convertir grace à FormData()
        const data = new FormData(this.form);
        // Creation d'une nouvelle url: si pas d'attribut 'action' récupère l'url courante
        const url = new URL(this.form.getAttribute('action') || window.location.href);
        // Génère les paramètres dynamiquement
        const params = new URLSearchParams();
        // en bouclant sur les données du formulaire
        data.forEach((value, key) => {
            params.append(key, value);
        });
        //
        // debugger;
        //
        // Convertit les données en url en recupérant la partie de l'url avant le "?"
        // à laquelle on ajoute les paramètres sous forme de chaine de caractères
        return this.loadUrl(url.pathname + '?' + params.toString());
    }

    /**
     * 
     * @param {*} url 
     */
    async loadUrl(url) {
        // Fix le problème de l'url du fichier json en cache: ajoute le suffixe ajax à l'url
        const ajaxUrl = url + '&ajax=1';
        // Passage de l'url préfixée dans le fetch
        const response = await fetch(ajaxUrl, {
            headers: {
                'X-Requested-with': 'XMLHttpRequest'
            }
        })
        if (response.status >= 200 && response.status < 300) {
            const data = await response.json();
            // Remplace les contenus par la réponse
            this.flipContent(data.content); /* Utilisation du package flip-toolkit de la librairie */
            this.sorting.innerHTML = data.sorting;
            this.pagination.innerHTML = data.pagination;
            // Si le traitement c'est bien déroulé les changement sont reflété dans l'url (ex: pour un partage)
            history.replaceState({}, '', url)
        } else {
            console.error(response);
        }
    }

    /**
     * Remplace les éléments cards produits avec un effet d'animation flip
     * @param {string} content 
     */
    flipContent(content) {
        // Initialisation de Flipper en lui passnat le container
        const flipper = new Flipper({
            element: this.content
        });
        // Parcours tous les enfants directs du content
        this.content.children.forEach(element => {
            // add flipped children to the parent
            flipper.addFlipped({
                element,
                flipId: element.id,
                /* Éléments déjà présents dans le DOM et qui ne vont pas changer */
                shouldFlip: false,
                /* Appelé lorsque l'élément est retiré du DOM: appelle la fonction remove()
                    lorsque la transition de sortie est terminée */
                onExit: (element, index, removeElement) => {
                    window.setTimeout(() => {
                        removeElement();
                    }, 2000);
                },
            });
        });
        // Mémorisation de la position de tous les éléments avant le changement
        flipper.recordBeforeUpdate();
        // Affichage du resultat
        this.content.innerHTML = content;
        // Parcours pour trouver la position des nouveaux éléments
        this.content.children.forEach(element => {
            // add flipped children to the parent
            flipper.addFlipped({
                element,
                flipId: element.id,
            });
        });
        // Enregistrer les nouvelles positions, et démarrer les animations
        flipper.update();
    }
}