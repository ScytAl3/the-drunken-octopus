// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
import $ from 'jquery';

$(document).ready(function () {
    var $container = $('.js-quantity-modifier');

    // Cherche l'evenement qui se produit sur un un tag <a> contenu dans le conteneur 
    $container.find('a').on('click', function (e) {
        e.preventDefault();

        // Recupère l'element en cours de clic: le lien <a>
        var $link = $(e.currentTarget);
        // Recupère l'identifiant du produit - data-itemId dans la balise du conteneur
        var $productid = $link.parent().data('itemid');
        // Recupère la direction - up: ajouter, down: enlever
        var $direction = $link.data('direction');
        // Récupère l'url préfixé avec la locale
        const $url = new URL(window.location.href);

        // console.log($link);
        // console.log($productid);
        // console.log($direction);
        // console.log($url);

        $.ajax({
            // Construction de l'url
            url: $url + '/' + $productid + '/quantity/' + $direction,
            type: 'POST',
            data: {
                "id": $productid,
                "direction": $direction,
            },
            dataType: 'json',
            success: function (data) {
                // console.log(data);

                // Mise à jour du produit qui est en coours de modification
                // Si la quantité voulue existe en stock
                if (data.newQuantity) {
                    // Affiche la nouvelle quantité
                    $("#js-quantity-product-" + $productid).text(data.newQuantity);
                } else {
                    // Sinon affiche une alerte de stock insuffisant
                    alert("The quantity in stock is insufficient");
                }
                $("#js-montant-product-" + $productid).html(data.newTotal);

                // mise a jour de la quantite et du montant total du panier
                $("#js-cart-quantity").html(data.newProductCount);
                $("#js-cart-total").html(data.panierNewTotal);

                // mise a jour du compteurpanier du header
                $("#js-cart-count").text(data.productCoundHeader);
            }
        })
    });
});

console.log('Cart is charged');