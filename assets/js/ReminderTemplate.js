/* global $ */
import {DeleteButton} from "../DeleteButton";
import {summernote} from "../summernote";
import {selectpicker} from "../utils";

export function ReminderTemplate(selector) {
    DeleteButton($(".js-delete"));

    $(selector).ModalForm({
        beforeShow: function () {
            const modal = $(this);
            selectpicker(modal);
            summernote(modal);

            const selectContext = $("select.js-modal-context, input.js-modal-context", modal);
            selectContext.change(function () {
                fetchPlaceholders();
            });

            const fetchPlaceholders = function () {
                $.ajax({
                    url: "/reminder/reminder-template/placeholders",
                    type: "GET",
                    data: {alias: selectContext.val()},
                    success: function (response) {
                        $(".note-placeholders", modal).empty();
                        response.placeholders.forEach(function (placeholder) {
                            $(".note-placeholders", modal).append("<span class=\"label label-primary js-placeholder\" data-placeholder=\"" +
                                placeholder.placeholder + "\">" + placeholder.title + "</span>");
                        });
                    }
                });
            };

            fetchPlaceholders();
        },
        afterClose: function () {
            window.location.reload();
        }
    });
}