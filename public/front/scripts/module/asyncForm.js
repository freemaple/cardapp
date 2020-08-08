define(['jquery'], function ($) {
    var asyncForm = (function () {
        var that = this;
        var iframe = function (options) {
            options = options || {
                id: "iframe" + Math.random(),
                name: "iframe",
                src: ""
            };
            var iframe;
            try {
                iframe = document.createElement("<iframe name=" + options.name + ">")
            } catch (e) {
                iframe = document.createElement("iframe");
                iframe.name = options.name
            }
            options.id && (iframe.id = options.id);
            iframe.src = options.src;
            iframe.style.cssText = options.cssText;
            return iframe
        };
        var getDoc = function (frame) {
            var doc = frame.contentWindow ? frame.contentWindow.document : frame.contentDocument ? frame.contentDocument :
                frame.document;
            return doc
        };
        function AsyncForm(form, op) {
            that.op = op;
            var uuid = AsyncForm.uuid++;
            this.state = 0;
            this.form = form;
            this.file = form;
            var name = "upload_file_" + uuid;
            this.iframe = iframe({
                name: name,
                src: "javascript:;",
                cssText: "display:none;"
            });
            document.body.appendChild(this.iframe);
            this.form.target = name
        }
        var callbackFunction;
        var cb = function (e) {
             var doc = getDoc(this);
                var docRoot = doc.body ? doc.body : doc.documentElement;
                var json = docRoot.innerHTML;
                try{
                    var responseText = eval("(" + json + ")");
                } catch(e) {
                    var responseText =(new Function("","return "+json))();
                    var responseText = JSON.parse(json);
                }
                callbackFunction(responseText, e);
                document.body.removeChild(this)
        };
        AsyncForm.uuid = 0;
        AsyncForm.prototype = {
            checkState: function () {
                var up = this;
                var iframe = getDoc(this.iframe);
                var state = iframe.readyState;
                if (state && state.toLowerCase() == "uninitialized") {
                    setTimeout(function () {
                        up.checkState.apply(up)
                    }, 50)
                }
            },
            submit: function (callback) {
                callbackFunction = callback;
                var async = this;
                if (this.iframe.attachEvent) {
                    this.iframe.attachEvent("onload", function (e) {
                        cb.apply(async.iframe, [e])
                    })
                } else {
                    this.iframe.addEventListener("load", cb, false)
                }
                setTimeout(function () {
                    async.checkState.apply(async)
                }, 15);
                this.form.submit()
            },
            readyState: function () {
                return this.state
            },
            cancel: function () {}
        };
        return AsyncForm
    })();
    return asyncForm;
});