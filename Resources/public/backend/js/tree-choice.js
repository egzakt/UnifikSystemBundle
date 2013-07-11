$(function(){
    if (typeof('toggleSelectAllCheckbox') != 'function') {
        var select_all_objects = $('input.select_all');

        function toggleSelectAllCheckbox(select_all, initialize) {
            if (!initialize || (initialize && !select_all.data('initialized'))) {
                select_all.data('initialized', 1);

                var childrens = select_all.parents('ul:first').find('input:checkbox').not('.select_all');

                if (childrens.length == childrens.filter(':checked').length) {
                    select_all.prop('checked', true).nextAll('label:first').html(str_unselect_all);
                } else {
                    select_all.prop('checked', false).nextAll('label:first').html(str_select_all);
                }
            }
        }

        function toggleChildrens(checkbox) {
            if (checkbox.is(':checked')) {
                level = checkbox.data('level');
                checkbox.parent().nextAll('li').each(function(i){
                    children = $(this).children('input:checkbox');
                    if (children.data('level') > level) {
                        children.prop('checked', true);
                    } else {
                        return false;
                    }
                });
            }
        }

        select_all_objects.each(function(i){
            toggleSelectAllCheckbox($(this), true);
        });

        select_all_objects.click(function(){
            var select_all = $(this);
            var childrens = select_all.parents('ul:first').find('input:checkbox').not('.select_all');

            if (select_all.is(':checked')) {
                childrens.prop('checked', true);
                select_all.nextAll('label:first').html(str_unselect_all);
            } else {
                childrens.prop('checked', false);
                select_all.nextAll('label:first').html(str_select_all);
            }
        });

        select_all_objects.parents('ul').find('input:checkbox').not('.select_all').change(function(){
            toggleSelectAllCheckbox($(this).siblings('input.select_all'));
            toggleChildrens($(this));
        });
    }
});