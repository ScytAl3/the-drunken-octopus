// Imports
import { Flipper, spring } from 'flip-toolkit'

/**
 * 
 * @property {HTMLElement} pagination
 * @property {HTMLElement} content
 * @property {HTMLElement} sorting
 * @property {HTMLFormElement} form
 * @property {HTMLElement} result
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
        // Recuperation de tous les éléments à modifier qui composent la page filtre + produits
        this.pagination = element.querySelector('.js-filter-pagination');
        this.content = element.querySelector('.js-filter-content');
        this.sorting = element.querySelector('.js-filter-sorting');
        this.form = element.querySelector('.js-filter-form');
        // Modification dynamique du résultat
        this.result = element.querySelector('.js-filter-result');

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
        // Appelle de la methode qui affiche le spinner-loader
        this.showLoader();
        /*
        Contruction d'une url pour la requête ajax
        */
        // Recupère la partie droite de l'url: les paramètres de la recherche
        const params = new URLSearchParams(url.split('?')[1] || '');
        params.set('ajax', 1); // Ajout de la clé ajax à l'url
        // Création de la requête: partie gauche  partie droite
        const response = await fetch(url.split('?')[0] + '?' + params.toString(), {
            headers: {
                'X-Requested-with': 'XMLHttpRequest'
            }
        });
        if (response.status >= 200 && response.status < 300) {
            const data = await response.json();
            console.log(data);
            // Remplace les contenus par la réponse
            this.flipContent(data.content); /* Utilisation du package flip-toolkit */
            this.sorting.innerHTML = data.sorting;
            this.pagination.innerHTML = data.pagination;
            this.result.innerHTML = data.result;
            // Si le traitement c'est bien déroulé les changement sont reflété dans l'url (ex: pour un partage)
            params.delete('ajax'); // Suppression de la clé ajax de l'url
            history.replaceState({}, '', url.split('?')[0] + '?' + params.toString());
        } else {
            console.error(response);
        }
        // Appelle de la methode qui masque le spinner-loader
        this.hideLoader();
    }

    /**
     * Remplace les éléments cards produits avec un effet d'animation flip
     * @param {string} content 
     */
    flipContent(content) {
        // Choix d'un préréglage spring
        const springPreset = 'gentle';
        /**
         * Initialisation de spring pour l'animation ressort - Exit
         */
        const onExitSpring = function (element, index, onComplete) {
            spring({
                config: 'stiff',
                values: {
                    translateY: [0, -20],
                    opacity: [1, 0]
                },
                onUpdate: ({ translateY, opacity }) => {
                    element.style.opacity = opacity;
                    element.style.transform = `translateY(${translateY}px)`;
                },
                // delay: i * 25,
                onComplete,
            });
        };

        /**
         * Initialisation de spring pour l'animation ressort - Appear
         */
        const onAppearSpring = function (element, index) {
            spring({
                config: 'stiff',
                values: {
                    translateY: [20, 0],
                    opacity: [0, 1]
                },
                onUpdate: ({ translateY, opacity }) => {
                    element.style.opacity = opacity;
                    element.style.transform = `translateY(${translateY}px)`;
                },
                delay: index * 15,
            });
        };

        /**
         * Initialisation de Flipper en lui passnat le container
         */
        const flipper = new Flipper({
            element: this.content
        });

        /*
        Parcours tous les enfants directs du content
        */
        this.content.children.forEach(element => {
            // add flipped children to the parent
            flipper.addFlipped({
                element,
                spring: springPreset, /* préréglage spring choisi */
                flipId: element.id,
                /* Éléments déjà présents dans le DOM et qui ne vont pas changer */
                shouldFlip: false,
                /* Appelé lorsque l'élément est retiré du DOM: appelle la fonction removeElement()
                    lorsque la transition de sortie est terminée */
                onExit: onExitSpring,
            });
        });
        // Mémorisation de la position de tous les éléments avant le changement
        flipper.recordBeforeUpdate();
        // Affichage du resultat
        this.content.innerHTML = content;

        /*
        Parcours pour trouver la position des nouveaux éléments
        */
        this.content.children.forEach(element => {
            // add flipped children to the parent
            flipper.addFlipped({
                element,
                spring: springPreset, /* préréglage spring choisi */
                flipId: element.id,
                /* Appelé lorsque l'élément apparaît pour la première fois dans le DOM */
                onAppear: onAppearSpring,
            });
        });
        // Enregistrer les nouvelles positions, et démarrer les animations
        flipper.update();
    }

    /**
     * Permet d'afficher le spinner-loader
     */
    showLoader() {
        this.form.classList.add('is-loading');
        const spinnerLoader = this.form.querySelector('.js-spinner-loading');
        // Si le loader n'existe pas on ne fait rien et on sort
        if (spinnerLoader == null) {
            return;
        }
        // S'il existe on passe la valeur du aria-hidden à false
        spinnerLoader.setAttribute('aria-hidden', 'false');
        // On rend l'élément visible
        spinnerLoader.style.display = null;
    }

    /**
     * Permet de cacher et masquer le spinner-loader
     */
    hideLoader() {
        this.form.classList.remove('is-loading');
        const spinnerLoader = this.form.querySelector('.js-spinner-loading');
        // Si le loader n'existe pas on ne fait rien et on sort
        if (spinnerLoader == null) {
            return;
        }
        // S'il existe on passe la valeur du aria-hidden à true
        spinnerLoader.setAttribute('aria-hidden', 'true');
        // On rend l'élément visible
        spinnerLoader.style.display = 'none';
    }
}