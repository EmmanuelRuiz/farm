$(document).ready(function() {
    var ias = jQuery.ias({
        container: '.box-products',
        item: '.product-item',
        pagination: '.pagination',
        next: '.pagination .next-link',
        triggerPageThreshold: 5
    });

    ias.extension(new IASTriggerExtension({
        text: 'Ver más',
        offset: 3
    }));

    ias.extension(new IASSpinnerExtension({
        src: URL + '/../assets/images/loader-farm.gif'
    }));

    ias.extension(new IASNoneLeftExtension({
        text: 'No hay más productos'
    }));
});
