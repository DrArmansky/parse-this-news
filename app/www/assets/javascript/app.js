'use strict';

function NewsParser() {

    this.routes = null;
    this.el = {
        'form': '#parseSettings', 'sources': '.source-table'
    };

    this.template = {
        'sourceElement': '<tr data-source="#DATA_SOURCE#">\n' + '<td>#RESOURCE#</td>\n' + '<td>#IS_PARSED#</td>\n' + '<td><button class="parse-resource">Парсить</button></td>\n' + '</tr>'
    }

    this.init = function (routes) {

        if (routes === undefined) {
            console.error('Routes are not defined');
            return false;
        }

        this.routes = routes
        this.initHandlers();
    }

    this.initHandlers = function () {
        $(document).on('submit', this.el.form, this.formSubmitHandler.bind(this));
        $(window).on('load', this.getSources.bind(this));
    }

    this.formSubmitHandler = function (e) {
        e.preventDefault();

        let path = this.routes['SETTINGS'];
        if (path === undefined) {
            console.error('Settings path are not defined');
            return false;
        }

        let formData = $(e.target).serialize();

        $.ajax({
            method: "POST",
            url: path,
            data: formData,
            success: this.successSettingSaveHandler.bind(this)
        });
    }

    this.successSettingSaveHandler = function (answer) {
        if (answer['result'] === undefined) {
            return false;
        }

        let template = this.template.sourceElement;
        template = template.replace(new RegExp('#RESOURCE#', 'g'), answer['result']['resource']);
        template = template.replace(new RegExp('#DATA_SOURCE#', 'g'), answer['result']['resource']);
        template = template.replace(new RegExp('#IS_PARSED#', 'g'), 'NO');
        $(this.el.sources).append(template);
    }

    this.getSources = function (e) {
        e.preventDefault();

        let path = this.routes['SOURCES'];
        if (path === undefined) {
            console.error('Sources path are not defined');
            return false;
        }

        $.ajax({
            method: "GET",
            url: path,
            success: this.updateSourcesByAjaxData.bind(this)
        });
    }

    this.updateSourcesByAjaxData = function (answer) {
        if (answer['result'] === undefined) {
            return false;
        }

        let that = this;
        $(answer['result']).each(function (key, item) {
            let template = that.template.sourceElement;
            template = template.replace(new RegExp('#RESOURCE#', 'g'), item['NAME']);
            template = template.replace(new RegExp('#DATA_SOURCE#', 'g'), item['NAME']);
            template = template.replace(new RegExp('#IS_PARSED#', 'g'), item['IS_PARSED'] ? 'YES' : 'NO');
            $(that.el.sources).append(template);
        });
    }
}