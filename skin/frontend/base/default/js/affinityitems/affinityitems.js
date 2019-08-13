/**
 * 2014 Affinity-Engine
 *
 * NOTICE OF LICENSE
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade AffinityItems to newer
 * versions in the future. If you wish to customize AffinityItems for your
 * needs please refer to http://www.affinity-engine.fr for more information.
 *
 *  @author    Affinity-Engine SARL <contact@affinity-engine.fr>
 *  @copyright 2014 Affinity-Engine SARL
 *  @license   http://www.gnu.org/licenses/gpl-2.0.txt GNU GPL Version 2 (GPLv2)
 *  International Registered Trademark & Property of Affinity Engine SARL
 */

function createCookie(name, value, days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        var expires = "; expires=" + date.toGMTString();
    }
    else
        var expires = "";
    document.cookie = name + "=" + value + expires + "; path=/";
}

function deleteCookie(name) {
    document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT; path=/';
}

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ')
            c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0)
            return c.substring(nameEQ.length, c.length);
    }
    return null;
}

/*function postAction(action, object) {
    var aegroup = readCookie('aegroup');
    var aeguest = readCookie('aeguest');

    if (!object.recoType) {
        aejQuery.ajax({
            type: 'POST',
            url: base_url + 'affinityitems/action/ajax',
            data: {recoType: object.recoType, productId: object.productId, action: action, guestId: aeguest, group: aegroup},
            async: true,
            dataType: 'json',
            complete: function(e, t, n) {
            }
        });
    } else {
        aejQuery.ajax({
            type: 'POST',
            url: base_url + 'affinityitems/action/ajax',
            data: {recoType: object.recoType, productId: object.productId, action: action, guestId: aeguest, group: aegroup},
            async: true,
            dataType: 'json',
            complete: function(e, t, n) {
            }
        });
        deleteCookie('aelastreco');
    }
}*/

function postAction(action, object) {
    var aegroup = readCookie('aegroup');
    var aeguest = readCookie('aeguest');
    if(!object.recoType) {
        if(object.productId) {
            aejQuery.ajax({
                type: 'POST',
                url: base_url + 'affinityitems/action/ajax',
                data: {productId: object.productId, action: action, guestId: aeguest, group: aegroup},
                async:true,
                dataType: 'json',
                complete: function(e,t,n) {}
            });
        } else if(object.categoryId) {
            aejQuery.ajax({
                type: 'POST',
                url: base_url + 'affinityitems/action/ajax',
                data: {categoryId: object.categoryId, action: action, guestId: aeguest, group: aegroup},
                async:true,
                dataType: 'json',
                complete: function(e,t,n) {}
            });
        }
    } else {
        aejQuery.ajax({
            type: 'POST',
            url: base_url + 'affinityitems/action/ajax',
            data: {recoType: object.recoType, productId: object.productId, action: action, guestId: aeguest, group: aegroup},
            async:true,
            dataType: 'json',
            complete: function(e,t,n) {}
        });
        deleteCookie('aelastreco');
    }
}

aejQuery(document).ready(function() {

    var paetimestamp = readCookie('paetimestamp');
    var caetimestamp = readCookie('caetimestamp');
    var aelastreco = readCookie('aelastreco');
    var aenow = new Date().getTime();

    if (aejQuery('#product_page_product_id').val()) {
        var aetimer = setInterval((function() {
            clearInterval(aetimer);
            postAction("read", {productId: aejQuery('#product_page_product_id').val()});
        }), 4000);
        createCookie('paetimestamp', (aenow + "." + aejQuery('#product_page_product_id').val()), 1);
    }

    if(paetimestamp){
        paetimestamp = paetimestamp.split('.');
        var diff = aenow - paetimestamp[0];
        if(diff < 4000) {
            postAction("rebound", {productId : paetimestamp[1]});
        }
    }

    if (typeof categoryId !== 'undefined') {
        var aetimer = setInterval((function() {
            clearInterval(aetimer);
            postAction("readCategory", {categoryId : categoryId});
        }), 4000);
        createCookie('caetimestamp', (aenow+"."+categoryId), 1);
    }

    if(caetimestamp) {
        caetimestamp = caetimestamp.split('.');
        var diff = aenow - caetimestamp[0];
        if(diff < 4000) {
            postAction("reboundCategory", {categoryId : caetimestamp[1]});
        }
    }

    /*var aetimestamp = readCookie('aetimestamp');
    var aelastreco = readCookie('aelastreco');
    var aenow = new Date().getTime();

    if (aejQuery('#product_page_product_id').val()) {
        var aetimer = setInterval((function() {
            clearInterval(aetimer);
            postAction("read", {productId: aejQuery('#product_page_product_id').val()});
        }), 4000);
        createCookie('aetimestamp', (aenow + "." + aejQuery('#product_page_product_id').val()), 1);
    }

    if (aetimestamp) {
        aetimestamp = aetimestamp.split('.');
        var diff = aenow - aetimestamp[0];
        if (diff < 4000) {
            postAction("rebound", {productId: aetimestamp[1]});
        }
    }*/

    if (aelastreco) {
        aelastreco = aelastreco.split('.');
        postAction("trackRecoClick", {recoType: aelastreco[1], productId: aelastreco[2]});
    }


});