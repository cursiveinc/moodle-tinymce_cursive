/**
 * @module     tiny_cursive/modal_functions
 * @category TinyMCE Editor
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 */

define(["jquery", "core/ajax", "core/str", "core/templates", "./replay"], function (
    $,
    AJAX,
    str,
    templates,
    Replay
) {
    var actions = {
        init: function () {

            $(".popup_item").on('click', function () {

                // $(".modal").hide();
                let mid = $(this).data('id');
                $("#" + mid).show();
            });
            $(".typeid").on('click', function () {
                // $(".modal").hide();
                let mid = $(this).data('id');
                $("#typeid" + mid).show();
            });
            $(".video_playback").on('click', function () {
                alert(1);
                let mid = $(this).data('id');
                var filepath = $(this).data("filepath");
                $("#playback" + mid).show();
                new Replay(
                    elementId = 'output',
                    filePath = filepath,
                    speed = 10,
                    loop = false,
                    controllerId = 'player'
                );
            });
            // $(window).on('click', function (e) {
            //     if (e.target.id == 'modal-close' + userid) {
            //         $("#" + userid).hide();
            //     }
            // });
        }
    };
    return actions;
});