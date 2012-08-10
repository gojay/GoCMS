tinymce.PluginManager.requireLangPack('advimagecustom');
(function () {
    tinymce.create("tinymce.plugins.AdvancedImageCustomPlugin", {
        init: function (a, b) {
            a.addCommand("mceAdvimageCustom", function () {
                if (a.dom.getAttrib(a.selection.getNode(), "class", "").indexOf("mceItem") != -1) {
                    return
                }
                a.windowManager.open({
                    file: b + "/image.htm",
                    width: 480 + parseInt(a.getLang("advimage.delta_width", 0)),
                    height: 385 + parseInt(a.getLang("advimage.delta_height", 0)),
                    inline: 1
                }, {
                    plugin_url: b
                })
            });
            a.addButton("advimagecustom", {
                title: "advimage.image_desc",
                cmd: "mceAdvimageCustom",
				image: 	b + '/images/image.gif'
            })
        },
        getInfo: function () {
            return {
                longname: "Advanced image",
                author: "Moxiecode Systems AB",
                authorurl: "http://tinymce.moxiecode.com",
                infourl: "http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/advimagecustom",
                version: tinymce.majorVersion + "." + tinymce.minorVersion
            }
        }
    });
    tinymce.PluginManager.add("advimagecustom", tinymce.plugins.AdvancedImageCustomPlugin)
})();