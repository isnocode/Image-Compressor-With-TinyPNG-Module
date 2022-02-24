/**
 * @author    ELEGANTAL <info@elegantal.com>
 * @copyright (c) 2022, ELEGANTAL <www.elegantal.com>
 * @license   Proprietary License - It is forbidden to resell or redistribute copies of the module or modified copies of the module.
 */

var elegantaltinypngimagecompressAdminUrl = '';
var elegantaltinypngimagecompressCompressUrl = '';
var elegantaltinypngimagecompressId = 0;
var elegantaltinypngimagecompressTotal = 0;
var elegantaltinypngimagecompressProcessed = 0;
var elegantaltinypngimagecompressNotProcessed = 0;
var elegantaltinypngimagecompressCompressed = 0;
var elegantaltinypngimagecompressFailed = 0;
var elegantaltinypngimagecompressPaused = false;
var elegantaltinypngimagecompressAnalyzing = false;
var elegantalFormGroupClass = 'form-group';

jQuery(document).ready(function () {

    // Identify form group class
    if (jQuery('[type="submit"]').parents('.margin-form').length > 0) {
        elegantalFormGroupClass = 'margin-form';
    }

    // Back button fix on < 1.6.1
    jQuery('.panel-footer button[name="submitOptionsmodule"]').click(function () {
        if (jQuery(this).find('.process-icon-back')) {
            var url = window.location.href.replace(/&event=\w+/gi, '');
            window.location.href = url;
        }
    });

    // List page
    jQuery('.elegantal_readme_btn').click(function () {
        jQuery('.elegantal_readme_modal').modal('show');
    });
    jQuery('.elegantal_select_all').click(function (e) {
        e.preventDefault();
        jQuery('.elegantal_select_checkbox input[type="checkbox"]').each(function (index, el) {
            jQuery(el).prop('checked', true);
        });
    });
    jQuery('.elegantal_unselect_all').click(function (e) {
        e.preventDefault();
        jQuery('.elegantal_select_checkbox input[type="checkbox"]').each(function (index, el) {
            jQuery(el).prop('checked', false);
        });
    });
    jQuery('.elegantal_delete_selected').click(function (e) {
        e.preventDefault();
        var bulk_delete_url = jQuery(this).data('url');
        var ids = new Array();
        jQuery('.elegantal_select_checkbox input[type="checkbox"]:checked').each(function (index, el) {
            ids.push(jQuery(el).data('id'));
        });
        if (ids.length > 0) {
            if (confirm("Delete selected items?")) {
                jQuery('.elegantal_ajax_loader').fadeIn();
                jQuery.ajax({
                    url: bulk_delete_url,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        ids: ids
                    },
                    success: function (result) {
                        if (result.redirect) {
                            window.location.href = result.redirect;
                        }
                        jQuery('.elegantal_ajax_loader').hide();
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        jQuery('.elegantal_ajax_loader').hide();
                    }
                });
            }
        } else {
            alert("You must select at least one element to delete.");
        }
    });

    // Settings Page
    if (jQuery('[name="editSettings"]').length > 0) {
        elegantalEditSettingsFormVisibility(0);
        jQuery('input, select').on('change', function () {
            elegantalEditSettingsFormVisibility(250);
        });
    }

    // Settings Page
    if (jQuery('.elegantal_log_panel').length > 0) {
        jQuery('.elegantal_log_panel .btn-group .dropdown-menu a').click(function (e) {
            jQuery(this).parents(".dropdown-menu").hide();
        });
    }

    // Analyze Page
    if (jQuery('.elegantal_analyze_panel').length > 0) {
        elegantaltinypngimagecompressAdminUrl = jQuery('.elegantaltinypngimagecompressJsDef').data('adminurl');
        elegantaltinypngimagecompressCompressUrl = jQuery('.elegantaltinypngimagecompressJsDef').data('compressurl');

        // Prevent accidental page reload
        window.onbeforeunload = function () {
            if (elegantaltinypngimagecompressAnalyzing) {
                return jQuery('.elegantal_analyze_panel').data('reloadmsg');
            }
        };

        // Start analyzing with the first request
        elegantalAnalyze(1);
    }

    // Compress Page
    if (jQuery('.elegantal_compress_panel').length > 0) {
        elegantaltinypngimagecompressAdminUrl = jQuery('.elegantaltinypngimagecompressJsDef').data('adminurl');
        elegantaltinypngimagecompressId = parseInt(jQuery('.elegantaltinypngimagecompressJsDef').data('modelid'));
        elegantaltinypngimagecompressTotal = parseInt(jQuery('.elegantaltinypngimagecompressJsDef').data('total'));
        elegantaltinypngimagecompressNotProcessed = parseInt(jQuery('.elegantaltinypngimagecompressJsDef').data('notprocessed'));
        elegantaltinypngimagecompressProcessed = elegantaltinypngimagecompressTotal - elegantaltinypngimagecompressNotProcessed;
        elegantaltinypngimagecompressCompressed = parseInt(jQuery('.elegantaltinypngimagecompressJsDef').data('compressed'));
        elegantaltinypngimagecompressFailed = parseInt(jQuery('.elegantaltinypngimagecompressJsDef').data('failed'));

        jQuery('.elegantal_compress_btn').click(function () {
            jQuery('.elegantal_hide_on_compress').hide();
            jQuery('.elegantal_show_on_compress').show();
            elegantalCompress();
        });
        jQuery('.elegantal_pause_btn').click(function () {
            elegantaltinypngimagecompressPaused = true;
            jQuery('.elegantal_hide_on_pause').hide();
            jQuery('.elegantal_show_on_pause').show();
        });
        jQuery('.elegantal_resume_btn').click(function () {
            elegantaltinypngimagecompressPaused = false;
            jQuery('.elegantal_hide_on_resume').hide();
            jQuery('.elegantal_show_on_resume').show();
            elegantalCompress();
        });
    }

    if (jQuery('#elegantal_products_search').length > 0) {
        var elegantal_timer;
        elegantaltinypngimagecompressAdminUrl = jQuery('.elegantaltinypngimagecompressJsDef').data('adminurl');
        jQuery('#elegantal_products_search').on('keyup', function (e) {
            var input = jQuery(this);
            var productsList = input.parent().parent().find("#elegantal_loaded_products_list");
            var query = input.val();
            var chosenProducts = [];
            jQuery('#elegantal_chosen_products_list input[type="checkbox"]').each(function (index, el) {
                chosenProducts.push(jQuery(el).parents('tr').data('id'));
            });
            if (query.length > 0) {
                window.clearTimeout(elegantal_timer);
                elegantal_timer = setTimeout(function () {
                    $.ajax({
                        url: elegantaltinypngimagecompressAdminUrl + '&event=loadProductsForSelect',
                        type: 'POST',
                        data: {
                            q: query,
                            chosenProducts: chosenProducts
                        },
                        success: function (result) {
                            productsList.find('tbody').html(result);
                        }
                    });
                }, 500);
            }
        });
        jQuery(document.body).on('change', '#elegantal_loaded_products_list input[type="checkbox"]', function () {
            if (jQuery(this).is(':checked')) {
                jQuery('#elegantal_products_search').parent().parent().find('#elegantal_chosen_products_list tbody').append(jQuery(this).parents('tr'));
                jQuery('.elegantal_products_list').animate({scrollTop: 0}, 500);
            }
        });
        jQuery(document.body).on('change', '#elegantal_chosen_products_list input[type="checkbox"]', function () {
            if (jQuery(this).not(':checked')) {
                jQuery(this).parents('tr').remove();
                jQuery('#elegantal_products_search').keyup();
            }
        });
        jQuery(document.body).on('click', '.elegantal_products_list table tr td:first-child', function () {
            jQuery(this).parent().find('input[type="checkbox"]').click();
        });
        jQuery(document.body).on('click', '#elegantal_products_search_icon', function () {
            jQuery(this).parent().find('#elegantal_products_search').focus().keyup();
        });
    }
});

/**
 * Function to analyze images in portions
 * @param {int} currentRequest
 */
function elegantalAnalyze(currentRequest) {
    elegantaltinypngimagecompressAnalyzing = true;
    var panel = jQuery('.elegantal_analyze_panel');
    var progress = panel.find('.elegantal_analyze_progress_bar');
    var id = panel.data('id');

    var offset = panel.data('offset');
    var limit = panel.data('limit');
    var totalRequests = panel.data('requests');

    var fakeProgressCounter = 5;
    if (totalRequests == 1) {
        var progressInt = setInterval(function () {
            progress.css({width: fakeProgressCounter + '%'});
            progress.text(Math.round(fakeProgressCounter) + '%');
            fakeProgressCounter += Math.random();
            if (fakeProgressCounter > 99) {
                clearInterval(progressInt);
            }
        }, 300);
    }

    // Generate random number for GET request. This is needed to prevent if there is cache for the URL
    var min = 100;
    var max = 100000000;
    var random = Math.floor(Math.random() * (max - min + 1)) + min;

    jQuery.ajax({
        url: elegantaltinypngimagecompressAdminUrl,
        type: 'GET',
        dataType: 'json',
        data: {
            event: 'analyze',
            ajax: 1,
            id_elegantaltinypngimagecompress: id,
            offset: offset,
            limit: limit,
            elegantal: random
        },
        success: function (result) {
            if (result.success) {
                var completed = (currentRequest * 100) / totalRequests;
                progress.css({width: completed + '%'});
                if (completed < 1) {
                    completed = completed.toFixed(2);
                } else {
                    completed = Math.round(completed);
                }
                progress.text(completed + '%');

                if (currentRequest < totalRequests) {
                    panel.data('offset', (offset + limit));
                    elegantalAnalyze(currentRequest + 1);
                } else {
                    fakeProgressCounter = 100;
                    elegantaltinypngimagecompressAnalyzing = false;
                    setTimeout(function () {
                        window.location.href = elegantaltinypngimagecompressCompressUrl;
                    }, 1000);
                }
            } else {
                fakeProgressCounter = 100;
                elegantaltinypngimagecompressAnalyzing = false;
                jQuery('.elegantal_analyze_error_txt').html(result.message);
                jQuery('.elegantal_analyze_error').fadeIn();
                jQuery('html, body').animate({scrollTop: 0});
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            if (errorThrown) {
                fakeProgressCounter = 100;
                elegantaltinypngimagecompressAnalyzing = false;
                jQuery('.elegantal_analyze_error_txt').text(errorThrown);
                jQuery('.elegantal_analyze_error').fadeIn();
                jQuery('html, body').animate({scrollTop: 0});
            } else {
                var completed = (currentRequest * 100) / totalRequests;
                progress.css({width: completed + '%'});
                if (completed < 1) {
                    completed = completed.toFixed(2);
                } else {
                    completed = Math.round(completed);
                }
                progress.text(completed + '%');

                if (currentRequest < totalRequests) {
                    panel.data('offset', (offset + limit));
                    elegantalAnalyze(currentRequest + 1);
                } else {
                    fakeProgressCounter = 100;
                    elegantaltinypngimagecompressAnalyzing = false;
                    setTimeout(function () {
                        window.location.href = elegantaltinypngimagecompressCompressUrl;
                    }, 1000);
                }
            }
        }
    });
}

/**
 * Function to compress images one by one
 */
function elegantalCompress() {
    var progress = jQuery('.elegantal_compress_progress_bar');

    // Generate random number for GET request. This is needed to prevent if there is cache for the URL
    var min = 100;
    var max = 100000000;
    var random = Math.floor(Math.random() * (max - min + 1)) + min;

    jQuery.ajax({
        url: elegantaltinypngimagecompressAdminUrl,
        type: 'GET',
        dataType: 'json',
        data: {
            id_elegantaltinypngimagecompress: elegantaltinypngimagecompressId,
            event: 'tinify',
            elegantal: random
        },
        success: function (result) {
            if (result.message) {
                alert(result.message);
            }
            if (result.next == 1 && elegantaltinypngimagecompressProcessed < elegantaltinypngimagecompressTotal) {
                elegantaltinypngimagecompressProcessed++;
                var i = (elegantaltinypngimagecompressProcessed * 100) / elegantaltinypngimagecompressTotal;
                progress.css({width: i + '%'});
                if (i < 1) {
                    i = i.toFixed(2);
                } else {
                    i = Math.round(i);
                }
                progress.text(i + '%');

                jQuery('.elegantal_images_not_compressed').text(elegantaltinypngimagecompressTotal - elegantaltinypngimagecompressProcessed);
                if (result.success) {
                    elegantaltinypngimagecompressCompressed++;
                    jQuery('.elegantal_images_compressed').text(elegantaltinypngimagecompressCompressed);
                } else {
                    elegantaltinypngimagecompressFailed++;
                    jQuery('.elegantal_images_failed').text(elegantaltinypngimagecompressFailed);
                }
                jQuery('.elegantal_images_size_after').text(result.imagesSizeAfter);
                jQuery('.elegantal_images_size_saved').text(result.sizeSaved);

                if (!elegantaltinypngimagecompressPaused) {
                    elegantalCompress();
                }
            } else {
                if (result.redirect) {
                    window.location.href = result.redirect;
                } else {
                    jQuery('.elegantal_hide_on_complete').hide();
                    jQuery('.elegantal_show_on_complete').show();

                    if (elegantaltinypngimagecompressFailed > 0) {
                        jQuery('.elegantal_readme_modal').modal('show');
                    }
                }
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            if (!elegantaltinypngimagecompressPaused) {
                jQuery('.elegantal_pause_btn').click();
                alert('There was a problem processing your request. Please try again. ' + errorThrown);
            }
        }
    });
}

function elegantalEditSettingsFormVisibility(speed) {
    if (jQuery('[name="compress_generated_images"]:checked').val() == 1) {
        jQuery('[name="image_formats_to_compress[]"]').parents('.' + elegantalFormGroupClass).fadeIn(speed);
        if (elegantalFormGroupClass == 'margin-form') {
            jQuery('[name="image_formats_to_compress[]"]').parents('.' + elegantalFormGroupClass).prev('label').fadeIn(speed);
        }
    } else {
        jQuery('[name="image_formats_to_compress[]"]').parents('.' + elegantalFormGroupClass).hide();
        if (elegantalFormGroupClass == 'margin-form') {
            jQuery('[name="image_formats_to_compress[]"]').parents('.' + elegantalFormGroupClass).prev('label').hide();
        }
    }
}